<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('products') || !Schema::hasTable('categories')) {
            return;
        }

        $catalog = [
            ['scib-interior-paint', 'scib-paints', 'دهان سكيب ديكورا هيرو داخلي', 'دهان ديكورا هيرو من سكيب للأعمال الداخلية. اسأل عن درجة اللون وحجم العبوة والتوافر.', ['سكيب', 'SCIB', 'ديكورا هيرو', 'دهان داخلي'], 'assets/site/img/horus-gallery/scib-paints.jpg', true],
            ['scib-exterior-paint', 'scib-paints', 'دهانات سكيب للواجهات الخارجية', 'اختيارات دهانات سكيب الخارجية. تواصل معنا لتحديد النظام المناسب للواجهة والكمية.', ['سكيب', 'SCIB', 'دهانات خارجية', 'واجهات'], 'assets/site/img/horus-gallery/paint-store.jpg', false],
            ['scib-wall-sealer', 'scib-paints', 'سيلر حوائط SCIB', 'سيلر حوائط ضمن تجهيز السطح قبل الدهان. اسأل عن النوع والعبوة المناسبة.', ['سيلر', 'SCIB', 'تجهيز الحوائط'], 'assets/site/img/horus-gallery/finishing-materials.jpg', false],
            ['ready-mix-putty', 'finishing-materials', 'معجون جاهز للتشطيب', 'معجون جاهز لأعمال تجهيز الحوائط. اسأل عن العلامة التجارية وحجم العبوة والتوافر.', ['معجون جاهز', 'معجون حوائط', 'تشطيب'], 'assets/site/img/horus-gallery/finishing-materials.jpg', false],
            ['gypsum-joint-compound', 'gypsum-board', 'معجون فواصل الجبسون بورد', 'مستلزمات معالجة فواصل الجبسون بورد. اسأل عن الأنواع والمقاسات المتاحة.', ['جبسون بورد', 'معجون فواصل', 'تشطيب'], 'assets/site/img/horus-gallery/horus-scib.jpg', false],
            ['painting-hand-tools', 'construction-tools', 'عدد يدوية لأعمال النقاشة', 'تشكيلة عدد يدوية للنقاشة والتشطيب. أرسل قائمة الأدوات المطلوبة لمعرفة المتوفر.', ['عدد يدوية', 'نقاشة', 'أدوات تشطيب'], 'assets/site/img/horus-gallery/tools-finishing.jpg', false],
        ];

        foreach ($catalog as $index => [$slug, $categorySlug, $name, $description, $keywords, $image, $featured]) {
            $category = DB::table('categories')->where('slug', $categorySlug)->first();
            if (!$category) {
                continue;
            }

            $existing = DB::table('products')->where('slug', $slug)->first();
            if ($existing) {
                if (Schema::hasColumn('products', 'image') && empty($existing->image)) {
                    DB::table('products')->where('id', $existing->id)->update(['image' => $image, 'updated_at' => now()]);
                }
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
            if (Schema::hasColumn('products', 'image')) $product['image'] = $image;
            if (Schema::hasColumn('products', 'quantity')) $product['quantity'] = 0;
            if (Schema::hasColumn('products', 'stock_quantity')) $product['stock_quantity'] = 0;
            if (Schema::hasColumn('products', 'discount_price')) $product['discount_price'] = null;
            if (Schema::hasColumn('products', 'featured')) $product['featured'] = $featured;
            if (Schema::hasColumn('products', 'is_weighted')) $product['is_weighted'] = false;
            if (Schema::hasColumn('products', 'unit_type')) $product['unit_type'] = 'piece';
            if (Schema::hasColumn('products', 'is_active')) $product['is_active'] = true;
            if (Schema::hasColumn('products', 'is_available')) $product['is_available'] = false;
            if (Schema::hasColumn('products', 'sort_order')) $product['sort_order'] = 20 + $index;

            DB::table('products')->insert($product);
        }
    }

    public function down(): void
    {
        // Keep catalog records to avoid deleting products after rollback.
    }
};
