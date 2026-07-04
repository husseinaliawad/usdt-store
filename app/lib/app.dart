part of '../main.dart';

void main() => runApp(
  ChangeNotifierProvider(
    create: (_) => AppState(),
    child: const UsdtStoreApp(),
  ),
);

class UsdtStoreApp extends StatelessWidget {
  const UsdtStoreApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      debugShowCheckedModeBanner: false,
      title: 'USDT STORE',
      locale: const Locale('ar'),
      theme: ThemeData(
        brightness: Brightness.dark,
        scaffoldBackgroundColor: black,
        fontFamily: 'Arial',
        textTheme: const TextTheme(
          headlineLarge: TextStyle(fontSize: 30, fontWeight: FontWeight.w800, height: 1.20),
          headlineMedium: TextStyle(fontSize: 24, fontWeight: FontWeight.w800, height: 1.25),
          titleLarge: TextStyle(fontSize: 20, fontWeight: FontWeight.w700, height: 1.30),
          titleMedium: TextStyle(fontSize: 16, fontWeight: FontWeight.w700, height: 1.35),
          bodyLarge: TextStyle(fontSize: 15, fontWeight: FontWeight.w400, height: 1.55),
          bodyMedium: TextStyle(fontSize: 14, fontWeight: FontWeight.w400, height: 1.55),
          bodySmall: TextStyle(fontSize: 12, fontWeight: FontWeight.w400, height: 1.45),
          labelLarge: TextStyle(fontSize: 15, fontWeight: FontWeight.w700, height: 1.20),
        ),
        colorScheme: const ColorScheme.dark(
          primary: gold,
          secondary: gold2,
          surface: panel,
        ),
        inputDecorationTheme: InputDecorationTheme(
          filled: true,
          fillColor: panelSoft,
          labelStyle: const TextStyle(color: muted, fontSize: 13),
          hintStyle: const TextStyle(color: mutedSoft, fontSize: 13),
          contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
          enabledBorder: OutlineInputBorder(
            borderRadius: BorderRadius.circular(16),
            borderSide: const BorderSide(color: line),
          ),
          focusedBorder: OutlineInputBorder(
            borderRadius: BorderRadius.circular(16),
            borderSide: const BorderSide(color: gold2, width: 1.15),
          ),
        ),
      ),
      builder: (context, child) =>
          Directionality(textDirection: TextDirection.rtl, child: child!),
      home: const SplashScreen(),
    );
  }
}
