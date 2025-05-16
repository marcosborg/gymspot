@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.promoCodeUsage.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.promo-code-usages.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.promoCodeUsage.fields.id') }}
                        </th>
                        <td>
                            {{ $promoCodeUsage->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.promoCodeUsage.fields.promo_code_item') }}
                        </th>
                        <td>
                            {{ $promoCodeUsage->promo_code_item->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.promoCodeUsage.fields.client') }}
                        </th>
                        <td>
                            {{ $promoCodeUsage->client->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.promoCodeUsage.fields.payment') }}
                        </th>
                        <td>
                            {{ $promoCodeUsage->payment->paid ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.promoCodeUsage.fields.value') }}
                        </th>
                        <td>
                            {{ $promoCodeUsage->value }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.promoCodeUsage.fields.used') }}
                        </th>
                        <td>
                            <input type="checkbox" disabled="disabled" {{ $promoCodeUsage->used ? 'checked' : '' }}>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.promo-code-usages.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection