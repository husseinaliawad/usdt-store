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
        colorScheme: const ColorScheme.dark(
          primary: gold,
          secondary: gold2,
          surface: panel,
        ),
        inputDecorationTheme: InputDecorationTheme(
          filled: true,
          fillColor: const Color(0xFF101010),
          labelStyle: const TextStyle(color: muted),
          enabledBorder: OutlineInputBorder(
            borderRadius: BorderRadius.circular(14),
            borderSide: const BorderSide(color: line),
          ),
          focusedBorder: OutlineInputBorder(
            borderRadius: BorderRadius.circular(14),
            borderSide: const BorderSide(color: gold),
          ),
        ),
      ),
      builder: (context, child) =>
          Directionality(textDirection: TextDirection.rtl, child: child!),
      home: const SplashScreen(),
    );
  }
}
