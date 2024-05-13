<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Mail\PaymentReceived;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use \Stripe\Charge;
use \Stripe\Stripe;
use \Stripe\Checkout\Session;
use App\Mail\PaymentConfirmation;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Stripe\Customer;
use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Orders\OrdersCreateRequest;
use PayPalHttp\HttpException;
use Illuminate\Support\Facades\Http;
use SatispayGBusiness\Api;

class PayController extends Controller
{
    public function show($token)
    {
        $payment = Payment::where('token', $token)->first();
        $payment->views += 1;
        $payment->save();
        $user = $payment->user;
        $settings = json_decode($user->settings, true);
        if ($payment) {
            $user = $payment->user;
        }

        return view('pay.show', ['pay' => $payment], compact('payment', 'user', 'settings'));
    }
    public function stripe(Request $request, Payment $payment)
    {
        $user = $payment->user;
        $settings = json_decode($user->settings, true);

        if ($settings['payMethods']['stripe']['active'] == 1) {
            $settings = json_decode($user->settings, true);

            \Stripe\Stripe::setApiKey($settings['payMethods']['stripe']['privateKey']);
            $YOUR_DOMAIN = env('APP_URL');
            $total_amount = 0;
            $line_items = [];
            foreach ($payment->products as $product) {
                $total_amount += $product['product_price'] * $product['quantity'];

                // vecchia logica che passava tutti i prodotti 
                // $line_items[] = [
                //     'price_data' => [
                //         'currency' => 'eur',
                //         'product_data' => [
                //             'name' => isset($product['product_name']) ? $product['product_name'] : 'prodotto',
                //         ],
                //         'unit_amount' => $product['product_price'] * 100,
                //     ],
                //     'quantity' => $product['quantity'],
                // ];
            }
            $total = [
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => 'totale',
                    ],
                    'unit_amount' => $total_amount * 100,
                ],
                'quantity' => 1,
            ];
            $request->session()->put('payment_id', $payment->id);
            $session = \Stripe\Checkout\Session::create([
                'customer_creation' => "always",
                'line_items' =>  [$total],
                'mode' => 'payment',
                'success_url' => $YOUR_DOMAIN . '/success',
                'cancel_url' => $YOUR_DOMAIN . '/checkout',
            ]);

            $payment->checkout_id = $session['id'];
            $payment->payment_method = "stripe";
            $payment->save();
            return redirect()->away($session->url);
        }
    }
    public function paypal(Request $request, Payment $payment)
    {
        $user = $payment->user;
        $settings = json_decode($user->settings, true);

        if ($settings['payMethods']['paypal']['active'] == 1) {
            $settings = json_decode($user->settings, true);

            $client = new Client();

            $clientId = $settings['payMethods']['paypal']['client_id'];
            $clientSecret = $settings['payMethods']['paypal']['secret_key'];

            // Effettua la richiesta POST per ottenere il token di autenticazione
            $response = $client->post('https://api.sandbox.paypal.com/v1/oauth2/token', [
                'form_params' => [
                    'grant_type' => 'client_credentials',
                ],
                'headers' => [
                    'Authorization' => 'Basic ' . base64_encode($clientId . ':' . $clientSecret),
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
            ]);

            $body = $response->getBody()->getContents();
            $data = json_decode($body);

            $accessToken = $data->access_token;
            $baseUrl = env('APP_URL');
            // Effettua la richiesta POST per creare l'ordine di pagamento
            $response = Http::withBasicAuth($clientId, $clientSecret)
                ->post('https://api.sandbox.paypal.com/v2/checkout/orders', [
                    'intent' => 'CAPTURE',
                    'purchase_units' => [
                        [
                            'amount' => [
                                'currency_code' => 'EUR',
                                'value' => $payment->total_price,
                            ],
                        ],

                    ],
                    'application_context' => [
                        'return_url' => $baseUrl .'/success',
                        'cancel_url' => 'https://example.com/checkout/cancel',
                    ],
                ]);

            if ($response->successful()) {
                $order = $response->json();
                $orderId = $order['id'];
                $request->session()->put('payment_id', $payment->id);
                return redirect($order['links'][1]['href']);
            } else {
                // Gestisci l'errore
                return $response->body();
            }
            
        }
    }
    public function satispay(Request $request, Payment $payment){

        $api = new Api(env('SATISPAY_KEY'), env('SATISPAY_SECRET'));
    
        $response = $api->createPayment([
            'flow' => 'MATCH_CODE',
            'amount_unit' => 100, // Importo dell'ordine in centesimi di euro
            'currency' => 'EUR',
            'metadata' => [
                'order_id' => '123456', // ID dell'ordine nel tuo sistema
            ],
        ]);

            // Controlla se la richiesta di pagamento è stata completata con successo
        if ($response['status_code'] == 200) {
            // Recupera il token di pagamento
            $paymentToken = $response['payment']['payment_token'];

            // Ora puoi utilizzare $paymentToken per confermare effettivamente il pagamento
            // Utilizza $paymentToken con l'SDK Satispay per confermare il pagamento

            // Ad esempio:
            $confirmationResponse = $api->confirmPayment([
                'payment_token' => $paymentToken,
            ]);

            // Verifica se il pagamento è stato confermato con successo
            if ($confirmationResponse['status_code'] == 200) {
                // Il pagamento è stato confermato con successo
                // Puoi procedere con le azioni necessarie dopo il pagamento
            } else {
                // Gestisci l'errore di conferma del pagamento
            }
        } else {
            // Gestisci l'errore nella creazione dell'intenzione di pagamento
        }
    }
    public function success(Request $request)
    {
  
        $paymentId = $request->session()->get('payment_id');
        $payment = Payment::find($paymentId);

        if ($paymentId) {
            $payment = Payment::find($paymentId);

            $user = $payment->user;
            $payment->status = 'paid';
            $payment->paid_date = now()->setTimezone('Europe/Rome');;
           
         
            $settings = json_decode($user->settings, true);
            if($payment->payment_method == 'stripe'){
                $client = new Client();
                $settings = json_decode($user->settings, true);
                $headers = [
                    'Authorization' => 'Bearer ' . $settings['payMethods']['stripe']['privateKey'],
                    'Content-Type' => 'application/json',
                ];
                // Disabilita la verifica del certificato SSL
                $options = [
                    'verify' => false,
                ];
    
                $url = 'https://api.stripe.com/v1/checkout/sessions/' . $payment->checkout_id;
                $res = $client->get($url, [
                    'headers' => $headers,
                    'verify' => false,
                ]);
                $body = $res->getBody()->getContents();
                $customerDetails = json_decode($body);
    
                $customer_name = $customerDetails->customer_details->name;
                $customer_email = $customerDetails->customer_details->email;
                $payment->customer_email = $customer_email;
                $payment->customer_name = $customer_name;
                $payment->save();
                Mail::to($user->email)->send(new PaymentReceived($payment));
           
            }
                // Rimuovi l'ID del pagamento dalla sessione
                $request->session()->forget('payment_id');

                // Visualizza la vista di successo
                return view('pay.success', compact('payment'));
            
        }
    

       
    }

    // public function createPayPalOrder(Request $request)
    // {
    //     // Logica per creare un nuovo ordine di pagamento PayPal
    //     // Utilizza l'API di PayPal per creare un nuovo ordine di pagamento
    //     // Restituisci l'URL di approvazione PayPal

    //     // Esempio di implementazione di base per scopi illustrativi
    //     $paymentId = $request->input('payment_id');
    //     $approvalUrl = 'https://www.paypal.com/checkout';

    //     return response()->json(['approval_url' => $approvalUrl]);
    // }

}
