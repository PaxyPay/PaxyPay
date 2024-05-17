@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row">
           
            <div class="col-md-6">
                <div class="card p-4 m-3">
                    <form action="{{ route('profile.paypal') }}" method="POST" id="payPalForm">
                        @csrf
                        <div class="d-flex justify-content-between align-items-center">

                            <div>
                                <h3>PayPal</h3>
                                <div class="form-check form-switch">
                                    <label class="form-check-label"
                                        for="flexSwitchCheckpaypal">{{ __('messages.disattivo') }}</label>
                                    <input class="form-check-input" type="checkbox" role="switch"
                                        id="flexSwitchCheckpaypal" value="1" name="activePayPal"
                                        @if ($settings['payMethods']['paypal']['active'] == 1) checked @endif>
                                </div>
                            </div>
                            <div>
                                <div class="mb-3">
                                    <label class="form-label" for="PayPalClientId">Paypal Client Id</label>
                                    <input class="form-control" type="text" id="PayPalClientId"
                                        value="{{ old('client_id', isset($settings['payMethods']['paypal']['client_id']) ? $settings['payMethods']['paypal']['client_id'] : '') }}"
                                        name="PayPalClientId">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="PayPalSecretKey">PayPal Secret</label>
                                    <input class="form-control" type="text" id="PayPalSecretKey"
                                        value="{{ old('secret_key', isset($settings['payMethods']['paypal']['secret_key']) ? $settings['payMethods']['paypal']['secret_key'] : '') }}"
                                        name="PayPalSecretKey">
                                </div>
                                <button type="submit" class="btn btn-success mt-4"
                                    id="saveStripeBtn">{{ __('messages.salva') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card p-4 m-3">
                    <form action="{{ route('profile.stripe') }}" method="POST" id="stripeForm">
                        @csrf
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3>Stripe</h3>
                                <div class="form-check form-switch">
                                    <label class="form-check-label"
                                        for="flexSwitchCheckStripe">{{ __('messages.disattivo') }}</label>
                                    <input class="form-check-input" type="checkbox" role="switch"
                                        id="flexSwitchCheckStripe" value="1" name="active"
                                        @if ($settings['payMethods']['stripe']['active'] == 1) checked @endif>
                                </div>
                            </div>
                            <div>
                                <div class="mb-3">
                                    <label class="form-label" for="stripePublicKey">Stripe Public Key</label>
                                    <input class="form-control" type="text" id="stripePublicKey"
                                        value="{{ old('public_key', isset($settings['payMethods']['stripe']['publickey']) ? $settings['payMethods']['stripe']['publickey'] : '') }}"
                                        name="public_key">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="stripeSecretKey">Stripe Secret Key</label>
                                    <input class="form-control" type="text" id="stripeSecretKey"
                                        value="{{ old('private_key', isset($settings['payMethods']['stripe']['privateKey']) ? $settings['payMethods']['stripe']['privateKey'] : '') }}"
                                        name="private_key">
                                </div>
                                <button type="submit" class="btn btn-success mt-4"
                                    id="saveStripeBtn">{{ __('messages.salva') }}</button>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="card p-4 m-3">
                    <form action="{{ route('profile.paypal') }}" method="POST" id="payPalForm">
                        @csrf
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3>Satispay</h3>
                                <div class="form-check form-switch">
                                    <label class="form-check-label"
                                        for="flexSwitchCheckStripe">{{ __('messages.disattivo') }}</label>
                                    <input class="form-check-input" type="checkbox" role="switch"
                                        id="flexSwitchCheckStripe" value="1" name="active"
                                        @if ($settings['payMethods']['paypal']['active'] == 1) checked @endif>
                                </div>
                            </div>
                            <div>
                                <div class="mb-3">
                                    <label class="form-label" for="PayPalClientId">Stripe Public Key</label>
                                    <input class="form-control" type="text" id="PayPalClientId"
                                        value="{{ old('client_id', isset($settings['payMethods']['paypal']['client_id']) ? $settings['payMethods']['paypal']['client_id'] : '') }}"
                                        name="PayPalClientId">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="PayPalSecretKey">Stripe Secret Key</label>
                                    <input class="form-control" type="text" id="PayPalSecretKey"
                                        value="{{ old('secret_key', isset($settings['payMethods']['paypal']['secret_key']) ? $settings['payMethods']['paypal']['secret_key'] : '') }}"
                                        name="PayPalSecretKey">
                                </div>
                                <button type="submit" class="btn btn-success mt-4"
                                    id="saveStripeBtn">{{ __('messages.salva') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card p-4 m-3">
                    <div>
                        <h3>{{ __('messages.settaggi') }}</h3>
                    </div>
                    <form action="{{ route('profile.updateSettings') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label" for="orderBy">Order By:</label>
                            <select class="form-select" id="orderBy" name="orderBy">
                                <option value="client_name" {{ $orderByValue == 'client_name' ? 'selected' : '' }}>Order
                                    For Name</option>
                                <option value="due_date" {{ $orderByValue == 'due_date' ? 'selected' : '' }}>Order For Due
                                    Date</option>
                                <option value="created_at" {{ $orderByValue == 'created_at' ? 'selected' : '' }}>Order For
                                    Creation Date</option>
                                <option value="active" {{ $orderByValue == 'active' ? 'selected' : '' }}>Order For Active
                                </option>
                                <option value="status" {{ $orderByValue == 'status' ? 'selected' : '' }}>Order For Status
                                </option>
                                <option value="total_price" {{ $orderByValue == 'total_price' ? 'selected' : '' }}>Order
                                    For Total Price</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="orderFor">Order For:</label>
                            <select class="form-select" id="orderFor" name="orderFor">
                                <option value="asc" {{ $settings['orderFor'] == 'asc' ? 'selected' : '' }}>Order For
                                    ASC</option>
                                <option value="desc" {{ $settings['orderFor'] == 'desc' ? 'selected' : '' }}>Order For
                                    DSC</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="perPage">Payments For Page:</label>
                            <select class="form-select" id="perPage" name="perPage">
                                <option value="10" {{ $settings['perPage'] == '10' ? 'selected' : '' }}>10</option>
                                <option value="20" {{ $settings['perPage'] == '20' ? 'selected' : '' }}>20</option>
                                <option value="50" {{ $settings['perPage'] == '50' ? 'selected' : '' }}>50</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-success">{{ __('messages.salva') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
