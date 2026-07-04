part of '../main.dart';

class BrandScaffold extends StatelessWidget {
  const BrandScaffold({super.key, required this.child});
  final Widget child;
  @override
  Widget build(BuildContext context) => Material(
    color: black,
    child: Container(
      decoration: const BoxDecoration(
        gradient: RadialGradient(
          center: Alignment.topRight,
          radius: 1.2,
          colors: [Color(0xFF2A220E), black],
        ),
      ),
      child: child,
    ),
  );
}

class BrandBlock extends StatelessWidget {
  const BrandBlock({super.key, this.big = false});
  final bool big;
  @override
  Widget build(BuildContext context) => Column(
    mainAxisSize: MainAxisSize.min,
    children: [
      Image.asset(
        'assets/images/logo.jpg',
        width: big ? 190 : 130,
        height: big ? 190 : 130,
        fit: BoxFit.contain,
      ),
      const SizedBox(height: 12),
      Text(
        'USDT STORE',
        style: TextStyle(
          color: gold2,
          fontSize: big ? 30 : 24,
          fontWeight: FontWeight.bold,
          letterSpacing: 0,
        ),
      ),
      const Text('محفظتك الرقمية الآمنة', style: TextStyle(color: gold)),
    ],
  );
}

class LuxuryCard extends StatelessWidget {
  const LuxuryCard({super.key, required this.child});
  final Widget child;
  @override
  Widget build(BuildContext context) => Container(
    margin: const EdgeInsets.symmetric(vertical: 8),
    padding: const EdgeInsets.all(16),
    decoration: BoxDecoration(
      gradient: const LinearGradient(
        begin: Alignment.topLeft,
        end: Alignment.bottomRight,
        colors: [Color(0xFF181818), Color(0xFF0B0B0B)],
      ),
      border: Border.all(color: gold, width: .55),
      borderRadius: BorderRadius.circular(16),
      boxShadow: [
        BoxShadow(
          color: gold.withValues(alpha: .10),
          blurRadius: 24,
          offset: const Offset(0, 10),
        ),
      ],
    ),
    child: child,
  );
}

class GoldButton extends StatelessWidget {
  const GoldButton({
    super.key,
    required this.text,
    required this.onTap,
    this.icon,
  });
  final String text;
  final VoidCallback onTap;
  final IconData? icon;
  @override
  Widget build(BuildContext context) => SizedBox(
    width: double.infinity,
    height: 54,
    child: DecoratedBox(
      decoration: BoxDecoration(
        gradient: const LinearGradient(colors: [gold, gold2]),
        borderRadius: BorderRadius.circular(14),
      ),
      child: TextButton.icon(
        onPressed: onTap,
        icon: Icon(icon ?? Icons.arrow_back, color: Colors.black),
        label: Text(
          text,
          style: const TextStyle(
            color: Colors.black,
            fontWeight: FontWeight.bold,
          ),
        ),
      ),
    ),
  );
}

class ActionTile extends StatelessWidget {
  const ActionTile(this.text, this.icon, this.onTap, {super.key});
  final String text;
  final IconData icon;
  final VoidCallback onTap;
  @override
  Widget build(BuildContext context) => InkWell(
    onTap: onTap,
    borderRadius: BorderRadius.circular(14),
    child: Container(
      decoration: BoxDecoration(
        color: panel,
        borderRadius: BorderRadius.circular(14),
        border: Border.all(color: line),
      ),
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Icon(icon, color: gold),
          const SizedBox(height: 8),
          Text(text, style: const TextStyle(fontSize: 12)),
        ],
      ),
    ),
  );
}

class AccountSummaryGrid extends StatelessWidget {
  const AccountSummaryGrid({super.key});

