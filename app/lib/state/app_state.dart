part of '../main.dart';

class AppState extends ChangeNotifier {
  final api = ApiClient();
  int tab = 0;
  String email = '';
  String userName = 'عميل USDT STORE';
  double balance = 2450;
  String kycStatus = 'pending';
  final networks = ['TRC20', 'ERC20', 'BEP20', 'Arbitrum', 'Solana'];
  final txs = <Tx>[
    Tx(
      'إرسال USDT',
      'send',
      -500,
      'TRC20',
      'completed',
      'TXN123456789',
      'اليوم 10:30 ص',
    ),
    Tx(
      'استلام USDT',
      'receive',
      1200,
      'ERC20',
      'completed',
      'RX9988123',
      'أمس 04:15 م',
    ),
    Tx(
      'إيداع USDT',
      'deposit',
      750,
      'TRC20',
      'pending',
      'DEP20260702',
      '2026/07/02',
    ),
    Tx(
      'سحب USDT',
      'withdraw',
      -50,
      'BEP20',
      'approved',
      'WD44550',
      'أمس 11:20 ص',
    ),
  ];
  final notifications = [
    'تم استلام طلب الإيداع وهو قيد المراجعة',
    'تذكير: أكمل توثيق الهوية لتفعيل كل العمليات',
  ];

  void setTab(int i) {
    tab = i;
    notifyListeners();
  }

  Future<void> requestOtp(String value) async {
    email = value;
    notifyListeners();
    await api.post('/auth/request-otp', {'email': value});
  }

  Future<void> verifyOtp(String code) async {
    final data = await api.post('/auth/verify-otp', {
      'email': email,
      'code': code,
      'name': userName,
    });
    api.token = data['token'] as String?;
  }

  void addTx(Tx tx) {
    txs.insert(0, tx);
    if (tx.type == 'send' || tx.type == 'withdraw') {
      balance -= tx.amount.abs();
    }
    if (tx.type == 'deposit' || tx.type == 'receive') {
      balance += tx.amount.abs();
    }
    notifyListeners();
  }
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
    this.fee = 1,
    this.note = '',
  });
  final String title, type, network, status, ref, date, note;
  final double amount, fee;
}
