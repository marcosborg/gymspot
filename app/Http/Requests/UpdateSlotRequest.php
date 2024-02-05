<?php

namespace App\Http\Requests;

use App\Models\Slot;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateSlotRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('slot_edit');
    }

    public function rules()
    {
        return [
            'name' => [
                'string',
                'required',
            ],
            'time' => [
                'required',
                'integer',
                'min:-2147483648',
                'max:2147483647',
            ],
        ];
    }
}