  @override
  Widget build(BuildContext context) {
    final s = context.watch<AppState>();
    final deposits = s.txs
        .where((t) => t.type == 'deposit' || t.type == 'receive')
        .fold<double>(0, (p, t) => p + t.amount.abs());
    final withdrawals = s.txs
        .where((t) => t.type == 'withdraw' || t.type == 'send')
        .fold<double>(0, (p, t) => p + t.amount.abs());

    return LuxuryCard(
      child: GridView.count(
        crossAxisCount: 4,
        shrinkWrap: true,
        physics: const NeverScrollableScrollPhysics(),
        childAspectRatio: .9,
        children: [
          SummaryMetric(
            Icons.account_balance_wallet_outlined,
            'الحساب',
            s.wallets.length.toString(),
            gold,
          ),
          SummaryMetric(
            Icons.swap_horiz_rounded,
            'عدد العمليات',
            s.txs.length.toString(),
            gold2,
          ),
          SummaryMetric(
            Icons.arrow_upward_rounded,
            'السحوبات',
            withdrawals.toStringAsFixed(2),
            const Color(0xFFFF4D6D),
          ),
          SummaryMetric(
            Icons.arrow_downward_rounded,
            'الإيداعات',
            deposits.toStringAsFixed(2),
            const Color(0xFF62D26F),
          ),
        ],
      ),
    );
  }
}

class SummaryMetric extends StatelessWidget {
  const SummaryMetric(
    this.icon,
    this.label,
    this.value,
    this.color, {
    super.key,
  });

  final IconData icon;
  final String label;
  final String value;
  final Color color;

  @override
  Widget build(BuildContext context) => Column(
    mainAxisAlignment: MainAxisAlignment.center,
    children: [
      Container(
        width: 38,
        height: 38,
        decoration: BoxDecoration(
          shape: BoxShape.circle,
          border: Border.all(color: color.withValues(alpha: .65)),
          color: color.withValues(alpha: .11),
        ),
        child: Icon(icon, color: color, size: 22),
      ),
      const SizedBox(height: 8),
      Text(
        label,
        maxLines: 1,
        overflow: TextOverflow.ellipsis,
        textAlign: TextAlign.center,
        style: const TextStyle(color: muted, fontSize: 11),
      ),
      const SizedBox(height: 4),
      Text(
        value,
        maxLines: 1,
        overflow: TextOverflow.ellipsis,
        style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 12),
      ),
    ],
  );
}

class SectionTitle extends StatelessWidget {
  const SectionTitle(this.title, {super.key, this.action, this.onTap});
  final String title;
  final String? action;
  final VoidCallback? onTap;
  @override
  Widget build(BuildContext context) => Padding(
    padding: const EdgeInsets.only(top: 18, bottom: 8),
    child: Row(
      children: [
        Text(
          title,
          style: const TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
        ),
        const Spacer(),
        if (action != null)
          TextButton(
            onPressed: onTap,
            child: Text(action!, style: const TextStyle(color: gold)),
          ),
      ],
    ),
  );
}

class TxTile extends StatelessWidget {
  const TxTile({super.key, required this.tx, required this.onTap});
  final Tx tx;
  final VoidCallback onTap;
  @override
  Widget build(BuildContext context) => LuxuryCard(
    child: ListTile(
      onTap: onTap,
      leading: CircleAvatar(
        backgroundColor: tx.amount >= 0
            ? Colors.green.withValues(alpha: .15)
            : gold.withValues(alpha: .15),
        child: Icon(
          tx.amount >= 0 ? Icons.south_west : Icons.north_east,
          color: tx.amount >= 0 ? Colors.greenAccent : gold,
        ),
      ),
      title: Text(tx.title),
      subtitle: Text(
        '${tx.network} - ${tx.date}',
        style: const TextStyle(color: muted),
      ),
      trailing: Text(
        '${tx.amount > 0 ? '+' : ''}${tx.amount.toStringAsFixed(2)} USDT',
        style: TextStyle(
          color: tx.amount >= 0 ? Colors.greenAccent : Colors.white,
          fontWeight: FontWeight.bold,
        ),
      ),
    ),
  );
}

