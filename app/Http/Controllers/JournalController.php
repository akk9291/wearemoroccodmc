<?php

namespace App\Http\Controllers;

use App\Models\JournalPost;

class JournalController extends Controller
{
    public function index()
    {
        return PageController::render('journal');
    }

    public function show(JournalPost $post)
    {
        return view('content', ['m' => $post]);
    }
}
