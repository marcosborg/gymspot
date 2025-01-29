@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.personalTrainer.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.personal-trainers.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.personalTrainer.fields.id') }}
                        </th>
                        <td>
                            {{ $personalTrainer->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.personalTrainer.fields.name') }}
                        </th>
                        <td>
                            {{ $personalTrainer->name }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.personalTrainer.fields.email') }}
                        </th>
                        <td>
                            {{ $personalTrainer->email }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.personalTrainer.fields.phone') }}
                        </th>
                        <td>
                            {{ $personalTrainer->phone }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.personalTrainer.fields.facebook') }}
                        </th>
                        <td>
                            {{ $personalTrainer->facebook }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.personalTrainer.fields.instagram') }}
                        </th>
                        <td>
                            {{ $personalTrainer->instagram }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.personalTrainer.fields.linkedin') }}
                        </th>
                        <td>
                            {{ $personalTrainer->linkedin }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.personalTrainer.fields.tiktok') }}
                        </th>
                        <td>
                            {{ $personalTrainer->tiktok }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.personalTrainer.fields.description') }}
                        </th>
                        <td>
                            {!! $personalTrainer->description !!}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.personalTrainer.fields.photos') }}
                        </th>
                        <td>
                            @foreach($personalTrainer->photos as $key => $media)
                                <a href="{{ $media->getUrl() }}" target="_blank" style="display: inline-block">
                                    <img src="{{ $media->getUrl('thumb') }}">
                                </a>
                            @endforeach
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.personalTrainer.fields.spots') }}
                        </th>
                        <td>
                            @foreach($personalTrainer->spots as $key => $spots)
                                <span class="label label-info">{{ $spots->name }}</span>
                            @endforeach
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.personalTrainer.fields.price') }}
                        </th>
                        <td>
                            {{ $personalTrainer->price }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.personalTrainer.fields.certificate_type') }}
                        </th>
                        <td>
                            {{ App\Models\PersonalTrainer::CERTIFICATE_TYPE_RADIO[$personalTrainer->certificate_type] ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.personalTrainer.fields.professional_certificate') }}
                        </th>
                        <td>
                            {{ $personalTrainer->professional_certificate }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.personalTrainer.fields.expiration') }}
                        </th>
                        <td>
                            {{ $personalTrainer->expiration }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.personalTrainer.fields.user') }}
                        </th>
                        <td>
                            {{ $personalTrainer->user->name ?? '' }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.personal-trainers.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection