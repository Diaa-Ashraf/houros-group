<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // 1. General & Branding
            [
                'group' => 'general',
                'key' => 'site_name',
                'value' => 'مؤسسة حورس للتوريدات والتشطيبات',
                'type' => 'text',
                'description' => 'اسم المتجر الرسمي',
            ],
            [
                'group' => 'general',
                'key' => 'site_tagline',
                'value' => 'حلول متكاملة للتشطيب والديكور',
                'type' => 'text',
                'description' => 'الشعار اللفظي للمتجر',
            ],
            [
                'group' => 'general',
                'key' => 'site_logo',
                'value' => 'assets/site/img/horus-logo.png',
                'type' => 'image',
                'description' => 'شعار المتجر الرسمي (Logo)',
            ],
            [
                'group' => 'general',
                'key' => 'site_favicon',
                'value' => 'assets/site/img/horus-mark.png',
                'type' => 'image',
                'description' => 'أيقونة التاب في المتصفح (Favicon)',
            ],
            [
                'group' => 'general',
                'key' => 'default_currency',
                'value' => 'EGP',
                'type' => 'text',
                'description' => 'العملة الأساسية (الجنيه المصري)',
            ],
            [
                'group' => 'general',
                'key' => 'currency_symbol',
                'value' => 'ج.م',
                'type' => 'text',
                'description' => 'رمز العملة المعروض للعملاء',
            ],

            // 2. Contact & Hours
            [
                'group' => 'contact',
                'key' => 'store_phone',
                'value' => '01002527072',
                'type' => 'text',
                'description' => 'رقم هاتف المتجر والشكاوى الرئيسي',
            ],
            [
                'group' => 'contact',
                'key' => 'store_phone_secondary',
                'value' => '',
                'type' => 'text',
                'description' => 'هاتف إضافي أو خط أرضي',
            ],
            [
                'group' => 'contact',
                'key' => 'store_whatsapp',
                'value' => '201002527072',
                'type' => 'text',
                'description' => 'رقم الواتساب الموحد لاستقبال الطلبات',
            ],
            [
                'group' => 'contact',
                'key' => 'store_email',
                'value' => '',
                'type' => 'text',
                'description' => 'البريد الإلكتروني الرسمي',
            ],
            [
                'group' => 'contact',
                'key' => 'address',
                'value' => '٢٢ع حدائق الأهرام، بوابة حورس',
                'type' => 'text',
                'description' => 'عنوان الإدارة والفرع الرئيسي',
            ],
            [
                'group' => 'contact',
                'key' => 'working_hours',
                'value' => 'تواصل معنا لمعرفة مواعيد العمل',
                'type' => 'text',
                'description' => 'مواعيد العمل الرسمية واستقبال الزوار',
            ],
            [
                'group' => 'contact',
                'key' => 'delivery_working_hours',
                'value' => 'تواصل معنا لمعرفة تفاصيل التوصيل',
                'type' => 'text',
                'description' => 'مواعيد حركة أسطول التوصيل للمنازل',
            ],
            [
                'group' => 'contact',
                'key' => 'google_maps_url',
                'value' => 'https://maps.google.com',
                'type' => 'text',
                'description' => 'رابط موقع الفرع الرئيسي على خرائط جوجل',
            ],

            // 3. Payments & InstaPay
            [
                'group' => 'payments',
                'key' => 'enable_cod',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'تفعيل الدفع نقداً عند الاستلام',
            ],
            [
                'group' => 'payments',
                'key' => 'enable_instapay',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'تفعيل الدفع الإلكتروني عبر InstaPay',
            ],
            [
                'group' => 'payments',
                'key' => 'instapay_username',
                'value' => 'horus.demo@instapay',
                'type' => 'text',
                'description' => 'عنوان الدفع اللحظي (InstaPay IPA / Username)',
            ],
            [
                'group' => 'payments',
                'key' => 'instapay_number',
                'value' => '01000000000',
                'type' => 'text',
                'description' => 'رقم هاتف حساب إنستاباي',
            ],
            [
                'group' => 'payments',
                'key' => 'instapay_name',
                'value' => 'مؤسسة حورس للتوريدات والتشطيبات (تجريبي)',
                'type' => 'text',
                'description' => 'اسم صاحب الحساب في تطبيق إنستاباي',
            ],
            [
                'group' => 'payments',
                'key' => 'instapay_instructions',
                'value' => 'بيانات تجريبية للعرض فقط: لا تحوّل أي مبالغ قبل تأكيد رقم الحساب الرسمي من المؤسسة.',
                'type' => 'textarea',
                'description' => 'تعليمات الدفع الموضحة للعميل في صفحة الدفع',
            ],
            [
                'group' => 'payments',
                'key' => 'enable_vodafone_cash',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'تفعيل الدفع عبر محافظ الكاش (فودافون/أورانج/اتصالات)',
            ],
            [
                'group' => 'payments',
                'key' => 'vodafone_cash_number',
                'value' => '01000000000',
                'type' => 'text',
                'description' => 'رقم محفظة فودافون كاش الرسمية',
            ],
            [
                'group' => 'payments',
                'key' => 'vodafone_cash_instructions',
                'value' => 'رقم تجريبي للعرض فقط: لا تحوّل أي مبالغ قبل تأكيد رقم المحفظة الرسمي من المؤسسة.',
                'type' => 'textarea',
                'description' => 'تعليمات الدفع الموضحة للعميل في صفحة الدفع',
            ],

            // 4. Shipping & Delivery
            [
                'group' => 'shipping',
                'key' => 'delivery_fee',
                'value' => '0',
                'type' => 'number',
                'description' => 'رسوم التوصيل الافتراضية (ج.م)',
            ],
            [
                'group' => 'shipping',
                'key' => 'free_delivery_threshold',
                'value' => '0',
                'type' => 'number',
                'description' => 'قيمة الطلب المؤهلة للشحن والتوصيل المجاني (ج.م)',
            ],
            [
                'group' => 'shipping',
                'key' => 'min_order_amount',
                'value' => '0',
                'type' => 'number',
                'description' => 'الحد الأدنى لقيمة الطلب (ج.م)',
            ],
            [
                'group' => 'shipping',
                'key' => 'estimated_delivery_time',
                'value' => 'يتم تأكيد موعد التجهيز والتوصيل عند التواصل',
                'type' => 'text',
                'description' => 'متوسط وقت التوصيل المتوقع',
            ],
            [
                'group' => 'shipping',
                'key' => 'delivery_coverage_areas',
                'value' => 'حدائق الأهرام والمناطق القريبة، يرجى التواصل لتأكيد التغطية',
                'type' => 'text',
                'description' => 'مناطق التغطية بالتوصيل الفوري',
            ],
            [
                'group' => 'shipping',
                'key' => 'allow_store_pickup',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'إتاحة خيار الاستلام المباشر من أحد الفروع مجاناً',
            ],

            // 5. Announcement Bar
            [
                'group' => 'announcements',
                'key' => 'announcement_bar_enabled',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'تفعيل الشريط الإعلاني العلوي في الموقع',
            ],
            [
                'group' => 'announcements',
                'key' => 'announcement_bar_text',
                'value' => 'كل احتياجات التشطيب والديكور في مكان واحد • حدائق الأهرام، بوابة حورس',
                'type' => 'text',
                'description' => 'نص الإعلان أو العرض الترويجي في الشريط العلوي',
            ],
            [
                'group' => 'announcements',
                'key' => 'announcement_bar_link',
                'value' => '',
                'type' => 'text',
                'description' => 'رابط اختياري يفتح عند النقر على الشريط الإعلاني',
            ],

            // 6. Social Media
            [
                'group' => 'social',
                'key' => 'facebook_link',
                'value' => '',
                'type' => 'text',
                'description' => 'رابط صفحة فيسبوك',
            ],
            [
                'group' => 'social',
                'key' => 'instagram_link',
                'value' => '',
                'type' => 'text',
                'description' => 'رابط حساب إنستغرام',
            ],
            [
                'group' => 'social',
                'key' => 'tiktok_link',
                'value' => '',
                'type' => 'text',
                'description' => 'رابط حساب تيك توك',
            ],
            [
                'group' => 'social',
                'key' => 'youtube_link',
                'value' => '',
                'type' => 'text',
                'description' => 'رابط قناة يوتيوب',
            ],

            // 7. SEO & Analytics
            [
                'group' => 'seo',
                'key' => 'meta_title',
                'value' => 'مؤسسة حورس للتوريدات والتشطيبات | حدائق الأهرام',
                'type' => 'text',
                'description' => 'عنوان المتجر الافتراضي في محركات البحث',
            ],
            [
                'group' => 'seo',
                'key' => 'meta_description',
                'value' => 'خامات التشطيب والديكور، دهانات SCIB، أدوات البناء والجبسون بورد. مؤسسة حورس، حدائق الأهرام، بوابة حورس.',
                'type' => 'textarea',
                'description' => 'الوصف التعريفي للمتجر في نتائج محركات البحث',
            ],
            [
                'group' => 'seo',
                'key' => 'meta_keywords',
                'value' => 'حورس للتوريدات والتشطيبات, دهانات SCIB, تشطيب, جبسون بورد, كرانيش فيوتك, دهانات, حدائق الأهرام',
                'type' => 'text',
                'description' => 'الكلمات المفتاحية للموقع',
            ],
            [
                'group' => 'seo',
                'key' => 'og_image',
                'value' => 'assets/site/img/horus-cover.svg',
                'type' => 'image',
                'description' => 'صورة المشاركة على شبكات التواصل (OG Image)',
            ],
            [
                'group' => 'seo',
                'key' => 'google_analytics_id',
                'value' => '',
                'type' => 'text',
                'description' => 'معرف Google Analytics (مثل G-XXXXXXXXXX)',
            ],
            [
                'group' => 'seo',
                'key' => 'facebook_pixel_id',
                'value' => '',
                'type' => 'text',
                'description' => 'معرف Facebook Pixel للحملات الإعلانية',
            ],

            // 8. Content & Policies
            [
                'group' => 'policies',
                'key' => 'about_us_content',
                'value' => 'مؤسسة حورس للتوريدات والتشطيبات. نوفر خامات الديكور والتشطيب، ودهانات SCIB، وأدوات ومستلزمات البناء. مقرنا ٢٢ع حدائق الأهرام، بوابة حورس. خبرة أكثر من 10 سنوات.',
                'type' => 'textarea',
                'description' => 'نبذة تعريفية عن مؤسسة حورس للتوريدات والتشطيبات',
            ],
            [
                'group' => 'policies',
                'key' => 'about_us_short',
                'value' => 'نوفر خامات التشطيب والديكور، دهانات SCIB، أدوات ومستلزمات البناء، وحلولاً عملية من التأسيس حتى اللمسة الأخيرة.',
                'type' => 'textarea',
                'description' => 'نبذة مختصرة لفوتر الموقع',
            ],
            [
                'group' => 'policies',
                'key' => 'terms_content',
                'value' => '',
                'type' => 'textarea',
                'description' => 'محتوى مخصص لصفحة الشروط والأحكام (فارغ = استخدام النص القياسي)',
            ],
            [
                'group' => 'policies',
                'key' => 'privacy_content',
                'value' => '',
                'type' => 'textarea',
                'description' => 'محتوى مخصص لصفحة سياسة الخصوصية (فارغ = استخدام النص القياسي)',
            ],
            [
                'group' => 'policies',
                'key' => 'return_policy_content',
                'value' => '',
                'type' => 'textarea',
                'description' => 'محتوى مخصص لصفحة سياسة الاسترجاع (فارغ = استخدام النص القياسي)',
            ],

            // 9. Features & System
            [
                'group' => 'features',
                'key' => 'reviews_enabled',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'تفعيل تقييمات وآراء العملاء على المنتجات',
            ],
            [
                'group' => 'features',
                'key' => 'wishlist_enabled',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'تفعيل ميزة قائمة المفضلة للعملاء',
            ],
            [
                'group' => 'features',
                'key' => 'whatsapp_floating_button',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'إظهار زر واتساب العائم في أسفل الشاشة',
            ],
            [
                'group' => 'features',
                'key' => 'maintenance_mode',
                'value' => '0',
                'type' => 'boolean',
                'description' => 'تفعيل وضع الصيانة أو إغلاق المتجر المؤقت',
            ],
            [
                'group' => 'features',
                'key' => 'maintenance_message',
                'value' => 'نعتذر منكم، الموقع يخضع لصيانة وتحديثات دورية وسيعود للعمل خلال دقائق.',
                'type' => 'textarea',
                'description' => 'رسالة وضع الصيانة المعروضة للزوار',
            ],
        ];

        foreach ($settings as $setting) {
            if (
                ($setting['group'] ?? null) === 'payments'
                && in_array($setting['key'] ?? '', ['instapay_username', 'instapay_number', 'instapay_name', 'instapay_instructions', 'vodafone_cash_number', 'vodafone_cash_instructions'], true)
                && Setting::where('key', $setting['key'])->whereNotNull('value')->where('value', '<>', '')->exists()
            ) {
                continue;
            }

            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
