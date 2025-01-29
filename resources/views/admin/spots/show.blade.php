@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.spot.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.spots.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.spot.fields.id') }}
                        </th>
                        <td>
                            {{ $spot->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.spot.fields.name') }}
                        </th>
                        <td>
                            {{ $spot->name }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.spot.fields.description') }}
                        </th>
                        <td>
                            {!! $spot->description !!}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.spot.fields.address') }}
                        </th>
                        <td>
                            {{ $spot->address }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.spot.fields.zip') }}
                        </th>
                        <td>
                            {{ $spot->zip }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.spot.fields.location') }}
                        </th>
                        <td>
                            {{ $spot->location->title ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.spot.fields.country') }}
                        </th>
                        <td>
                            {{ $spot->country->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.spot.fields.price') }}
                        </th>
                        <td>
                            {{ $spot->price }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.spot.fields.sale') }}
                        </th>
                        <td>
                            {{ $spot->sale }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.spot.fields.capacity') }}
                        </th>
                        <td>
                            {{ $spot->capacity }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.spot.fields.email') }}
                        </th>
                        <td>
                            {{ $spot->email }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.spot.fields.phone') }}
                        </th>
                        <td>
                            {{ $spot->phone }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.spot.fields.photos') }}
                        </th>
                        <td>
                            @foreach($spot->photos as $key => $media)
                                <a href="{{ $media->getUrl() }}" target="_blank" style="display: inline-block">
                                    <img src="{{ $media->getUrl('thumb') }}">
                                </a>
                            @endforeach
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.spot.fields.item') }}
                        </th>
                        <td>
                            @foreach($spot->items as $key => $item)
                                <span class="label label-info">{{ $item->name }}</span>
                            @endforeach
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.spot.fields.soon') }}
                        </th>
                        <td>
                            <input type="checkbox" disabled="disabled" {{ $spot->soon ? 'checked' : '' }}>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.spots.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection