<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('products') || !Schema::hasColumn('products', 'slug') || !Schema::hasColumn('products', 'image')) {
            return;
        }

        $images = [
            'scib-interior-exterior-paints' => 'assets/site/img/horus-gallery/scib-paints.jpg',
            'computer-color-mixing' => 'assets/site/img/horus-gallery/scib-paints.jpg',
            'gypsum-board-systems' => 'assets/site/img/horus-gallery/horus-scib.jpg',
            'painting-and-finishing-tools' => 'assets/site/img/horus-gallery/tools-finishing.jpg',
            'interior-exterior-putty' => 'assets/site/img/horus-gallery/finishing-materials.jpg',
            'fasteners-and-adhesives' => 'assets/site/img/horus-gallery/tools-finishing.jpg',
        ];

        foreach ($images as $slug => $image) {
            DB::table('products')->where('slug', $slug)->where(function ($query) {
                $query->whereNull('image')->orWhere('image', '');
            })->update(['image' => $image, 'updated_at' => now()]);
        }
    }

    public function down(): void
    {
        // Keep attached promotional images in place.
    }
};
