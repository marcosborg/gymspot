<?php

namespace App\Http\Requests;

use App\Models\PersonalTrainer;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyPersonalTrainerRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('personal_trainer_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:personal_trainers,id',
        ];
    }
}
