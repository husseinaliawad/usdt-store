part of '../main.dart';

class SendScreen extends StatefulWidget {
  const SendScreen({super.key});
  @override
  State<SendScreen> createState() => _SendScreenState();
}

class _SendScreenState extends State<SendScreen> {
  String network = 'TRC20';
  final address = TextEditingController();
  final amount = TextEditingController(text: '100.00');
  final note = TextEditingController();
  String? message;
  @override
  Widget build(BuildContext context) => FormPage(
    title: 'إرسال USDT',
    child: Column(
      children: [
        TextField(
          controller: address,
          decoration: const InputDecoration(
            labelText: 'عنوان المحفظة',
            prefixIcon: Icon(Icons.account_circle_outlined),
          ),
        ),
        const SizedBox(height: 14),
        NetworkDropdown(
          value: network,
          onChanged: (v) => setState(() => network = v!),
        ),
        const SizedBox(height: 14),
        TextField(
          controller: amount,
          keyboardType: TextInputType.number,
          decoration: const InputDecoration(
            labelText: 'المبلغ',
            suffixText: 'USDT',
          ),
        ),
        const SizedBox(height: 14),
        TextField(
          controller: note,
          decoration: const InputDecoration(labelText: 'ملاحظة اختيارية'),
        ),
        const SizedBox(height: 24),
        if (message != null) ...[
          Text(message!, style: const TextStyle(color: Colors.redAccent)),
          const SizedBox(height: 12),
        ],
        GoldButton(
          text: 'متابعة',
          onTap: () {
            final parsedAmount = double.tryParse(amount.text);
            if (address.text.trim().isEmpty || parsedAmount == null) {
              setState(() => message = 'تأكد من العنوان والمبلغ');
              return;
            }
            Navigator.push(
              context,
              MaterialPageRoute(
                builder: (_) => ConfirmScreen(
                  amount: amount.text,
                  network: network,
                  address: address.text,
                  onConfirm: () => context.read<AppState>().sendUsdt(
                    networkCode: network,
                    address: address.text.trim(),
                    amount: parsedAmount,
                    note: note.text.trim(),
                  ),
                ),
              ),
            );
          },
        ),
      ],
    ),
  );
}

class ConfirmScreen extends StatelessWidget {
  const ConfirmScreen({
    super.key,
    required this.amount,
    required this.network,
    required this.address,
    required this.onConfirm,
  });
  final String amount, network, address;
  final Future<void> Function() onConfirm;
  @override
  Widget build(BuildContext context) => FormPage(
    title: 'تأكيد العملية',
    child: Column(
      children: [
        const Icon(Icons.check_circle_outline, color: gold, size: 96),
        const SizedBox(height: 20),
        const Text('تأكيد إرسال', style: TextStyle(fontSize: 20)),
        Text(
          '$amount USDT',
          style: const TextStyle(
            color: gold2,
            fontSize: 30,
            fontWeight: FontWeight.bold,
          ),
        ),
        InfoRow('إلى', address.isEmpty ? 'عنوان خارجي' : address),
        InfoRow('الشبكة', network),
        InfoRow('الرسوم', '1.00 USDT'),
        const SizedBox(height: 24),
        GoldButton(
          text: 'تم',
          onTap: () async {
            try {
              await onConfirm();
              if (!context.mounted) return;
              Navigator.popUntil(context, (r) => r.isFirst);
            } catch (e) {
              if (!context.mounted) return;
              ScaffoldMessenger.of(
                context,
              ).showSnackBar(SnackBar(content: Text(e.toString())));
            }
          },
        ),
      ],
    ),
  );
}

class ReceiveScreen extends StatefulWidget {
  const ReceiveScreen({super.key});
  @override
  State<ReceiveScreen> createState() => _ReceiveScreenState();
}

