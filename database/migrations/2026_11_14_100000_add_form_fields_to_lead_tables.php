<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Phase 4: which website form a lead came from, plus the drafted brief and source page for contact messages. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quote_requests', function (Blueprint $t) {
            $t->string('form_type', 20)->default('inquiry')->index()->after('id')->comment('quote | inquiry');
            $t->text('brief')->nullable()->after('note');
        });

        Schema::table('contact_messages', function (Blueprint $t) {
            $t->string('form_type', 20)->default('contact')->after('id');
            $t->string('source_page')->nullable()->after('note');
            $t->string('ip', 45)->nullable()->after('source_page');
        });
    }

    public function down(): void
    {
        Schema::table('quote_requests', fn (Blueprint $t) => $t->dropColumn(['form_type', 'brief']));
        Schema::table('contact_messages', fn (Blueprint $t) => $t->dropColumn(['form_type', 'source_page', 'ip']));
    }
};
