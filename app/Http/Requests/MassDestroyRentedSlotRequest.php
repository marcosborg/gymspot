<?php

namespace App\Http\Requests;

use App\Models\RentedSlot;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyRentedSlotRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('rented_slot_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:rented_slots,id',
        ];
    }
}
