@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.rentedSlot.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.rented-slots.update", [$rentedSlot->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="form-group">
                <label class="required" for="spot_id">{{ trans('cruds.rentedSlot.fields.spot') }}</label>
                <select class="form-control select2 {{ $errors->has('spot') ? 'is-invalid' : '' }}" name="spot_id" id="spot_id" required>
                    @foreach($spots as $id => $entry)
                        <option value="{{ $id }}" {{ (old('spot_id') ? old('spot_id') : $rentedSlot->spot->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('spot'))
                    <div class="invalid-feedback">
                        {{ $errors->first('spot') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.rentedSlot.fields.spot_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="start_date_time">{{ trans('cruds.rentedSlot.fields.start_date_time') }}</label>
                <input class="form-control datetime {{ $errors->has('start_date_time') ? 'is-invalid' : '' }}" type="text" name="start_date_time" id="start_date_time" value="{{ old('start_date_time', $rentedSlot->start_date_time) }}" required>
                @if($errors->has('start_date_time'))
                    <div class="invalid-feedback">
                        {{ $errors->first('start_date_time') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.rentedSlot.fields.start_date_time_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="end_date_time">{{ trans('cruds.rentedSlot.fields.end_date_time') }}</label>
                <input class="form-control datetime {{ $errors->has('end_date_time') ? 'is-invalid' : '' }}" type="text" name="end_date_time" id="end_date_time" value="{{ old('end_date_time', $rentedSlot->end_date_time) }}" required>
                @if($errors->has('end_date_time'))
                    <div class="invalid-feedback">
                        {{ $errors->first('end_date_time') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.rentedSlot.fields.end_date_time_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="client_id">{{ trans('cruds.rentedSlot.fields.client') }}</label>
                <select class="form-control select2 {{ $errors->has('client') ? 'is-invalid' : '' }}" name="client_id" id="client_id" required>
                    @foreach($clients as $id => $entry)
                        <option value="{{ $id }}" {{ (old('client_id') ? old('client_id') : $rentedSlot->client->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('client'))
                    <div class="invalid-feedback">
                        {{ $errors->first('client') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.rentedSlot.fields.client_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="keypass">{{ trans('cruds.rentedSlot.fields.keypass') }}</label>
                <input class="form-control {{ $errors->has('keypass') ? 'is-invalid' : '' }}" type="text" name="keypass" id="keypass" value="{{ old('keypass', $rentedSlot->keypass) }}">
                @if($errors->has('keypass'))
                    <div class="invalid-feedback">
                        {{ $errors->first('keypass') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.rentedSlot.fields.keypass_helper') }}</span>
            </div>
            <div class="form-group">
                <button class="btn btn-danger" type="submit">
                    {{ trans('global.save') }}
                </button>
            </div>
        </form>
    </div>
</div>



@endsection