<?php

namespace App\Http\Requests;

use App\Models\Step;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyStepRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('step_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:steps,id',
        ];
    }
}
