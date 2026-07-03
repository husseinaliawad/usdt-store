part of '../main.dart';

class VirtualCardScreen extends StatelessWidget {
  const VirtualCardScreen({super.key});
  @override
  Widget build(BuildContext context) => BrandScaffold(
    child: SafeArea(
      child: ListView(
        padding: const EdgeInsets.all(16),
        children: [
          const PageHeader('البطاقة'),
          Container(
            height: 210,
            padding: const EdgeInsets.all(22),
            decoration: BoxDecoration(
              borderRadius: BorderRadius.circular(18),
              border: Border.all(color: gold),
              gradient: const LinearGradient(
                colors: [Color(0xFF111111), Color(0xFF262014)],
              ),
            ),
            child: const Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  'USDT STORE',
                  style: TextStyle(fontWeight: FontWeight.bold),
                ),
                Text(
                  'VIRTUAL CARD',
                  style: TextStyle(color: muted, fontSize: 12),
                ),
                Spacer(),
                Text(
                  '**** **** **** 4567',
                  style: TextStyle(fontSize: 22, letterSpacing: 2),
                ),
                Spacer(),
                Row(
                  children: [
                    Text('12/28'),
                    Spacer(),
                    Text(
                      'VISA',
                      style: TextStyle(
                        color: gold2,
                        fontSize: 24,
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                  ],
                ),
              ],
            ),
          ),
          const SizedBox(height: 18),
          const InfoRow('الرصيد المتاح', '1,250.00 USDT'),
          const InfoRow('حالة البطاقة', 'فعالة'),
        ],
      ),
    ),
  );
}

class StatsScreen extends StatelessWidget {
  const StatsScreen({super.key});
  @override
  Widget build(BuildContext context) {
    final txs = context.watch<AppState>().txs;
    final sent = txs
        .where((t) => t.type == 'send')
        .fold<double>(0, (p, t) => p + t.amount.abs());
    final rec = txs
        .where((t) => t.type == 'receive')
        .fold<double>(0, (p, t) => p + t.amount.abs());
    final fees = txs.fold<double>(0, (p, t) => p + t.fee);
    return BrandScaffold(
      child: SafeArea(
        child: ListView(
          padding: const EdgeInsets.all(16),
          children: [
            const PageHeader('الإحصائيات'),
            StatCard('إجمالي الإرسال', sent),
            StatCard('إجمالي الاستلام', rec),
            StatCard('رسوم العمليات', fees),
            LuxuryCard(
              child: SizedBox(
                height: 180,
                child: Row(
                  crossAxisAlignment: CrossAxisAlignment.end,
                  children: List.generate(
                    9,
                    (i) => Expanded(
                      child: Container(
                        margin: const EdgeInsets.symmetric(horizontal: 4),
                        height: 40 + (i * 13 % 120).toDouble(),
                        decoration: BoxDecoration(
                          color: i.isEven ? gold : gold2,
                          borderRadius: BorderRadius.circular(6),
                        ),
                      ),
                    ),
                  ),
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}

class ProfileScreen extends StatelessWidget {
  const ProfileScreen({super.key});
  @override
  Widget build(BuildContext context) => BrandScaffold(
    child: SafeArea(
      child: ListView(
        padding: const EdgeInsets.all(16),
        children: [
          const PageHeader('الملف الشخصي'),
          const LuxuryCard(
            child: Row(
              children: [
                CircleAvatar(
                  radius: 28,
                  backgroundImage: AssetImage('assets/images/logo.jpg'),
                ),
                SizedBox(width: 14),
                Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      'عميل USDT STORE',
                      style: TextStyle(fontWeight: FontWeight.bold),
                    ),
                    Text('KYC: قيد المراجعة', style: TextStyle(color: gold)),
                  ],
                ),
              ],
            ),
          ),
          MenuButton(
            'توثيق الهوية KYC',
            Icons.verified_user,
            () => Navigator.push(
              context,
              MaterialPageRoute(builder: (_) => const KycScreen()),
            ),
          ),
          MenuButton(
            'الإشعارات',
            Icons.notifications,
            () => Navigator.push(
              context,
              MaterialPageRoute(builder: (_) => const NotificationsScreen()),
            ),
          ),
          MenuButton(
            'الدعم الفني',
            Icons.support_agent,
            () => Navigator.push(
              context,
              MaterialPageRoute(builder: (_) => const SupportScreen()),
            ),
          ),
        ],
      ),
    ),
  );
}

class KycScreen extends StatelessWidget {
  const KycScreen({super.key});
  @override
  Widget build(BuildContext context) => FormPage(
    title: 'توثيق الهوية KYC',
    child: Column(
      children: const [
        TextField(decoration: InputDecoration(labelText: 'الاسم الكامل')),
        SizedBox(height: 14),
        TextField(decoration: InputDecoration(labelText: 'رقم الهاتف')),
        SizedBox(height: 14),
        DashedUpload(text: 'رفع صورة الهوية'),
        SizedBox(height: 14),
        DashedUpload(text: 'رفع صورة شخصية'),
        SizedBox(height: 20),
        InfoRow('حالة التوثيق', 'قيد المراجعة'),
      ],
    ),
  );
}

class NotificationsScreen extends StatelessWidget {
  const NotificationsScreen({super.key});
  @override
  Widget build(BuildContext context) => FormPage(
    title: 'الإشعارات',
    child: Column(
      children: context
          .watch<AppState>()
          .notifications
          .map(
            (n) => LuxuryCard(
              child: ListTile(
                leading: const Icon(
                  Icons.notifications_active_outlined,
                  color: gold,
                ),
                title: Text(n),
              ),
            ),
          )
          .toList(),
    ),
  );
}

class SupportScreen extends StatelessWidget {
  const SupportScreen({super.key});
  @override
  Widget build(BuildContext context) => FormPage(
    title: 'الدعم الفني',
    child: Column(
      children: [
        const TextField(
          maxLines: 5,
          decoration: InputDecoration(labelText: 'اكتب رسالتك'),
        ),
        const SizedBox(height: 18),
        GoldButton(
          text: 'إرسال',
          icon: Icons.send,
          onTap: () => Navigator.pop(context),
        ),
      ],
    ),
  );
}
