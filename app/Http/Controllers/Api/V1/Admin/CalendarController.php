<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\RentedSlot;
use App\Models\Spot;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    public function month($year = null, $month = null)
    {
        $timezone = config('app.timezone');
        Carbon::setLocale('pt_PT');

        if ($year == null || $month == null) {
            $now = Carbon::now($timezone);
        } else {
            $now = Carbon::create($year, $month, 1, 0, 0, 0, $timezone);
        }

        $currentYear = $now->year;
        $currentMonth = $now->month;
        $monthName = $now->translatedFormat('F');
        $firstDayOfMonth = $now->copy()->startOfMonth();
        $lastDayOfMonth = $now->copy()->endOfMonth();

        $today = Carbon::now($timezone);

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
                $status = 'occupied';
            } else {
                $status = $date->month === $currentMonth ? 'active' : 'occupied';
            }

            $daysWithWeekday[] = [
                'month' => $date->format('m'),
                'year' => $date->format('Y'),
                'dayNumber' => $date->day,
                'weekDay' => $date->isoFormat('dddd'),
                'status' => $status,
            ];
        }

        return [
            'year' => $currentYear,
            'currentMonth' => $currentMonth,
            'name' => $monthName,
            'previousMonthLink' => $previousMonthLink,
            'nextMonthLink' => $nextMonthLink,
            'daysWithWeekday' => $daysWithWeekday,
        ];
    }

    public function day(Request $request)
    {
        $spot_id = $request->spot_id;
        $year = $request->year;
        $currentMonth = $request->currentMonth;
        $dayNumber = $request->dayNumber;

        $spot = Spot::find($spot_id)->load('location');

        Carbon::setLocale('pt_PT');

        $inputDate = CarbonImmutable::create($year, $currentMonth, $dayNumber, 0, 0, 0, config('app.timezone'))->startOfDay();
        $slots = $this->generateDaySlots($spot_id, $inputDate);

        $nextDay = $inputDate->addDay();
        $pastDay = $inputDate->subDay();

        return [
            'spot' => $spot,
            'day' => $inputDate->format('Y-m-d'),
            'dayWeek' => $inputDate->translatedFormat('l'),
            'slots' => $slots,
            'nextDay' => [
                'year' => $nextDay->year,
                'currentMonth' => $nextDay->month,
                'dayNumber' => $nextDay->day,
            ],
            'pastDay' => [
                'year' => $pastDay->year,
                'currentMonth' => $pastDay->month,
                'dayNumber' => $pastDay->day,
            ],
        ];
    }

    private function generateDaySlots($spot_id, CarbonImmutable $day)
    {
        $spot = Spot::find($spot_id);

        $rented_slots = RentedSlot::where('spot_id', $spot_id)
            ->whereDate('start_date_time', $day->format('Y-m-d'))
            ->get();

        $slots = [];
        $startSlot = $day->startOfDay();
        $dayEnd = $day->addDay()->startOfDay();
        $now = CarbonImmutable::now(config('app.timezone'));
        $roundedNow = $now->copy()->addMinutes(30 - ($now->minute % 30))->second(0);

        while ($startSlot->lt($dayEnd)) {
            $endSlot = $startSlot->addMinutes(30);

            if ($endSlot->lt($roundedNow)) {
                $state = 'occupied';
            } else {
                $isOccupied = $rented_slots->contains(function ($rentedSlot) use ($startSlot, $endSlot) {
                    $rentedStart = CarbonImmutable::parse($rentedSlot->start_date_time, config('app.timezone'));
                    $rentedEnd = CarbonImmutable::parse($rentedSlot->end_date_time, config('app.timezone'));

                    return $startSlot->lt($rentedEnd) && $endSlot->gt($rentedStart);
                });

                $state = $isOccupied ? 'occupied' : 'free';
            }

            $slots[] = [
                'start' => $startSlot->format('H:i'),
                'end' => $endSlot->format('H:i'),
                'timestamp' => $startSlot->format('Y-m-d H:i:s'),
                'spot' => $spot,
                'state' => $state,
            ];

            $startSlot = $endSlot;
        }

        return $slots;
    }
}
