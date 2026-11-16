<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/** Route map image shown on the itinerary page, and its alt text. Sets the supplied maps on the three matching journeys. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('itineraries', function (Blueprint $t) {
            $t->string('route_map', 1024)->nullable()->after('gallery');
            $t->string('route_map_alt')->nullable()->after('route_map');
        });

        $maps = [
            'grand-tour' => ['/assets/img/maps/route-map-01-ifrane-midelt-2400.jpg',
                'Route map of The Grand Morocco: Casablanca, Rabat, Fes, Ifrane, Midelt, Erfoud, Merzouga, Ouarzazate, Marrakech'],
            'magical-morocco' => ['/assets/img/maps/route-map-02-four-routes-2400.jpg',
                'Route map of Magical Morocco: Rabat, Fes, Casablanca, Marrakech'],
            'imperial-cities' => ['/assets/img/maps/route-map-02-four-routes-2400.jpg',
                'Route map of Imperial Morocco: Casablanca, Rabat, Fes, Marrakech'],
        ];
        foreach ($maps as $slug => [$map, $alt]) {
            DB::table('itineraries')->where('slug', $slug)->whereNull('route_map')
                ->update(['route_map' => $map, 'route_map_alt' => $alt]);
        }
    }

    public function down(): void
    {
        Schema::table('itineraries', fn (Blueprint $t) => $t->dropColumn(['route_map', 'route_map_alt']));
    }
};
