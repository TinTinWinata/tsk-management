<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\User;
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
        $before_date = 7;
        $after_date = 7;
        $current_date = Carbon::now();
        $dates = [];

        // Get Before Dates
        for ($i = $before_date - 1; $i >= 0; $i--) {
            $copy_date = null;
            $copy_date = $current_date->copy()->subDay($i);
            $date = $copy_date->format('Y-m-d');
            $formatted_date = $copy_date->format('D d - m - Y');
            $dates[$date]['formatted'] = $formatted_date;
            $dates[$date]['schedules'] = [];
        }

        // Get After Dates
        for ($i = 1; $i <= $after_date; $i++) {
            $copy_date = null;
            $copy_date = $current_date->copy()->addDay($i);
            $date = $copy_date->format('Y-m-d');
            $formatted_date = $copy_date->format('D d - m - Y');
            $dates[$date]['formatted'] = $formatted_date;
            $dates[$date]['schedules'] = [];
        }
        return $dates;
    }

    public static function getDatesForMonth($input_month_year)
    {
        $date_format = 'F-Y';
        $dates = [];
        $date = Carbon::createFromFormat($date_format, $input_month_year)->startOfMonth();

        while ($date->format($date_format) === $input_month_year) {
            $date_str = $date->format('Y-m-d');
            $dates[$date_str]['formatted'] = $date->format('D d - m - Y');
            $dates[$date_str]['schedules'] = [];
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
            ->where('scheduleable_id', $user->id)
            ->get();
        return $datas;
    }

    public static function getSchedules($dates, $schedulable_id = null)
    {
        if($schedulable_id == null) {
            $schedulable_id = Auth::user()->id;
        }

        $first_date = key($dates);
        end($dates);
        $last_date = key($dates);

        $schedules = DB::table('schedules')
            ->where('scheduleable_id', $schedulable_id)
            ->where('date', '>=', $first_date)
            ->where('date', '<=', $last_date)
            ->orderBy('position', 'asc')->get();
        foreach ($schedules as $schedule) {
            $date_str = Carbon::parse($schedule->date)->format('Y-m-d');
            array_push($dates[$date_str]['schedules'], $schedule);
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
            'data' => ScheduleController::getSchedules($dates)
        ]);
    }

    public function indexList()
    {
        $user = User::find(Auth::user()->id);
        $schedules = $user->scheduels()->orderBy('date', 'asc')->get();
        if (auth()->check()) {
            return Inertia::render('List/List', [
                'data' => $schedules
            ]);
        }
    }

    public function index()
    {
        if (auth()->check()) {
            return Inertia::render('Dashboard/Dashboard', [
                'data' => ScheduleController::getSchedules(ScheduleController::getDates())
            ]);
        } else {
            return Inertia::render('Auth/Login');
        }
    }

    public  static function reminder()
    {
        $users = [];
        $current_date = now()->todate_string();

        $datas = DB::table('users')
            ->join('schedules', 'schedules.scheduleable_id', '=', 'users.id')
            ->whereNotNull('users.line_id')
            ->whereDate('date', $current_date)
            ->select('users.line_id', 'users.name', 'schedules.date', 'schedules.title')
            ->get();

        foreach ($datas as $data) {
            if (array_key_exists($data->line_id, $users)) {
                $users[$data->line_id] .=  "- " . $data->title . "\n";
            } else {
                $users[$data->line_id] = "Hello, " . $data->name . "😊!\n\nThis is Schedule for today agendas!\n" . "- " . $data->title . "\n";
            }
        }
        $url = env('APP_URL');
        foreach ($users as $line_id => $text) {
            $text .= "\n You can access the website on " . $url . "!";
            LineController::pushMessage($text, $line_id);
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
