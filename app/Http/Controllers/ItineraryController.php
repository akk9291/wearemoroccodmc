<?php

namespace App\Http\Controllers;

use App\Models\Itinerary;

class ItineraryController extends Controller
{
    public function index()
    {
        return PageController::render('itineraries');
    }

    public function show(Itinerary $itinerary)
    {
        $itinerary->load('itineraryDays');

        return view('itineraries.show', ['itinerary' => $itinerary]);
    }
}
