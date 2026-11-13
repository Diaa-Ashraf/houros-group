<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->string('title')->nullable()->after('code');
            $table->text('description')->nullable()->after('title');
            $table->foreignId('product_id')->nullable()->after('value')->constrained('products')->nullOnDelete();
            $table->foreignId('category_id')->nullable()->after('product_id')->constrained('categories')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropForeign(['category_id']);
            $table->dropColumn(['title', 'description', 'product_id', 'category_id']);
        });
    }
};
