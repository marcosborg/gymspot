<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \Carbon\Carbon;
use \App\Models\Spot;
use App\Models\RentedSlot;

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
                $status = 'occupied';
            } else {
                $status = $date->month === $currentMonth ? 'active' : 'occupied';
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

        $slots = $this->generateDaySlots($spot_id, $inputDate);

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

    private function generateDaySlots($spot_id, Carbon $day)
    {

        $spot = Spot::find($spot_id);

        $rented_slots = RentedSlot::where('spot_id', $spot_id)
            ->whereDate('start_date_time', $day->format('Y-m-d'))
            ->get();

        $slots = [];
        $startSlot = $day->copy()->startOfDay();
        $now = Carbon::now();

        // Arredondar a hora atual para a próxima meia hora
        $roundedNow = $now->copy()->addMinutes(30 - ($now->minute % 30))->second(0);

        for ($slot = 0; $slot < 48; $slot++) {
            $endSlot = $startSlot->copy()->addMinutes(30);

            // Verificar se o slot é antes da hora arredondada atual
            if ($endSlot->lt($roundedNow)) {
                $state = 'occupied';
            } else {
                $isOccupied = $rented_slots->contains(function ($rentedSlot) use ($startSlot, $endSlot) {
                    $rentedStart = Carbon::parse($rentedSlot->start_date_time);
                    $rentedEnd = Carbon::parse($rentedSlot->end_date_time);
                    return ($startSlot->lt($rentedEnd) && $endSlot->gt($rentedStart));
                });

                $state = $isOccupied ? 'occupied' : 'free';
            }

            $slots[] = [
                'start' => $startSlot->format('H:i'),
                'end' => $endSlot->format('H:i'),
                'timestamp' => $startSlot->format('Y-m-d H:i:s'),
                'spot' => $spot,
                'state' => $state
            ];

            $startSlot->addMinutes(30);
        }

        return $slots;
    }
}
