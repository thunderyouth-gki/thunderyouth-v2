<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;

class PublicController extends Controller
{
    public function home(): \Illuminate\View\View
    {
        $nearestService = Service::where('status', 'published')
            ->whereDate('service_date', '>=', now()->toDateString())
            ->orderBy('service_date', 'asc')
            ->first();

        return view('home', compact('nearestService'));
    }

    public function services(): \Illuminate\View\View
    {
        $nearestService = Service::where('status', 'published')
            ->whereDate('service_date', '>=', now()->toDateString())
            ->orderBy('service_date', 'asc')
            ->first();

        $pastServices = Service::where('status', 'published')
            ->whereDate('service_date', '<', now()->toDateString())
            ->whereMonth('service_date', now()->month)
            ->whereYear('service_date', now()->year)
            ->orderBy('service_date', 'desc')
            ->get();

        return view('services', compact('nearestService', 'pastServices'));
    }
}