class PageHeader extends StatelessWidget {
  const PageHeader(this.title, {super.key});
  final String title;
  @override
  Widget build(BuildContext context) => Padding(
    padding: const EdgeInsets.symmetric(vertical: 10),
    child: Row(
      children: [
        IconButton(
          onPressed: () {
            if (Navigator.canPop(context)) Navigator.pop(context);
          },
          icon: const Icon(Icons.arrow_back_ios_new),
        ),
        Expanded(
          child: Text(
            title,
            textAlign: TextAlign.center,
            style: const TextStyle(fontSize: 20, fontWeight: FontWeight.bold),
          ),
        ),
        const SizedBox(width: 48),
      ],
    ),
  );
}

class FormPage extends StatelessWidget {
  const FormPage({super.key, required this.title, required this.child});
  final String title;
  final Widget child;
  @override
  Widget build(BuildContext context) => BrandScaffold(
    child: SafeArea(
      child: ListView(
        padding: const EdgeInsets.all(18),
        children: [PageHeader(title), child],
      ),
    ),
  );
}

class NetworkDropdown extends StatelessWidget {
  const NetworkDropdown({
    super.key,
    required this.value,
    required this.onChanged,
  });
  final String value;
  final ValueChanged<String?> onChanged;
  @override
  Widget build(BuildContext context) {
    final codes = context
        .watch<AppState>()
        .networks
        .map((e) => e.code)
        .toList();
    final items = codes.isEmpty ? [value] : codes;
    final selected = items.contains(value) ? value : items.first;
    return DropdownButtonFormField(
      value: selected,
      decoration: const InputDecoration(labelText: 'الشبكة'),
      items: items
          .map((e) => DropdownMenuItem(value: e, child: Text(e)))
          .toList(),
      onChanged: onChanged,
    );
  }
}

class InfoRow extends StatelessWidget {
  const InfoRow(this.label, this.value, {super.key});
  final String label, value;
  @override
  Widget build(BuildContext context) => Padding(
    padding: const EdgeInsets.symmetric(vertical: 9),
    child: Row(
      children: [
        Text(label, style: const TextStyle(color: muted)),
        const Spacer(),
        Flexible(
          child: Text(
            value,
            textAlign: TextAlign.left,
            style: const TextStyle(fontWeight: FontWeight.bold),
          ),
        ),
      ],
    ),
  );
}

class DashedUpload extends StatelessWidget {
  const DashedUpload({super.key, this.text = 'رفع صورة إثبات التحويل'});
  final String text;
  @override
  Widget build(BuildContext context) => Container(
    height: 118,
    decoration: BoxDecoration(
      color: panel,
      borderRadius: BorderRadius.circular(16),
      border: Border.all(color: gold.withValues(alpha: .55)),
    ),
    child: Center(
      child: Column(
        mainAxisSize: MainAxisSize.min,
        children: [
          const Icon(Icons.cloud_upload_outlined, color: gold, size: 34),
          const SizedBox(height: 8),
          Text(text, style: const TextStyle(color: muted)),
        ],
      ),
    ),
  );
}

class StatCard extends StatelessWidget {
  const StatCard(this.title, this.value, {super.key});
  final String title;
  final double value;
  @override
  Widget build(BuildContext context) => LuxuryCard(
    child: Row(
      children: [
        Text(title),
        const Spacer(),
        Text(
          '${value.toStringAsFixed(2)} USDT',
          style: const TextStyle(
            color: gold2,
            fontSize: 18,
            fontWeight: FontWeight.bold,
          ),
        ),
      ],
    ),
  );
}

class MenuButton extends StatelessWidget {
  const MenuButton(this.text, this.icon, this.onTap, {super.key});
  final String text;
  final IconData icon;
  final VoidCallback onTap;
  @override
  Widget build(BuildContext context) => LuxuryCard(
    child: ListTile(
      onTap: onTap,
      leading: Icon(icon, color: gold),
      title: Text(text),
      trailing: const Icon(Icons.chevron_left),
    ),
  );
}
