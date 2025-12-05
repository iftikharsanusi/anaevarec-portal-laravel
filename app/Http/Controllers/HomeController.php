<?php

namespace App\Http\Controllers;

use App\Models\HeaderAnalysis;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $analyses = HeaderAnalysis::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();

        // Get latest analysis if just analyzed
        $latestAnalysis = null;
        if (session('analysis_id')) {
            $latestAnalysis = HeaderAnalysis::where('user_id', auth()->id())
                ->find(session('analysis_id'));
        }

        return view('home', compact('analyses', 'latestAnalysis'));
    }
}
