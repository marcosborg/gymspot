@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.about.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.abouts.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.about.fields.id') }}
                        </th>
                        <td>
                            {{ $about->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.about.fields.title') }}
                        </th>
                        <td>
                            {{ $about->title }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.about.fields.text') }}
                        </th>
                        <td>
                            {{ $about->text }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.about.fields.button') }}
                        </th>
                        <td>
                            {{ $about->button }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.about.fields.link') }}
                        </th>
                        <td>
                            {{ $about->link }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.about.fields.image') }}
                        </th>
                        <td>
                            @if($about->image)
                                <a href="{{ $about->image->getUrl() }}" target="_blank" style="display: inline-block">
                                    <img src="{{ $about->image->getUrl('thumb') }}">
                                </a>
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.abouts.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection