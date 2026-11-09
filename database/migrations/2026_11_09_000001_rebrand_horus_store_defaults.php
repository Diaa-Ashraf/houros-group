<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('settings') || !Schema::hasColumn('settings', 'group')) {
            return;
        }

        $replacements = [
            'site_name' => ['ألبان وسوبرماركت التوحيد', 'مؤسسة حورس للتوريدات والتشطيبات'],
            'site_tagline' => ['أصالة الطعم البلدي وجودة السوبرماركت في العامرية', 'حلول متكاملة للتشطيب والديكور'],
            'site_logo' => ['assets/site/img/logo.png', 'assets/site/img/horus-symbol.svg'],
            'site_favicon' => ['assets/site/img/logo.png', 'assets/site/img/horus-symbol.svg'],
            'store_phone' => ['01000000000', '01002527072'],
            'store_phone_secondary' => ['034400000', ''],
            'store_whatsapp' => ['201000000000', '201002527072'],
            'store_email' => ['info@altawheed-dairy.com', ''],
            'address' => ['شارع المستشفى - بجوار الإسعاف، العامرية، الإسكندرية', '٢٢ع حدائق الأهرام، بوابة حورس'],
            'working_hours' => ['يومياً: من 8:00 صباحاً حتى 2:00 بعد منتصف الليل', 'تواصل معنا لمعرفة مواعيد العمل'],
            'instapay_username' => ['altawheed@instapay', ''],
            'instapay_number' => ['01000000000', ''],
            'instapay_name' => ['ألبان وسوبرماركت التوحيد', ''],
            'instapay_instructions' => ['يرجى تحويل القيمة عبر تطبيق InstaPay ثم إرفاق صورة إشعار التحويل لتأكيد تجهيز الطلب فوراً.', 'حوّل قيمة الطلب إلى حساب المؤسسة، ثم أرفق صورة الإيصال لمراجعة التحويل.'],
            'vodafone_cash_number' => ['01000000000', ''],
            'delivery_coverage_areas' => ['العامرية، كينج مريوط، شارع الإسعاف، النهضة، عبد القادر، وقرى مريوط', 'حدائق الأهرام والمناطق القريبة، يرجى التواصل لتأكيد التغطية'],
            'delivery_fee' => ['15', '0'],
            'free_delivery_threshold' => ['300', '0'],
            'min_order_amount' => ['50', '0'],
            'estimated_delivery_time' => ['30 - 45 دقيقة', 'يتم تأكيد موعد التجهيز والتوصيل عند التواصل'],
            'announcement_bar_text' => ['التوصيل السريع متاح لجميع مناطق العامرية وقرى مريوط والإسكندرية', 'كل احتياجات التشطيب والديكور في مكان واحد • حدائق الأهرام، بوابة حورس'],
            'facebook_link' => ['https://facebook.com/altawheed.dairy', ''],
            'instagram_link' => ['https://instagram.com/altawheed.dairy', ''],
            'tiktok_link' => ['https://tiktok.com/@altawheed.dairy', ''],
            'meta_title' => ['ألبان وسوبرماركت التوحيد | العامرية - الإسكندرية', 'مؤسسة حورس للتوريدات والتشطيبات | حدائق الأهرام'],
            'meta_description' => ['ألبان وسوبرماركت التوحيد في العامرية، الإسكندرية. أجود منتجات الألبان والأجبان الطبيعية والسلع الغذائية وخدمة توصيل فورية.', 'خامات التشطيب والديكور، دهانات SCIB، أدوات البناء والجبسون بورد. مؤسسة حورس، حدائق الأهرام، بوابة حورس.'],
            'meta_keywords' => ['ألبان التوحيد, سوبرماركت التوحيد, جبن بلدي, ألبان العامرية, توصيل طلبات العامرية, سوبرماركت الإسكندرية, كينج مريوط', 'حورس للتوريدات والتشطيبات, دهانات SCIB, تشطيب, جبسون بورد, كرانيش فيوتك, دهانات, حدائق الأهرام'],
            'og_image' => ['assets/site/img/og-cover.png', 'assets/site/img/horus-cover.svg'],
            'about_us_content' => ['ألبان وسوبرماركت التوحيد، الاسم الرائد في تقديم أجود منتجات الألبان البلدية الطبيعية والجبن الطازج والسلع الغذائية في العامرية والإسكندرية عبر 4 فروع وخدمة توصيل فورية.', 'مؤسسة حورس للتوريدات والتشطيبات. نوفر خامات الديكور والتشطيب، ودهانات SCIB، وأدوات ومستلزمات البناء. مقرنا ٢٢ع حدائق الأهرام، بوابة حورس. خبرة أكثر من 10 سنوات.'],
            'about_us_short' => ['من أكبر مراكز الألبان والسوبرماركت في منطقة العامرية والإسكندرية. نوفر أجود منتجات الألبان الطازجة، الأجبان الطبيعية، والسلع الغذائية بأسعار الجملة والتجزئة وخدمة توصيل سريعة حتى باب بيتك.', 'نوفر خامات التشطيب والديكور، دهانات SCIB، أدوات ومستلزمات البناء، وحلولاً عملية من التأسيس حتى اللمسة الأخيرة.'],
        ];

        foreach ($replacements as $key => [$old, $new]) {
            DB::table('settings')->where('key', $key)->where('value', $old)->update(['value' => $new]);
        }

        DB::table('settings')->where('key', 'delivery_fee')->where('value', '20')->update(['value' => '0']);

        DB::table('settings')->updateOrInsert(
            ['key' => 'enable_vodafone_cash'],
            ['group' => 'payments', 'value' => '1', 'type' => 'boolean', 'description' => 'تفعيل الدفع عبر فودافون كاش', 'updated_at' => now(), 'created_at' => now()]
        );
        DB::table('settings')->updateOrInsert(
            ['key' => 'vodafone_cash_number'],
            ['group' => 'payments', 'value' => '', 'type' => 'text', 'description' => 'رقم محفظة فودافون كاش', 'updated_at' => now(), 'created_at' => now()]
        );
        DB::table('settings')->where('key', 'instapay_instructions')->update([
            'value' => 'يرجى التواصل معنا لتأكيد بيانات حساب إنستاباي قبل تحويل قيمة الطلب.',
        ]);

        $horusCategories = [
            ['slug' => 'scib-paints', 'name' => 'دهانات SCIB', 'description' => 'دهانات داخلية وخارجية ومنتجات سكيب.'],
            ['slug' => 'gypsum-board', 'name' => 'جبسون بورد وأسقف معلقة', 'description' => 'خامات ومستلزمات أنظمة الجبسون بورد.'],
            ['slug' => 'fyutec-cornices', 'name' => 'كرانيش فيوتك', 'description' => 'كرانيش وقطع ديكور بتصميمات متنوعة.'],
            ['slug' => 'wall-panels', 'name' => 'بديل الخشب وورق الحائط', 'description' => 'حلول ديكور عصرية للجدران والمساحات الداخلية.'],
            ['slug' => 'construction-tools', 'name' => 'أدوات ومستلزمات البناء', 'description' => 'عدد ومستلزمات لأعمال البناء والتشطيب.'],
            ['slug' => 'finishing-materials', 'name' => 'مواد التشطيب', 'description' => 'خامات ومستلزمات مراحل التشطيب المختلفة.'],
            ['slug' => 'doors', 'name' => 'أبواب مصفحة', 'description' => 'أبواب مصفحة وخيارات تناسب احتياجات المنزل.'],
            ['slug' => 'decor', 'name' => 'أعمال الديكور والتشطيب', 'description' => 'توريد وتنفيذ حلول الديكور والتشطيبات.'],
        ];

        if (Schema::hasTable('categories') && Schema::hasColumn('categories', 'name') && Schema::hasColumn('categories', 'description')) {
            foreach ($horusCategories as $category) {
                DB::table('categories')->updateOrInsert(
                    ['slug' => $category['slug']],
                    array_merge($category, ['image' => '', 'updated_at' => now(), 'created_at' => now()])
                );
            }

            if (Schema::hasTable('products') && Schema::hasColumn('products', 'is_active') && Schema::hasColumn('products', 'category_id')) {
                $horusSlugs = array_column($horusCategories, 'slug');
                $legacyCategoryIds = DB::table('categories')->whereNotIn('slug', $horusSlugs)->pluck('id');
                DB::table('products')->whereIn('category_id', $legacyCategoryIds)->update(['is_active' => false]);
                DB::table('products')->whereNull('category_id')->update(['is_active' => false]);
            }
        }

        foreach ([['deals', 'is_active'], ['bundles', 'is_active'], ['magazines', 'is_active'], ['banners', 'status'] ] as [$table, $column]) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, $column)) {
                // The legacy storefront content is tied to the former business and must not appear in Horus.
                DB::table($table)->update([$column => false]);
            }
        }

        if (Schema::hasTable('branches') && Schema::hasColumn('branches', 'is_active')) {
            DB::table('branches')->where('is_active', true)->update(['is_active' => false]);
            DB::table('branches')->updateOrInsert(
                ['slug' => 'horus-gardens'],
                [
                    'name' => 'مؤسسة حورس - حدائق الأهرام',
                    'address' => '٢٢ع حدائق الأهرام، بوابة حورس',
                    'phone' => '01002527072',
                    'whatsapp' => '201002527072',
                    'working_hours' => null,
                    'is_active' => true,
                    'sort_order' => 1,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }

    public function down(): void
    {
        // Branding settings are intentionally not rolled back to the previous business.
    }
};
