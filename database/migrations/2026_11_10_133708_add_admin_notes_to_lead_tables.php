<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['quote_requests', 'contact_messages'] as $table) {
            Schema::table($table, function (Blueprint $t) {
                $t->text('admin_notes')->nullable()->after('status');
            });
        }
    }

    public function down(): void
    {
        foreach (['quote_requests', 'contact_messages'] as $table) {
            Schema::table($table, fn (Blueprint $t) => $t->dropColumn('admin_notes'));
        }
    }
};
