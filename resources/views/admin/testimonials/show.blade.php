@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.testimonial.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.testimonials.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.testimonial.fields.id') }}
                        </th>
                        <td>
                            {{ $testimonial->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.testimonial.fields.title') }}
                        </th>
                        <td>
                            {{ $testimonial->title }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.testimonial.fields.text') }}
                        </th>
                        <td>
                            {{ $testimonial->text }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.testimonial.fields.name') }}
                        </th>
                        <td>
                            {{ $testimonial->name }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.testimonial.fields.position') }}
                        </th>
                        <td>
                            {{ $testimonial->position }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.testimonials.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection