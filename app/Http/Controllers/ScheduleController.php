<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Console\Application;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Inertia\Inertia;

class ScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public static function getDates()
    {
        $beforeDate = 7;
        $afterDate = 7;
        $currentDate = Carbon::now();
        $dates = [];


        // Get Before Dates
        for ($i = $beforeDate - 1; $i >= 0; $i--) {
            $date = $currentDate->copy()->subDay($i)->format('D d - m - Y');
            array_push($dates, $date);
        }

        // Get After Dates
        for ($i = 1; $i <= $afterDate; $i++) {
            $date = $currentDate->copy()->addDays($i)->format('D d - m - Y');
            array_push($dates, $date);
        }
        return $dates;
    }

    public function index()
    {
        if (auth()->check()) {
            return Inertia::render('Dashboard', [
                'dates' => ScheduleController::getDates()
            ]);
        } else {
            return Inertia::render('Auth/Login');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
