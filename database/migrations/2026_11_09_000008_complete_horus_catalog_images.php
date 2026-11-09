<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $imagesBySlug = [
            'fyutec-cornices' => 'assets/site/img/horus-gallery/horus-scib.jpg',
            'three-dimensional-wallpaper' => 'assets/site/img/horus-gallery/horus-scib.jpg',
            'wood-alternative-panels' => 'assets/site/img/horus-gallery/horus-scib.jpg',
            'armored-doors' => 'assets/site/img/horus-gallery/paint-store.jpg',
        ];

        foreach ($imagesBySlug as $slug => $image) {
            DB::table('products')
                ->where('slug', $slug)
                ->where(function ($query) {
                    $query->whereNull('image')->orWhere('image', '');
                })
                ->update(['image' => $image, 'updated_at' => now()]);
        }
    }

    public function down(): void
    {
        // Keep product media in place on rollback to preserve existing catalog records.
    }
};
