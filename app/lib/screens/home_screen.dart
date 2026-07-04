part of '../main.dart';

class HomeScreen extends StatelessWidget {
  const HomeScreen({super.key});
  @override
  Widget build(BuildContext context) {
    final s = context.watch<AppState>();
    if (s.loading && s.txs.isEmpty && s.wallets.isEmpty) {
      return const BrandScaffold(
        child: SafeArea(child: Center(child: CircularProgressIndicator())),
      );
    }
    return BrandScaffold(
      child: SafeArea(
        child: RefreshIndicator(
          onRefresh: context.read<AppState>().refreshAll,
          child: ListView(
            padding: const EdgeInsets.all(16),
            children: [
              Row(
                children: [
                  IconButton(
                    onPressed: () => Navigator.push(
                      context,
                      MaterialPageRoute(
                        builder: (_) => const NotificationsScreen(),
                      ),
                    ),
                    icon: const Icon(Icons.notifications_none),
                  ),
                  const Spacer(),
                  const Text(
                    'الرئيسية',
                    style: TextStyle(fontWeight: FontWeight.bold),
                  ),
                  const Spacer(),
                  CircleAvatar(
                    backgroundColor: Colors.transparent,
                    child: ClipOval(
                      child: BrandLogo(
                        width: 40,
                        height: 40,
                        fit: BoxFit.contain,
                      ),
                    ),
                  ),
                ],
              ),
              if (s.error != null)
                LuxuryCard(
                  child: Text(
                    s.error!,
                    textDirection: TextDirection.ltr,
                    style: const TextStyle(color: Colors.redAccent),
                  ),
                ),
              LuxuryCard(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Text(
                      'إجمالي الرصيد',
                      style: TextStyle(color: Colors.white70),
                    ),
                    const SizedBox(height: 14),
                    Text(
                      '${s.balance.toStringAsFixed(2)} USDT',
                      style: const TextStyle(
                        color: gold2,
                        fontSize: 30,
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                    const SizedBox(height: 8),
                    Text(
                      '~ ${s.balance.toStringAsFixed(2)} USD',
                      style: const TextStyle(color: muted),
                    ),
                  ],
                ),
              ),
              GridView.count(
                crossAxisCount: 4,
                shrinkWrap: true,
                physics: const NeverScrollableScrollPhysics(),
                mainAxisSpacing: 10,
                crossAxisSpacing: 10,
                children: [
                  ActionTile(
                    'إرسال',
                    Icons.upload,
                    () => Navigator.push(
                      context,
                      MaterialPageRoute(builder: (_) => const SendScreen()),
                    ),
                  ),
                  ActionTile(
                    'استلام',
                    Icons.download,
                    () => Navigator.push(
                      context,
                      MaterialPageRoute(builder: (_) => const ReceiveScreen()),
                    ),
                  ),
                  ActionTile(
                    'إيداع',
                    Icons.account_balance_wallet_outlined,
                    () => Navigator.push(
                      context,
                      MaterialPageRoute(builder: (_) => const DepositScreen()),
                    ),
                  ),
                  ActionTile(
                    'سحب',
                    Icons.payments_outlined,
                    () => Navigator.push(
                      context,
                      MaterialPageRoute(builder: (_) => const WithdrawScreen()),
                    ),
                  ),
                ],
              ),
              const SectionTitle('ملخص الحساب'),
              const AccountSummaryGrid(),
              SectionTitle(
                'آخر العمليات',
                action: 'عرض الكل',
                onTap: () => s.setTab(1),
              ),
              ...s.txs
                  .take(5)
                  .map(
                    (tx) => TxTile(
                      tx: tx,
                      onTap: () => Navigator.push(
                        context,
                        MaterialPageRoute(
                          builder: (_) => TxDetailsScreen(tx: tx),
                        ),
                      ),
                    ),
                  ),
            ],
          ),
        ),
      ),
    );
  }
}
