<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    
    public function __construct()
    {
        # By default we are using here auth:api middleware 
        $this->middleware('auth:api', []);
    }

    public function create(Request $request)
    {
     
        // Verifica che l'utente sia autenticato e ha il token OAuth2 valido
        if (!$request->user()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        // return response()->json(['request'=>$request->getContent()]);

        // Creazione del pagamento
        $requestData = json_decode($request->getContent(), true);
       
        $user = User::where('token', $requestData['token'])->firstOrFail();
      
        $randomString = uniqid();
        $md5Hash = md5(uniqid($randomString, true));

        $payment = new Payment();;     
        $payment->user_id = $user->id;
        $payment->token = $md5Hash;
        $payment->client_name = $requestData['client_name'];
        $payment->due_date = $requestData['due_date'];
        $payment->description = $requestData['description'];
        $payment->total_price = 0;
        $payment->status = 'not_paid';
        $payment->webhook = $requestData['webhook'] ? $requestData['webhook'] : '';
        $payment->active = $requestData['active'];
        $payment->save();

        // Creazione dei prodotti associati al pagamento
        foreach ($requestData['products'] as $productData) {
            $product = new Product();
            $product->payment_id = $payment->id; // Associa il prodotto al pagamento appena creato
            $product->product_name = $productData['product_name'];
            $product->quantity = $productData['quantity'];
            $product->product_price = $productData['product_price'];
            $product->save();
            $payment->total_price += $product->product_price * $product->quantity;
        }
        $payment->save();

        return response()->json(['payment' => $payment,'status' => 'success','link' => 'https://paxypay.com/pay/'.$payment->token], 201);
    }
    public function status(Request $request)
    {
        //  Verifica che l'utente sia autenticato e ha il token OAuth2 valido
         if (!$request->user()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $requestData = $request->json()->all();
        if (!isset($requestData['token'])) {
            return response()->json(['error' => 'Token not provided'], 400);
        }

        $payment = Payment::where('token', $requestData['token'])->firstOrFail();
        if (!$payment) {
            return response()->json(['error' => 'Payment not found'], 404);
        }
        return response()->json(['payment' => $payment,'status' => 'success'], 201);
       
    }

    public function filter(Request $request, Payment $payment)
    {
        // Ottieni i dati inviati con la richiesta
        $requestData = $request->json()->all();
    
        // Estrai il token dalla richiesta
        $token = $requestData['token'];
    
        // Cerca l'utente basato sul token
        $user = User::where('token', $token)->firstOrFail();
    
        // Verifica se l'utente è autenticato
        if (!$request->user()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    
        // Recupera tutti i pagamenti associati all'utente
        $payments = Payment::where('user_id', $user->id);
    
        // Filtra i pagamenti in base allo stato, se specificato
        $status = $request->query('status');
        if ($status) {
            $payments->where('status', $status);
        }
    
        // Filtra i pagamenti in base all'attivazione, se specificato
        $active = $request->query('active');
        if ($active !== null) {
            $payments->where('active', $active);
        }
    
        // Filtra i pagamenti in base al nome del cliente, se specificato
        $name = $request->query('name');
        if ($name) {
            $payments->where('client_name', 'like', '%' . $name . '%');
        }
    
        // Filtra i pagamenti in base alla data compresa tra due date specifiche
        $start_date = $request->query('start_date');
        $end_date = $request->query('end_date');
        if ($start_date && $end_date) {
            $payments->whereBetween('created_at', [$start_date, $end_date]);
        }
    
        // Filtra i pagamenti in base alla data specifica in poi
        $from_date = $request->query('from_date');
        if ($from_date) {
            $payments->whereDate('created_at', '>=', $from_date);
        }
        $limit = $request->query('limit');
        if ($limit) {
            $payments->take($limit);
        }
        // Esegui la query per ottenere i risultati
        $filteredPayments = $payments->get();
        $resultsCount = $filteredPayments->count();
        // Restituisci i pagamenti filtrati
        return response()->json(['results' => $resultsCount, 'payments' => $filteredPayments, 'status' => 'success'], 200);
    }
    
    // public function testWebhook(Request $request){
    //     $data = $request;
    //     $payment = new Payment();
    //     $payment->description = $request->description;
    //     $payment->token = $request->token;
        
    //     $payment->save();

    //     return response()->json(['status' => true, 'data' => $data]);
    // }
}

