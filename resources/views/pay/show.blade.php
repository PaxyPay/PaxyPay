@extends('layouts.client')
@section('content')

    <div class="container mt-5 p-4 max-container">
        <div class="row">
            @if (
                $payment &&
                    $payment->active == 1 &&
                    $user &&
                    $payment->status != 'paid' &&
                    (!$payment->due_date || $payment->due_date >= \Carbon\Carbon::now()))
                <div>
                    @if ($user->image)
                        <img class="logo-pay" src="{{ $user->image }}" alt="Immagine del profilo">
                    @else
                        <img class="logo-pay" src="{{ env('APP_URL') }}/paxy-pay-logo.png" alt="">
                    @endif
                </div>
                <div>
                    <span>{{ $user->name }} sta richiedendo questo pagamento</span>
                </div>
                <div class="card p-3 shadow my-2 bg-viola">
                    <span>{{ $payment->description }}</span>
                </div>
                <div class="card p-3 shadow my-2 bg-viola">
                    <span>
                        Data di scadenza:
                        <span class="fw-bold">
                            {{ isset($payment->due_date) ? \Carbon\Carbon::parse($payment->due_date)->format('d/m/Y') : '∞' }}
                        </span>
                    </span>
                </div>
                <div class="card p-3 shadow my-2 bg-viola">
                    <table class="table no-border">
                        <thead>
                            <tr class="bg-viola">
                                <th class="bg-viola">Descrizione prodotto/servizio</th>
                                <th class="d-flex bg-viola justify-content-end no-border">Prezzo €</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($payment->products as $product)
                                <tr>
                                    <td scope="row">
                                        <div>
                                            {{ $product->quantity }} x <span
                                                class="fw-bold">{{ $product->product_name }}</span>
                                        </div>
                                        <div class="fs-10">unity price : <span
                                                class="fw-bold">{{ number_format($product->product_price, 2, ',', '.') }}</span>
                                        </div>
                                    </td>
                                    <td class="d-flex  justify-content-end fw-bold">
                                        {{ number_format($product->product_price * $product->quantity, 2, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card d-flex justify-content-between flex-row px-4 py-2 bg-purple">
                    <span>Totale €: </span>
                    <span class="fw-bold">{{ $payment->total_price }}</span>
                </div>
                <div>
                    <input class="form-check-input" type="checkbox" role="switch" id="stripeCheckbox" value="1"
                        name="police" required>
                    <label for="">Accetto le policy d'uso e privacy (click per info)</label>

                </div>
                <div class="d-flex justify-content-center">
                    <div class="m-3 d-flex justify-content-center ">
                        <form action="{{ route('pay.stripe', $payment->id) }}" method="POST"
                            onsubmit="return checkStripeCheckbox()">
                            @csrf
                            <div class="mt-2">

                            </div>
                            @if ($settings['payMethods']['stripe']['active'] == 0)
                            @else
                                <button class="btn btn-success m-2" type="submit">Paga con Carta</button>
                            @endif
                        </form>
                        <div id="paypal-button-container">

                        </div>


                        <form action="{{ route('pay.satispay', $payment->id) }}" method="POST"
                            onsubmit="return checkStripeCheckbox()">
                            @csrf
                            <div class="mt-2">

                            </div>
                            @if ($settings['payMethods']['paypal']['active'] == 0)
                            @else
                                <button class="btn btn-danger  m-2" type="submit">Satispay</button>
                            @endif
                        </form>
                        @if ($settings['payMethods']['stripe']['active'] == 0 && $settings['payMethods']['paypal']['active'] == 0)
                            <div>
                                <span class="btn btn-danger ">NESSUN METODO DI PAGAMENTO DISPONIBILE</span>
                            </div>
                        @endif
                    </div>
                </div>
            @else
                <div class="d-flex justify-content-center align-items-center">
                    <img class="logo-pay" src="http://192.168.1.8:8000/immagine.png" alt="">
                </div>
                <div class="card p-3 shadow my-2 bg-viola d-flex justify-content-center align-items-center">
                    <span>!! Attenzione Pagamento Non Presente !!</span>
                </div>
            @endif
        </div>
    </div>


    <style>
        .card {
            background-color: hsl(257.14deg 35% 92.16%) !important;
            border-radius: 25px;
            border: none;
        }

        th,
        td,
        tr {
            background-color: hsl(257.14deg 35% 92.16%) !important;
            border-radius: 25px;
            border: none;
        }

        .bg-purple {
            background-color: hsl(256.36deg 37.29% 53.73%) !important;
        }
    </style>

    <script
        src="https://www.paypal.com/sdk/js?client-id=ARJ0V5nK822d1uryQ-Ox70cDXlOwJHVItyABiAkUddkMWnlZ4C04BvIHiPkc_UddkASQGhEmYOpSauwE">
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", (event) => {
            paypal.Buttons({
                async createOrder() {
                    const response = await fetch('https://webservice.paxypay.com/api/createOrder', {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                        },
                        body: JSON.stringify({
                            cart: [{
                                sku: "10",
                                quantity: "1",
                            }]
                        })
                    });

                    const order = await response.json();
                    return order.id;
                },
                onApprove: async function(data, actions) {

                    let isCompleted = false;
                    try{

                        const response = 
                            await fetch(
                                'https://webservice.paxypay.com/api/onApprove', 
                                {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json'
                                    },
                                    body: JSON.stringify(
                                        {
                                            orderID: data.orderID
                                        }
                                    )
                                }
                            );
                        
                        if(response.status == 200){
                            const payloadResponse = await response.json();
                            
                            if(payloadResponse.status == "COMPLETED"){
                                isCompleted = true;
                                alert('Pagamento pagato con successo da ' + payloadResponse.payer.name.given_name);
                            }
                        }

                    }catch(e){
                        console.log(e);
                    }finally{

                        if(isCompleted){
                            // Completato
                        }else{
                            // Non completato
                        }

                    }

                },
                onError: function(err) {
                    console.error('An error occurred:', err);
                    alert('An error occurred during the transaction. Please try again.');
                },
            
            }).render('#paypal-button-container');
        });
    </script>

    <script>
        function checkStripeCheckbox() {
            var checkbox = document.getElementById('stripeCheckbox');
            if (checkbox.checked) {
                return true;
            } else {
                alert('Per favore, accetta le policy d\'uso e privacy.');
                return false;
            }
        }
    </script>

@endsection
