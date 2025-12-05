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
            ->get();

        return view('history', compact('analyses'));
    }

    /**
     * Delete an analysis from the database.
     */
    public function destroy($id)
    {
        $analysis = HeaderAnalysis::where('user_id', auth()->id())
            ->findOrFail($id);

        $analysis->delete();

        return redirect()->route('history')->with('success', 'Analysis deleted successfully.');
    }
}
