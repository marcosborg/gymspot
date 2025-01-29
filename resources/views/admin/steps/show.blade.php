@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.step.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.steps.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.step.fields.id') }}
                        </th>
                        <td>
                            {{ $step->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.step.fields.number') }}
                        </th>
                        <td>
                            {{ $step->number }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.step.fields.title') }}
                        </th>
                        <td>
                            {{ $step->title }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.step.fields.text') }}
                        </th>
                        <td>
                            {{ $step->text }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.step.fields.button') }}
                        </th>
                        <td>
                            {{ $step->button }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.step.fields.link') }}
                        </th>
                        <td>
                            {{ $step->link }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.steps.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection