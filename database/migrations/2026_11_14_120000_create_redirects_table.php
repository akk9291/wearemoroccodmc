<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('redirects', function (Blueprint $t) {
            $t->id();
            $t->string('from_path')->unique()->comment('/old-path, no domain, no query');
            $t->string('to_path', 1024)->comment('/new-path or a full URL');
            $t->unsignedSmallInteger('status_code')->default(301);
            $t->unsignedInteger('hits')->default(0);
            $t->timestamp('last_hit_at')->nullable();
            $t->string('source', 20)->default('manual')->comment('manual | legacy | slug');
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('redirects');
    }
};
