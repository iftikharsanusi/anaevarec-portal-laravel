<?php

namespace App\Http\Controllers;

use App\Models\HeaderAnalysis;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    /**
     * Display the user's history page.
     */
    public function index()
    {
        $analyses = HeaderAnalysis::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('history', compact('analyses'));
    }
}
