@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.clientData.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.client-datas.update", [$clientData->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="form-group">
                <label class="required" for="client_id">{{ trans('cruds.clientData.fields.client') }}</label>
                <select class="form-control select2 {{ $errors->has('client') ? 'is-invalid' : '' }}" name="client_id" id="client_id" required>
                    @foreach($clients as $id => $entry)
                        <option value="{{ $id }}" {{ (old('client_id') ? old('client_id') : $clientData->client->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('client'))
                    <div class="invalid-feedback">
                        {{ $errors->first('client') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.clientData.fields.client_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="age">{{ trans('cruds.clientData.fields.age') }}</label>
                <input class="form-control {{ $errors->has('age') ? 'is-invalid' : '' }}" type="text" name="age" id="age" value="{{ old('age', $clientData->age) }}" required>
                @if($errors->has('age'))
                    <div class="invalid-feedback">
                        {{ $errors->first('age') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.clientData.fields.age_helper') }}</span>
            </div>
            <div class="form-group">
                <label>{{ trans('cruds.clientData.fields.gender') }}</label>
                @foreach(App\Models\ClientData::GENDER_RADIO as $key => $label)
                    <div class="form-check {{ $errors->has('gender') ? 'is-invalid' : '' }}">
                        <input class="form-check-input" type="radio" id="gender_{{ $key }}" name="gender" value="{{ $key }}" {{ old('gender', $clientData->gender) === (string) $key ? 'checked' : '' }}>
                        <label class="form-check-label" for="gender_{{ $key }}">{{ $label }}</label>
                    </div>
                @endforeach
                @if($errors->has('gender'))
                    <div class="invalid-feedback">
                        {{ $errors->first('gender') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.clientData.fields.gender_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required">{{ trans('cruds.clientData.fields.primary_objective') }}</label>
                @foreach(App\Models\ClientData::PRIMARY_OBJECTIVE_RADIO as $key => $label)
                    <div class="form-check {{ $errors->has('primary_objective') ? 'is-invalid' : '' }}">
                        <input class="form-check-input" type="radio" id="primary_objective_{{ $key }}" name="primary_objective" value="{{ $key }}" {{ old('primary_objective', $clientData->primary_objective) === (string) $key ? 'checked' : '' }} required>
                        <label class="form-check-label" for="primary_objective_{{ $key }}">{{ $label }}</label>
                    </div>
                @endforeach
                @if($errors->has('primary_objective'))
                    <div class="invalid-feedback">
                        {{ $errors->first('primary_objective') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.clientData.fields.primary_objective_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required">{{ trans('cruds.clientData.fields.fitness_level') }}</label>
                @foreach(App\Models\ClientData::FITNESS_LEVEL_RADIO as $key => $label)
                    <div class="form-check {{ $errors->has('fitness_level') ? 'is-invalid' : '' }}">
                        <input class="form-check-input" type="radio" id="fitness_level_{{ $key }}" name="fitness_level" value="{{ $key }}" {{ old('fitness_level', $clientData->fitness_level) === (string) $key ? 'checked' : '' }} required>
                        <label class="form-check-label" for="fitness_level_{{ $key }}">{{ $label }}</label>
                    </div>
                @endforeach
                @if($errors->has('fitness_level'))
                    <div class="invalid-feedback">
                        {{ $errors->first('fitness_level') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.clientData.fields.fitness_level_helper') }}</span>
            </div>
            <div class="form-group">
                <label>{{ trans('cruds.clientData.fields.condition') }}</label>
                @foreach(App\Models\ClientData::CONDITION_RADIO as $key => $label)
                    <div class="form-check {{ $errors->has('condition') ? 'is-invalid' : '' }}">
                        <input class="form-check-input" type="radio" id="condition_{{ $key }}" name="condition" value="{{ $key }}" {{ old('condition', $clientData->condition) === (string) $key ? 'checked' : '' }}>
                        <label class="form-check-label" for="condition_{{ $key }}">{{ $label }}</label>
                    </div>
                @endforeach
                @if($errors->has('condition'))
                    <div class="invalid-feedback">
                        {{ $errors->first('condition') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.clientData.fields.condition_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="condition_obs">{{ trans('cruds.clientData.fields.condition_obs') }}</label>
                <input class="form-control {{ $errors->has('condition_obs') ? 'is-invalid' : '' }}" type="text" name="condition_obs" id="condition_obs" value="{{ old('condition_obs', $clientData->condition_obs) }}">
                @if($errors->has('condition_obs'))
                    <div class="invalid-feedback">
                        {{ $errors->first('condition_obs') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.clientData.fields.condition_obs_helper') }}</span>
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