# USDT STORE

مشروع كامل لإدارة وتحويل USDT:

- `backend/`: Laravel 12 REST API + Laravel Sanctum + Filament Admin Panel.
- `app/`: Flutter mobile app بواجهة عربية RTL وتصميم أسود/ذهبي.
- `logo.jpg`: الهوية البصرية المستخدمة داخل تطبيق Flutter.

## تشغيل Laravel API

1. أنشئ قاعدة MySQL باسم `usdt_store`.
2. افتح `backend/.env` واضبط:

```env
APP_URL=http://127.0.0.1:8000
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=usdt_store
DB_USERNAME=root
DB_PASSWORD=
```

3. شغّل الأوامر:

```bash
cd backend
composer install
php artisan key:generate
php artisan storage:link
php artisan migrate:fresh --seed
php artisan serve --host=127.0.0.1 --port=8000
```

لوحة الإدارة:

- الرابط: `http://127.0.0.1:8000/admin`
- المستخدم: `admin@usdt-store.local`
- كلمة المرور: `password`

## API الأساسي

- `POST /api/auth/request-otp`
- `POST /api/auth/verify-otp`
- `GET /api/home`
- `POST /api/receive`
- `POST /api/kyc`
- `GET /api/transactions`
- `POST /api/transactions/send`
- `POST /api/transactions/deposit`
- `POST /api/transactions/withdraw`
- `GET /api/notifications`
- `POST /api/support`

في بيئة التطوير رمز OTP هو `123456`.

## تشغيل Flutter

```bash
cd app
flutter pub get
flutter run --dart-define=API_BASE_URL=http://10.0.2.2:8000/api
```

على جهاز حقيقي بدّل `10.0.2.2` بعنوان IP الجهاز الذي يشغل Laravel.

## ما تم تنفيذه

- Authentication برقم الهاتف وOTP.
- أدوار `admin` و`user`.
- KYC وحالاته.
- جداول الشبكات، المحافظ، العمليات، الرسوم، أسعار الصرف، الإشعارات، وسجل التدقيق.
- منع الإرسال والسحب قبل موافقة KYC في API.
- Rate limiting لمسارات API وOTP.
- لوحة Filament لإدارة المستخدمين، KYC، العمليات، الشبكات، العمولات، أسعار الصرف، الإشعارات، وسجل التدقيق.
- تطبيق Flutter RTL يحتوي Splash, Onboarding, Login, OTP, Home, Send, Receive QR, Deposit, Withdraw, Transactions, Details, Virtual Card, Stats, Notifications, Support, Profile, KYC.

## التحقق

تم تشغيل:

```bash
cd app
flutter analyze
flutter test
```

كلاهما نجح بدون مشاكل.
