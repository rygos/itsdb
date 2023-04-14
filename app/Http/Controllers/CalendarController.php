<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    public function index($year = null, $month = null){
        if(empty($year) or empty($month)){
            $date = Carbon::now();
        }else{
            $date = Carbon::parse('01.'.$month.'.'.$year);
        }

        $start_of_calendar = $date->copy()->firstOfMonth()->startOfWeek(Carbon::MONDAY);
        $end_of_calendar = $date->copy()->lastOfMonth()->endOfWeek(Carbon::SUNDAY);

        $day_labels = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];

        return view('calendar.index', [
            'date' => $date,
            'day_labels' => $day_labels,
            'start_of_calendar' => $start_of_calendar,
            'end_of_calendar' => $end_of_calendar,
        ]);
    }
}
