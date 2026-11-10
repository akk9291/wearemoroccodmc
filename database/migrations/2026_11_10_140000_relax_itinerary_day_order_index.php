<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Reordering days in the admin renumbers them one by one, which a unique (itinerary_id, day_no) index rejects. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('itinerary_days', function (Blueprint $t) {
            $t->index(['itinerary_id', 'day_no'], 'itinerary_days_order_index');
            $t->dropUnique(['itinerary_id', 'day_no']);
        });
    }

    public function down(): void
    {
        Schema::table('itinerary_days', function (Blueprint $t) {
            $t->unique(['itinerary_id', 'day_no']);
            $t->dropIndex('itinerary_days_order_index');
        });
    }
};
