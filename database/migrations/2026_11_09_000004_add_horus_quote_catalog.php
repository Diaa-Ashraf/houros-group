<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('products') || !Schema::hasColumn('products', 'price') || !Schema::hasColumn('products', 'category_id')) {
            return;
        }

        $catalog = [
            ['scib-interior-exterior-paints', 'scib-paints', 'دهانات SCIB الداخلية والخارجية', 'اسأل عن أنواع دهانات سكيب المناسبة لمشروعك ومقاسات العبوات المتاحة.', ['سكيب', 'SCIB', 'دهانات', 'داخلي', 'خارجي'], true],
            ['computer-color-mixing', 'scib-paints', 'تجهيز ألوان الدهانات بالكمبيوتر', 'اختار الدرجة التي تناسب المكان واسألنا عن تجهيزها وتكلفتها.', ['ألوان', 'دهانات كمبيوتر', 'تلوين'], true],
            ['scib-interior-paint', 'scib-paints', 'دهان سكيب ديكورا هيرو داخلي', 'دهان ديكورا هيرو من سكيب للأعمال الداخلية. اسأل عن درجة اللون وحجم العبوة والتوافر.', ['سكيب', 'SCIB', 'ديكورا هيرو', 'دهان داخلي'], true],
            ['scib-exterior-paint', 'scib-paints', 'دهانات سكيب للواجهات الخارجية', 'اختيارات دهانات سكيب الخارجية. تواصل معنا لتحديد النظام المناسب للواجهة والكمية.', ['سكيب', 'SCIB', 'دهانات خارجية', 'واجهات'], false],
            ['scib-wall-sealer', 'scib-paints', 'سيلر حوائط SCIB', 'سيلر حوائط ضمن تجهيز السطح قبل الدهان. اسأل عن النوع والعبوة المناسبة.', ['سيلر', 'SCIB', 'تجهيز الحوائط'], false],
            ['gypsum-board-systems', 'gypsum-board', 'أنظمة الجبسون بورد والأسقف المعلقة', 'خامات ومستلزمات الجبسون بورد. تواصل معنا لتحديد الكميات والمواصفات المناسبة.', ['جبسون بورد', 'أسقف معلقة', 'ديكور'], true],
            ['fyutec-cornices', 'fyutec-cornices', 'كرانيش فيوتك', 'تشكيلة كرانيش فيوتك بتصميمات ومقاسات متنوعة. اسأل عن المتاح والسعر.', ['فيوتك', 'كرانيش', 'ديكور'], true],
            ['three-dimensional-wallpaper', 'wall-panels', 'ورق حائط ثلاثي الأبعاد', 'اختيارات ورق حائط بتصميمات عصرية. أرسل لنا مقاس الحائط للاستفسار عن التوافر والتكلفة.', ['ورق حائط', 'ثلاثي الأبعاد', 'ديكور'], false],
            ['wood-alternative-panels', 'wall-panels', 'بديل الخشب', 'ألواح بديل الخشب لإضافة لمسة ديكورية للمساحات الداخلية. اسأل عن المقاسات المتاحة.', ['بديل الخشب', 'ألواح', 'ديكور'], false],
            ['armored-doors', 'doors', 'أبواب مصفحة', 'اسأل عن الموديلات والمقاسات المتاحة وتكلفة التوريد والتركيب.', ['أبواب', 'أبواب مصفحة'], false],
            ['painting-and-finishing-tools', 'construction-tools', 'أدوات النقاشة والتشطيب', 'أدوات ومستلزمات لأعمال النقاشة والتشطيب. أرسل احتياجاتك لمعرفة المتاح.', ['أدوات', 'نقاشة', 'تشطيب'], false],
            ['interior-exterior-putty', 'finishing-materials', 'معجون داخلي وخارجي', 'اسأل عن أنواع المعجون المناسبة لطبيعة السطح والاستخدام.', ['معجون', 'داخلي', 'خارجي', 'تشطيب'], false],
            ['ready-mix-putty', 'finishing-materials', 'معجون جاهز للتشطيب', 'معجون جاهز لأعمال تجهيز الحوائط. اسأل عن العلامة التجارية وحجم العبوة والتوافر.', ['معجون جاهز', 'معجون حوائط', 'تشطيب'], false],
            ['gypsum-joint-compound', 'gypsum-board', 'معجون فواصل الجبسون بورد', 'مستلزمات معالجة فواصل الجبسون بورد. اسأل عن الأنواع والمقاسات المتاحة.', ['جبسون بورد', 'معجون فواصل', 'تشطيب'], false],
            ['painting-hand-tools', 'construction-tools', 'عدد يدوية لأعمال النقاشة', 'تشكيلة عدد يدوية للنقاشة والتشطيب. أرسل قائمة الأدوات المطلوبة لمعرفة المتوفر.', ['عدد يدوية', 'نقاشة', 'أدوات تشطيب'], false],
            ['fasteners-and-adhesives', 'construction-tools', 'مسامير ومواد لاصقة ومستلزمات تركيب', 'مستلزمات تركيب بأحجام واستخدامات مختلفة. تواصل معنا لتحديد احتياجك.', ['مسامير', 'مواد لاصقة', 'تركيب'], false],
        ];

        foreach ($catalog as $index => [$slug, $categorySlug, $name, $description, $keywords, $featured]) {
            $category = DB::table('categories')->where('slug', $categorySlug)->first();
            if (!$category || DB::table('products')->where('slug', $slug)->exists()) {
                continue;
            }

            $product = [
                'slug' => $slug,
                'name' => $name,
                'description' => $description,
                'category_id' => $category->id,
                'price' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if (Schema::hasColumn('products', 'keywords')) $product['keywords'] = json_encode($keywords, JSON_UNESCAPED_UNICODE);
            if (Schema::hasColumn('products', 'image')) $product['image'] = null;
            $image = match ($slug) {
                'scib-interior-paint', 'scib-wall-sealer', 'ready-mix-putty' => 'assets/site/img/horus-gallery/scib-paints.jpg',
                'scib-exterior-paint' => 'assets/site/img/horus-gallery/paint-store.jpg',
                'gypsum-joint-compound' => 'assets/site/img/horus-gallery/horus-scib.jpg',
                'painting-hand-tools' => 'assets/site/img/horus-gallery/tools-finishing.jpg',
                default => null,
            };
            if ($image && Schema::hasColumn('products', 'image')) $product['image'] = $image;
            if (Schema::hasColumn('products', 'quantity')) $product['quantity'] = 0;
            if (Schema::hasColumn('products', 'stock_quantity')) $product['stock_quantity'] = 0;
            if (Schema::hasColumn('products', 'discount_price')) $product['discount_price'] = null;
            if (Schema::hasColumn('products', 'featured')) $product['featured'] = $featured;
            if (Schema::hasColumn('products', 'is_weighted')) $product['is_weighted'] = false;
            if (Schema::hasColumn('products', 'unit_type')) $product['unit_type'] = 'piece';
            if (Schema::hasColumn('products', 'is_active')) $product['is_active'] = true;
            if (Schema::hasColumn('products', 'is_available')) $product['is_available'] = false;
            if (Schema::hasColumn('products', 'sort_order')) $product['sort_order'] = $index + 1;

            DB::table('products')->insert($product);
        }
    }

    public function down(): void
    {
        // The inquiry catalog is retained if this migration is rolled back.
    }
};
