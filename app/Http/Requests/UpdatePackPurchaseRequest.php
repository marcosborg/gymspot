<?php

namespace App\Http\Requests;

use App\Models\PackPurchase;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdatePackPurchaseRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('pack_purchase_edit');
    }

    public function rules()
    {
        return [
            'client_id' => [
                'required',
                'integer',
            ],
            'pack_id' => [
                'required',
                'integer',
            ],
            'quantity' => [
                'required',
                'integer',
                'min:-2147483648',
                'max:2147483647',
            ],
            'available' => [
                'required',
                'integer',
                'min:-2147483648',
                'max:2147483647',
            ],
            'limit_date' => [
                'required',
                'date_format:' . config('panel.date_format'),
            ],
        ];
    }
}
