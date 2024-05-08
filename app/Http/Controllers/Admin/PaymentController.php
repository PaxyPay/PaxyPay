<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentStoreRequest;
use App\Http\Requests\PaymentUpdateRequest;
use App\Models\Cart;
use App\Models\Payment;
use Illuminate\Support\Facades\Session;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Stripe\Charge;
use Stripe\Stripe;

class PaymentController extends Controller
{


    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        $imageData = $user->image;
        $data_encode = $user->settings;

        $settings = json_decode($data_encode, true);

        // Recupera il valore perPage dalla richiesta, se presente, altrimenti dalla sessione
        $perPage = $request->input('perPage', $settings['perPage']);

        // Recupera il valore perPage dalla richiesta, se presente, altrimenti dalla sessione

        $keyword = $request->input('keyword');
        $active = $request->input('active');
        $status = $request->input('paid');
        $query = Payment::where('user_id', $user->id);

        if (!empty($keyword)) {
            $query->where(function ($query) use ($keyword) {
                $query->where('client_name', 'LIKE', "%$keyword%")
                    ->orWhere('description', 'LIKE', "%$keyword%")
                    ->orWhereHas('products', function ($query) use ($keyword) {
                        $query->where('product_name', 'LIKE', "%$keyword%");
                    });
            });
        }

        if ($active !== null) {
            if ($active == 1) {
                $query->where('active', 1);
            } else if ($active == 2) {
                $query->where('active', 0);
            }
        }
        if ($status !== null) {
            $query->where('status', $status);
        }
        $column = $request->input('column', $settings['orderBy']);
        $order = $request->input('order', $settings['orderFor']);

        $query->orderBy($column, $order);

        // Esegui la query con la paginazione utilizzando il valore $perPage
        $payments = $query->paginate($perPage);

        // Salva il valore $perPage nella sessione per l'utente
        Session::put('perPage', $perPage);



        $query->orderBy($column, $order);

        // Esegui la query con la paginazione utilizzando il valore $perPage
        $payments = $query->paginate($perPage);

        // Salva il valore $perPage nella sessione per l'utente
        Session::put('perPage', $perPage);

        // Reindirizza l'utente all'ultima pagina se la pagina corrente è superiore all'ultima pagina disponibile
        if ($payments->currentPage() > $payments->lastPage()) {
            $lastPage = $payments->lastPage();
            return redirect()->route('admin.payment.index', ['page' => $lastPage]);
        }

        $settings['perPage'] = $perPage;
        $settings['orderBy'] = $column;
        $settings['orderFor'] = $order;

        $data_update = json_encode($settings);

        $user->settings = $data_update;
        $user->save();

        return view('admin.payment.index', compact('payments', 'user', 'settings', 'imageData'))->with('perPage', $perPage);
    }



    public function show(Payment $payment)
    {
        return view('admin.payment.show', compact('payment'));
    }


    public function create()
    {
        return view('admin.payment.create');
    }

    public function store(PaymentStoreRequest $request)
    {
        $data = $request->validated();
        $userID = auth()->user()->id;
    
        // Creazione del nuovo pagamento
        $payment = Payment::create([
            'client_name' => $data['client_name'],
            'description' => $data['description'],
            'due_date' => $data['due_date'],
            'total_price' => 0,
            'token' => uniqid(), // Genera un token unico
            'paid' => false,
            'active' => isset($data['active']) ? true : false,
            'user_id' => $userID,
        ]);
    
        // Calcolo del prezzo totale del pagamento e salvataggio
        $totalPrice = 0;
    
        foreach ($data['products'] as $productData) {
            $product = new Product([
                'product_name' => $productData['product_name'],
                'quantity' => $productData['quantity'],
                'product_price' => $productData['product_price'],
            ]);
    
            $totalPrice += $product->quantity * $product->product_price;
    
            $payment->products()->save($product);
        }
    
        $payment->total_price = $totalPrice;
        $payment->save();
    
        return view('admin.payment.create_success', compact('payment'));
    }
    


    public function edit(Payment $payment)
    {
        if ($payment->status == 'paid') {
            return redirect()->route('admin.payment.show', compact('payment'));
        } else {
            $statusValues = Payment::getStatusValues();
            $products = $payment->product;
            return view('admin.payment.edit', compact('payment', 'statusValues'));
        };
    }

    public function update(PaymentUpdateRequest $request, Payment $payment)
    {
        $payment = Payment::findOrFail($payment->id);

        // Ottieni i dati validati dal form
        $data = $request->validate($request->rules());

        // Elimina tutti i prodotti associati al pagamento
        $payment->products()->delete();

        // Aggiorna i dati del pagamento
        $payment->update([
            'client_name' => $data['client_name'],
            'description' => $data['description'],
            'due_date' => $data['due_date'],
            'active' => isset($data['active']) ? true : false,
            // 'total_price' => $data['total_price'],
        ]);
        $totalPrice = 0;
        // Aggiungi i nuovi prodotti associati al pagamento


        foreach ($data['products'] as $product) {
            if (empty($product['product_name'])) {
                // Se il nome del prodotto è vuoto, assegna una stringa vuota al nome del prodotto
                $product['product_name'] = '';
            }
            $new_product = new Product();
            $new_product->product_name = $product['product_name'];
            $new_product->quantity = $product['quantity'];
            $new_product->product_price = $product['product_price'];
            $new_product->payment_id = $payment->id;

            $price_row = $product['quantity'] * $product['product_price'];
            $totalPrice += $price_row;
            $new_product->save();
            $payment->total_price = $totalPrice;
        }
        $payment->save();


        return redirect()->route('admin.payment.index');
    }

    public function destroy(Payment $payment)
    {
        $payment->delete();
        return redirect()->route('admin.payment.index');
    }

    public function search(Request $request)
    {
        $user = Auth::user();
        $keyword = $request->input('keyword');
        $active = $request->input('active');
        $perPage = $request->input('perPage');
        $query = Payment::where('user_id', $user->id);

        // Condizione di ricerca per la parola chiave
        if (!empty($keyword)) {
            $query->where(function ($query) use ($keyword) {
                $query->where('client_name', 'LIKE', "%$keyword%")
                    ->orWhere('description', 'LIKE', "%$keyword%")
                    ->orWhereHas('products', function ($query) use ($keyword) {
                        $query->where('product_name', 'LIKE', "%$keyword%");
                    });
            });
        }

        // Condizione per lo stato del pagamento
        if ($active !== null) {
            $query->where('active', $active);
        }

        // Esegui la query e paginazione
        $payments = $query->paginate($perPage);

        return view('admin.payment.index', compact('payments'));
    }
    public function updatePerPage(Request $request)
    {
        // Salva il nuovo valore per il numero di elementi per pagina nella sessione
        session(['perPage' => $request->perPage]);

        // Reindirizza l'utente alla pagina precedente
        return back();
    }
    public function copyCreate(Payment $payment)
    {

        return view('admin.payment.copy', compact('payment'));
    }
}
