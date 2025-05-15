@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('cruds.promoCodeUsage.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.promo-code-usages.store") }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label class="required" for="promo_code_item_id">{{ trans('cruds.promoCodeUsage.fields.promo_code_item') }}</label>
                <select class="form-control select2 {{ $errors->has('promo_code_item') ? 'is-invalid' : '' }}" name="promo_code_item_id" id="promo_code_item_id" required>
                    @foreach($promo_code_items as $id => $entry)
                        <option value="{{ $id }}" {{ old('promo_code_item_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('promo_code_item'))
                    <div class="invalid-feedback">
                        {{ $errors->first('promo_code_item') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.promoCodeUsage.fields.promo_code_item_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="client_id">{{ trans('cruds.promoCodeUsage.fields.client') }}</label>
                <select class="form-control select2 {{ $errors->has('client') ? 'is-invalid' : '' }}" name="client_id" id="client_id" required>
                    @foreach($clients as $id => $entry)
                        <option value="{{ $id }}" {{ old('client_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('client'))
                    <div class="invalid-feedback">
                        {{ $errors->first('client') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.promoCodeUsage.fields.client_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="item">{{ trans('cruds.promoCodeUsage.fields.item') }}</label>
                <input class="form-control {{ $errors->has('item') ? 'is-invalid' : '' }}" type="text" name="item" id="item" value="{{ old('item', '') }}" required>
                @if($errors->has('item'))
                    <div class="invalid-feedback">
                        {{ $errors->first('item') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.promoCodeUsage.fields.item_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="inicial_value">{{ trans('cruds.promoCodeUsage.fields.inicial_value') }}</label>
                <input class="form-control {{ $errors->has('inicial_value') ? 'is-invalid' : '' }}" type="number" name="inicial_value" id="inicial_value" value="{{ old('inicial_value', '') }}" step="0.01" required>
                @if($errors->has('inicial_value'))
                    <div class="invalid-feedback">
                        {{ $errors->first('inicial_value') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.promoCodeUsage.fields.inicial_value_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="final_value">{{ trans('cruds.promoCodeUsage.fields.final_value') }}</label>
                <input class="form-control {{ $errors->has('final_value') ? 'is-invalid' : '' }}" type="number" name="final_value" id="final_value" value="{{ old('final_value', '') }}" step="0.01" required>
                @if($errors->has('final_value'))
                    <div class="invalid-feedback">
                        {{ $errors->first('final_value') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.promoCodeUsage.fields.final_value_helper') }}</span>
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