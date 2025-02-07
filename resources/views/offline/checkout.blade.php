@extends('layouts.frontend')

@section('title', trans('messages.subscriptions'))

@section('page_script')
    <script type="text/javascript" src="{{ URL::asset('assets/js/plugins/forms/styling/uniform.min.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('js/validate.js') }}"></script>
@endsection

@section('page_header')

    <div class="page-title">
        <ul class="breadcrumb breadcrumb-caret position-right">
            <li><a href="{{ \Ace\Cashier\Cashier::lr_action("HomeController@index") }}">{{ trans('messages.home') }}</a></li>
            <li class="active">{{ trans('messages.subscription') }}</li>
        </ul>
    </div>

@endsection

@section('content')

    @include("account._menu", ['tab' => 'subscription'])

    <div class="row">
        <div class="col-md-6">
            <h2>{!! trans('cashier::messages.pay_invoice') !!}</h2>  

            <div class="alert alert-info bg-grey-light">
                {!! $service->getPaymentInstruction() !!}
            </div>
            <hr>
                
            <div class="d-flex align-items-center">
                <form method="POST"
                    action="{{ \Ace\Cashier\Cashier::lr_action('\Ace\Cashier\Controllers\OfflineController@claim', [
                        'invoice_uid' => $invoice->uid
                    ]) }}"
                >
                    {{ csrf_field() }}
                    <button
                        class="btn btn-primary mr-10 mr-4"
                    >{{ trans('cashier::messages.offline.claim_payment') }}</button>
                </form>

                <form id="cancelForm" method="POST" action="{{ action('AccountSubscriptionController@cancelInvoice', [
                            'invoice_uid' => $invoice->uid,
                ]) }}">
                    {{ csrf_field() }}
                    <a href="{{ action('AccountSubscriptionController@index') }}">
                        {{ trans('cashier::messages.go_back') }}
                    </a>
                </form>
            </div>
            
        </div>
        <div class="col-md-2"></div>
        <div class="col-md-4">
            @include('invoices.bill', [
                'bill' => $invoice->getBillingInfo(),
            ])
        </div>
    </div>
@endsection