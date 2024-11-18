<?php

namespace App\Http\Requests;

use App\Models\ClientData;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateClientDataRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('client_data_edit');
    }

    public function rules()
    {
        return [
            'client_id' => [
                'required',
                'integer',
            ],
            'primary_objective' => [
                'required',
            ],
            'fitness_level' => [
                'required',
            ],
            'condition_obs' => [
                'string',
                'nullable',
            ],
        ];
    }
}
