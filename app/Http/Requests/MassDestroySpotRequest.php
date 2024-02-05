<?php

namespace App\Http\Requests;

use App\Models\Spot;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroySpotRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('spot_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:spots,id',
        ];
    }
}
