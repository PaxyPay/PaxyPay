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

            const clientId = 'ARJ0V5nK822d1uryQ-Ox70cDXlOwJHVItyABiAkUddkMWnlZ4C04BvIHiPkc_UddkASQGhEmYOpSauwE';
            const clientSecret = 'EMWW60COgp5_7KeDfF1c6l1nlKwxdxUOTUXzBBCnCxAEsadM4AEAZX8QbHP-VdvECRXGF_qKD-LOmaz_';
            let accessToken = '';
            paypal.Buttons({
                async createOrder() {
                    // const response = await fetch('https://paxypay.com/api/createOrder', {
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
                async getAccessToken(clientId, clientSecret) {
                    const url = 'https://api-m.sandbox.paypal.com/v1/oauth2/token';
                    const credentials = btoa(`${clientId}:${clientSecret}`);

                    try {
                        const response = await fetch(url, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                                'Authorization': `Basic ${credentials}`
                            },
                            body: 'grant_type=client_credentials'
                        });

                        if (!response.ok) {
                            throw new Error(`Error obtaining access token: ${response.statusText}`);
                        }

                        const data = await response.json();
                        accessToken = data.access_token;
                        console.log(accessToken);
                        return data.access_token;
                    } catch (error) {
                        console.error('Error:', error);
                        return null;
                    }
                },
              
                onApprove: async function(data, actions,) {
                    
                    try {
                        const orderID = data.orderID;
                        const accessToken = await getAccessToken();
                        const response = await fetch(
                            `https://api-m.sandbox.paypal.com/v2/checkout/orders/${orderID}/capture`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Authorization': `Bearer ${accessToken}`
                                },
                                body: JSON.stringify({})
                            });

                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }

                        const data = await response.json();
                        console.log('Order captured successfully:', data);
                        // Gestisci la logica di successo qui
                    } catch (error) {
                        console.error('Error capturing order:', error);
                        alert('There was an error processing your payment. Please try again.');
                    }
                }
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
