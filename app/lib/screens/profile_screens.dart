part of '../main.dart';

class VirtualCardScreen extends StatelessWidget {
  const VirtualCardScreen({super.key});
  @override
  Widget build(BuildContext context) {
    final s = context.watch<AppState>();
    return BrandScaffold(
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
            InfoRow('الرصيد المتاح', '${s.balance.toStringAsFixed(2)} USDT'),
            const InfoRow('حالة البطاقة', 'فعالة'),
          ],
        ),
      ),
    );
  }
}

class StatsScreen extends StatelessWidget {
  const StatsScreen({super.key});
  @override
  Widget build(BuildContext context) {
    final stats = context.watch<AppState>().stats;
    final sent = stats['sent_total'] ?? 0;
    final rec = stats['received_total'] ?? 0;
    final fees = stats['fees_total'] ?? 0;
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
  Widget build(BuildContext context) {
    final s = context.watch<AppState>();
    return BrandScaffold(
      child: SafeArea(
        child: ListView(
          padding: const EdgeInsets.all(16),
          children: [
            const PageHeader('الملف الشخصي'),
            LuxuryCard(
              child: Row(
                children: [
                  CircleAvatar(
                    radius: 28,
                    backgroundColor: Colors.transparent,
                    child: ClipOval(
                      child: BrandLogo(
                        width: 56,
                        height: 56,
                        fit: BoxFit.contain,
                      ),
                    ),
                  ),
                  const SizedBox(width: 14),
                  Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        s.userName.isEmpty ? s.email : s.userName,
                        style: const TextStyle(fontWeight: FontWeight.bold),
                      ),
                      Text(
                        'KYC: ${s.kycStatus}',
                        style: const TextStyle(color: gold),
                      ),
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
}

class KycScreen extends StatefulWidget {
  const KycScreen({super.key});

  @override
  State<KycScreen> createState() => _KycScreenState();
}

class _KycScreenState extends State<KycScreen> {
  final fullName = TextEditingController();
  final phone = TextEditingController();
  PlatformFile? idImage;
  PlatformFile? selfieImage;
  String? message;

  @override
  Widget build(BuildContext context) => FormPage(
    title: 'توثيق الهوية KYC',
    child: Column(
      children: [
        TextField(
          controller: fullName,
          decoration: const InputDecoration(labelText: 'الاسم الكامل'),
        ),
        const SizedBox(height: 14),
        TextField(
          controller: phone,
          decoration: const InputDecoration(labelText: 'رقم الهاتف'),
        ),
        const SizedBox(height: 14),
        InkWell(
          onTap: () async {
            final picked = await FilePicker.platform.pickFiles(
              type: FileType.image,
              withData: true,
            );
            if (picked != null) setState(() => idImage = picked.files.single);
          },
          child: DashedUpload(
            text: idImage == null ? 'رفع صورة الهوية' : idImage!.name,
          ),
        ),
        const SizedBox(height: 14),
        InkWell(
          onTap: () async {
            final picked = await FilePicker.platform.pickFiles(
              type: FileType.image,
              withData: true,
            );
            if (picked != null) {
              setState(() => selfieImage = picked.files.single);
            }
          },
          child: DashedUpload(
            text: selfieImage == null ? 'رفع صورة شخصية' : selfieImage!.name,
          ),
        ),
        const SizedBox(height: 20),
        InfoRow('حالة التوثيق', context.watch<AppState>().kycStatus),
        if (message != null) ...[
          const SizedBox(height: 12),
          Text(message!, style: const TextStyle(color: Colors.redAccent)),
        ],
        const SizedBox(height: 14),
        GoldButton(
          text: 'إرسال للمراجعة',
          onTap: () async {
            if (fullName.text.trim().isEmpty ||
                phone.text.trim().isEmpty ||
                idImage == null ||
                selfieImage == null) {
              setState(() => message = 'أكمل البيانات والصور المطلوبة');
              return;
            }
            try {
              await context.read<AppState>().submitKyc(
                fullName: fullName.text.trim(),
                phone: phone.text.trim(),
                idImage: idImage!,
                selfieImage: selfieImage!,
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

class SupportScreen extends StatefulWidget {
  const SupportScreen({super.key});

  @override
  State<SupportScreen> createState() => _SupportScreenState();
}

class _SupportScreenState extends State<SupportScreen> {
  final message = TextEditingController();
  String? error;

  List<Widget> get supportContacts => const [
    SupportContactTile(
      icon: Icons.telegram,
      label: 'Telegram - تلغرام',
      value: '@MANAGINGPARTNE',
    ),
    SupportContactTile(
      icon: Icons.ondemand_video,
      label: 'YouTube - يوتيوب',
      value: 'https://www.youtube.com/@MANAGINGPARTNER-k3k',
    ),
    SupportContactTile(
      icon: Icons.email_outlined,
      label: 'Email - البريد الإلكتروني',
      value: 'talalbouzan611@gmail.com',
    ),
    SizedBox(height: 12),
  ];

  @override
  Widget build(BuildContext context) => FormPage(
    title: 'الدعم الفني',
    child: Column(
      children: [
        ...supportContacts,
        TextField(
          controller: message,
          maxLines: 5,
          decoration: const InputDecoration(labelText: 'اكتب رسالتك'),
        ),
        const SizedBox(height: 18),
        if (error != null) ...[
          Text(error!, style: const TextStyle(color: Colors.redAccent)),
          const SizedBox(height: 12),
        ],
        GoldButton(
          text: 'إرسال',
          icon: Icons.send,
          onTap: () async {
            if (message.text.trim().isEmpty) {
              setState(() => error = 'اكتب الرسالة أولا');
              return;
            }
            try {
              await context.read<AppState>().sendSupport(message.text.trim());
              if (!context.mounted) return;
              Navigator.pop(context);
            } catch (e) {
              if (!context.mounted) return;
              setState(() => error = e.toString());
            }
          },
        ),
      ],
    ),
  );
}

class SupportContactTile extends StatelessWidget {
  const SupportContactTile({
    super.key,
    required this.icon,
    required this.label,
    required this.value,
  });

  final IconData icon;
  final String label;
  final String value;

  @override
  Widget build(BuildContext context) => LuxuryCard(
    child: Row(
      children: [
        Icon(icon, color: gold),
        const SizedBox(width: 12),
        Expanded(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(label, style: const TextStyle(color: muted, fontSize: 12)),
              const SizedBox(height: 4),
              SelectableText(
                value,
                style: const TextStyle(fontWeight: FontWeight.bold),
              ),
            ],
          ),
        ),
      ],
    ),
  );
}
