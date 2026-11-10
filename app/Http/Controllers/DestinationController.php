<?php

namespace App\Http\Controllers;

use App\Models\Destination;

class DestinationController extends Controller
{
    public function index()
    {
        return PageController::render('destinations');
    }

    public function show(Destination $destination)
    {
        return view('content', ['m' => $destination]);
    }
}
