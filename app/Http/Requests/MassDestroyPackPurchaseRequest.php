<?php

namespace App\Http\Requests;

use App\Models\PackPurchase;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyPackPurchaseRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('pack_purchase_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:pack_purchases,id',
        ];
    }
}
