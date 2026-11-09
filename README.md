# Horus Group

موقع ولوحة إدارة **مؤسسة حورس للتوريدات والتشطيبات**، مبني باستخدام Laravel.

## المتطلبات

- PHP 8.1 أو أحدث مع إضافات Laravel المطلوبة
- Composer
- Node.js و npm
- قاعدة بيانات يدعمها Laravel

## التشغيل محليًا

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm install
npm run build
php artisan serve
```

حدّث إعدادات قاعدة البيانات وخدمات البريد والدفع في ملف `.env` قبل التشغيل.

## الاختبارات

```bash
php artisan test
```
