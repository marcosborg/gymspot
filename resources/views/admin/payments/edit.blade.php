@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.payment.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.payments.update", [$payment->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="form-group">
                <label class="required" for="client_id">{{ trans('cruds.payment.fields.client') }}</label>
                <select class="form-control select2 {{ $errors->has('client') ? 'is-invalid' : '' }}" name="client_id" id="client_id" required>
                    @foreach($clients as $id => $entry)
                        <option value="{{ $id }}" {{ (old('client_id') ? old('client_id') : $payment->client->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('client'))
                    <div class="invalid-feedback">
                        {{ $errors->first('client') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.payment.fields.client_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required">{{ trans('cruds.payment.fields.method') }}</label>
                @foreach(App\Models\Payment::METHOD_RADIO as $key => $label)
                    <div class="form-check {{ $errors->has('method') ? 'is-invalid' : '' }}">
                        <input class="form-check-input" type="radio" id="method_{{ $key }}" name="method" value="{{ $key }}" {{ old('method', $payment->method) === (string) $key ? 'checked' : '' }} required>
                        <label class="form-check-label" for="method_{{ $key }}">{{ $label }}</label>
                    </div>
                @endforeach
                @if($errors->has('method'))
                    <div class="invalid-feedback">
                        {{ $errors->first('method') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.payment.fields.method_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="cart">{{ trans('cruds.payment.fields.cart') }}</label>
                <textarea class="form-control {{ $errors->has('cart') ? 'is-invalid' : '' }}" name="cart" id="cart" required>{{ old('cart', $payment->cart) }}</textarea>
                @if($errors->has('cart'))
                    <div class="invalid-feedback">
                        {{ $errors->first('cart') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.payment.fields.cart_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="amount">{{ trans('cruds.payment.fields.amount') }}</label>
                <input class="form-control {{ $errors->has('amount') ? 'is-invalid' : '' }}" type="number" name="amount" id="amount" value="{{ old('amount', $payment->amount) }}" step="0.01" required>
                @if($errors->has('amount'))
                    <div class="invalid-feedback">
                        {{ $errors->first('amount') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.payment.fields.amount_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="request">{{ trans('cruds.payment.fields.request') }}</label>
                <input class="form-control {{ $errors->has('request') ? 'is-invalid' : '' }}" type="text" name="request" id="request" value="{{ old('request', $payment->request) }}">
                @if($errors->has('request'))
                    <div class="invalid-feedback">
                        {{ $errors->first('request') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.payment.fields.request_helper') }}</span>
            </div>
            <div class="form-group">
                <div class="form-check {{ $errors->has('paid') ? 'is-invalid' : '' }}">
                    <input type="hidden" name="paid" value="0">
                    <input class="form-check-input" type="checkbox" name="paid" id="paid" value="1" {{ $payment->paid || old('paid', 0) === 1 ? 'checked' : '' }}>
                    <label class="form-check-label" for="paid">{{ trans('cruds.payment.fields.paid') }}</label>
                </div>
                @if($errors->has('paid'))
                    <div class="invalid-feedback">
                        {{ $errors->first('paid') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.payment.fields.paid_helper') }}</span>
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