<?php

namespace App\Http\Requests;

use App\Models\Pack;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyPackRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('pack_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:packs,id',
        ];
    }
}
