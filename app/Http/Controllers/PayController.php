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

class PayController extends Controller
{
    public function show($token)
    {
        $payment = Payment::where('token', $token)->first();
        // $payment->views += 1;
        if ($payment->status === 'paid') {
            return back();
        };
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
            \Stripe\Stripe::setApiKey($settings['payMethods']['stripe']['privateKey']);
            $YOUR_DOMAIN = env('APP_URL');
            $discount_amount = 0;
            $line_items = [];

            foreach ($payment->products as $product) {
                if ($product['product_price'] > 0) {
                    $line_items[] = [
                        'price_data' => [
                            'currency' => 'eur',
                            'product_data' => [
                                'name' => isset($product['product_name']) ? $product['product_name'] : 'prodotto',
                            ],
                            'unit_amount' => $product['product_price'] * 100,
                        ],
                        'quantity' => $product['quantity'],
                    ];
                } else {
                    $discount_amount += $product['product_price'] * $product['quantity'];
                }
            }

            $session_params = [
                'customer_creation' => "always",
                'line_items' => $line_items,
                'mode' => 'payment',
                'success_url' => $YOUR_DOMAIN . '/success',
                'cancel_url' => $YOUR_DOMAIN . '/checkout',
            ];

            if ($discount_amount < 0) {
                $coupon = \Stripe\Coupon::create([
                    'amount_off' => abs($discount_amount) * 100, // Sconto in centesimi
                    'currency' => 'eur',
                ]);

                $session_params['discounts'] = [
                    [
                        'coupon' => $coupon->id,
                    ],
                ];
            }

            $request->session()->put('payment_id', $payment->id);
            $session = \Stripe\Checkout\Session::create($session_params);

            $payment->checkout_id = $session['id'];
            $payment->payment_method = "stripe";
            $payment->save();

            return redirect()->away($session->url);
        }
    }

    public function satispay(Request $request, Payment $payment)
    {
        $amount = $request->input('amount');
        $description = $request->input('description');

        // Simula la creazione del checkout
        $checkoutUrl = 'https://staging.authservices.satispay.com/'; // Sostituisci con un URL di checkout simulato
    }
    public function success(Request $request)
    {
        if ($request->session()->has('payment_id')) {
            $paymentId = $request->session()->get('payment_id');
            $payment = Payment::find($paymentId);
            $payment = Payment::find($paymentId);

            $user = $payment->user;
            $payment->status = 'paid';
            $payment->paid_date = now()->setTimezone('Europe/Rome');;


            $settings = json_decode($user->settings, true);
            if ($payment->payment_method == 'stripe') {
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

        return view('pay.success');
    }
}
