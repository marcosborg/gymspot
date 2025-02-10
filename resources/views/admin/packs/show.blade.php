@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.pack.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.packs.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.pack.fields.id') }}
                        </th>
                        <td>
                            {{ $pack->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.pack.fields.name') }}
                        </th>
                        <td>
                            {{ $pack->name }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.pack.fields.description') }}
                        </th>
                        <td>
                            {{ $pack->description }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.pack.fields.spot') }}
                        </th>
                        <td>
                            {{ $pack->spot->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.pack.fields.price') }}
                        </th>
                        <td>
                            {{ $pack->price }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.pack.fields.quantity') }}
                        </th>
                        <td>
                            {{ $pack->quantity }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.pack.fields.start_date') }}
                        </th>
                        <td>
                            {{ $pack->start_date }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.pack.fields.end_date') }}
                        </th>
                        <td>
                            {{ $pack->end_date }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.pack.fields.promo_title') }}
                        </th>
                        <td>
                            {{ $pack->promo_title }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.pack.fields.promo_description') }}
                        </th>
                        <td>
                            {{ $pack->promo_description }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.pack.fields.image') }}
                        </th>
                        <td>
                            @if($pack->image)
                                <a href="{{ $pack->image->getUrl() }}" target="_blank" style="display: inline-block">
                                    <img src="{{ $pack->image->getUrl('thumb') }}">
                                </a>
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.packs.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection