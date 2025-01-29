@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.rentedSlot.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.rented-slots.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.rentedSlot.fields.id') }}
                        </th>
                        <td>
                            {{ $rentedSlot->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.rentedSlot.fields.spot') }}
                        </th>
                        <td>
                            {{ $rentedSlot->spot->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.rentedSlot.fields.start_date_time') }}
                        </th>
                        <td>
                            {{ $rentedSlot->start_date_time }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.rentedSlot.fields.end_date_time') }}
                        </th>
                        <td>
                            {{ $rentedSlot->end_date_time }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.rentedSlot.fields.client') }}
                        </th>
                        <td>
                            {{ $rentedSlot->client->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.rentedSlot.fields.keypass') }}
                        </th>
                        <td>
                            {{ $rentedSlot->keypass }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.rented-slots.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection