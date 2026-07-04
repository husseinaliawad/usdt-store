part of '../main.dart';

class SplashScreen extends StatefulWidget {
  const SplashScreen({super.key});

  @override
  State<SplashScreen> createState() => _SplashScreenState();
}

class _SplashScreenState extends State<SplashScreen>
    with SingleTickerProviderStateMixin {
  late final AnimationController _controller;
  late final Animation<double> _fadeAnimation;
  late final Animation<double> _scaleAnimation;
  late final Animation<Offset> _slideAnimation;

  @override
  void initState() {
    super.initState();

    _controller = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 1300),
    );

    _fadeAnimation = CurvedAnimation(
      parent: _controller,
      curve: Curves.easeOut,
    );

    _scaleAnimation = Tween<double>(
      begin: .86,
      end: 1,
    ).animate(CurvedAnimation(parent: _controller, curve: Curves.easeOutBack));

    _slideAnimation = Tween<Offset>(
      begin: const Offset(0, .18),
      end: Offset.zero,
    ).animate(CurvedAnimation(parent: _controller, curve: Curves.easeOutCubic));

    _controller.forward();

    Future.delayed(const Duration(seconds: 3), () {
      if (!mounted) return;
      Navigator.pushReplacement(
        context,
        PageRouteBuilder(
          transitionDuration: const Duration(milliseconds: 500),
          pageBuilder: (_, animation, __) => const OnboardingScreen(),
          transitionsBuilder: (_, animation, __, child) =>
              FadeTransition(opacity: animation, child: child),
        ),
      );
    });
  }

  @override
  void dispose() {
    _controller.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) => BrandScaffold(
    child: Stack(
      children: [
        const Positioned(
          top: -90,
          right: -70,
          child: _SplashGlow(size: 220, opacity: .22),
        ),
        const Positioned(
          bottom: -120,
          left: -80,
          child: _SplashGlow(size: 260, opacity: .16),
        ),
        SafeArea(
          child: Padding(
            padding: const EdgeInsets.all(24),
            child: Column(
              children: [
                const Spacer(),
                FadeTransition(
                  opacity: _fadeAnimation,
                  child: SlideTransition(
                    position: _slideAnimation,
                    child: ScaleTransition(
                      scale: _scaleAnimation,
                      child: const _SplashBrandCard(),
                    ),
                  ),
                ),
                const SizedBox(height: 28),
                FadeTransition(
                  opacity: _fadeAnimation,
                  child: const _SplashLoadingBar(),
                ),
                const SizedBox(height: 14),
                FadeTransition(
                  opacity: _fadeAnimation,
                  child: const Text(
                    'جاري تجهيز محفظتك الرقمية...',
                    textAlign: TextAlign.center,
                    style: TextStyle(
                      color: muted,
                      fontSize: 13,
                      fontWeight: FontWeight.w500,
                    ),
                  ),
                ),
                const Spacer(),
                FadeTransition(
                  opacity: _fadeAnimation,
                  child: Container(
                    padding: const EdgeInsets.symmetric(
                      horizontal: 14,
                      vertical: 8,
                    ),
                    decoration: BoxDecoration(
                      color: Colors.white.withValues(alpha: .05),
                      borderRadius: BorderRadius.circular(40),
                      border: Border.all(color: Colors.white12),
                    ),
                    child: const Row(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        Icon(Icons.lock_outline_rounded, color: gold, size: 15),
                        SizedBox(width: 6),
                        Text(
                          'آمن • سريع • موثوق',
                          style: TextStyle(color: Colors.white70, fontSize: 12),
                        ),
                      ],
                    ),
                  ),
                ),
                const SizedBox(height: 18),
              ],
            ),
          ),
        ),
      ],
    ),
  );
}

class _SplashBrandCard extends StatelessWidget {
  const _SplashBrandCard();

  @override
  Widget build(BuildContext context) => Container(
    width: double.infinity,
    padding: const EdgeInsets.symmetric(horizontal: 22, vertical: 30),
    decoration: BoxDecoration(
      borderRadius: BorderRadius.circular(30),
      gradient: LinearGradient(
        begin: Alignment.topLeft,
        end: Alignment.bottomRight,
        colors: [
          Colors.white.withValues(alpha: .08),
          Colors.white.withValues(alpha: .025),
        ],
      ),
      border: Border.all(color: gold.withValues(alpha: .55), width: .8),
      boxShadow: [
        BoxShadow(
          color: gold.withValues(alpha: .15),
          blurRadius: 45,
          offset: const Offset(0, 22),
        ),
        BoxShadow(
          color: Colors.black.withValues(alpha: .35),
          blurRadius: 28,
          offset: const Offset(0, 16),
        ),
      ],
    ),
    child: Column(
      mainAxisSize: MainAxisSize.min,
      children: [
        Container(
          width: 148,
          height: 148,
          padding: const EdgeInsets.all(12),
          decoration: BoxDecoration(
            shape: BoxShape.circle,
            gradient: const LinearGradient(colors: [gold, gold2]),
            boxShadow: [
              BoxShadow(
                color: gold.withValues(alpha: .28),
                blurRadius: 35,
                offset: const Offset(0, 14),
              ),
            ],
          ),
          child: Container(
            padding: const EdgeInsets.all(8),
            decoration: const BoxDecoration(
              color: black,
              shape: BoxShape.circle,
            ),
            child: ClipOval(child: const BrandLogo(fit: BoxFit.contain)),
          ),
        ),
        const SizedBox(height: 20),
        const Text(
          'USDT STORE',
          textAlign: TextAlign.center,
          style: TextStyle(
            color: gold2,
            fontSize: 30,
            height: 1,
            fontWeight: FontWeight.w900,
            letterSpacing: .5,
          ),
        ),
        const SizedBox(height: 10),
        const Text(
          'محفظتك الرقمية الآمنة',
          textAlign: TextAlign.center,
          style: TextStyle(
            color: gold,
            fontSize: 15,
            fontWeight: FontWeight.w600,
          ),
        ),
      ],
    ),
  );
}

class _SplashLoadingBar extends StatefulWidget {
  const _SplashLoadingBar();

  @override
  State<_SplashLoadingBar> createState() => _SplashLoadingBarState();
}

class _SplashLoadingBarState extends State<_SplashLoadingBar>
    with SingleTickerProviderStateMixin {
  late final AnimationController _controller;

  @override
  void initState() {
    super.initState();
    _controller = AnimationController(
      vsync: this,
      duration: const Duration(seconds: 2),
    )..repeat(reverse: true);
  }

  @override
  void dispose() {
    _controller.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) => Container(
    width: 170,
    height: 6,
    clipBehavior: Clip.antiAlias,
    decoration: BoxDecoration(
      color: Colors.white.withValues(alpha: .08),
      borderRadius: BorderRadius.circular(20),
      border: Border.all(color: Colors.white10),
    ),
    child: AnimatedBuilder(
      animation: _controller,
      builder: (_, __) => FractionallySizedBox(
        alignment: AlignmentDirectional.centerStart,
        widthFactor: .35 + (_controller.value * .45),
        child: Container(
          decoration: BoxDecoration(
            borderRadius: BorderRadius.circular(20),
            gradient: const LinearGradient(colors: [gold, gold2]),
          ),
        ),
      ),
    ),
  );
}

class _SplashGlow extends StatelessWidget {
  const _SplashGlow({required this.size, required this.opacity});

  final double size;
  final double opacity;

  @override
  Widget build(BuildContext context) => Container(
    width: size,
    height: size,
    decoration: BoxDecoration(
      shape: BoxShape.circle,
      gradient: RadialGradient(
        colors: [
          gold.withValues(alpha: opacity),
          gold.withValues(alpha: 0),
        ],
      ),
    ),
  );
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

class _LoginScreenState extends State<LoginScreen>
    with SingleTickerProviderStateMixin {
  final emailController = TextEditingController();
  final passwordController = TextEditingController();
  bool sending = false;
  bool rememberMe = false;
  bool obscurePassword = true;
  String? loginMessage;

  late final AnimationController _animationController;
  late final Animation<double> _fadeAnimation;
  late final Animation<Offset> _slideAnimation;

  @override
  void initState() {
    super.initState();
    _animationController = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 900),
    );
    _fadeAnimation = CurvedAnimation(
      parent: _animationController,
      curve: Curves.easeOut,
    );
    _slideAnimation =
        Tween<Offset>(begin: const Offset(0, .08), end: Offset.zero).animate(
          CurvedAnimation(
            parent: _animationController,
            curve: Curves.easeOutCubic,
          ),
        );
    _animationController.forward();
  }

  @override
  void dispose() {
    _animationController.dispose();
    emailController.dispose();
    passwordController.dispose();
    super.dispose();
  }

  Future<void> login() async {
    if (sending) return;
    final email = emailController.text.trim();
    final password = passwordController.text;
    if (email.isEmpty) {
      setState(() => loginMessage = 'أدخل اسم المستخدم أو البريد الإلكتروني');
      return;
    }
    if (password.isEmpty) {
      setState(() => loginMessage = 'أدخل كلمة المرور');
      return;
    }

    setState(() {
      sending = true;
      loginMessage = null;
    });

    try {
      await context.read<AppState>().login(email, password);
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
    child: Stack(
      children: [
        const Positioned(
          top: -85,
          right: -70,
          child: _LoginGlow(size: 230, opacity: .16),
        ),
        const Positioned(
          bottom: -120,
          left: -90,
          child: _LoginGlow(size: 280, opacity: .11),
        ),
        Positioned.fill(
          child: IgnorePointer(
            child: CustomPaint(painter: _LoginGridPainter()),
          ),
        ),
        SafeArea(
          child: Center(
            child: SingleChildScrollView(
              physics: const BouncingScrollPhysics(),
              padding: const EdgeInsets.symmetric(horizontal: 22, vertical: 24),
              child: FadeTransition(
                opacity: _fadeAnimation,
                child: SlideTransition(
                  position: _slideAnimation,
                  child: _LoginCard(
                    emailController: emailController,
                    passwordController: passwordController,
                    sending: sending,
                    rememberMe: rememberMe,
                    obscurePassword: obscurePassword,
                    loginMessage: loginMessage,
                    onRememberChanged: (v) =>
                        setState(() => rememberMe = v ?? false),
                    onTogglePassword: () =>
                        setState(() => obscurePassword = !obscurePassword),
                    onLogin: login,
                    onRegister: () => Navigator.push(
                      context,
                      MaterialPageRoute(builder: (_) => const RegisterScreen()),
                    ),
                  ),
                ),
              ),
            ),
          ),
        ),
      ],
    ),
  );
}

class _LoginCard extends StatelessWidget {
  const _LoginCard({
    required this.emailController,
    required this.passwordController,
    required this.sending,
    required this.rememberMe,
    required this.obscurePassword,
    required this.loginMessage,
    required this.onRememberChanged,
    required this.onTogglePassword,
    required this.onLogin,
    required this.onRegister,
  });

  final TextEditingController emailController;
  final TextEditingController passwordController;
  final bool sending;
  final bool rememberMe;
  final bool obscurePassword;
  final String? loginMessage;
  final ValueChanged<bool?> onRememberChanged;
  final VoidCallback onTogglePassword;
  final VoidCallback onLogin;
  final VoidCallback onRegister;

  @override
  Widget build(BuildContext context) => Container(
    width: double.infinity,
    constraints: const BoxConstraints(maxWidth: 430),
    padding: const EdgeInsets.fromLTRB(22, 30, 22, 24),
    decoration: BoxDecoration(
      borderRadius: BorderRadius.circular(34),
      gradient: LinearGradient(
        begin: Alignment.topCenter,
        end: Alignment.bottomCenter,
        colors: [
          const Color(0xFF191814).withValues(alpha: .96),
          const Color(0xFF070707).withValues(alpha: .98),
        ],
      ),
      boxShadow: [
        BoxShadow(
          color: gold.withValues(alpha: .22),
          blurRadius: 32,
          spreadRadius: -6,
          offset: const Offset(0, 0),
        ),
        BoxShadow(
          color: Colors.black.withValues(alpha: .60),
          blurRadius: 42,
          offset: const Offset(0, 24),
        ),
      ],
    ),
    child: Column(
      mainAxisSize: MainAxisSize.min,
      children: [
        const _LoginLogo(),
        const SizedBox(height: 24),
        const Text(
          'مرحباً بك',
          style: TextStyle(
            color: Colors.white,
            fontSize: 26,
            fontWeight: FontWeight.w800,
          ),
        ),
        const SizedBox(height: 5),
        const Text(
          'تسجيل الدخول إلى حسابك',
          style: TextStyle(color: Colors.white70, fontSize: 14),
        ),
        const SizedBox(height: 26),
        _LuxuryTextField(
          controller: emailController,
          hint: 'اسم المستخدم أو البريد الإلكتروني',
          icon: Icons.person_outline_rounded,
          keyboardType: TextInputType.emailAddress,
          textInputAction: TextInputAction.next,
        ),
        const SizedBox(height: 14),
        _LuxuryTextField(
          controller: passwordController,
          hint: 'كلمة المرور',
          icon: Icons.lock_outline_rounded,
          obscureText: obscurePassword,
          textInputAction: TextInputAction.done,
          onSubmitted: (_) => onLogin(),
          suffix: IconButton(
            onPressed: onTogglePassword,
            icon: Icon(
              obscurePassword
                  ? Icons.visibility_outlined
                  : Icons.visibility_off_outlined,
              color: Colors.white54,
              size: 19,
            ),
          ),
        ),
        const SizedBox(height: 10),
        Row(
          children: [
            Transform.scale(
              scale: .86,
              child: Checkbox(
                value: rememberMe,
                onChanged: onRememberChanged,
                activeColor: gold,
                checkColor: Colors.black,
                side: const BorderSide(color: Colors.white38),
                visualDensity: VisualDensity.compact,
              ),
            ),
            const Text(
              'تذكرني',
              style: TextStyle(color: Colors.white70, fontSize: 13),
            ),
            const Spacer(),
            TextButton(
              onPressed: () {},
              style: TextButton.styleFrom(
                foregroundColor: Colors.white70,
                padding: EdgeInsets.zero,
                minimumSize: const Size(10, 32),
              ),
              child: const Text(
                'نسيت كلمة المرور؟',
                style: TextStyle(fontSize: 13),
              ),
            ),
          ],
        ),
        const SizedBox(height: 12),
        _LoginButton(sending: sending, onTap: onLogin),
        if (loginMessage != null) ...[
          const SizedBox(height: 12),
          Text(
            loginMessage!,
            textAlign: TextAlign.center,
            style: const TextStyle(color: Colors.redAccent, fontSize: 13),
          ),
        ],
        const SizedBox(height: 22),
        const _OrDivider(),
        const SizedBox(height: 18),
        _GoogleButton(onTap: () {}),
        const SizedBox(height: 24),
        Wrap(
          alignment: WrapAlignment.center,
          crossAxisAlignment: WrapCrossAlignment.center,
          spacing: 5,
          children: [
            const Text(
              'ليس لديك حساب؟',
              style: TextStyle(color: Colors.white70, fontSize: 14),
            ),
            GestureDetector(
              onTap: onRegister,
              child: const Text(
                'إنشاء حساب جديد',
                style: TextStyle(
                  color: gold2,
                  fontSize: 14,
                  fontWeight: FontWeight.w700,
                ),
              ),
            ),
          ],
        ),
      ],
    ),
  );
}

class RegisterScreen extends StatefulWidget {
  const RegisterScreen({super.key});

  @override
  State<RegisterScreen> createState() => _RegisterScreenState();
}

class _RegisterScreenState extends State<RegisterScreen> {
  final nameController = TextEditingController();
  final emailController = TextEditingController();
  final phoneController = TextEditingController();
  final passwordController = TextEditingController();
  final confirmPasswordController = TextEditingController();
  bool sending = false;
  bool obscurePassword = true;
  bool obscureConfirmPassword = true;
  String? registerMessage;

  @override
  void dispose() {
    nameController.dispose();
    emailController.dispose();
    phoneController.dispose();
    passwordController.dispose();
    confirmPasswordController.dispose();
    super.dispose();
  }

  Future<void> register() async {
    if (sending) return;
    final name = nameController.text.trim();
    final email = emailController.text.trim();
    final phone = phoneController.text.trim();
    final password = passwordController.text;
    final confirmPassword = confirmPasswordController.text;

    if (name.isEmpty) {
      setState(() => registerMessage = 'أدخل الاسم الكامل');
      return;
    }
    if (email.isEmpty) {
      setState(() => registerMessage = 'أدخل البريد الإلكتروني');
      return;
    }
    if (password.length < 8) {
      setState(
        () => registerMessage = 'كلمة المرور يجب أن تكون 8 أحرف على الأقل',
      );
      return;
    }
    if (password != confirmPassword) {
      setState(() => registerMessage = 'كلمتا المرور غير متطابقتين');
      return;
    }

    setState(() {
      sending = true;
      registerMessage = null;
    });

    try {
      await context.read<AppState>().register(
        name: name,
        email: email,
        phone: phone,
        password: password,
      );
      if (!mounted) return;
      Navigator.pushAndRemoveUntil(
        context,
        MaterialPageRoute(builder: (_) => const MainShell()),
        (_) => false,
      );
    } catch (e) {
      if (!mounted) return;
      setState(() => registerMessage = 'فشل إنشاء الحساب: $e');
    } finally {
      if (mounted) setState(() => sending = false);
    }
  }

  @override
  Widget build(BuildContext context) => BrandScaffold(
    child: Stack(
      children: [
        const Positioned(
          top: -85,
          right: -70,
          child: _LoginGlow(size: 230, opacity: .16),
        ),
        const Positioned(
          bottom: -120,
          left: -90,
          child: _LoginGlow(size: 280, opacity: .11),
        ),
        Positioned.fill(
          child: IgnorePointer(
            child: CustomPaint(painter: _LoginGridPainter()),
          ),
        ),
        SafeArea(
          child: Center(
            child: SingleChildScrollView(
              physics: const BouncingScrollPhysics(),
              padding: const EdgeInsets.symmetric(horizontal: 22, vertical: 24),
              child: Container(
                width: double.infinity,
                constraints: const BoxConstraints(maxWidth: 430),
                padding: const EdgeInsets.fromLTRB(22, 30, 22, 24),
                decoration: BoxDecoration(
                  borderRadius: BorderRadius.circular(34),
                  gradient: LinearGradient(
                    begin: Alignment.topCenter,
                    end: Alignment.bottomCenter,
                    colors: [
                      const Color(0xFF191814).withValues(alpha: .96),
                      const Color(0xFF070707).withValues(alpha: .98),
                    ],
                  ),
                  boxShadow: [
                    BoxShadow(
                      color: gold.withValues(alpha: .22),
                      blurRadius: 32,
                      spreadRadius: -6,
                      offset: const Offset(0, 0),
                    ),
                    BoxShadow(
                      color: Colors.black.withValues(alpha: .60),
                      blurRadius: 42,
                      offset: const Offset(0, 24),
                    ),
                  ],
                ),
                child: Column(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    const _LoginLogo(),
                    const SizedBox(height: 24),
                    const Text(
                      'إنشاء حساب جديد',
                      style: TextStyle(
                        color: Colors.white,
                        fontSize: 26,
                        fontWeight: FontWeight.w800,
                      ),
                    ),
                    const SizedBox(height: 5),
                    const Text(
                      'أدخل معلوماتك لإنشاء حسابك',
                      style: TextStyle(color: Colors.white70, fontSize: 14),
                    ),
                    const SizedBox(height: 26),
                    _LuxuryTextField(
                      controller: nameController,
                      hint: 'الاسم الكامل',
                      icon: Icons.badge_outlined,
                      textInputAction: TextInputAction.next,
                    ),
                    const SizedBox(height: 14),
                    _LuxuryTextField(
                      controller: emailController,
                      hint: 'البريد الإلكتروني',
                      icon: Icons.email_outlined,
                      keyboardType: TextInputType.emailAddress,
                      textInputAction: TextInputAction.next,
                    ),
                    const SizedBox(height: 14),
                    _LuxuryTextField(
                      controller: phoneController,
                      hint: 'رقم الهاتف (اختياري)',
                      icon: Icons.phone_outlined,
                      keyboardType: TextInputType.phone,
                      textInputAction: TextInputAction.next,
                    ),
                    const SizedBox(height: 14),
                    _LuxuryTextField(
                      controller: passwordController,
                      hint: 'كلمة المرور',
                      icon: Icons.lock_outline_rounded,
                      obscureText: obscurePassword,
                      textInputAction: TextInputAction.next,
                      suffix: IconButton(
                        onPressed: () =>
                            setState(() => obscurePassword = !obscurePassword),
                        icon: Icon(
                          obscurePassword
                              ? Icons.visibility_outlined
                              : Icons.visibility_off_outlined,
                          color: Colors.white54,
                          size: 19,
                        ),
                      ),
                    ),
                    const SizedBox(height: 14),
                    _LuxuryTextField(
                      controller: confirmPasswordController,
                      hint: 'تأكيد كلمة المرور',
                      icon: Icons.lock_reset_outlined,
                      obscureText: obscureConfirmPassword,
                      textInputAction: TextInputAction.done,
                      onSubmitted: (_) => register(),
                      suffix: IconButton(
                        onPressed: () => setState(
                          () =>
                              obscureConfirmPassword = !obscureConfirmPassword,
                        ),
                        icon: Icon(
                          obscureConfirmPassword
                              ? Icons.visibility_outlined
                              : Icons.visibility_off_outlined,
                          color: Colors.white54,
                          size: 19,
                        ),
                      ),
                    ),
                    const SizedBox(height: 18),
                    _LoginButton(
                      sending: sending,
                      onTap: register,
                      text: 'إنشاء الحساب',
                    ),
                    if (registerMessage != null) ...[
                      const SizedBox(height: 12),
                      Text(
                        registerMessage!,
                        textAlign: TextAlign.center,
                        style: const TextStyle(
                          color: Colors.redAccent,
                          fontSize: 13,
                        ),
                      ),
                    ],
                    const SizedBox(height: 18),
                    TextButton(
                      onPressed: () => Navigator.pop(context),
                      style: TextButton.styleFrom(
                        foregroundColor: gold2,
                        padding: EdgeInsets.zero,
                      ),
                      child: const Text(
                        'لديك حساب؟ تسجيل الدخول',
                        style: TextStyle(fontWeight: FontWeight.w700),
                      ),
                    ),
                  ],
                ),
              ),
            ),
          ),
        ),
      ],
    ),
  );
}

