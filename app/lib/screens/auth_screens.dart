part of '../main.dart';

class SplashScreen extends StatefulWidget {
  const SplashScreen({super.key});

  @override
  State<SplashScreen> createState() => _SplashScreenState();
}

class _SplashScreenState extends State<SplashScreen> {
  @override
  void initState() {
    super.initState();
    Future.delayed(const Duration(seconds: 2), () {
      if (!mounted) return;
      Navigator.pushReplacement(
        context,
        MaterialPageRoute(builder: (_) => const OnboardingScreen()),
      );
    });
  }

  @override
  Widget build(BuildContext context) =>
      const BrandScaffold(child: Center(child: BrandBlock(big: true)));
}

class OnboardingScreen extends StatefulWidget {
  const OnboardingScreen({super.key});

  @override
  State<OnboardingScreen> createState() => _OnboardingScreenState();
}

class _OnboardingScreenState extends State<OnboardingScreen> {
  final page = PageController();
  int index = 0;

  final items = const [
    (
      'أمان عالي',
      Icons.verified_user_outlined,
      'حماية على مستوى عال وسجل تدقيق لكل عملية',
    ),
    ('تحويل سريع', Icons.speed_outlined, 'إرسال واستلام USDT عبر أهم الشبكات'),
    ('دعم 24/7', Icons.support_agent_outlined, 'فريق جاهز لمساعدتك في أي وقت'),
  ];

  @override
  Widget build(BuildContext context) => BrandScaffold(
    child: SafeArea(
      child: Column(
        children: [
          const SizedBox(height: 12),
          const BrandBlock(),
          const SizedBox(height: 12),
          SizedBox(
            height: 250,
            child: PageView.builder(
              controller: page,
              itemCount: items.length,
              onPageChanged: (v) => setState(() => index = v),
              itemBuilder: (_, i) => LuxuryCard(
                child: Column(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    Icon(items[i].$2, color: gold, size: 48),
                    const SizedBox(height: 12),
                    Text(
                      items[i].$1,
                      style: const TextStyle(
                        fontSize: 22,
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                    const SizedBox(height: 8),
                    Text(
                      items[i].$3,
                      textAlign: TextAlign.center,
                      maxLines: 3,
                      overflow: TextOverflow.ellipsis,
                      style: const TextStyle(color: muted, height: 1.35),
                    ),
                  ],
                ),
              ),
            ),
          ),
          Row(
            mainAxisAlignment: MainAxisAlignment.center,
            children: List.generate(
              3,
              (i) => Container(
                width: i == index ? 22 : 8,
                height: 8,
                margin: const EdgeInsets.all(4),
                decoration: BoxDecoration(
                  color: i == index ? gold : Colors.white24,
                  borderRadius: BorderRadius.circular(10),
                ),
              ),
            ),
          ),
          const SizedBox(height: 10),
          GoldButton(
            text: index == 2 ? 'إنشاء حساب جديد' : 'متابعة',
            onTap: () {
              if (index < 2) {
                page.nextPage(
                  duration: const Duration(milliseconds: 250),
                  curve: Curves.easeOut,
                );
              } else {
                Navigator.pushReplacement(
                  context,
                  MaterialPageRoute(builder: (_) => const LoginScreen()),
                );
              }
            },
          ),
          TextButton(
            onPressed: () => Navigator.pushReplacement(
              context,
              MaterialPageRoute(builder: (_) => const LoginScreen()),
            ),
            child: const Text(
              'تسجيل الدخول',
              style: TextStyle(color: Colors.white),
            ),
          ),
          const SizedBox(height: 8),
        ],
      ),
    ),
  );
}

class LoginScreen extends StatefulWidget {
  const LoginScreen({super.key});

  @override
  State<LoginScreen> createState() => _LoginScreenState();
}

class _LoginScreenState extends State<LoginScreen> {
  final emailController = TextEditingController();
  bool sending = false;
  String? loginMessage;

  Future<void> login() async {
    if (sending) return;
    final email = emailController.text.trim();
    if (email.isEmpty) {
      setState(() => loginMessage = 'أدخل الإيميل');
      return;
    }

    setState(() {
      sending = true;
      loginMessage = null;
    });

    try {
      await context.read<AppState>().login(email);
      if (!mounted) return;
      Navigator.pushAndRemoveUntil(
        context,
        MaterialPageRoute(builder: (_) => const MainShell()),
        (_) => false,
      );
    } catch (e) {
      if (!mounted) return;
      setState(() => loginMessage = 'فشل تسجيل الدخول: $e');
    } finally {
      if (mounted) setState(() => sending = false);
    }
  }

  @override
  Widget build(BuildContext context) => BrandScaffold(
    child: SafeArea(
      child: Padding(
        padding: const EdgeInsets.all(20),
        child: Column(
          children: [
            const Spacer(),
            const BrandBlock(),
            const SizedBox(height: 36),
            TextField(
              controller: emailController,
              keyboardType: TextInputType.emailAddress,
              textInputAction: TextInputAction.done,
              textDirection: TextDirection.ltr,
              textAlign: TextAlign.left,
              onSubmitted: (_) => login(),
              decoration: const InputDecoration(
                labelText: 'الإيميل',
                prefixIcon: Icon(Icons.email_outlined),
              ),
            ),
            const SizedBox(height: 18),
            GoldButton(text: 'دخول / إنشاء حساب', onTap: login),
            if (sending) ...[
              const SizedBox(height: 12),
              const SizedBox(
                width: 22,
                height: 22,
                child: CircularProgressIndicator(strokeWidth: 2),
              ),
            ],
            if (loginMessage != null) ...[
              const SizedBox(height: 12),
              Text(
                loginMessage!,
                textAlign: TextAlign.center,
                style: const TextStyle(color: Colors.redAccent),
              ),
            ],
            const Spacer(),
          ],
        ),
      ),
    ),
  );
}
