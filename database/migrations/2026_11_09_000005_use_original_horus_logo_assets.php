<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('settings') || !Schema::hasColumn('settings', 'key') || !Schema::hasColumn('settings', 'value')) {
            return;
        }

        DB::table('settings')->where('key', 'site_logo')->where('value', 'assets/site/img/horus-symbol.svg')->update(['value' => 'assets/site/img/horus-logo.png']);
        DB::table('settings')->where('key', 'site_favicon')->where('value', 'assets/site/img/horus-symbol.svg')->update(['value' => 'assets/site/img/horus-mark.png']);
    }

    public function down(): void
    {
        // Keep the original brand assets active.
    }
};
