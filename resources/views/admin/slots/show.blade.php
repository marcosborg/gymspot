@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.slot.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.slots.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.slot.fields.id') }}
                        </th>
                        <td>
                            {{ $slot->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.slot.fields.name') }}
                        </th>
                        <td>
                            {{ $slot->name }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.slot.fields.time') }}
                        </th>
                        <td>
                            {{ $slot->time }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.slots.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection