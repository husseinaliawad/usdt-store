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
  final c = TextEditingController();
  bool sending = false;
  String? loginMessage;
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
              controller: c,
              keyboardType: TextInputType.emailAddress,
              textDirection: TextDirection.ltr,
              textAlign: TextAlign.left,
              decoration: const InputDecoration(
                labelText: 'الإيميل',
                prefixIcon: Icon(Icons.email_outlined),
              ),
            ),
            const SizedBox(height: 18),
            GoldButton(
              text: 'إرسال رمز OTP',
              onTap: () async {
                if (sending) return;
                setState(() {
                  sending = true;
                  loginMessage = null;
                });
                final appState = context.read<AppState>();
                try {
                  await appState.requestOtp(c.text.trim());
                  if (!context.mounted) return;
                  Navigator.push(
                    context,
                    MaterialPageRoute(builder: (_) => const OtpScreen()),
                  );
                } catch (e) {
                  if (!context.mounted) return;
                  setState(() => loginMessage = 'فشل إرسال الرمز: $e');
                } finally {
                  if (mounted) setState(() => sending = false);
                }
              },
            ),
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

class OtpScreen extends StatefulWidget {
  const OtpScreen({super.key});
  @override
  State<OtpScreen> createState() => _OtpScreenState();
}

class _OtpScreenState extends State<OtpScreen> {
  final c = TextEditingController();
  bool verifying = false;
  String? otpMessage;
  @override
  Widget build(BuildContext context) => BrandScaffold(
    child: SafeArea(
      child: Padding(
        padding: const EdgeInsets.all(20),
        child: Column(
          children: [
            Align(
              alignment: AlignmentDirectional.centerStart,
              child: IconButton(
                tooltip: 'رجوع',
                onPressed: verifying ? null : () => Navigator.pop(context),
                icon: const Icon(Icons.arrow_back_ios_new_rounded),
                color: Colors.white,
              ),
            ),
            const Spacer(),
            const Text(
              'تأكيد OTP',
              style: TextStyle(fontSize: 24, fontWeight: FontWeight.bold),
            ),
            const SizedBox(height: 10),
            Text(
              'أدخل الرمز المرسل إلى ${context.watch<AppState>().email}',
              style: const TextStyle(color: muted),
              textAlign: TextAlign.center,
            ),
            const SizedBox(height: 24),
            TextField(
              controller: c,
              keyboardType: TextInputType.number,
              textDirection: TextDirection.ltr,
              textAlign: TextAlign.center,
              maxLength: 6,
              style: const TextStyle(fontSize: 24, letterSpacing: 4),
              decoration: const InputDecoration(
                counterText: '',
                labelText: 'رمز التحقق',
              ),
            ),
            const SizedBox(height: 18),
            GoldButton(
              text: 'تأكيد',
              onTap: () async {
                if (verifying) return;
                setState(() {
                  verifying = true;
                  otpMessage = null;
                });
                final appState = context.read<AppState>();
                try {
                  await appState.verifyOtp(c.text.trim());
                  if (!context.mounted) return;
                  Navigator.pushAndRemoveUntil(
                    context,
                    MaterialPageRoute(builder: (_) => const MainShell()),
                    (_) => false,
                  );
                } catch (_) {
                  if (!context.mounted) return;
                  setState(() {
                    otpMessage = 'فشل التحقق. تأكد من الرمز المرسل إلى الإيميل';
                  });
                } finally {
                  if (mounted) setState(() => verifying = false);
                }
              },
            ),
            if (verifying) ...[
              const SizedBox(height: 12),
              const SizedBox(
                width: 22,
                height: 22,
                child: CircularProgressIndicator(strokeWidth: 2),
              ),
            ],
            if (otpMessage != null) ...[
              const SizedBox(height: 12),
              Text(
                otpMessage!,
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