class _ReceiveScreenState extends State<ReceiveScreen> {
  String network = 'TRC20';
  @override
  Widget build(BuildContext context) {
    final appState = context.watch<AppState>();
    final address = appState.walletByNetwork(network)?.address ?? '';
    return FormPage(
      title: 'استلام USDT',
      child: Column(
        children: [
          NetworkDropdown(
            value: network,
            onChanged: (v) => setState(() => network = v!),
          ),
          const SizedBox(height: 18),
          Container(
            padding: const EdgeInsets.all(14),
            decoration: BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.circular(16),
            ),
            child: address.isEmpty
                ? const SizedBox(width: 190, height: 190)
                : QrImageView(data: address, size: 190),
          ),
          const SizedBox(height: 18),
          SelectableText(
            address.isEmpty ? 'لا يوجد عنوان لهذه الشبكة' : address,
            textAlign: TextAlign.center,
            style: const TextStyle(color: gold2, fontSize: 16),
          ),
          const SizedBox(height: 16),
          GoldButton(
            text: address.isEmpty ? 'إنشاء عنوان' : 'تحديث العنوان',
            icon: Icons.refresh,
            onTap: () async {
              try {
                await context.read<AppState>().receiveWallet(network);
              } catch (e) {
                if (!context.mounted) return;
                ScaffoldMessenger.of(
                  context,
                ).showSnackBar(SnackBar(content: Text(e.toString())));
              }
            },
          ),
        ],
      ),
    );
  }
}

class DepositScreen extends StatefulWidget {
  const DepositScreen({super.key});
  @override
  State<DepositScreen> createState() => _DepositScreenState();
}

class _DepositScreenState extends State<DepositScreen> {
  String network = 'TRC20';
  final amount = TextEditingController();
  final txid = TextEditingController();
  PlatformFile? proof;
  String? message;
  @override
  Widget build(BuildContext context) => FormPage(
    title: 'طلب إيداع',
    child: Column(
      children: [
        NetworkDropdown(
          value: network,
          onChanged: (v) => setState(() => network = v!),
        ),
        const SizedBox(height: 14),
        TextField(
          controller: amount,
          keyboardType: TextInputType.number,
          decoration: const InputDecoration(labelText: 'المبلغ'),
        ),
        const SizedBox(height: 14),
        TextField(
          controller: txid,
          decoration: const InputDecoration(labelText: 'رقم العملية TxID'),
        ),
        const SizedBox(height: 14),
        InkWell(
          onTap: () async {
            final picked = await FilePicker.platform.pickFiles(
              type: FileType.image,
              withData: true,
            );
            if (picked != null) setState(() => proof = picked.files.single);
          },
          child: DashedUpload(
            text: proof == null ? 'رفع صورة إثبات التحويل' : proof!.name,
          ),
        ),
        const SizedBox(height: 22),
        if (message != null) ...[
          Text(message!, style: const TextStyle(color: Colors.redAccent)),
          const SizedBox(height: 12),
        ],
        GoldButton(
          text: 'إرسال الطلب للمراجعة',
          onTap: () async {
            final parsedAmount = double.tryParse(amount.text);
            if (parsedAmount == null ||
                txid.text.trim().isEmpty ||
                proof == null) {
              setState(() => message = 'أدخل المبلغ و TxID وارفع صورة الإثبات');
              return;
            }
            try {
              await context.read<AppState>().deposit(
                networkCode: network,
                amount: parsedAmount,
                txid: txid.text.trim(),
                proof: proof!,
              );
              if (!context.mounted) return;
              Navigator.pop(context);
            } catch (e) {
              if (!context.mounted) return;
              setState(() => message = e.toString());
            }
          },
        ),
      ],
    ),
  );
}

class WithdrawScreen extends StatefulWidget {
  const WithdrawScreen({super.key});
  @override
  State<WithdrawScreen> createState() => _WithdrawScreenState();
}

