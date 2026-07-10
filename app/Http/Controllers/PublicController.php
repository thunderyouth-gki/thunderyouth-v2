<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\View\View;

class PublicController extends Controller
{
    public function home(): View
    {
        $nearestService = Event::where('status', 'published')
            ->whereDate('event_date', '>=', now()->toDateString())
            ->orderBy('event_date', 'asc')
            ->first();

        return view('home', compact('nearestService'));
    }

    public function services(): View
    {
        $nearestService = Event::where('status', 'published')
            ->whereDate('event_date', '>=', now()->toDateString())
            ->orderBy('event_date', 'asc')
            ->first();

        $pastServices = Event::where('status', 'published')
            ->whereDate('event_date', '<', now()->toDateString())
            ->whereMonth('event_date', now()->month)
            ->whereYear('event_date', now()->year)
            ->orderBy('event_date', 'desc')
            ->get();

        return view('services', compact('nearestService', 'pastServices'));
    }
}
