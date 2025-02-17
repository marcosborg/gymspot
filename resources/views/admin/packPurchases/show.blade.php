@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.packPurchase.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.pack-purchases.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.packPurchase.fields.id') }}
                        </th>
                        <td>
                            {{ $packPurchase->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.packPurchase.fields.client') }}
                        </th>
                        <td>
                            {{ $packPurchase->client->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.packPurchase.fields.pack') }}
                        </th>
                        <td>
                            {{ $packPurchase->pack->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.packPurchase.fields.quantity') }}
                        </th>
                        <td>
                            {{ $packPurchase->quantity }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.packPurchase.fields.available') }}
                        </th>
                        <td>
                            {{ $packPurchase->available }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.packPurchase.fields.limit_date') }}
                        </th>
                        <td>
                            {{ $packPurchase->limit_date }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.pack-purchases.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection