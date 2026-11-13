<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('products')) {
            return;
        }

        // 1. Specific realistic market prices & stock for Horus products
        $productUpdates = [
            'scib-interior-exterior-paints' => ['price' => 650.00, 'stock' => 120],
            'computer-color-mixing' => ['price' => 80.00, 'stock' => 500],
            'scib-interior-paint' => ['price' => 720.00, 'stock' => 80],
            'scib-exterior-paint' => ['price' => 890.00, 'stock' => 70],
            'scib-wall-sealer' => ['price' => 310.00, 'stock' => 100],
            'gypsum-board-systems' => ['price' => 280.00, 'stock' => 150],
            'fyutec-cornices' => ['price' => 185.00, 'stock' => 120],
            'three-dimensional-wallpaper' => ['price' => 320.00, 'stock' => 90],
            'wood-alternative-panels' => ['price' => 420.00, 'stock' => 110],
            'armored-doors' => ['price' => 6800.00, 'stock' => 25],
            'painting-and-finishing-tools' => ['price' => 175.00, 'stock' => 160],
            'interior-exterior-putty' => ['price' => 220.00, 'stock' => 140],
            'ready-mix-putty' => ['price' => 260.00, 'stock' => 130],
            'gypsum-joint-compound' => ['price' => 195.00, 'stock' => 120],
            'painting-hand-tools' => ['price' => 145.00, 'stock' => 180],
            'fasteners-and-adhesives' => ['price' => 95.00, 'stock' => 300],
        ];

        foreach ($productUpdates as $slug => $data) {
            $update = [
                'price' => $data['price'],
                'updated_at' => now(),
            ];

            if (Schema::hasColumn('products', 'stock_quantity')) {
                $update['stock_quantity'] = $data['stock'];
            }
            if (Schema::hasColumn('products', 'quantity')) {
                $update['quantity'] = $data['stock'];
            }
            if (Schema::hasColumn('products', 'is_available')) {
                $update['is_available'] = true;
            }
            if (Schema::hasColumn('products', 'is_active')) {
                $update['is_active'] = true;
            }

            DB::table('products')->where('slug', $slug)->update($update);
        }

        // 2. Global fallback for any product that still has price <= 0 or null
        $zeroPriceQuery = DB::table('products')->where(function ($q) {
            $q->whereNull('price')->orWhere('price', '<=', 0);
        });

        $zeroPriceQuery->update(array_filter([
            'price' => 150.00,
            'is_available' => Schema::hasColumn('products', 'is_available') ? true : null,
            'is_active' => Schema::hasColumn('products', 'is_active') ? true : null,
            'stock_quantity' => Schema::hasColumn('products', 'stock_quantity') ? 100 : null,
            'quantity' => Schema::hasColumn('products', 'quantity') ? 100 : null,
            'updated_at' => now(),
        ], fn($v) => !is_null($v)));

        // 3. Fallback for any product with stock_quantity <= 0 or is_available = false
        if (Schema::hasColumn('products', 'stock_quantity')) {
            DB::table('products')->where('stock_quantity', '<=', 0)->update([
                'stock_quantity' => 100,
                'quantity' => 100,
                'is_available' => true,
                'updated_at' => now(),
            ]);
        }

        if (Schema::hasColumn('products', 'is_available')) {
            DB::table('products')->where('is_available', false)->update([
                'is_available' => true,
                'updated_at' => now(),
            ]);
        }

        // 4. Ensure categories all have valid images
        if (Schema::hasTable('categories') && Schema::hasColumn('categories', 'image')) {
            $categoryImageMap = [
                'scib-paints' => 'assets/site/img/categories/scib-paints.jpg',
                'gypsum-board' => 'assets/site/img/categories/gypsum-board.jpg',
                'fyutec-cornices' => 'assets/site/img/categories/fyutec-cornices.jpg',
                'wall-panels' => 'assets/site/img/categories/wall-panels.jpg',
                'construction-tools' => 'assets/site/img/categories/construction-tools.jpg',
                'finishing-materials' => 'assets/site/img/categories/finishing-materials.jpg',
                'doors' => 'assets/site/img/categories/doors.jpg',
                'decor' => 'assets/site/img/categories/decor.jpg',
            ];

            foreach ($categoryImageMap as $cSlug => $imgPath) {
                DB::table('categories')
                    ->where('slug', $cSlug)
                    ->where(function ($q) {
                        $q->whereNull('image')->orWhere('image', '');
                    })
                    ->update(['image' => $imgPath]);
            }

            // Fill any remaining empty category images
            DB::table('categories')
                ->whereNull('image')
                ->orWhere('image', '')
                ->update(['image' => 'assets/site/img/categories/finishing-materials.jpg']);
        }

        // 5. Clear application cache automatically
        try {
            Cache::flush();
        } catch (\Throwable $e) {
            // Ignore cache flush failures
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
