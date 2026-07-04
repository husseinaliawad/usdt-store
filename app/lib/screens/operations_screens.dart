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
  void dispose() {
    amount.dispose();
    txid.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final appState = context.watch<AppState>();
    final wallet = appState.walletByNetwork(network);
    final address = wallet?.address ?? '';

    return OperationPageShell(
      selectedIndex: 0,
      onDeposit: () {},
      onWithdraw: () => Navigator.pushReplacement(
        context,
        MaterialPageRoute(builder: (_) => const WithdrawScreen()),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.stretch,
        children: [
          OperationCard(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.stretch,
              children: [
                const GoldLabel('اختر العملة'),
                StyledSelectField(
                  value: 'USDT - $network',
                  icon: Icons.keyboard_arrow_down_rounded,
                  trailing: const TetherBadge(),
                  onTap: () {},
                ),
                const SizedBox(height: 16),
                const GoldLabel('شبكة الإيداع'),
                StyledNetworkDropdown(
                  value: network,
                  onChanged: (v) => setState(() => network = v ?? network),
                ),
                const SizedBox(height: 8),
                const Text(
                  'تأكد من اختيار نفس الشبكة عند الإرسال',
                  textAlign: TextAlign.left,
                  style: TextStyle(color: muted, fontSize: 12),
                ),
              ],
            ),
          ),
          OperationCard(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.stretch,
              children: [
                const GoldLabel('مبلغ الإيداع'),
                GoldInputField(
                  controller: amount,
                  hint: 'أدخل المبلغ',
                  suffix: 'USDT',
                  keyboardType: TextInputType.number,
                ),
                const SizedBox(height: 12),
                Row(
                  children: ['100', '500', '1,000', '5,000']
                      .map(
                        (v) => Expanded(
                          child: Padding(
                            padding: const EdgeInsets.symmetric(horizontal: 3),
                            child: AmountChip(
                              text: v,
                              onTap: () => setState(
                                () => amount.text = v.replaceAll(',', ''),
                              ),
                            ),
                          ),
                        ),
                      )
                      .toList(),
                ),
              ],
            ),
          ),
          OperationCard(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.stretch,
              children: [
                const GoldLabel('تفاصيل الإيداع'),
                const SizedBox(height: 8),
                Row(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Container(
                      padding: const EdgeInsets.all(8),
                      decoration: BoxDecoration(
                        color: Colors.white,
                        borderRadius: BorderRadius.circular(14),
                        boxShadow: [
                          BoxShadow(
                            color: gold.withValues(alpha: .16),
                            blurRadius: 18,
                            offset: const Offset(0, 8),
                          ),
                        ],
                      ),
                      child: address.isEmpty
                          ? const SizedBox(width: 128, height: 128)
                          : QrImageView(data: address, size: 128),
                    ),
                    const SizedBox(width: 14),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.stretch,
                        children: [
                          const Text(
                            'عنوان المحفظة',
                            textAlign: TextAlign.right,
                            style: TextStyle(color: muted, fontSize: 13),
                          ),
                          const SizedBox(height: 8),
                          SelectableText(
                            address.isEmpty
                                ? 'لا يوجد عنوان لهذه الشبكة'
                                : _shortAddress(address),
                            textAlign: TextAlign.right,
                            style: const TextStyle(
                              color: Colors.white,
                              fontSize: 14,
                              fontWeight: FontWeight.bold,
                            ),
                          ),
                          const SizedBox(height: 10),
                          MiniGoldAction(
                            text: address.isEmpty ? 'إنشاء العنوان' : 'تحديث العنوان',
                            icon: Icons.refresh_rounded,
                            onTap: () async {
                              try {
                                await context.read<AppState>().receiveWallet(network);
                              } catch (e) {
                                if (!context.mounted) return;
                                ScaffoldMessenger.of(context).showSnackBar(
                                  SnackBar(content: Text(e.toString())),
                                );
                              }
                            },
                          ),
                          const SizedBox(height: 10),
                          MiniGoldAction(
                            text: 'مشاركة العنوان',
                            icon: Icons.share_rounded,
                            onTap: () => ScaffoldMessenger.of(context).showSnackBar(
                              const SnackBar(content: Text('انسخ العنوان من النص الظاهر')),
                            ),
                          ),
                        ],
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 18),
                const GoldLabel('رقم العملية TxID'),
                GoldInputField(controller: txid, hint: 'أدخل TxID بعد التحويل'),
                const SizedBox(height: 12),
                InkWell(
                  onTap: () async {
                    final picked = await FilePicker.platform.pickFiles(
                      type: FileType.image,
                      withData: true,
                    );
                    if (picked != null) setState(() => proof = picked.files.single);
                  },
                  borderRadius: BorderRadius.circular(16),
                  child: DashedUpload(
                    text: proof == null ? 'رفع صورة إثبات التحويل' : proof!.name,
                  ),
                ),
                const SizedBox(height: 18),
                const ImportantNotes(
                  items: [
                    'الحد الأدنى للإيداع: 10 USDT',
                    'سيتم إضافة المبلغ إلى حسابك بعد تأكيد الشبكة.',
                    'لا تقم بإرسال أي أصول أخرى إلى هذا العنوان.',
                  ],
                ),
              ],
            ),
          ),
          if (message != null) ...[
            const SizedBox(height: 8),
            Text(message!, textAlign: TextAlign.center, style: const TextStyle(color: Colors.redAccent)),
          ],
          const SizedBox(height: 12),
          GoldButton(
            text: 'إرسال طلب الإيداع',
            icon: Icons.check_rounded,
            onTap: () async {
              final parsedAmount = double.tryParse(amount.text);
              if (parsedAmount == null || txid.text.trim().isEmpty || proof == null) {
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
}

class WithdrawScreen extends StatefulWidget {
  const WithdrawScreen({super.key});
  @override
  State<WithdrawScreen> createState() => _WithdrawScreenState();
}

class _WithdrawScreenState extends State<WithdrawScreen> {
  final amount = TextEditingController();
  final recipient = TextEditingController();
  String network = 'TRC20';
  String? message;

  @override
  void dispose() {
    amount.dispose();
    recipient.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final appState = context.watch<AppState>();
    final available = appState.balance;
    const fee = 1.00;
    const minWithdraw = 10.00;
    final parsedAmount = double.tryParse(amount.text) ?? 0;
    final received = (parsedAmount - fee).clamp(0, double.infinity);

    return OperationPageShell(
      selectedIndex: 1,
      onDeposit: () => Navigator.pushReplacement(
        context,
        MaterialPageRoute(builder: (_) => const DepositScreen()),
      ),
      onWithdraw: () {},
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.stretch,
        children: [
          OperationCard(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.stretch,
              children: [
                const GoldLabel('اختر العملة'),
                StyledSelectField(
                  value: 'USDT - $network',
                  icon: Icons.keyboard_arrow_down_rounded,
                  trailing: const TetherBadge(),
                  onTap: () {},
                ),
                const SizedBox(height: 16),
                const GoldLabel('عنوان السحب'),
                GoldInputField(
                  controller: recipient,
                  hint: 'أدخل عنوان المحفظة',
                  prefixIcon: Icons.qr_code_scanner_rounded,
                ),
                const SizedBox(height: 16),
                const GoldLabel('شبكة السحب'),
                StyledNetworkDropdown(
                  value: network,
                  onChanged: (v) => setState(() => network = v ?? network),
                ),
                const SizedBox(height: 16),
                const GoldLabel('مبلغ السحب'),
                GoldInputField(
                  controller: amount,
                  hint: 'أدخل المبلغ',
                  suffix: 'USDT',
                  keyboardType: TextInputType.number,
                  onChanged: (_) => setState(() {}),
                ),
                const SizedBox(height: 10),
                Text.rich(
                  TextSpan(
                    text: 'المتاح للسحب: ',
                    style: const TextStyle(color: muted, fontSize: 13),
                    children: [
                      TextSpan(
                        text: '${available.toStringAsFixed(2)} USDT',
                        style: const TextStyle(
                          color: gold2,
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                    ],
                  ),
                  textAlign: TextAlign.left,
                ),
              ],
            ),
          ),
          OperationCard(
            child: Column(
              children: [
                InfoRow('رسوم الشبكة', '${fee.toStringAsFixed(2)} USDT'),
                InfoRow('الحد الأدنى للسحب', '${minWithdraw.toStringAsFixed(2)} USDT'),
                InfoRow('الصافي المتوقع', '${received.toStringAsFixed(2)} USDT'),
                const SizedBox(height: 16),
                Container(
                  padding: const EdgeInsets.all(16),
                  decoration: BoxDecoration(
                    color: gold.withValues(alpha: .08),
                    borderRadius: BorderRadius.circular(16),
                    border: Border.all(color: gold.withValues(alpha: .45)),
                  ),
                  child: const Row(
                    children: [
                      Icon(Icons.gpp_good_rounded, color: gold2, size: 34),
                      SizedBox(width: 12),
                      Expanded(
                        child: Text(
                          'يرجى التأكد من صحة عنوان المحفظة والشبكة. لا يمكن التراجع عن عملية السحب بعد تأكيدها.',
                          textAlign: TextAlign.right,
                          style: TextStyle(color: gold2, height: 1.5),
                        ),
                      ),
                    ],
                  ),
                ),
              ],
            ),
          ),
          if (message != null) ...[
            const SizedBox(height: 8),
            Text(message!, textAlign: TextAlign.center, style: const TextStyle(color: Colors.redAccent)),
          ],
          const SizedBox(height: 12),
          GoldButton(
            text: 'تأكيد السحب',
            icon: Icons.check_rounded,
            onTap: () async {
              final parsedAmount = double.tryParse(amount.text);
              if (parsedAmount == null || recipient.text.trim().isEmpty) {
                setState(() => message = 'أدخل المبلغ وعنوان المحفظة');
                return;
              }
              try {
                await context.read<AppState>().withdraw(
                  amount: parsedAmount,
                  method: 'عنوان محفظة - $network',
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
}

class OperationPageShell extends StatelessWidget {
  const OperationPageShell({
    super.key,
    required this.selectedIndex,
    required this.child,
    required this.onDeposit,
    required this.onWithdraw,
  });

  final int selectedIndex;
  final Widget child;
  final VoidCallback onDeposit;
  final VoidCallback onWithdraw;

  @override
  Widget build(BuildContext context) => BrandScaffold(
    child: SafeArea(
      child: ListView(
        padding: const EdgeInsets.fromLTRB(18, 8, 18, 110),
        children: [
          Row(
            children: [
              IconButton(
                onPressed: () => Navigator.pop(context),
                icon: const Icon(Icons.arrow_back_ios_new_rounded, color: gold2),
              ),
              const Expanded(
                child: Text(
                  'الإيداع والسحب',
                  textAlign: TextAlign.center,
                  style: TextStyle(
                    color: gold2,
                    fontSize: 21,
                    fontWeight: FontWeight.bold,
                  ),
                ),
              ),
              IconButton(
                onPressed: () {},
                icon: const Icon(Icons.history_rounded, color: gold2),
              ),
            ],
          ),
          const SizedBox(height: 14),
          OperationSegmentedTabs(
            selectedIndex: selectedIndex,
            onDeposit: onDeposit,
            onWithdraw: onWithdraw,
          ),
          const SizedBox(height: 14),
          child,
        ],
      ),
    ),
  );
}

class OperationSegmentedTabs extends StatelessWidget {
  const OperationSegmentedTabs({
    super.key,
    required this.selectedIndex,
    required this.onDeposit,
    required this.onWithdraw,
  });

  final int selectedIndex;
  final VoidCallback onDeposit;
  final VoidCallback onWithdraw;

  @override
  Widget build(BuildContext context) => Container(
    height: 56,
    padding: const EdgeInsets.all(5),
    decoration: BoxDecoration(
      color: const Color(0xFF101010),
      borderRadius: BorderRadius.circular(15),
      border: Border.all(color: gold.withValues(alpha: .20)),
      boxShadow: [
        BoxShadow(
          color: Colors.black.withValues(alpha: .35),
          blurRadius: 18,
          offset: const Offset(0, 10),
        ),
      ],
    ),
    child: Row(
      children: [
        Expanded(
          child: SegmentButton(
            text: 'إيداع',
            selected: selectedIndex == 0,
            onTap: onDeposit,
          ),
        ),
        const SizedBox(width: 6),
        Expanded(
          child: SegmentButton(
            text: 'سحب',
            selected: selectedIndex == 1,
            onTap: onWithdraw,
          ),
        ),
      ],
    ),
  );
}

class SegmentButton extends StatelessWidget {
  const SegmentButton({
    super.key,
    required this.text,
    required this.selected,
    required this.onTap,
  });

  final String text;
  final bool selected;
  final VoidCallback onTap;

  @override
  Widget build(BuildContext context) => InkWell(
    onTap: onTap,
    borderRadius: BorderRadius.circular(12),
    child: AnimatedContainer(
      duration: const Duration(milliseconds: 180),
      alignment: Alignment.center,
      decoration: BoxDecoration(
        gradient: selected ? const LinearGradient(colors: [gold, gold2]) : null,
        color: selected ? null : Colors.transparent,
        borderRadius: BorderRadius.circular(12),
        boxShadow: selected
            ? [
                BoxShadow(
                  color: gold.withValues(alpha: .25),
                  blurRadius: 18,
                  offset: const Offset(0, 6),
                ),
              ]
            : null,
      ),
      child: Text(
        text,
        style: TextStyle(
          color: selected ? Colors.black : muted,
          fontWeight: FontWeight.bold,
          fontSize: 16,
        ),
      ),
    ),
  );
}

class OperationCard extends StatelessWidget {
  const OperationCard({super.key, required this.child});
  final Widget child;

  @override
  Widget build(BuildContext context) => Container(
    margin: const EdgeInsets.only(bottom: 14),
    padding: const EdgeInsets.all(14),
    decoration: BoxDecoration(
      gradient: const LinearGradient(
        begin: Alignment.topLeft,
        end: Alignment.bottomRight,
        colors: [Color(0xFF181818), Color(0xFF090909)],
      ),
      borderRadius: BorderRadius.circular(18),
      border: Border.all(color: gold.withValues(alpha: .25)),
      boxShadow: [
        BoxShadow(
          color: Colors.black.withValues(alpha: .45),
          blurRadius: 22,
          offset: const Offset(0, 12),
        ),
      ],
    ),
    child: child,
  );
}

class GoldLabel extends StatelessWidget {
  const GoldLabel(this.text, {super.key});
  final String text;

  @override
  Widget build(BuildContext context) => Padding(
    padding: const EdgeInsets.only(bottom: 8),
    child: Text(
      text,
      textAlign: TextAlign.right,
      style: const TextStyle(
        color: gold2,
        fontSize: 14,
        fontWeight: FontWeight.bold,
      ),
    ),
  );
}

class GoldInputField extends StatelessWidget {
  const GoldInputField({
    super.key,
    required this.controller,
    required this.hint,
    this.suffix,
    this.prefixIcon,
    this.keyboardType,
    this.onChanged,
  });

  final TextEditingController controller;
  final String hint;
  final String? suffix;
  final IconData? prefixIcon;
  final TextInputType? keyboardType;
  final ValueChanged<String>? onChanged;

  @override
  Widget build(BuildContext context) => TextField(
    controller: controller,
    keyboardType: keyboardType,
    onChanged: onChanged,
    textAlign: TextAlign.right,
    decoration: InputDecoration(
      hintText: hint,
      prefixIcon: prefixIcon == null ? null : Icon(prefixIcon, color: gold2),
      suffixText: suffix,
      suffixStyle: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold),
      filled: true,
      fillColor: const Color(0xFF111111),
      enabledBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(14),
        borderSide: BorderSide(color: gold.withValues(alpha: .38)),
      ),
      focusedBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(14),
        borderSide: const BorderSide(color: gold2, width: 1.2),
      ),
    ),
  );
}

class StyledNetworkDropdown extends StatelessWidget {
  const StyledNetworkDropdown({
    super.key,
    required this.value,
    required this.onChanged,
  });

  final String value;
  final ValueChanged<String?> onChanged;

  @override
  Widget build(BuildContext context) {
    final codes = context.watch<AppState>().networks.map((e) => e.code).toList();
    final items = codes.isEmpty ? [value] : codes;
    final selected = items.contains(value) ? value : items.first;

    return Container(
      height: 58,
      padding: const EdgeInsets.symmetric(horizontal: 12),
      decoration: BoxDecoration(
        color: const Color(0xFF111111),
        borderRadius: BorderRadius.circular(14),
        border: Border.all(color: gold.withValues(alpha: .38)),
      ),
      child: DropdownButtonHideUnderline(
        child: DropdownButton<String>(
          value: selected,
          dropdownColor: panel,
          icon: const Icon(Icons.keyboard_arrow_down_rounded, color: gold2),
          isExpanded: true,
          items: items
              .map(
                (e) => DropdownMenuItem(
                  value: e,
                  child: Row(
                    children: [
                      const TronBadge(),
                      const SizedBox(width: 10),
                      Expanded(
                        child: Text(
                          e == 'TRC20' ? 'TRC20 (Tron)' : e,
                          textAlign: TextAlign.right,
                          style: const TextStyle(fontWeight: FontWeight.bold),
                        ),
                      ),
                    ],
                  ),
                ),
              )
              .toList(),
          onChanged: onChanged,
        ),
      ),
    );
  }
}

class StyledSelectField extends StatelessWidget {
  const StyledSelectField({
    super.key,
    required this.value,
    required this.icon,
    required this.trailing,
    required this.onTap,
  });

  final String value;
  final IconData icon;
  final Widget trailing;
  final VoidCallback onTap;

  @override
  Widget build(BuildContext context) => InkWell(
    onTap: onTap,
    borderRadius: BorderRadius.circular(14),
    child: Container(
      height: 58,
      padding: const EdgeInsets.symmetric(horizontal: 12),
      decoration: BoxDecoration(
        color: const Color(0xFF111111),
        borderRadius: BorderRadius.circular(14),
        border: Border.all(color: gold.withValues(alpha: .38)),
      ),
      child: Row(
        children: [
          Icon(icon, color: gold2),
          const Spacer(),
          Text(value, style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 15)),
          const SizedBox(width: 10),
          trailing,
        ],
      ),
    ),
  );
}

class AmountChip extends StatelessWidget {
  const AmountChip({super.key, required this.text, required this.onTap});
  final String text;
  final VoidCallback onTap;

  @override
  Widget build(BuildContext context) => InkWell(
    onTap: onTap,
    borderRadius: BorderRadius.circular(10),
    child: Container(
      height: 48,
      alignment: Alignment.center,
      decoration: BoxDecoration(
        color: const Color(0xFF111111),
        borderRadius: BorderRadius.circular(10),
        border: Border.all(color: gold.withValues(alpha: .28)),
      ),
      child: Text(
        text,
        style: const TextStyle(color: gold2, fontWeight: FontWeight.bold),
      ),
    ),
  );
}

class MiniGoldAction extends StatelessWidget {
  const MiniGoldAction({
    super.key,
    required this.text,
    required this.icon,
    required this.onTap,
  });

  final String text;
  final IconData icon;
  final VoidCallback onTap;

  @override
  Widget build(BuildContext context) => InkWell(
    onTap: onTap,
    borderRadius: BorderRadius.circular(10),
    child: Padding(
      padding: const EdgeInsets.symmetric(vertical: 4),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.end,
        children: [
          Text(text, style: const TextStyle(color: gold2, fontWeight: FontWeight.bold)),
          const SizedBox(width: 8),
          Icon(icon, color: gold2, size: 18),
        ],
      ),
    ),
  );
}

class ImportantNotes extends StatelessWidget {
  const ImportantNotes({super.key, required this.items});
  final List<String> items;

  @override
  Widget build(BuildContext context) => Column(
    crossAxisAlignment: CrossAxisAlignment.stretch,
    children: [
      const Text(
        'ملاحظات مهمة',
        textAlign: TextAlign.right,
        style: TextStyle(color: gold2, fontWeight: FontWeight.bold),
      ),
      const SizedBox(height: 8),
      ...items.map(
        (e) => Padding(
          padding: const EdgeInsets.only(bottom: 6),
          child: Row(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              const Text('•', style: TextStyle(color: gold2)),
              const SizedBox(width: 8),
              Expanded(
                child: Text(
                  e,
                  textAlign: TextAlign.right,
                  style: const TextStyle(color: muted, height: 1.45),
                ),
              ),
            ],
          ),
        ),
      ),
    ],
  );
}

class TetherBadge extends StatelessWidget {
  const TetherBadge({super.key});
  @override
  Widget build(BuildContext context) => Container(
    width: 34,
    height: 34,
    decoration: const BoxDecoration(
      shape: BoxShape.circle,
      gradient: LinearGradient(colors: [Color(0xFF1F8B66), Color(0xFF6DE0B1)]),
    ),
    child: const Center(
      child: Text(
        '₮',
        style: TextStyle(color: Colors.white, fontSize: 22, fontWeight: FontWeight.bold),
      ),
    ),
  );
}

class TronBadge extends StatelessWidget {
  const TronBadge({super.key});
  @override
  Widget build(BuildContext context) => Container(
    width: 34,
    height: 34,
    decoration: const BoxDecoration(
      shape: BoxShape.circle,
      gradient: LinearGradient(colors: [Color(0xFFB5112B), Color(0xFFFF5A6B)]),
    ),
    child: const Icon(Icons.change_history_rounded, color: Colors.white, size: 21),
  );
}

String _shortAddress(String value) {
  if (value.length <= 18) return value;
  return '${value.substring(0, 6)}...${value.substring(value.length - 10)}';
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
