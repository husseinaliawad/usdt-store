# Repository Guidelines

## Project Structure & Module Organization
This repository contains a Laravel API in `backend/` and a Flutter client in `app/`. The Flutter app imports all feature files through `app/lib/main.dart` using `part` files; shared client state and API calls live in `app/lib/state/app_state.dart` and `app/lib/services/api_client.dart`. The backend exposes REST routes from `backend/routes/api.php`, uses Sanctum tokens, and stores user-specific wallets, transactions, KYC, and notifications by `user_id`.

## Build, Test, and Development Commands
Backend local server for the Android emulator:

```bat
cd /d "C:\Users\User\Desktop\usdt store\backend"
C:\xampp\php\php.exe artisan serve --host=0.0.0.0 --port=8001
```

Verify it with `curl http://127.0.0.1:8001/api/health`.

Flutter commands:

```bat
cd /d "C:\Users\User\Desktop\usdt store\app"
flutter pub get
flutter analyze
flutter test
flutter run -d emulator-5554 --dart-define=API_BASE_URL=http://10.0.2.2:8001/api
```

Backend tests: `cd backend && C:\xampp\php\php.exe artisan test`.

## Android Emulator Workflow For Agents
Before every run, stop stale Flutter, Gradle, or install processes. Then confirm `flutter devices` shows `emulator-5554` and confirm backend health. If installation hangs at `Installing build\app\outputs\flutter-apk\app-debug.apk`, restart ADB and install manually:

```bat
%LOCALAPPDATA%\Android\Sdk\platform-tools\adb.exe kill-server
%LOCALAPPDATA%\Android\Sdk\platform-tools\adb.exe start-server
%LOCALAPPDATA%\Android\Sdk\platform-tools\adb.exe -s emulator-5554 install -r -t "build\app\outputs\flutter-apk\app-debug.apk"
```

If the emulator shows stale UI, uninstall first, then rebuild:

```bat
%LOCALAPPDATA%\Android\Sdk\platform-tools\adb.exe -s emulator-5554 uninstall com.usdtstore.usdt_store
flutter clean
flutter pub get
flutter run -d emulator-5554 --dart-define=API_BASE_URL=http://10.0.2.2:8001/api
```

Use `uiautomator dump` to verify visible text after fixes, not just source code.

## Current Authentication Rule
The Flutter app must not use OTP. Real users sign in or create accounts directly by email through `POST /api/auth/login`. Do not reintroduce OTP UI, `requestOtp`, `verifyOtp`, `OtpScreen`, `/auth/request-otp`, or `/auth/verify-otp` unless explicitly requested.

## Coding Style & Testing
Flutter uses `flutter_lints` from `app/analysis_options.yaml`; run `flutter analyze` after Dart edits. Laravel uses PHPUnit via Artisan; run `C:\xampp\php\php.exe artisan test` after backend edits. Keep user data isolated by authenticated `user_id`; never restore static demo balances or shared transactions in Flutter state.
