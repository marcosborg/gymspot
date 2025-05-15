<?php

namespace App\Http\Requests;

use App\Models\PromoCodeItem;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdatePromoCodeItemRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('promo_code_item_edit');
    }

    public function rules()
    {
        return [
            'user_id' => [
                'required',
                'integer',
            ],
            'name' => [
                'string',
                'required',
            ],
            'code' => [
                'string',
                'required',
            ],
            'type' => [
                'required',
            ],
            'amount' => [
                'numeric',
                'required',
            ],
            'start_date' => [
                'required',
                'date_format:' . config('panel.date_format'),
            ],
            'end_date' => [
                'required',
                'date_format:' . config('panel.date_format'),
            ],
            'qty' => [
                'nullable',
                'integer',
                'min:-2147483648',
                'max:2147483647',
            ],
            'qty_remain' => [
                'required',
                'integer',
                'min:-2147483648',
                'max:2147483647',
            ],
        ];
    }
}
