<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \Carbon\Carbon;

class CalendarController extends Controller
{
    public function month($year = null, $month = null)
    {
        Carbon::setLocale('pt_PT');

        if ($year == null || $month == null) {
            $now = Carbon::now();
        } else {
            $now = Carbon::create($year, $month, 1, 0, 0, 0);
        }

        $currentYear = $now->year;
        $currentMonth = $now->month;
        $monthName = $now->translatedFormat('F');
        $firstDayOfMonth = $now->copy()->startOfMonth();
        $lastDayOfMonth = $now->copy()->endOfMonth();

        $previousMonthDate = $now->copy()->subMonth();
        $previousMonthLink = $previousMonthDate->format('Y/m');

        $nextMonthDate = $now->copy()->addMonth();
        $nextMonthLink = $nextMonthDate->format('Y/m');

        while ($firstDayOfMonth->dayOfWeekIso != 1) {
            $firstDayOfMonth->subDay();
        }

        while ($lastDayOfMonth->dayOfWeekIso != 7) {
            $lastDayOfMonth->addDay();
        }

        $daysWithWeekday = [];

        for ($date = $firstDayOfMonth; $date->lte($lastDayOfMonth); $date->addDay()) {
            $status = $date->month === $currentMonth ? 'active' : 'inactive';
            $daysWithWeekday[] = [
                'month' => $date->format('m'),
                'dayNumber' => $date->day,
                'weekDay' => $date->isoFormat('dddd'),
                'status' => $status
            ];
        }

        $month = [
            'year' => $currentYear,
            'currentMonth' => $currentMonth,
            'name' => $monthName,
            'previousMonthLink' => $previousMonthLink,
            'nextMonthLink' => $nextMonthLink,
            'daysWithWeekday' => $daysWithWeekday
        ];

        return $month;
    }


}
