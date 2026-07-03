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
        GoldButton(
          text: 'متابعة',
          onTap: () => Navigator.push(
            context,
            MaterialPageRoute(
              builder: (_) => ConfirmScreen(
                amount: amount.text,
                network: network,
                address: address.text,
                onConfirm: () {
                  context.read<AppState>().addTx(
                    Tx(
                      'إرسال USDT',
                      'send',
                      -double.parse(amount.text),
                      network,
                      'pending',
                      'TX${DateTime.now().millisecondsSinceEpoch}',
                      'الآن',
                      note: note.text,
                    ),
                  );
                },
              ),
            ),
          ),
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
  final VoidCallback onConfirm;
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
          onTap: () {
            onConfirm();
            Navigator.popUntil(context, (r) => r.isFirst);
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
    final address = 'USDT-$network-DEMO-2450';
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
            child: QrImageView(data: address, size: 190),
          ),
          const SizedBox(height: 18),
          SelectableText(
            address,
            textAlign: TextAlign.center,
            style: const TextStyle(color: gold2, fontSize: 16),
          ),
          const SizedBox(height: 16),
          GoldButton(text: 'نسخ العنوان', icon: Icons.copy, onTap: () {}),
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
        const DashedUpload(),
        const SizedBox(height: 22),
        GoldButton(
          text: 'إرسال الطلب للمراجعة',
          onTap: () {
            context.read<AppState>().addTx(
              Tx(
                'إيداع USDT',
                'deposit',
                double.tryParse(amount.text) ?? 0,
                network,
                'pending',
                txid.text.isEmpty
                    ? 'DEP-${DateTime.now().millisecondsSinceEpoch}'
                    : txid.text,
                'الآن',
              ),
            );
            Navigator.pop(context);
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
        GoldButton(
          text: 'إرسال طلب السحب',
          onTap: () {
            context.read<AppState>().addTx(
              Tx(
                'سحب USDT',
                'withdraw',
                -(double.tryParse(amount.text) ?? 0),
                'USDT',
                'pending',
                'WD-${DateTime.now().millisecondsSinceEpoch}',
                'الآن',
              ),
            );
            Navigator.pop(context);
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
