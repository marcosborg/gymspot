<?php

namespace App\Http\Requests;

use App\Models\Spot;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateSpotRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('spot_edit');
    }

    public function rules()
    {
        return [
            'name' => [
                'string',
                'required',
            ],
            'address' => [
                'string',
                'nullable',
            ],
            'zip' => [
                'string',
                'nullable',
            ],
            'location_id' => [
                'required',
                'integer',
            ],
            'price' => [
                'required',
            ],
            'capacity' => [
                'required',
                'integer',
                'min:-2147483648',
                'max:2147483647',
            ],
            'email' => [
                'string',
                'nullable',
            ],
            'phone' => [
                'string',
                'nullable',
            ],
            'photos' => [
                'array',
            ],
            'items.*' => [
                'integer',
            ],
            'items' => [
                'array',
            ],
        ];
    }
}
