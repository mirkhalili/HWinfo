# تولید و استقرار

## وضعیت
این نسخه هسته عملیاتی سامانه را شامل می‌کند: ورود، RBAC، CSRF، import CSV، ثبت audit، تأیید/رد و تخصیص رایانه، جست‌وجوی JSON پرسنل و UI اولیه.

## قبل از production
- اجرای schema روی MySQL 8+
- ساخت کاربر دیتابیس محدود به همان schema
- تنظیم HTTPS و secure cookies
- قرار دادن document root روی public/
- تنظیم upload size و backup
- اجرای lint و تست‌های CSV
- اجرای تست تراکنش rollback و audit
- تست مرورگر برای سه نقش
- ایجاد حساب admin با password_hash()؛ رمز نمونه داخل repository قرار نمی‌گیرد.

## نکته داده
CSV مرجع دارای 75 ستون اصلی collector است. داده خام رایانه در JSON نگهداری می‌شود تا اطلاعات سخت‌افزاری متغیر از بین نرود؛ هویت، وضعیت، مالکیت و audit رابطه‌ای هستند.
