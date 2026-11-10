<?php

namespace App\Http\Controllers;

use App\Models\CaseStudy;

class CaseStudyController extends Controller
{
    public function index()
    {
        return PageController::render('case-studies');
    }

    public function show(CaseStudy $caseStudy)
    {
        return view('content', ['m' => $caseStudy]);
    }
}
