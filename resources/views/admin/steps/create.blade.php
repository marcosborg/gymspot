@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('cruds.step.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.steps.store") }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label for="number">{{ trans('cruds.step.fields.number') }}</label>
                <input class="form-control {{ $errors->has('number') ? 'is-invalid' : '' }}" type="number" name="number" id="number" value="{{ old('number', '') }}" step="1">
                @if($errors->has('number'))
                    <div class="invalid-feedback">
                        {{ $errors->first('number') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.step.fields.number_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="title">{{ trans('cruds.step.fields.title') }}</label>
                <input class="form-control {{ $errors->has('title') ? 'is-invalid' : '' }}" type="text" name="title" id="title" value="{{ old('title', '') }}">
                @if($errors->has('title'))
                    <div class="invalid-feedback">
                        {{ $errors->first('title') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.step.fields.title_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="text">{{ trans('cruds.step.fields.text') }}</label>
                <input class="form-control {{ $errors->has('text') ? 'is-invalid' : '' }}" type="text" name="text" id="text" value="{{ old('text', '') }}">
                @if($errors->has('text'))
                    <div class="invalid-feedback">
                        {{ $errors->first('text') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.step.fields.text_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="button">{{ trans('cruds.step.fields.button') }}</label>
                <input class="form-control {{ $errors->has('button') ? 'is-invalid' : '' }}" type="text" name="button" id="button" value="{{ old('button', '') }}">
                @if($errors->has('button'))
                    <div class="invalid-feedback">
                        {{ $errors->first('button') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.step.fields.button_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="link">{{ trans('cruds.step.fields.link') }}</label>
                <input class="form-control {{ $errors->has('link') ? 'is-invalid' : '' }}" type="text" name="link" id="link" value="{{ old('link', '') }}">
                @if($errors->has('link'))
                    <div class="invalid-feedback">
                        {{ $errors->first('link') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.step.fields.link_helper') }}</span>
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