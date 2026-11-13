<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $map = [
            ['keywords' => ['دهان', 'سكيب', 'scib'], 'image' => 'assets/site/img/categories/scib-paints.jpg'],
            ['keywords' => ['جبسون', 'جبس', 'أسقف', 'اسقف'], 'image' => 'assets/site/img/categories/gypsum-board.jpg'],
            ['keywords' => ['فيوتك', 'كرانيش'], 'image' => 'assets/site/img/categories/fyutec-cornices.jpg'],
            ['keywords' => ['خشب', 'ورق', 'حائط', 'بديل'], 'image' => 'assets/site/img/categories/wall-panels.jpg'],
            ['keywords' => ['أدوات', 'ادوات', 'مستلزمات', 'بناء'], 'image' => 'assets/site/img/categories/construction-tools.jpg'],
            ['keywords' => ['مواد', 'تشطيب', 'عزل'], 'image' => 'assets/site/img/categories/finishing-materials.jpg'],
            ['keywords' => ['أبواب', 'ابواب', 'مصفح'], 'image' => 'assets/site/img/categories/doors.jpg'],
            ['keywords' => ['ديكور'], 'image' => 'assets/site/img/categories/decor.jpg'],
        ];

        $categories = DB::table('categories')->get();
        foreach ($categories as $cat) {
            $matchedImage = null;
            $name = mb_strtolower($cat->name ?? '');

            foreach ($map as $rule) {
                foreach ($rule['keywords'] as $kw) {
                    if (str_contains($name, $kw)) {
                        $matchedImage = $rule['image'];
                        break 2;
                    }
                }
            }

            if (!$matchedImage) {
                $matchedImage = 'assets/site/img/categories/finishing-materials.jpg';
            }

            // Update if image is empty or null
            if (empty($cat->image)) {
                DB::table('categories')->where('id', $cat->id)->update([
                    'image' => $matchedImage,
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op
    }
};
