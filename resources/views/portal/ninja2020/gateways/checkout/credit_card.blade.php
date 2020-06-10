@extends('portal.ninja2020.layout.app')
@section('meta_title', ctrans('texts.checkout_com'))

@push('head')
    <meta name="public-key" content="{{ $gateway->getPublishableKey() }}">
    <meta name="customer-email" content="{{ $customer_email }}">
    <meta name="value" content="{{ $value }}">
    <meta name="currency" content="{{ $currency }}">

    <script src="{{ asset('js/clients/payments/checkout.com.js') }}"></script>
@endpush

@section('body')
    <form action="{{ route('client.payments.response') }}" method="post" id="server-response">
        @csrf
        <input type="hidden" name="gateway_response">
        <input type="hidden" name="store_card">
        @foreach($invoices as $invoice)
            <input type="hidden" name="hashed_ids[]" value="{{ $invoice->hashed_id }}">
        @endforeach
        <input type="hidden" name="company_gateway_id" value="{{ $gateway->getCompanyGatewayId() }}">
        <input type="hidden" name="payment_method_id" value="{{ $payment_method_id }}">
    </form>

    <div class="container mx-auto">
        <div class="grid grid-cols-6 gap-4">
            <div class="col-span-6 md:col-start-2 md:col-span-4">
                <div class="alert alert-failure mb-4" hidden id="errors"></div>
                <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                    <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">
                            {{ ctrans('texts.pay_now') }}
                        </h3>
                        <p class="mt-1 max-w-2xl text-sm leading-5 text-gray-500">
                            {{ ctrans('texts.complete_your_payment') }}
                        </p>
                    </div>
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 flex items-center">
                        <dt class="text-sm leading-5 font-medium text-gray-500 mr-4">
                            {{ ctrans('texts.payment_type') }}
                        </dt>
                        <dd class="mt-1 text-sm leading-5 text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ ctrans('texts.checkout_com') }} ({{ ctrans('texts.credit_card') }})
                        </dd>
                    </div>
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 flex items-center">
                        <dt class="text-sm leading-5 font-medium text-gray-500 mr-4">
                            {{ ctrans('texts.amount') }}
                        </dt>
                        <dd class="mt-1 text-sm leading-5 text-gray-900 sm:mt-0 sm:col-span-2">
                            <span class="font-bold">{{ App\Utils\Number::formatMoney($amount, $client) }}</span>
                        </dd>
                    </div>
                    <div class="bg-gray-50 px-4 py-5 flex justify-end">
                        <form class="payment-form" method="POST" action="https://merchant.com/successUrl">
                            @if(app()->environment() == 'production')
                            <script async src="https://cdn.checkout.com/js/checkout.js"></script>
                            @else
                            <script async src="https://cdn.checkout.com/sandbox/js/checkout.js"></script>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection