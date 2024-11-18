<?php

namespace App\Http\Requests;

use App\Models\ClientData;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyClientDataRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('client_data_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:client_datas,id',
        ];
    }
}
