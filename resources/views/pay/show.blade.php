@extends('layouts.client')
@section('content')
    <div class="background background1 visible"></div>
    <div class="background background2"></div>

    <div class="container mt-5 p-4 max-container">
        <div class="row p-md-5 p-2 glass">
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
                    <div class="m-3 d-flex justify-content-center flex-column align-items-center">
                        <form action="{{ route('pay.stripe', $payment->id) }}" method="POST"
                            onsubmit="return checkStripeCheckbox()">
                            @csrf
                            <div class="mt-2"></div>
                            @if ($settings['payMethods']['stripe']['active'] == 0)
                            @else
                                <button class="btn btn-success m-2" type="submit">Paga con Carta</button>
                            @endif
                        </form>
                        <div class="mt-2" id="paypal-button-container"></div>
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

        .glass {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
            -webkit-backdrop-filter: blur(10px);
            backdrop-filter: blur(5px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            overflow-y: aut;
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

        /* body {
                            background-image: url('https://images.unsplash.com/photo-1545579133-99bb5ab189bd?q=80&w=2070&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D');
                        } */

                        body, html {
    margin: 0;
    padding: 0;
    height: 100%;
    overflow: hidden;
    position: relative;
}

.background {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-size: cover;
    background-position: center;
    opacity: 0;
    transition: opacity 1.5s ease-in-out;
    z-index: 0;
}

.visible {
    opacity: 1;
    z-index: 1;
}

.hidden {
    z-index: 0;
}

.container {
    position: relative;
    z-index: 2; /* Ensures the container is above the background */
   
}
    </style>

    <script
        src="https://www.paypal.com/sdk/js?client-id=ARJ0V5nK822d1uryQ-Ox70cDXlOwJHVItyABiAkUddkMWnlZ4C04BvIHiPkc_UddkASQGhEmYOpSauwE">
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", (event) => {
            const images = [
                'https://images.unsplash.com/photo-1545579133-99bb5ab189bd?q=80&w=2070&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
                'https://images.unsplash.com/photo-1714572877812-7b416fbd4314?q=80&w=1974&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
                
                'https://images.unsplash.com/photo-1715646528606-1f0a4f2db091?q=80&w=1974&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
                'https://images.unsplash.com/photo-1683610959796-b5eda734af7d?q=80&w=1974&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
                'https://images.unsplash.com/photo-1715673336295-9487981ab5fd?q=80&w=2070&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
                'https://images.unsplash.com/photo-1715607347255-8ab4816bf923?q=80&w=1974&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
                'https://images.unsplash.com/photo-1714987524876-f5f3cc746013?q=80&w=1964&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
                'https://images.unsplash.com/photo-1700234272590-9202ed1758c3?q=80&w=1964&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
                'https://images.unsplash.com/photo-1715514922735-f5020e67f98a?q=80&w=1974&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D'
            ];

            let currentIndex = 0;
            let currentBackground = 1;
            const background1 = document.querySelector('.background1');
            const background2 = document.querySelector('.background2');

            function changeBackground() {
                currentIndex = (currentIndex + 1) % images.length;
                const nextBackground = currentBackground === 1 ? background2 : background1;
                const currentVisible = currentBackground === 1 ? background1 : background2;

                nextBackground.style.backgroundImage = `url(${images[currentIndex]})`;
                nextBackground.classList.add('visible');
                currentVisible.classList.remove('visible');

                currentBackground = currentBackground === 1 ? 2 : 1;
            }

      
            background1.style.backgroundImage = `url(${images[currentIndex]})`;
            background1.classList.add('visible');


            setInterval(changeBackground,5000);
            paypal.Buttons({
                style: {
                layout: 'vertical',   // Layout verticale
                color: 'gold',        // Colore del pulsante (oro)
                shape: 'rect',        // Forma del pulsante (rettangolare)
                label: 'paypal'       // Etichetta del pulsante (PayPal)
            },
                async createOrder() {
                    const response = await fetch('https://webservice.paxypay.com/api/createOrder', {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                        },
                        body: JSON.stringify({
                            amount : {{$payment->total_price}};
                        })
                    });
                    const order = await response.json();
                    return order.id;
                },
                onApprove: async function(data, actions) {

                    let isCompleted = false;
                    try {

                        const response =
                            await fetch(
                                'https://webservice.paxypay.com/api/onApprove', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json'
                                    },
                                    body: JSON.stringify({
                                        orderID: data.orderID,
                                        paymentId: {{ $payment->id }}
                                    })
                                }
                            );
                        if (response.status == 200) {
                            const payloadResponse = await response.json();
                            if (payloadResponse.status == "COMPLETED") {
                                isCompleted = true;
                                window.location.href = '/success';
                            }
                        } else if (response.status == 500) {
                            const payloadResponse = await response.json();

                            isCompleted = false;
                            alert('PAGAMENTO GIA PAGATO')

                        }
                    } catch (e) {
                        console.log(e);
                    } finally {

                        if (isCompleted) {
                            // Completato
                        } else {
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
