part of '../main.dart';

class MainShell extends StatelessWidget {
  const MainShell({super.key});
  @override
  Widget build(BuildContext context) {
    final s = context.watch<AppState>();
    final pages = [
      const HomeScreen(),
      const TransactionsScreen(),
      const VirtualCardScreen(),
      const StatsScreen(),
      const ProfileScreen(),
    ];
    return Scaffold(
      extendBody: true,
      body: pages[s.tab],
      bottomNavigationBar: LuxuryBottomNav(
        selectedIndex: s.tab,
        onSelected: s.setTab,
      ),
    );
    /* bottomNavigationBar: NavigationBar(
        backgroundColor: panel,
        indicatorColor: gold.withValues(alpha: .16),
        selectedIndex: s.tab,
        onDestinationSelected: s.setTab,
        destinations: const [
          NavigationDestination(
            icon: Icon(Icons.home_outlined),
            selectedIcon: Icon(Icons.home, color: gold),
            label: 'الرئيسية',
          ),
          NavigationDestination(
            icon: Icon(Icons.receipt_long_outlined),
            selectedIcon: Icon(Icons.receipt_long, color: gold),
            label: 'العمليات',
          ),
          NavigationDestination(
            icon: Icon(Icons.credit_card),
            label: 'البطاقة',
          ),
          NavigationDestination(
            icon: Icon(Icons.bar_chart),
            label: 'الإحصائيات',
          ),
          NavigationDestination(
            icon: Icon(Icons.person_outline),
            label: 'المزيد',
          ),
        ],
      ), */
  }
}

class LuxuryBottomNav extends StatelessWidget {
  const LuxuryBottomNav({
    super.key,
    required this.selectedIndex,
    required this.onSelected,
  });

  final int selectedIndex;
  final ValueChanged<int> onSelected;

  static const items = [
    (Icons.home_rounded, 'الرئيسية'),
    (Icons.receipt_long_rounded, 'المعاملات'),
    (Icons.credit_card_rounded, 'البطاقات'),
    (Icons.bar_chart_rounded, 'الإحصائيات'),
    (Icons.person_rounded, 'الحساب'),
  ];

  @override
  Widget build(BuildContext context) => SafeArea(
    minimum: const EdgeInsets.fromLTRB(16, 0, 16, 10),
    child: Container(
      height: 76,
      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 8),
      decoration: BoxDecoration(
        color: const Color(0xF2080808),
        border: Border.all(color: gold.withValues(alpha: .32)),
        borderRadius: BorderRadius.circular(28),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withValues(alpha: .55),
            blurRadius: 24,
            offset: const Offset(0, 12),
          ),
        ],
      ),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: List.generate(items.length, (i) {
          final selected = selectedIndex == i;
          final isHome = i == 0;
          return Expanded(
            child: InkWell(
              borderRadius: BorderRadius.circular(22),
              onTap: () => onSelected(i),
              child: AnimatedContainer(
                duration: const Duration(milliseconds: 180),
                height: isHome ? 62 : 54,
                decoration: BoxDecoration(
                  shape: isHome ? BoxShape.circle : BoxShape.rectangle,
                  borderRadius: isHome ? null : BorderRadius.circular(18),
                  gradient: selected
                      ? const LinearGradient(colors: [gold, gold2])
                      : null,
                  color: selected ? null : Colors.transparent,
                ),
                child: Column(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    Icon(
                      items[i].$1,
                      color: selected ? Colors.black : gold2,
                      size: isHome ? 27 : 23,
                    ),
                    const SizedBox(height: 3),
                    Text(
                      items[i].$2,
                      maxLines: 1,
                      overflow: TextOverflow.ellipsis,
                      style: TextStyle(
                        color: selected ? Colors.black : gold2,
                        fontSize: 10,
                        fontWeight: selected
                            ? FontWeight.bold
                            : FontWeight.w600,
                      ),
                    ),
                  ],
                ),
              ),
            ),
          );
        }),
      ),
    ),
  );
}
