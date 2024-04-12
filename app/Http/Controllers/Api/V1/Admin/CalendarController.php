<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \Carbon\Carbon;
use \App\Models\Spot;

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

        $today = Carbon::now();

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
            if ($date->toDateString() === $today->toDateString()) {
                $status = 'active';
            } elseif ($date->lt($today)) {
                $status = 'inactive'; //
            } else {
                $status = $date->month === $currentMonth ? 'active' : 'inactive';
            }

            $daysWithWeekday[] = [
                'month' => $date->format('m'),
                'year' => $date->format('Y'),
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

    public function day(Request $request)
    {
        $spot_id = $request->spot_id;
        $year = $request->year;
        $currentMonth = $request->currentMonth;
        $dayNumber = $request->dayNumber;

        $spot = Spot::find($spot_id)->load('location');

        Carbon::setLocale('pt_PT');

        $inputDate = Carbon::createFromDate($year, $currentMonth, $dayNumber)->startOfDay();

        $slots = $this->generateDaySlots($inputDate);

        // Calculando o próximo dia
        $nextDay = $inputDate->copy()->addDay();
        $pastDay = $inputDate->copy()->subDay();

        $day = [
            'spot' => $spot,
            'day' => $inputDate->format('Y-m-d'),
            'dayWeek' => $inputDate->translatedFormat('l'),
            'slots' => $slots,
            'nextDay' => [
                'year' => $nextDay->year,
                'currentMonth' => $nextDay->month,
                'dayNumber' => $nextDay->day
            ],
            'pastDay' => [
                'year' => $pastDay->year,
                'currentMonth' => $pastDay->month,
                'dayNumber' => $pastDay->day
            ],
        ];

        return $day;
    }



    private function generateDaySlots(Carbon $day)
    {
        $slots = [];

        $startSlot = $day->copy()->startOfDay();

        for ($slot = 0; $slot < 48; $slot++) {
            $endSlot = $startSlot->copy()->addMinutes(30);

            $slots[] = [
                'start' => $startSlot->format('H:i'),
                'end' => $endSlot->format('H:i'),
            ];

            $startSlot->addMinutes(30);
        }

        return $slots;
    }


}
