<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('settings') || !Schema::hasColumn('settings', 'key') || !Schema::hasColumn('settings', 'value')) {
            return;
        }

        $replacements = [
            'site_name' => ['أزياء لافيستا', 'مؤسسة حورس للتوريدات والتشطيبات'],
            'site_tagline' => ['LAVISTA FASHION — أرقى الملابس والأزياء العصرية', 'حلول متكاملة للتشطيب والديكور'],
            'address' => ['فروعنا: الإسكندرية (العامرية) • القاهرة • طنطا', '٢٢ع حدائق الأهرام، بوابة حورس'],
            'working_hours' => ['يومياً: من 10:00 صباحاً حتى 11:00 مساءً', 'تواصل معنا لمعرفة مواعيد العمل'],
            'announcement_bar_text' => ['شحن سريع لكافة محافظات مصر • معاينة وقياس قبل الدفع • استبدال مجاني 14 يوماً', 'كل احتياجات التشطيب والديكور في مكان واحد • حدائق الأهرام، بوابة حورس'],
            'about_us_content' => ['أزياء لافيستا، الاسم الرائد في تقديم أجود الملابس الجاهزة والأزياء العصرية والفساتين والتنسيقات الفاخرة لكافة أفراد الأسرة في مصر، مع خدمة معاينة وقياس وتوصيل سريع حتى باب بيتك.', 'مؤسسة حورس للتوريدات والتشطيبات. نوفر خامات الديكور والتشطيب، ودهانات SCIB، وأدوات ومستلزمات البناء. مقرنا ٢٢ع حدائق الأهرام، بوابة حورس. خبرة أكثر من 10 سنوات.'],
            'about_us_short' => ['بوتيك أزياء مصري رائد في عالم الموضة والملابس الراقية، نوفر أجود الخامات والتصاميم العصرية مع شحن سريع وحق المعاينة والقياس قبل الاستلام.', 'نوفر خامات التشطيب والديكور، دهانات SCIB، أدوات ومستلزمات البناء، وحلولاً عملية من التأسيس حتى اللمسة الأخيرة.'],
            'meta_title' => ['أزياء لافيستا | أرقى الملابس والتنسيقات في مصر', 'مؤسسة حورس للتوريدات والتشطيبات | حدائق الأهرام'],
            'meta_description' => ['أزياء لافيستا — وجهتك الأولى لأرقى الملابس الجاهزة النسائية والرجالية والأطفال مع ضمان المعاينة والقياس قبل الاستلام.', 'خامات التشطيب والديكور، دهانات SCIB، أدوات البناء والجبسون بورد. مؤسسة حورس، حدائق الأهرام، بوابة حورس.'],
            'meta_keywords' => ['أزياء لافيستا, بوتيك لافيستا, ملابس راقية, فساتين, بدلات, أزياء كاجوال, مقاسات خاصة, توصيل ملابس مصر', 'حورس للتوريدات والتشطيبات, دهانات SCIB, تشطيب, جبسون بورد, كرانيش فيوتك, دهانات, حدائق الأهرام'],
            'facebook_link' => ['https://facebook.com/lavista.fashion', ''],
            'instagram_link' => ['https://instagram.com/lavista.fashion', ''],
            'tiktok_link' => ['https://tiktok.com/@lavista.fashion', ''],
        ];

        foreach ($replacements as $key => [$old, $new]) {
            DB::table('settings')->where('key', $key)->where('value', $old)->update(['value' => $new]);
        }

        foreach (['site_logo', 'site_favicon'] as $key) {
            DB::table('settings')->where('key', $key)->whereNull('value')->update(['value' => 'assets/site/img/horus-symbol.svg']);
        }

        // Remove payment destinations from the previous store; Horus can enter verified accounts in settings.
        foreach (['instapay_username', 'instapay_number', 'instapay_name', 'vodafone_cash_number'] as $key) {
            DB::table('settings')->where('key', $key)->update(['value' => '']);
        }
        DB::table('settings')->where('key', 'instapay_instructions')->update([
            'value' => 'تواصل معنا لتأكيد بيانات حساب إنستاباي قبل تحويل قيمة الطلب.',
        ]);
        DB::table('settings')->where('key', 'store_phone')->update(['value' => '01002527072']);
        DB::table('settings')->where('key', 'store_phone_secondary')->update(['value' => '']);
        DB::table('settings')->where('key', 'store_whatsapp')->update(['value' => '201002527072']);
        DB::table('settings')->where('key', 'store_email')->update(['value' => '']);
    }

    public function down(): void
    {
        // Legacy fashion branding and payment destinations are not restored.
    }
};
