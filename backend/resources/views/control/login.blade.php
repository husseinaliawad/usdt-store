<!doctype html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>USDT STORE Control</title>
    <style>
        :root {
            --black: #070707;
            --panel: #11110f;
            --panel-2: #181611;
            --gold: #dca83a;
            --gold-2: #f6d27a;
            --line: rgba(246, 210, 122, .22);
            --text: #f7f2e8;
            --muted: #a8a093;
            font-family: Arial, Tahoma, sans-serif;
        }

        * { box-sizing: border-box; }
        body {
            min-height: 100vh;
            margin: 0;
            display: grid;
            place-items: center;
            background:
                radial-gradient(circle at top right, rgba(220, 168, 58, .20), transparent 34rem),
                radial-gradient(circle at bottom left, rgba(246, 210, 122, .10), transparent 28rem),
                var(--black);
            color: var(--text);
            padding: 24px;
        }

        .login {
            width: min(460px, 100%);
            padding: 34px;
            border: 1px solid var(--line);
            border-radius: 26px;
            background: linear-gradient(180deg, rgba(24, 22, 17, .96), rgba(7, 7, 7, .98));
            box-shadow: 0 30px 80px rgba(0, 0, 0, .55), 0 0 55px rgba(220, 168, 58, .10);
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 30px;
        }

        .mark {
            width: 58px;
            height: 58px;
            border-radius: 50%;
            display: grid;
            place-items: center;
            color: #161107;
            font-size: 30px;
            font-weight: 900;
            background: linear-gradient(135deg, var(--gold), var(--gold-2));
            box-shadow: 0 12px 34px rgba(220, 168, 58, .32);
        }

        h1 { margin: 0; font-size: 24px; letter-spacing: 0; }
        p { margin: 7px 0 0; color: var(--muted); font-size: 14px; }
        label { display: block; margin: 18px 0 8px; color: var(--gold-2); font-weight: 700; font-size: 13px; }
        input {
            width: 100%;
            height: 52px;
            border-radius: 14px;
            border: 1px solid var(--line);
            background: rgba(0, 0, 0, .36);
            color: var(--text);
            padding: 0 16px;
            outline: none;
            font-size: 15px;
        }
        input:focus { border-color: rgba(246, 210, 122, .60); }

        button {
            width: 100%;
            height: 54px;
            margin-top: 24px;
            border: 0;
            border-radius: 14px;
            color: #111;
            font-weight: 900;
            font-size: 16px;
            cursor: pointer;
            background: linear-gradient(90deg, var(--gold), var(--gold-2));
            box-shadow: 0 14px 30px rgba(220, 168, 58, .22);
        }

        .error {
            margin-top: 16px;
            color: #ff7777;
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
                <p>لوحة إدارة العمليات والمحافظ</p>
            </div>
        </div>

        <form method="post" action="{{ route('control.login.submit') }}">
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
