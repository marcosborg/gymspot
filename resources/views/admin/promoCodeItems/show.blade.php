@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.promoCodeItem.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.promo-code-items.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.promoCodeItem.fields.id') }}
                        </th>
                        <td>
                            {{ $promoCodeItem->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.promoCodeItem.fields.user') }}
                        </th>
                        <td>
                            {{ $promoCodeItem->user->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.promoCodeItem.fields.name') }}
                        </th>
                        <td>
                            {{ $promoCodeItem->name }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.promoCodeItem.fields.code') }}
                        </th>
                        <td>
                            {{ $promoCodeItem->code }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.promoCodeItem.fields.type') }}
                        </th>
                        <td>
                            {{ App\Models\PromoCodeItem::TYPE_RADIO[$promoCodeItem->type] ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.promoCodeItem.fields.amount') }}
                        </th>
                        <td>
                            {{ $promoCodeItem->amount }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.promoCodeItem.fields.start_date') }}
                        </th>
                        <td>
                            {{ $promoCodeItem->start_date }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.promoCodeItem.fields.end_date') }}
                        </th>
                        <td>
                            {{ $promoCodeItem->end_date }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.promoCodeItem.fields.qty') }}
                        </th>
                        <td>
                            {{ $promoCodeItem->qty }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.promoCodeItem.fields.qty_remain') }}
                        </th>
                        <td>
                            {{ $promoCodeItem->qty_remain }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.promo-code-items.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection