<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // quote.html builder + inquiry drawer
        Schema::create('quote_requests', function (Blueprint $t) {
            $t->id();
            $t->string('name')->nullable();
            $t->string('email')->nullable();
            $t->string('intent')->nullable();
            $t->string('party')->nullable();
            $t->string('travelers')->nullable();
            $t->string('dates')->nullable();
            $t->string('length')->nullable();
            $t->string('pace')->nullable();
            $t->text('mood')->nullable();
            $t->text('stays')->nullable();
            $t->text('prio')->nullable();
            $t->string('lead')->nullable();
            $t->text('note')->nullable();
            $t->string('status', 20)->default('new')->index()->comment('new | contacted | quoted | won | lost');
            $t->string('source_page')->nullable();
            $t->string('ip', 45)->nullable();
            $t->timestamps();
            $t->softDeletes();
        });

        Schema::create('contact_messages', function (Blueprint $t) {
            $t->id();
            $t->string('name')->nullable();
            $t->string('email')->nullable();
            $t->string('intent')->nullable();
            $t->string('travelers')->nullable();
            $t->string('dates')->nullable();
            $t->text('note')->nullable();
            $t->string('status', 20)->default('new')->index();
            $t->timestamps();
            $t->softDeletes();
        });

        Schema::create('newsletter_subscribers', function (Blueprint $t) {
            $t->id();
            $t->string('email')->unique();
            $t->string('source')->nullable();
            $t->timestamp('confirmed_at')->nullable();
            $t->timestamps();
        });

        Schema::create('menus', function (Blueprint $t) {
            $t->id();
            $t->string('name')->unique();
            $t->timestamps();
        });

        Schema::create('menu_items', function (Blueprint $t) {
            $t->id();
            $t->foreignId('menu_id')->constrained()->cascadeOnDelete();
            $t->foreignId('parent_id')->nullable()->constrained('menu_items')->cascadeOnDelete();
            $t->string('label');
            $t->string('url', 1024)->nullable()->comment('used when route is empty');
            $t->string('route')->nullable();
            $t->string('route_param')->nullable();
            $t->string('active_routes')->nullable()->comment('comma separated route patterns that highlight this item');
            $t->unsignedInteger('sort_order')->default(0);
            $t->timestamps();
        });

        Schema::create('settings', function (Blueprint $t) {
            $t->id();
            $t->string('key')->unique();
            $t->json('value')->nullable();
            $t->string('group')->default('general')->index();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        foreach (['settings', 'menu_items', 'menus', 'newsletter_subscribers', 'contact_messages', 'quote_requests'] as $table) {
            Schema::dropIfExists($table);
        }
    }
};
