<!doctype html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>USDT STORE - تسجيل الدخول</title>
    <style>
        :root {
            --bg: #0a0b0d;
            --surface: #15181c;
            --gold: #d7a84a;
            --gold-2: #f3d17d;
            --line: rgba(255, 255, 255, .12);
            --text: #f4f7f9;
            --muted: #97a3ad;
            font-family: Tahoma, Arial, sans-serif;
        }

        * { box-sizing: border-box; }
        body {
            min-height: 100vh;
            margin: 0;
            display: grid;
            place-items: center;
            background: linear-gradient(180deg, #11151a, var(--bg));
            color: var(--text);
            padding: 24px;
        }

        .login {
            width: min(460px, 100%);
            padding: 32px;
            border: 1px solid var(--line);
            border-radius: 12px;
            background: linear-gradient(180deg, rgba(25, 29, 34, .98), rgba(14, 16, 19, .98));
            box-shadow: 0 28px 70px rgba(0, 0, 0, .46);
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 28px;
        }

        .mark {
            width: 52px;
            height: 52px;
            border-radius: 12px;
            display: grid;
            place-items: center;
            color: #111;
            font-size: 28px;
            font-weight: 900;
            background: linear-gradient(135deg, var(--gold-2), var(--gold));
        }

        h1 { margin: 0; font-size: 23px; letter-spacing: 0; }
        p { margin: 7px 0 0; color: var(--muted); font-size: 14px; }
        label { display: block; margin: 18px 0 8px; color: #f6dba0; font-weight: 700; font-size: 13px; }
        input {
            width: 100%;
            height: 50px;
            border-radius: 8px;
            border: 1px solid var(--line);
            background: rgba(0, 0, 0, .30);
            color: var(--text);
            padding: 0 14px;
            outline: none;
            font-size: 15px;
        }
        input:focus { border-color: rgba(215, 168, 74, .68); }

        button {
            width: 100%;
            height: 52px;
            margin-top: 24px;
            border: 0;
            border-radius: 8px;
            color: #101114;
            font-weight: 900;
            font-size: 16px;
            cursor: pointer;
            background: linear-gradient(90deg, var(--gold-2), var(--gold));
        }

        .error {
            margin-top: 16px;
            color: #ff9a9a;
            text-align: center;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <main class="login">
        <div class="brand">
            <div class="mark">T</div>
            <div>
                <h1>USDT STORE Control</h1>
                <p>لوحة إدارة العمليات والمحافظ والمستخدمين</p>
            </div>
        </div>

        <form method="post" action="{{ route('control.login.submit', [], false) }}">
            @csrf
            <label for="email">البريد الإلكتروني</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required>

            <label for="password">كلمة المرور</label>
            <input id="password" name="password" type="password" autocomplete="current-password" required>

            <button type="submit">دخول لوحة التحكم</button>

            @if ($errors->any())
                <div class="error">{{ $errors->first() }}</div>
            @endif
        </form>
    </main>
</body>
</html>
