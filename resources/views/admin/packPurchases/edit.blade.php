@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.packPurchase.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.pack-purchases.update", [$packPurchase->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="form-group">
                <label class="required" for="client_id">{{ trans('cruds.packPurchase.fields.client') }}</label>
                <select class="form-control select2 {{ $errors->has('client') ? 'is-invalid' : '' }}" name="client_id" id="client_id" required>
                    @foreach($clients as $id => $entry)
                        <option value="{{ $id }}" {{ (old('client_id') ? old('client_id') : $packPurchase->client->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('client'))
                    <div class="invalid-feedback">
                        {{ $errors->first('client') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.packPurchase.fields.client_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="pack_id">{{ trans('cruds.packPurchase.fields.pack') }}</label>
                <select class="form-control select2 {{ $errors->has('pack') ? 'is-invalid' : '' }}" name="pack_id" id="pack_id" required>
                    @foreach($packs as $id => $entry)
                        <option value="{{ $id }}" {{ (old('pack_id') ? old('pack_id') : $packPurchase->pack->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('pack'))
                    <div class="invalid-feedback">
                        {{ $errors->first('pack') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.packPurchase.fields.pack_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="quantity">{{ trans('cruds.packPurchase.fields.quantity') }}</label>
                <input class="form-control {{ $errors->has('quantity') ? 'is-invalid' : '' }}" type="number" name="quantity" id="quantity" value="{{ old('quantity', $packPurchase->quantity) }}" step="1" required>
                @if($errors->has('quantity'))
                    <div class="invalid-feedback">
                        {{ $errors->first('quantity') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.packPurchase.fields.quantity_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="available">{{ trans('cruds.packPurchase.fields.available') }}</label>
                <input class="form-control {{ $errors->has('available') ? 'is-invalid' : '' }}" type="number" name="available" id="available" value="{{ old('available', $packPurchase->available) }}" step="1" required>
                @if($errors->has('available'))
                    <div class="invalid-feedback">
                        {{ $errors->first('available') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.packPurchase.fields.available_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="limit_date">{{ trans('cruds.packPurchase.fields.limit_date') }}</label>
                <input class="form-control date {{ $errors->has('limit_date') ? 'is-invalid' : '' }}" type="text" name="limit_date" id="limit_date" value="{{ old('limit_date', $packPurchase->limit_date) }}" required>
                @if($errors->has('limit_date'))
                    <div class="invalid-feedback">
                        {{ $errors->first('limit_date') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.packPurchase.fields.limit_date_helper') }}</span>
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