class _LoginLogo extends StatelessWidget {
  const _LoginLogo();

  @override
  Widget build(BuildContext context) => Container(
    width: 180,
    height: 180,
    decoration: BoxDecoration(
      shape: BoxShape.circle,
      boxShadow: [
        BoxShadow(
          color: gold.withValues(alpha: .25),
          blurRadius: 30,
          offset: const Offset(0, 16),
        ),
      ],
    ),
    child: ClipOval(child: const BrandLogo(fit: BoxFit.contain)),
  );
}

class _LuxuryTextField extends StatelessWidget {
  const _LuxuryTextField({
    required this.controller,
    required this.hint,
    required this.icon,
    this.keyboardType,
    this.textInputAction,
    this.obscureText = false,
    this.suffix,
    this.onSubmitted,
  });

  final TextEditingController controller;
  final String hint;
  final IconData icon;
  final TextInputType? keyboardType;
  final TextInputAction? textInputAction;
  final bool obscureText;
  final Widget? suffix;
  final ValueChanged<String>? onSubmitted;

  @override
  Widget build(BuildContext context) => SizedBox(
    height: 52,
    child: TextField(
      controller: controller,
      keyboardType: keyboardType,
      textInputAction: textInputAction,
      obscureText: obscureText,
      textAlign: TextAlign.right,
      onSubmitted: onSubmitted,
      style: const TextStyle(color: Colors.white, fontSize: 14),
      decoration: InputDecoration(
        hintText: hint,
        hintStyle: const TextStyle(color: Colors.white60, fontSize: 13),
        prefixIcon: Icon(icon, color: gold2, size: 21),
        suffixIcon: suffix,
        filled: true,
        fillColor: Colors.black.withValues(alpha: .32),
        contentPadding: const EdgeInsets.symmetric(
          horizontal: 14,
          vertical: 14,
        ),
        enabledBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(12),
          borderSide: BorderSide(color: gold2.withValues(alpha: .42)),
        ),
        focusedBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(12),
          borderSide: const BorderSide(color: gold2, width: 1.2),
        ),
      ),
    ),
  );
}

class _LoginButton extends StatelessWidget {
  const _LoginButton({
    required this.sending,
    required this.onTap,
    this.text = 'تسجيل الدخول',
  });

