<?php

namespace App\Http\Requests;

use App\Models\PromoCodeItem;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyPromoCodeItemRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('promo_code_item_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:promo_code_items,id',
        ];
    }
}
