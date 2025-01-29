@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.call.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.calls.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.call.fields.id') }}
                        </th>
                        <td>
                            {{ $call->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.call.fields.title') }}
                        </th>
                        <td>
                            {{ $call->title }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.call.fields.subtitle') }}
                        </th>
                        <td>
                            {{ $call->subtitle }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.call.fields.button') }}
                        </th>
                        <td>
                            {{ $call->button }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.call.fields.link') }}
                        </th>
                        <td>
                            {{ $call->link }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.call.fields.image') }}
                        </th>
                        <td>
                            @if($call->image)
                                <a href="{{ $call->image->getUrl() }}" target="_blank" style="display: inline-block">
                                    <img src="{{ $call->image->getUrl('thumb') }}">
                                </a>
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.calls.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection