<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HistoryController extends Controller
{
    /**
     * Display the user's history page.
     */
    public function index()
    {
        return view('history');
    }
}
