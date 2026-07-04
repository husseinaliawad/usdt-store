part of '../main.dart';

class AppState extends ChangeNotifier {
  final api = ApiClient();
  int tab = 0;
  String email = '';
  String userName = '';
  String kycStatus = 'pending';
  double balance = 0;
  bool loading = false;
  String? error;
  final networks = <NetworkOption>[];
  final wallets = <WalletInfo>[];
  final txs = <Tx>[];
  final notifications = <String>[];
  Map<String, double> stats = {
    'sent_total': 0,
    'received_total': 0,
    'fees_total': 0,
  };

  void setTab(int i) {
    tab = i;
    notifyListeners();
  }

  Future<void> login(String value) async {
    email = value;
    _clearSession();
    notifyListeners();
    final data = await api.post('/auth/login', {
      'email': value,
      'name': userName.isEmpty ? 'USDT STORE User' : userName,
    });
    api.token = data['token'] as String?;
    _applyUser(data['user'] as Map<String, dynamic>?);
    await refreshAll();
  }

  Future<void> refreshAll() async {
    loading = true;
    error = null;
    notifyListeners();
    try {
      final me = await api.get('/me');
      _applyUser(me);
      final home = await api.get('/home');
      _applyHome(home);
      final txPage = await api.get('/transactions');
      _applyTransactions(txPage['data'] as List<dynamic>? ?? []);
      final notePage = await api.get('/notifications');
      _applyNotifications(notePage['data'] as List<dynamic>? ?? []);
      final statsData = await api.get('/transactions/stats');
      stats = {
        'sent_total': _num(statsData['sent_total']),
        'received_total': _num(statsData['received_total']),
        'fees_total': _num(statsData['fees_total']),
      };
    } catch (e) {
      error = e.toString();
      rethrow;
    } finally {
      loading = false;
      notifyListeners();
    }
  }

  Future<void> sendUsdt({
    required String networkCode,
    required String address,
    required double amount,
    String? note,
  }) async {
    final network = networkByCode(networkCode);
    await api.post('/transactions/send', {
      'network_id': network.id,
      'wallet_address': address,
      'amount': amount,
      if (note != null && note.isNotEmpty) 'note': note,
    });
    await refreshAll();
  }

  Future<WalletInfo> receiveWallet(String networkCode) async {
    final network = networkByCode(networkCode);
    final data = await api.post('/receive', {'network_id': network.id});
    final wallet = WalletInfo.fromJson(data);
    await refreshAll();
    return wallet;
  }

  Future<void> deposit({
    required String networkCode,
    required double amount,
    required String txid,
    required PlatformFile proof,
  }) async {
    final network = networkByCode(networkCode);
    await api.multipart(
      '/transactions/deposit',
      fields: {
        'network_id': network.id.toString(),
        'amount': amount.toString(),
        'txid': txid,
      },
      files: {'proof': proof},
    );
    await refreshAll();
  }

  Future<void> withdraw({
    required double amount,
    required String method,
    required String recipient,
    String? note,
  }) async {
    await api.post('/transactions/withdraw', {
      'amount': amount,
      'withdraw_method': method,
      'recipient_payload': {'recipient': recipient},
      if (note != null && note.isNotEmpty) 'note': note,
    });
    await refreshAll();
  }

  Future<void> submitKyc({
    required String fullName,
    required String phone,
    required PlatformFile idImage,
    required PlatformFile selfieImage,
  }) async {
    await api.multipart(
      '/kyc',
      fields: {'full_name': fullName, 'phone': phone},
      files: {'id_image': idImage, 'selfie_image': selfieImage},
    );
    await refreshAll();
  }

  Future<void> sendSupport(String message) async {
    await api.post('/support', {'message': message});
    await refreshAll();
  }

  NetworkOption networkByCode(String code) {
    return networks.firstWhere(
      (n) => n.code == code,
      orElse: () => networks.isNotEmpty
          ? networks.first
          : NetworkOption(id: 1, code: code, name: code),
    );
  }

  WalletInfo? walletByNetwork(String code) {
    for (final wallet in wallets) {
      if (wallet.network.code == code) return wallet;
    }
    return null;
  }

