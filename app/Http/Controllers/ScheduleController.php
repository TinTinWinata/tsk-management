<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Rules\MonthYearFormat;
use Carbon\Carbon;
use DateTime;
use Illuminate\Console\Application;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Support\Str;

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
            $copyDate = null;
            $copyDate = $currentDate->copy()->subDay($i);
            $date = $copyDate->format('Y-m-d');
            $formattedDate = $copyDate->format('D d - m - Y');
            $dates[$date]['formatted'] = $formattedDate;
            $dates[$date]['schedules'] = [];
        }

        // Get After Dates
        for ($i = 1; $i <= $afterDate; $i++) {
            $copyDate = null;
            $copyDate = $currentDate->copy()->addDay($i);
            $date = $copyDate->format('Y-m-d');
            $formattedDate = $copyDate->format('D d - m - Y');
            $dates[$date]['formatted'] = $formattedDate;
            $dates[$date]['schedules'] = [];
        }
        return $dates;
    }

    public static function getDatesForMonth($inputMonthYear)
    {
        $dateFormat = 'F-Y';
        $dates = [];
        $date = Carbon::createFromFormat($dateFormat, $inputMonthYear)->startOfMonth();

        while ($date->format($dateFormat) === $inputMonthYear) {
            $dateStr = $date->format('Y-m-d');
            $dates[$dateStr]['formatted'] = $date->format('D d - m - Y');
            $dates[$dateStr]['schedules'] = [];
            $date->modify('+1 day');
        }

        return $dates;
    }


    public static function getAvailableMonths()
    {
        $user = Auth::user();
        $datas = DB::table('schedules as s')
            ->select(DB::raw('CONCAT(MONTHNAME(s.date), \' - \', YEAR(s.date)) as month'))
            ->distinct()
            ->where('user_id', $user->id)
            ->get();
        return $datas;
    }

    public function save(Request $req)
    {
        $user = $req->user();
        $data = $req->all();
        $schedules = [];
        $i = 0;

        $startDate = null;
        $endDate = null;

        foreach ($data as $date => $detail) {
            if ($i === 0) {
                $startDate =  $date;
            }
            if ($i === count($data) - 1) {
                $endDate = $date;
            }
            foreach ($detail['schedules'] as $key => $schedule) {
                $schedule['id'] = Str::uuid();
                unset($schedule['created_at']);
                unset($schedule['updated_at']);
                $schedule['date'] = $date;
                $schedule['user_id'] = $user->id;
                $schedule['position'] = $key;
                array_push($schedules, $schedule);
            }
            $i++;
        }

        Schedule::where('user_id', $user->id)
            ->whereBetween(
                'date',
                [$startDate, $endDate]
            )
            ->delete();

        Schedule::insert($schedules);
        return response()->json('Succesfully saved new schedules', 200);
    }


    public function getUserSchedule($dates)
    {
        $user = auth()->user();

        $firstDate = key($dates);
        end($dates);
        $lastDate = key($dates);

        $schedules = DB::table('schedules')
            ->where('user_id', $user->id)
            ->where('date', '>=', $firstDate)
            ->where('date', '<=', $lastDate)
            ->orderBy('position', 'asc')->get();
        foreach ($schedules as $schedule) {
            $dateStr = Carbon::parse($schedule->date)->format('Y-m-d');
            array_push($dates[$dateStr]['schedules'], $schedule);
        }
        return $dates;
    }

    public function indexMonth(Request $request)
    {
        $request->validate([
            'date' => [new MonthYearFormat]
        ]);
        $month = $request->query->get('date'); // October-2023
        $dates = ScheduleController::getDatesForMonth($month);

        return Inertia::render('Dashboard/Dashboard', [
            'data' => $this->getUserSchedule($dates)
        ]);
    }

    public function index()
    {
        if (auth()->check()) {
            return Inertia::render('Dashboard/Dashboard', [
                'data' => $this->getUserSchedule(ScheduleController::getDates())
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
