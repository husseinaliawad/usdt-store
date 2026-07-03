# Render Docker Deploy

Use `backend/` as the Render service root directory. Render will build `backend/Dockerfile`.

## Required Render environment variables

Set these in Render > Environment:

```env
APP_NAME="USDT STORE"
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:PUT_GENERATED_KEY_HERE
APP_URL=https://YOUR_RENDER_SERVICE.onrender.com

DB_CONNECTION=pgsql
DB_URL=YOUR_RENDER_POSTGRES_INTERNAL_DATABASE_URL
DB_SSLMODE=require

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=sync

MAIL_MAILER=smtp
MAIL_SCHEME=smtps
MAIL_HOST=smtp.gmail.com
MAIL_PORT=465
MAIL_USERNAME=your-gmail-address@gmail.com
MAIL_PASSWORD=your-google-app-password
MAIL_FROM_ADDRESS=your-gmail-address@gmail.com
MAIL_FROM_NAME="USDT STORE"

RUN_MIGRATIONS=true
RUN_SEEDERS=true
```

Generate `APP_KEY` locally with:

```bash
php artisan key:generate --show
```

For Gmail, `MAIL_PASSWORD` must be a Google App Password, not the normal Gmail account password. `MAIL_FROM_ADDRESS` should match `MAIL_USERNAME` unless the Gmail account has that sender alias configured.

After Render deploys, the mobile API base URL should be:

```text
https://YOUR_RENDER_SERVICE.onrender.com/api
```

Then rebuild Flutter with:

```bash
flutter build apk --release --dart-define=API_BASE_URL=https://YOUR_RENDER_SERVICE.onrender.com/api
```

After the first successful deploy, set `RUN_SEEDERS=false` unless you intentionally want the demo seed data checked/updated on each deploy.
