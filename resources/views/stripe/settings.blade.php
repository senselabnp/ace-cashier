@extends('layouts.backend')

@section('title', trans('cashier::messages.stripe'))

@section('page_script')
	<script type="text/javascript" src="{{ URL::asset('assets/js/plugins/forms/styling/uniform.min.js') }}"></script>

    <script type="text/javascript" src="{{ URL::asset('js/validate.js') }}"></script>
	<script type="text/javascript" src="{{ URL::asset('js/tinymce/tinymce.min.js') }}"></script>
        
    <script type="text/javascript" src="{{ URL::asset('js/editor.js') }}"></script>
@endsection

@section('page_header')

    <div class="page-title">
        <ul class="breadcrumb breadcrumb-caret position-right">
            <li><a href="{{ action("HomeController@index") }}">{{ trans('messages.home') }}</a></li>
            <li><a href="{{ action("Admin\PaymentController@index") }}">{{ trans('messages.payment_gateways') }}</a></li>
            <li class="active">{{ trans('messages.update') }}</li>
        </ul>
        <h1>
            <span class="text-semibold"><i class="icon-credit-card2"></i> {{ trans('cashier::messages.stripe') }}</span>
        </h1>
    </div>

@endsection

@section('content')
		<div class="row">
			<div class="col-md-6">
				<p>
					{!! trans('cashier::messages.stripe.intro') !!}
				</p>
			</div>
		</div>
			
		<h3>{{ trans('cashier::messages.payment.options') }}</h3>

        <form enctype="multipart/form-data" action="{{ $gateway->getSettingsUrl() }}" method="POST" class="form-validate-jquery">
            {{ csrf_field() }}
            <div class="row">
                <div class="col-md-6">
                    @include('helpers.form_control', [
                        'type' => 'text',
                        'name' => 'publishable_key',
                        'value' => $gateway->getPublishableKey(),
                        'label' => trans('cashier::messages.stripe.publishable_key'),
                        'help_class' => 'payment',
                        'rules' => ['publishable_key' => 'required'],
                    ])

                    @include('helpers.form_control', [
                        'type' => 'text',
                        'name' => 'secret_key',
                        'value' => $gateway->getSecretKey(),
                        'label' => trans('cashier::messages.stripe.secret_key'),
                        'help_class' => 'payment',
                        'rules' => ['secretKey' => 'required'],
                    ])
                </div>
            </div>


            <hr>
            <div class="text-left">
                @if ($gateway->isActive())
                    @if (!\Ace\Library\Facades\Billing::isGatewayEnabled($gateway))
                        <input type="submit" name="enable_gateway" class="btn btn-mc_primary mr-5" value="{{ trans('cashier::messages.save_and_enable') }}" />
                        <button class="btn btn-mc_default mr-5">{{ trans('messages.save') }}</button>
                    @else
                        <button class="btn btn-mc_primary mr-5">{{ trans('messages.save') }}</button>
                    @endif
                @else
                    <input type="submit" name="enable_gateway" class="btn btn-mc_primary mr-5" value="{{ trans('cashier::messages.connect') }}" />
                @endif
                <a class="btn btn-mc_default" href="{{ action('Admin\PaymentController@index') }}">{{ trans('messages.cancel') }}</a>
            </div>

        </form>

@endsection