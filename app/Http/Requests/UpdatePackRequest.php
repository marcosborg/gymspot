<?php

namespace App\Http\Requests;

use App\Models\Pack;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdatePackRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('pack_edit');
    }

    public function rules()
    {
        return [
            'name' => [
                'string',
                'required',
            ],
            'spot_id' => [
                'required',
                'integer',
            ],
            'price' => [
                'required',
            ],
            'quantity' => [
                'required',
                'integer',
                'min:-2147483648',
                'max:2147483647',
            ],
            'start_date' => [
                'required',
                'date_format:' . config('panel.date_format') . ' ' . config('panel.time_format'),
            ],
            'end_date' => [
                'required',
                'date_format:' . config('panel.date_format') . ' ' . config('panel.time_format'),
            ],
            'promo_title' => [
                'string',
                'nullable',
            ],
            'promo_description' => [
                'string',
                'nullable',
            ],
            'vality_days' => [
                'required',
                'integer',
            ],
        ];
    }
}
