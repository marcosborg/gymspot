<?php

namespace App\Http\Requests;

use App\Models\Step;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreStepRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('step_create');
    }

    public function rules()
    {
        return [
            'number' => [
                'nullable',
                'integer',
                'min:-2147483648',
                'max:2147483647',
            ],
            'title' => [
                'string',
                'nullable',
            ],
            'text' => [
                'string',
                'nullable',
            ],
            'button' => [
                'string',
                'nullable',
            ],
            'link' => [
                'string',
                'nullable',
            ],
        ];
    }
}