  final bool sending;
  final VoidCallback onTap;
  final String text;

  @override
  Widget build(BuildContext context) => SizedBox(
    width: double.infinity,
    height: 54,
    child: DecoratedBox(
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(13),
        gradient: const LinearGradient(
          begin: Alignment.centerLeft,
          end: Alignment.centerRight,
          colors: [gold, gold2, gold],
        ),
        boxShadow: [
          BoxShadow(
            color: gold.withValues(alpha: .28),
            blurRadius: 20,
            offset: const Offset(0, 8),
          ),
        ],
      ),
      child: TextButton(
        onPressed: sending ? null : onTap,
        style: ButtonStyle(
          overlayColor: WidgetStateProperty.resolveWith((states) {
            if (states.contains(WidgetState.pressed)) {
              return Colors.black.withValues(alpha: .08);
            }
            return Colors.transparent;
          }),
          shape: WidgetStateProperty.all(
            RoundedRectangleBorder(borderRadius: BorderRadius.circular(13)),
          ),
        ),
        child: sending
            ? const SizedBox(
                width: 22,
                height: 22,
                child: CircularProgressIndicator(
                  strokeWidth: 2.2,
                  color: Colors.black,
                ),
              )
            : Text(
                text,
                style: const TextStyle(
                  color: Colors.black,
                  fontSize: 16,
                  fontWeight: FontWeight.w800,
                ),
              ),
      ),
    ),
  );
}

class _OrDivider extends StatelessWidget {
  const _OrDivider();

  @override
  Widget build(BuildContext context) => Row(
    children: [
      Expanded(
        child: Container(
          height: 1,
          decoration: BoxDecoration(
            gradient: LinearGradient(
              colors: [Colors.transparent, Colors.white.withValues(alpha: .30)],
            ),
          ),
        ),
      ),
      const Padding(
        padding: EdgeInsets.symmetric(horizontal: 14),
        child: Text('أو', style: TextStyle(color: Colors.white70)),
      ),
      Expanded(
        child: Container(
          height: 1,
          decoration: BoxDecoration(
            gradient: LinearGradient(
              colors: [Colors.white.withValues(alpha: .30), Colors.transparent],
            ),
          ),
        ),
      ),
    ],
  );
}

class _GoogleButton extends StatelessWidget {
  const _GoogleButton({required this.onTap});

  final VoidCallback onTap;

  @override
  Widget build(BuildContext context) => SizedBox(
    width: double.infinity,
    height: 52,
    child: OutlinedButton(
      onPressed: onTap,
      style: OutlinedButton.styleFrom(
        foregroundColor: Colors.white,
        side: BorderSide(color: gold2.withValues(alpha: .48)),
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(13)),
      ),
      child: const Row(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Text(
            'G',
            style: TextStyle(
              color: gold2,
              fontSize: 22,
              fontWeight: FontWeight.w900,
            ),
          ),
          SizedBox(width: 12),
          Text(
            'تسجيل الدخول عبر Google',
            style: TextStyle(fontSize: 14, fontWeight: FontWeight.w600),
          ),
        ],
      ),
    ),
  );
}

class _LoginGlow extends StatelessWidget {
  const _LoginGlow({required this.size, required this.opacity});

  final double size;
  final double opacity;

  @override
  Widget build(BuildContext context) => Container(
    width: size,
    height: size,
    decoration: BoxDecoration(
      shape: BoxShape.circle,
      gradient: RadialGradient(
        colors: [
          gold.withValues(alpha: opacity),
          gold.withValues(alpha: 0),
        ],
      ),
    ),
  );
}

class _LoginGridPainter extends CustomPainter {
  @override
  void paint(Canvas canvas, Size size) {
    final paint = Paint()
      ..color = gold.withValues(alpha: .025)
      ..strokeWidth = .7;

    for (double x = 0; x < size.width; x += 42) {
      canvas.drawLine(Offset(x, 0), Offset(x, size.height), paint);
    }
    for (double y = 0; y < size.height; y += 42) {
      canvas.drawLine(Offset(0, y), Offset(size.width, y), paint);
    }
  }

  @override
  bool shouldRepaint(covariant CustomPainter oldDelegate) => false;
}
