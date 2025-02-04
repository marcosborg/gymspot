<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;

class SystemCalendarController extends Controller
{
    public $sources = [
        [
            'model'      => '\App\Models\RentedSlot',
            'date_field' => 'start_date_time',
            'field'      => 'keypass',
            'prefix'     => 'Cliente',
            'suffix'     => 'Até',
            'route'      => 'admin.rented-slots.edit',
        ],
    ];

    public function index()
    {
        $events = [];
        foreach ($this->sources as $source) {
            foreach ($source['model']::all()->load('client') as $model) {
                $crudFieldValue = $model->getAttributes()[$source['date_field']];

                if (! $crudFieldValue) {
                    continue;
                }

                $events[] = [
                    'title' => $model->client->name . ' | ' . Carbon::parse($model->start_date_time)->diffInMinutes(Carbon::parse($model->end_date_time)) . ' minutos',
                    'start' => $crudFieldValue,
                    'url'   => route($source['route'], $model->id),
                ];
            }
        }

        return view('admin.calendar.calendar', compact('events'));
    }
}