class _WithdrawScreenState extends State<WithdrawScreen> {
  final amount = TextEditingController();
  final recipient = TextEditingController();
  String method = 'حوالة نقدية';
  String? message;
  @override
  Widget build(BuildContext context) => FormPage(
    title: 'طلب سحب',
    child: Column(
      children: [
        TextField(
          controller: amount,
          keyboardType: TextInputType.number,
          decoration: const InputDecoration(
            labelText: 'المبلغ',
            suffixText: 'USDT',
          ),
        ),
        const SizedBox(height: 14),
        DropdownButtonFormField(
          value: method,
          decoration: const InputDecoration(labelText: 'طريقة السحب'),
          items: [
            'حوالة نقدية',
            'حساب بنكي',
            'عنوان محفظة',
          ].map((e) => DropdownMenuItem(value: e, child: Text(e))).toList(),
          onChanged: (v) => setState(() => method = v!),
        ),
        const SizedBox(height: 14),
        TextField(
          controller: recipient,
          decoration: const InputDecoration(labelText: 'بيانات المستلم'),
        ),
        const SizedBox(height: 22),
        if (message != null) ...[
          Text(message!, style: const TextStyle(color: Colors.redAccent)),
          const SizedBox(height: 12),
        ],
        GoldButton(
          text: 'إرسال طلب السحب',
          onTap: () async {
            final parsedAmount = double.tryParse(amount.text);
            if (parsedAmount == null || recipient.text.trim().isEmpty) {
              setState(() => message = 'أدخل المبلغ وبيانات المستلم');
              return;
            }
            try {
              await context.read<AppState>().withdraw(
                amount: parsedAmount,
                method: method,
                recipient: recipient.text.trim(),
              );
              if (!context.mounted) return;
              Navigator.pop(context);
            } catch (e) {
              if (!context.mounted) return;
              setState(() => message = e.toString());
            }
          },
        ),
      ],
    ),
  );
}

class TransactionsScreen extends StatelessWidget {
  const TransactionsScreen({super.key});
  @override
  Widget build(BuildContext context) => BrandScaffold(
    child: SafeArea(
      child: Consumer<AppState>(
        builder: (_, s, __) => ListView(
          padding: const EdgeInsets.all(16),
          children: [
            const PageHeader('سجل العمليات'),
            Wrap(
              spacing: 8,
              children: ['الكل', 'pending', 'completed', 'approved', 'rejected']
                  .map(
                    (e) => Chip(
                      label: Text(e),
                      backgroundColor: e == 'الكل' ? gold : panel,
                    ),
                  )
                  .toList(),
            ),
            const SizedBox(height: 10),
            ...s.txs.map(
              (tx) => TxTile(
                tx: tx,
                onTap: () => Navigator.push(
                  context,
                  MaterialPageRoute(builder: (_) => TxDetailsScreen(tx: tx)),
                ),
              ),
            ),
          ],
        ),
      ),
    ),
  );
}

class TxDetailsScreen extends StatelessWidget {
  const TxDetailsScreen({super.key, required this.tx});
  final Tx tx;
  @override
  Widget build(BuildContext context) => FormPage(
    title: 'تفاصيل العملية',
    child: Column(
      children: [
        Icon(
          tx.amount >= 0 ? Icons.south_west : Icons.north_east,
          color: gold,
          size: 72,
        ),
        Text(
          tx.title,
          style: const TextStyle(fontSize: 22, fontWeight: FontWeight.bold),
        ),
        const SizedBox(height: 10),
        Text(
          '${tx.amount.toStringAsFixed(2)} USDT',
          style: TextStyle(
            color: tx.amount >= 0 ? Colors.greenAccent : Colors.white,
            fontSize: 26,
          ),
        ),
        InfoRow('الشبكة', tx.network),
        InfoRow('الرسوم', '${tx.fee.toStringAsFixed(2)} USDT'),
        InfoRow('رقم العملية', tx.ref),
        InfoRow('الحالة', tx.status),
        InfoRow('التاريخ', tx.date),
      ],
    ),
  );
}
