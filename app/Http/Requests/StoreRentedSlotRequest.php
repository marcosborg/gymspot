<?php

namespace App\Http\Requests;

use App\Models\RentedSlot;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreRentedSlotRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('rented_slot_create');
    }

    public function rules()
    {
        return [
            'spot_id' => [
                'required',
                'integer',
            ],
            'start_date_time' => [
                'required',
                'date_format:' . config('panel.date_format') . ' ' . config('panel.time_format'),
            ],
            'end_date_time' => [
                'required',
                'date_format:' . config('panel.date_format') . ' ' . config('panel.time_format'),
            ],
            'client_id' => [
                'required',
                'integer',
            ],
            'keypass' => [
                'string',
                'nullable',
            ],
        ];
    }
}
