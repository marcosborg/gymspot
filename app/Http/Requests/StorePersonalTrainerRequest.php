<?php

namespace App\Http\Requests;

use App\Models\PersonalTrainer;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StorePersonalTrainerRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('personal_trainer_create');
    }

    public function rules()
    {
        return [
            'name' => [
                'string',
                'required',
            ],
            'phone' => [
                'string',
                'nullable',
            ],
            'facebook' => [
                'string',
                'nullable',
            ],
            'instagram' => [
                'string',
                'nullable',
            ],
            'linkedin' => [
                'string',
                'nullable',
            ],
            'tiktok' => [
                'string',
                'nullable',
            ],
            'photos' => [
                'array',
            ],
            'spots.*' => [
                'integer',
            ],
            'spots' => [
                'array',
            ],
            'price' => [
                'required',
            ],
            'certificate_type' => [
                'required',
            ],
            'professional_certificate' => [
                'string',
                'required',
            ],
            'expiration' => [
                'required',
                'date_format:' . config('panel.date_format'),
            ],
        ];
    }
}
