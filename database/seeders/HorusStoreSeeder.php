<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class HorusStoreSeeder extends Seeder
{
    /** Seed the categories and location confirmed for Horus Group. */
    public function run(): void
    {
        $categories = [
            ['slug' => 'scib-paints', 'name' => 'دهانات SCIB', 'description' => 'دهانات داخلية وخارجية ومنتجات سكيب.'],
            ['slug' => 'gypsum-board', 'name' => 'جبسون بورد وأسقف معلقة', 'description' => 'خامات ومستلزمات أنظمة الجبسون بورد.'],
            ['slug' => 'fyutec-cornices', 'name' => 'كرانيش فيوتك', 'description' => 'كرانيش وقطع ديكور بتصميمات متنوعة.'],
            ['slug' => 'wall-panels', 'name' => 'بديل الخشب وورق الحائط', 'description' => 'حلول ديكور عصرية للجدران والمساحات الداخلية.'],
            ['slug' => 'construction-tools', 'name' => 'أدوات ومستلزمات البناء', 'description' => 'عدد ومستلزمات لأعمال البناء والتشطيب.'],
            ['slug' => 'finishing-materials', 'name' => 'مواد التشطيب', 'description' => 'خامات ومستلزمات مراحل التشطيب المختلفة.'],
            ['slug' => 'doors', 'name' => 'أبواب مصفحة', 'description' => 'أبواب مصفحة وخيارات تناسب احتياجات المنزل.'],
            ['slug' => 'decor', 'name' => 'أعمال الديكور والتشطيب', 'description' => 'توريد وتنفيذ حلول الديكور والتشطيبات.'],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['slug' => $category['slug']],
                ['name' => $category['name'], 'description' => $category['description'], 'image' => '']
            );
        }

        $catalog = [
            ['slug' => 'scib-interior-exterior-paints', 'category' => 'scib-paints', 'name' => 'دهانات SCIB الداخلية والخارجية', 'description' => 'اسأل عن أنواع دهانات سكيب المناسبة لمشروعك ومقاسات العبوات المتاحة.', 'keywords' => ['سكيب', 'SCIB', 'دهانات', 'داخلي', 'خارجي'], 'image' => 'assets/site/img/horus-gallery/scib-paints.jpg', 'featured' => true],
            ['slug' => 'computer-color-mixing', 'category' => 'scib-paints', 'name' => 'تجهيز ألوان الدهانات بالكمبيوتر', 'description' => 'اختار الدرجة التي تناسب المكان واسألنا عن تجهيزها وتكلفتها.', 'keywords' => ['ألوان', 'دهانات كمبيوتر', 'تلوين'], 'image' => 'assets/site/img/horus-gallery/scib-paints.jpg', 'featured' => true],
            ['slug' => 'scib-interior-paint', 'category' => 'scib-paints', 'name' => 'دهان سكيب ديكورا هيرو داخلي', 'description' => 'دهان ديكورا هيرو من سكيب للأعمال الداخلية. اسأل عن درجة اللون وحجم العبوة والتوافر.', 'keywords' => ['سكيب', 'SCIB', 'ديكورا هيرو', 'دهان داخلي'], 'image' => 'assets/site/img/horus-gallery/scib-paints.jpg', 'featured' => true],
            ['slug' => 'scib-exterior-paint', 'category' => 'scib-paints', 'name' => 'دهانات سكيب للواجهات الخارجية', 'description' => 'اختيارات دهانات سكيب الخارجية. تواصل معنا لتحديد النظام المناسب للواجهة والكمية.', 'keywords' => ['سكيب', 'SCIB', 'دهانات خارجية', 'واجهات'], 'image' => 'assets/site/img/horus-gallery/paint-store.jpg', 'featured' => false],
            ['slug' => 'scib-wall-sealer', 'category' => 'scib-paints', 'name' => 'سيلر حوائط SCIB', 'description' => 'سيلر حوائط ضمن تجهيز السطح قبل الدهان. اسأل عن النوع والعبوة المناسبة.', 'keywords' => ['سيلر', 'SCIB', 'تجهيز الحوائط'], 'image' => 'assets/site/img/horus-gallery/finishing-materials.jpg', 'featured' => false],
            ['slug' => 'gypsum-board-systems', 'category' => 'gypsum-board', 'name' => 'أنظمة الجبسون بورد والأسقف المعلقة', 'description' => 'خامات ومستلزمات الجبسون بورد. تواصل معنا لتحديد الكميات والمواصفات المناسبة.', 'keywords' => ['جبسون بورد', 'أسقف معلقة', 'ديكور'], 'image' => 'assets/site/img/horus-gallery/horus-scib.jpg', 'featured' => true],
            ['slug' => 'fyutec-cornices', 'category' => 'fyutec-cornices', 'name' => 'كرانيش فيوتك', 'description' => 'تشكيلة كرانيش فيوتك بتصميمات ومقاسات متنوعة. اسأل عن المتاح والسعر.', 'keywords' => ['فيوتك', 'كرانيش', 'ديكور'], 'featured' => true],
            ['slug' => 'three-dimensional-wallpaper', 'category' => 'wall-panels', 'name' => 'ورق حائط ثلاثي الأبعاد', 'description' => 'اختيارات ورق حائط بتصميمات عصرية. أرسل لنا مقاس الحائط للاستفسار عن التوافر والتكلفة.', 'keywords' => ['ورق حائط', 'ثلاثي الأبعاد', 'ديكور'], 'featured' => false],
            ['slug' => 'wood-alternative-panels', 'category' => 'wall-panels', 'name' => 'بديل الخشب', 'description' => 'ألواح بديل الخشب لإضافة لمسة ديكورية للمساحات الداخلية. اسأل عن المقاسات المتاحة.', 'keywords' => ['بديل الخشب', 'ألواح', 'ديكور'], 'featured' => false],
            ['slug' => 'armored-doors', 'category' => 'doors', 'name' => 'أبواب مصفحة', 'description' => 'اسأل عن الموديلات والمقاسات المتاحة وتكلفة التوريد والتركيب.', 'keywords' => ['أبواب', 'أبواب مصفحة'], 'featured' => false],
            ['slug' => 'painting-and-finishing-tools', 'category' => 'construction-tools', 'name' => 'أدوات النقاشة والتشطيب', 'description' => 'أدوات ومستلزمات لأعمال النقاشة والتشطيب. أرسل احتياجاتك لمعرفة المتاح.', 'keywords' => ['أدوات', 'نقاشة', 'تشطيب'], 'image' => 'assets/site/img/horus-gallery/tools-finishing.jpg', 'featured' => false],
            ['slug' => 'interior-exterior-putty', 'category' => 'finishing-materials', 'name' => 'معجون داخلي وخارجي', 'description' => 'اسأل عن أنواع المعجون المناسبة لطبيعة السطح والاستخدام.', 'keywords' => ['معجون', 'داخلي', 'خارجي', 'تشطيب'], 'image' => 'assets/site/img/horus-gallery/finishing-materials.jpg', 'featured' => false],
            ['slug' => 'ready-mix-putty', 'category' => 'finishing-materials', 'name' => 'معجون جاهز للتشطيب', 'description' => 'معجون جاهز لأعمال تجهيز الحوائط. اسأل عن العلامة التجارية وحجم العبوة والتوافر.', 'keywords' => ['معجون جاهز', 'معجون حوائط', 'تشطيب'], 'image' => 'assets/site/img/horus-gallery/finishing-materials.jpg', 'featured' => false],
            ['slug' => 'gypsum-joint-compound', 'category' => 'gypsum-board', 'name' => 'معجون فواصل الجبسون بورد', 'description' => 'مستلزمات معالجة فواصل الجبسون بورد. اسأل عن الأنواع والمقاسات المتاحة.', 'keywords' => ['جبسون بورد', 'معجون فواصل', 'تشطيب'], 'image' => 'assets/site/img/horus-gallery/horus-scib.jpg', 'featured' => false],
            ['slug' => 'painting-hand-tools', 'category' => 'construction-tools', 'name' => 'عدد يدوية لأعمال النقاشة', 'description' => 'تشكيلة عدد يدوية للنقاشة والتشطيب. أرسل قائمة الأدوات المطلوبة لمعرفة المتوفر.', 'keywords' => ['عدد يدوية', 'نقاشة', 'أدوات تشطيب'], 'image' => 'assets/site/img/horus-gallery/tools-finishing.jpg', 'featured' => false],
            ['slug' => 'fasteners-and-adhesives', 'category' => 'construction-tools', 'name' => 'مسامير ومواد لاصقة ومستلزمات تركيب', 'description' => 'مستلزمات تركيب بأحجام واستخدامات مختلفة. تواصل معنا لتحديد احتياجك.', 'keywords' => ['مسامير', 'مواد لاصقة', 'تركيب'], 'image' => 'assets/site/img/horus-gallery/tools-finishing.jpg', 'featured' => false],
        ];

        $fallbackImages = [
            'fyutec-cornices' => 'assets/site/img/horus-gallery/horus-scib.jpg',
            'three-dimensional-wallpaper' => 'assets/site/img/horus-gallery/horus-scib.jpg',
            'wood-alternative-panels' => 'assets/site/img/horus-gallery/horus-scib.jpg',
            'armored-doors' => 'assets/site/img/horus-gallery/paint-store.jpg',
        ];

        foreach ($catalog as $index => $item) {
            $category = Category::where('slug', $item['category'])->first();
            if (!$category) {
                continue;
            }

            $product = Product::firstOrCreate(
                ['slug' => $item['slug']],
                [
                    'name' => $item['name'],
                    'description' => $item['description'],
                    'keywords' => $item['keywords'],
                    'category_id' => $category->id,
                    'image' => $item['image'] ?? ($fallbackImages[$item['slug']] ?? null),
                    'quantity' => 0,
                    'stock_quantity' => 0,
                    'price' => 0,
                    'discount_price' => null,
                    'featured' => $item['featured'],
                    'is_weighted' => false,
                    'unit_type' => 'piece',
                    'is_active' => true,
                    'is_available' => false,
                    'sort_order' => $index + 1,
                ]
            );

            $image = $item['image'] ?? ($fallbackImages[$item['slug']] ?? null);
            if (!$product->image && $image) {
                $product->update(['image' => $image]);
            }
        }

        Branch::updateOrCreate(
            ['slug' => 'horus-gardens'],
            [
                'name' => 'مؤسسة حورس - حدائق الأهرام',
                'address' => '٢٢ع حدائق الأهرام، بوابة حورس',
                'phone' => '01002527072',
                'whatsapp' => '201002527072',
                'working_hours' => null,
                'is_active' => true,
                'sort_order' => 1,
            ]
        );
    }
}
