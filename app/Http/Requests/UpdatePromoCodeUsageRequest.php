<?php

namespace App\Http\Requests;

use App\Models\PromoCodeUsage;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdatePromoCodeUsageRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('promo_code_usage_edit');
    }

    public function rules()
    {
        return [
            'promo_code_item_id' => [
                'required',
                'integer',
            ],
            'client_id' => [
                'required',
                'integer',
            ],
            'payment_id' => [
                'required',
                'integer',
            ],
            'value' => [
                'required',
            ],
        ];
    }
}