  void _clearSession() {
    api.token = null;
    userName = '';
    kycStatus = 'pending';
    balance = 0;
    networks.clear();
    wallets.clear();
    txs.clear();
    notifications.clear();
    stats = {'sent_total': 0, 'received_total': 0, 'fees_total': 0};
  }

  void _applyUser(Map<String, dynamic>? data) {
    if (data == null) return;
    userName = (data['name'] ?? '').toString();
    email = (data['email'] ?? email).toString();
    kycStatus = (data['kyc_status'] ?? 'pending').toString();
  }

  void _applyHome(Map<String, dynamic> data) {
    balance = _num(data['balance_usdt']);
    networks
      ..clear()
      ..addAll(
        (data['networks'] as List<dynamic>? ?? []).map(
          (e) => NetworkOption.fromJson(e as Map<String, dynamic>),
        ),
      );
    wallets
      ..clear()
      ..addAll(
        (data['wallets'] as List<dynamic>? ?? []).map(
          (e) => WalletInfo.fromJson(e as Map<String, dynamic>),
        ),
      );
    txs
      ..clear()
      ..addAll(
        (data['latest_transactions'] as List<dynamic>? ?? []).map(
          (e) => Tx.fromJson(e as Map<String, dynamic>),
        ),
      );
  }

  void _applyTransactions(List<dynamic> data) {
    txs
      ..clear()
      ..addAll(data.map((e) => Tx.fromJson(e as Map<String, dynamic>)));
  }

  void _applyNotifications(List<dynamic> data) {
    notifications
      ..clear()
      ..addAll(data.map((e) => (e as Map<String, dynamic>)['body'].toString()));
  }
}

class NetworkOption {
  NetworkOption({required this.id, required this.code, required this.name});

  factory NetworkOption.fromJson(Map<String, dynamic> json) => NetworkOption(
    id: (json['id'] as num).toInt(),
    code: (json['code'] ?? json['name'] ?? '').toString(),
    name: (json['name'] ?? json['code'] ?? '').toString(),
  );

  final int id;
  final String code;
  final String name;
}

class WalletInfo {
  WalletInfo({
    required this.address,
    required this.balance,
    required this.network,
  });

  factory WalletInfo.fromJson(Map<String, dynamic> json) => WalletInfo(
    address: (json['address'] ?? '').toString(),
    balance: _num(json['balance']),
    network: NetworkOption.fromJson(json['network'] as Map<String, dynamic>),
  );

  final String address;
  final double balance;
  final NetworkOption network;
}

class Tx {
  Tx(
    this.title,
    this.type,
    this.amount,
    this.network,
    this.status,
    this.ref,
    this.date, {
    this.fee = 0,
    this.note = '',
  });

  factory Tx.fromJson(Map<String, dynamic> json) {
    final type = (json['type'] ?? '').toString();
    final amount = _num(json['amount']);
    final signedAmount = (type == 'send' || type == 'withdraw')
        ? -amount.abs()
        : amount.abs();
    final network = json['network'] is Map<String, dynamic>
        ? NetworkOption.fromJson(json['network'] as Map<String, dynamic>).code
        : (json['withdraw_method'] ?? 'USDT').toString();
    return Tx(
      _titleFor(type),
      type,
      signedAmount,
      network,
      (json['status'] ?? '').toString(),
      (json['txid'] ?? json['id'] ?? '').toString(),
      (json['created_at'] ?? json['completed_at'] ?? '').toString(),
      fee: _num(json['fee']),
      note: (json['note'] ?? '').toString(),
    );
  }

  final String title, type, network, status, ref, date, note;
  final double amount, fee;
}

double _num(dynamic value) {
  if (value is num) return value.toDouble();
  return double.tryParse((value ?? '0').toString()) ?? 0;
}

String _titleFor(String type) {
  switch (type) {
    case 'send':
      return 'Send USDT';
    case 'receive':
      return 'Receive USDT';
    case 'deposit':
      return 'Deposit USDT';
    case 'withdraw':
      return 'Withdraw USDT';
    default:
      return 'USDT Transaction';
  }
}
