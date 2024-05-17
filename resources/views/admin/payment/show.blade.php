@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-lg-8 col-md-10 col-sm-12 mx-auto mt-5">
                <span class="fs-1">{{ $payment->total_price }} €</span>
                <div class="my-5">
                    <h5>Status History</h5>
                    <hr>
                    <div>
                        <p>Rejected</p>
                    </div>
                </div>
                <div class="my-5">
                    <h5>Payment Info</h4>
                        <hr>
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <h6 class="text-primary">Nome Cliente</h6>
                                <p>{{ $payment->client_name }}</p>
                            </div>
                            <div class="col-12 col-md-6">
                                <h6 class="text-primary">Prezzo Ordine</h6>
                                <p>{{ $payment->total_price }}</p>
                            </div>
                            <div class="col-12 col-md-6">
                                <h6 class="text-primary">Data di scadenza</h6>
                                <p>{{ $payment->due_date }}</p>
                            </div>
                            <div class="col-12 col-md-6">
                                <h6 class="text-primary">Stato del Pagamento</h6>
                                <p>{{ $payment->status }}</p>
                            </div>
                            <div class="col-12 col-md-6">
                                <h6 class="text-primary">token payment</h6>
                                <p class="text-wrap word-wrap">{{ $payment->token }}</p>
                            </div>
                            <div class="col-12 col-md-6">
                                <h6 class="text-primary">Descrizione del Pagamento</h6>
                                <p>{{ $payment->description }}</p>
                            </div>
                        </div>
                </div>
                <div class="my-5">
                    <h5>Payment Method</h5>
                    <hr>
                    <div>
                        <p>Mastercard</p>
                    </div>
                </div>
                <div class="my-5">
                    <h5>Products</h5>
                    <hr>
                    <div class="row">
                        @foreach ($payment->products as $product)
                            <div class=" col-6 col-md-4">
                                <h6 class="text-primary">Product Name</h6>
                                <p>{{ $product->product_name }}</p>
                            </div>
                            <div class=" col-6 col-md-4">
                                <h6 class="text-primary">Quantità Prodotto</h6>
                                <p>{{ $product->quantity }}</p>
                            </div>
                            <div class=" col-6 col-md-4">
                                <h6 class="text-primary">Prezzo Prodotto</h6>
                                <p>{{ $product->product_price }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
   
@endsection
