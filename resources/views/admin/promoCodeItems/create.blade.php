@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('cruds.promoCodeItem.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.promo-code-items.store") }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label class="required" for="user_id">{{ trans('cruds.promoCodeItem.fields.user') }}</label>
                <select class="form-control select2 {{ $errors->has('user') ? 'is-invalid' : '' }}" name="user_id" id="user_id" required>
                    @foreach($users as $id => $entry)
                        <option value="{{ $id }}" {{ old('user_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('user'))
                    <div class="invalid-feedback">
                        {{ $errors->first('user') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.promoCodeItem.fields.user_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="name">{{ trans('cruds.promoCodeItem.fields.name') }}</label>
                <input class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" type="text" name="name" id="name" value="{{ old('name', '') }}" required>
                @if($errors->has('name'))
                    <div class="invalid-feedback">
                        {{ $errors->first('name') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.promoCodeItem.fields.name_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="description">{{ trans('cruds.promoCodeItem.fields.description') }}</label>
                <textarea class="form-control {{ $errors->has('description') ? 'is-invalid' : '' }}" name="description" id="description">{{ old('description') }}</textarea>
                @if($errors->has('description'))
                    <div class="invalid-feedback">
                        {{ $errors->first('description') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.promoCodeItem.fields.description_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="code">{{ trans('cruds.promoCodeItem.fields.code') }}</label>
                <input class="form-control {{ $errors->has('code') ? 'is-invalid' : '' }}" type="text" name="code" id="code" value="{{ old('code', '') }}" required>
                @if($errors->has('code'))
                    <div class="invalid-feedback">
                        {{ $errors->first('code') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.promoCodeItem.fields.code_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required">{{ trans('cruds.promoCodeItem.fields.type') }}</label>
                @foreach(App\Models\PromoCodeItem::TYPE_RADIO as $key => $label)
                    <div class="form-check {{ $errors->has('type') ? 'is-invalid' : '' }}">
                        <input class="form-check-input" type="radio" id="type_{{ $key }}" name="type" value="{{ $key }}" {{ old('type', 'amount') === (string) $key ? 'checked' : '' }} required>
                        <label class="form-check-label" for="type_{{ $key }}">{{ $label }}</label>
                    </div>
                @endforeach
                @if($errors->has('type'))
                    <div class="invalid-feedback">
                        {{ $errors->first('type') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.promoCodeItem.fields.type_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="amount">{{ trans('cruds.promoCodeItem.fields.amount') }}</label>
                <input class="form-control {{ $errors->has('amount') ? 'is-invalid' : '' }}" type="number" name="amount" id="amount" value="{{ old('amount', '') }}" step="0.01" required>
                @if($errors->has('amount'))
                    <div class="invalid-feedback">
                        {{ $errors->first('amount') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.promoCodeItem.fields.amount_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="min_value">{{ trans('cruds.promoCodeItem.fields.min_value') }}</label>
                <input class="form-control {{ $errors->has('min_value') ? 'is-invalid' : '' }}" type="number" name="min_value" id="min_value" value="{{ old('min_value', '') }}" step="0.01" required>
                @if($errors->has('min_value'))
                    <div class="invalid-feedback">
                        {{ $errors->first('min_value') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.promoCodeItem.fields.min_value_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="start_date">{{ trans('cruds.promoCodeItem.fields.start_date') }}</label>
                <input class="form-control date {{ $errors->has('start_date') ? 'is-invalid' : '' }}" type="text" name="start_date" id="start_date" value="{{ old('start_date') }}" required>
                @if($errors->has('start_date'))
                    <div class="invalid-feedback">
                        {{ $errors->first('start_date') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.promoCodeItem.fields.start_date_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="end_date">{{ trans('cruds.promoCodeItem.fields.end_date') }}</label>
                <input class="form-control date {{ $errors->has('end_date') ? 'is-invalid' : '' }}" type="text" name="end_date" id="end_date" value="{{ old('end_date') }}" required>
                @if($errors->has('end_date'))
                    <div class="invalid-feedback">
                        {{ $errors->first('end_date') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.promoCodeItem.fields.end_date_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="qty">{{ trans('cruds.promoCodeItem.fields.qty') }}</label>
                <input class="form-control {{ $errors->has('qty') ? 'is-invalid' : '' }}" type="number" name="qty" id="qty" value="{{ old('qty', '') }}" step="1">
                @if($errors->has('qty'))
                    <div class="invalid-feedback">
                        {{ $errors->first('qty') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.promoCodeItem.fields.qty_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="qty_remain">{{ trans('cruds.promoCodeItem.fields.qty_remain') }}</label>
                <input class="form-control {{ $errors->has('qty_remain') ? 'is-invalid' : '' }}" type="number" name="qty_remain" id="qty_remain" value="{{ old('qty_remain', '') }}" step="1" required>
                @if($errors->has('qty_remain'))
                    <div class="invalid-feedback">
                        {{ $errors->first('qty_remain') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.promoCodeItem.fields.qty_remain_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required">{{ trans('cruds.promoCodeItem.fields.promo') }}</label>
                @foreach(App\Models\PromoCodeItem::PROMO_RADIO as $key => $label)
                    <div class="form-check {{ $errors->has('promo') ? 'is-invalid' : '' }}">
                        <input class="form-check-input" type="radio" id="promo_{{ $key }}" name="promo" value="{{ $key }}" {{ old('promo', 'slots') === (string) $key ? 'checked' : '' }} required>
                        <label class="form-check-label" for="promo_{{ $key }}">{{ $label }}</label>
                    </div>
                @endforeach
                @if($errors->has('promo'))
                    <div class="invalid-feedback">
                        {{ $errors->first('promo') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.promoCodeItem.fields.promo_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="pack_id">{{ trans('cruds.promoCodeItem.fields.pack') }}</label>
                <select class="form-control select2 {{ $errors->has('pack') ? 'is-invalid' : '' }}" name="pack_id" id="pack_id">
                    @foreach($packs as $id => $entry)
                        <option value="{{ $id }}" {{ old('pack_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('pack'))
                    <div class="invalid-feedback">
                        {{ $errors->first('pack') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.promoCodeItem.fields.pack_helper') }}</span>
            </div>
            <div class="form-group">
                <div class="form-check {{ $errors->has('status') ? 'is-invalid' : '' }}">
                    <input type="hidden" name="status" value="0">
                    <input class="form-check-input" type="checkbox" name="status" id="status" value="1" {{ old('status', 0) == 1 || old('status') === null ? 'checked' : '' }}>
                    <label class="form-check-label" for="status">{{ trans('cruds.promoCodeItem.fields.status') }}</label>
                </div>
                @if($errors->has('status'))
                    <div class="invalid-feedback">
                        {{ $errors->first('status') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.promoCodeItem.fields.status_helper') }}</span>
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