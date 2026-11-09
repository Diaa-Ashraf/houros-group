<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('settings') && Schema::hasColumn('settings', 'key') && Schema::hasColumn('settings', 'value')) {
            // Replace only the previous store's default, leaving any explicitly configured Horus fee intact.
            DB::table('settings')->where('key', 'delivery_fee')->where('value', '20')->update(['value' => '0']);
        }
    }

    public function down(): void
    {
        // The former store's delivery policy is not restored.
    }
};
