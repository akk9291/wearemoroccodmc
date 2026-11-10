<?php

namespace App\Http\Controllers;

use App\Models\Service;

class ServiceController extends Controller
{
    public function index()
    {
        return PageController::render('services');
    }

    public function show(Service $service)
    {
        return view('content', ['m' => $service]);
    }
}
