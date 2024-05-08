<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentStoreRequest;
use App\Http\Requests\PaymentUpdateRequest;
use App\Models\Cart;
use App\Models\Payment;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller

{
    public function index()
    {
    }
    public function create(Request $request)
    {

        return response()->json(['payment' => 'ciao'], 201);
        // Verifica che l'utente sia autenticato e ha il token OAuth2 valido
        if (!$request->user()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
      
        // Validazione dei dati (se necessario)
        // ...

        // Creazione del pagamento
        $requestData = json_decode($request->getContent(), true);

        $payment = new Payment();
        $payment->user_id = $requestData['user_id'];
        $payment->client_name = $requestData['client_name'];
        $payment->total_price = $requestData['total_price'];
        $payment->due_date = $requestData['due_date'];
        $payment->description = $requestData['description'];
        $payment->status = $requestData['status'];
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
        }

        return response()->json(['payment' => $payment], 201);
    }



    public function store()
    {
    }
    public function destroy()
    {
    }
}

// {
//     "user_id": "1",
//     "client_name": "",
//     "total_price": "",
//     "due_date": "",
//     "description": "",
//     "status": "",
//     "active": "",
//     "products": [
//         {
//             "product_name": "",
//             "quantity": "",
//             "product_price": ""
//         }
//     ]
// }
