# HWinfo — نرم‌افزار پایش سخت‌افزار سازمان

سامانه وب برای جمع‌آوری، تأیید، تخصیص و پایش دارایی‌های سخت‌افزاری سازمان.

## فناوری
PHP 8.2+، MySQL 8+، PDO، HTML5/CSS3/Vanilla JS، RTL Persian UI.

## اجرای سریع
1. `database/schema.sql` را روی MySQL 8 اجرا کنید.
2. متغیرهای محیطی `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS` را تنظیم کنید.
3. وب‌ریشه را روی `public/` قرار دهید.
4. با `php -S 127.0.0.1:8080 -t public public/index.php` اجرا کنید.
5. پس از ورود، کارشناس CSV تولیدشده توسط collector را بارگذاری می‌کند.

نسخه توسعه: `e001.1.00.000-alpha1`

مستندات: `docs/` و قرارداد CSV: `docs/csv-format.md`.
