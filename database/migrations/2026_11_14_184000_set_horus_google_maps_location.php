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
        $mapsUrl = 'https://maps.app.goo.gl/AL2gbPB3KYh7Y2VFA?g_st=iwb';

        // 1. Update settings table
        if (Schema::hasTable('settings')) {
            DB::table('settings')->updateOrInsert(
                ['key' => 'google_maps_url'],
                [
                    'value' => $mapsUrl,
                    'group' => 'contact',
                    'type' => 'text',
                    'description' => 'رابط موقع مؤسسة حورس على خرائط جوجل',
                    'updated_at' => now(),
                ]
            );
        }

        // 2. Update branches table for Horus branch in Hadayek Al-Ahram
        if (Schema::hasTable('branches')) {
            DB::table('branches')
                ->where('slug', 'horus-gardens')
                ->orWhere('name', 'LIKE', '%حورس%')
                ->orWhere('is_active', true)
                ->update([
                    'google_maps_url' => $mapsUrl,
                    'updated_at' => now(),
                ]);
        }

        // 3. Clear cache
        try {
            Cache::flush();
        } catch (\Throwable $e) {
            // Ignore cache flush error
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
