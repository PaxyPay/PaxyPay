<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{


    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        // dd('ciao');
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    public function update(Request $request)
    {
        $data = $request;
        // Recupera l'utente attualmente autenticato o qualsiasi altro metodo per ottenere l'ID dell'utente
        $user = Auth::user();
        // dd($data);
        // Verifica se è stata caricata un'immagine
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('uploads', 'public');
            // dd($imagePath);
            $imageUrl = Storage::url($imagePath);
            // Salva l'URL dell'immagine nel campo 'image' del modello User
            $user->image = $imageUrl;
        }
   
        // $imagePath = $request->file('image')->storePublicly('uploads','public');
        // $imageUrl = asset('storage/'. str_replace('public','',$imagePath));
        // $data['image'] = $imageUrl;
  
         // Salva eventuali altri dati
        $user->fill($request->except('image'));
        
        // Salva le modifiche nel database
        $user->save();
        return Redirect::route('profile.edit')->with('status', 'profile-updated');
        // return redirect()->route('dashboard', $company->id);
    }

    /**
     * Update the user's profile information.
     */
    // public function update(ProfileUpdateRequest $request)
    // {
    //     dd($request);
        
    //     $request->user()->fill($request->validated());

    //     if ($request->user()->isDirty('email')) {
    //         $request->user()->email_verified_at = null;
    //     }
        
    //     // if ($request->hasFile('image')) {
    //     //     $imagePath = $request->file('image')->storePublicly('uploads', 'public');
    //     //     $imageUrl = asset('storage/' . str_replace('public/', '', $imagePath));
    //     //     $request->user()->image = $imageUrl; // Assicurati che 'image' sia un campo stringa nel modello dell'utente
    //     // }
    //     $imagePath = $request->file('image')->store('uploads', 'public');
    //     $imageUrl = Storage::url($imagePath);
    //     $data['image'] = 'ciao';

    //     $request->user()->fill($data);
    //     $request->user()->save();

    //     return Redirect::route('profile.edit')->with('status', 'profile-updated');
    // }



    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    public function settings()
    {
        $user = auth()->user();
        $settings = json_decode($user->settings, true);
        $orderByValue = $settings['orderBy'] ?? null;
        $orderForValue = $settings['orderFor'] ?? null;
        $orderPerPageValue = $settings['perPage'] ?? null;
        $user->settings = json_encode($settings);
        $user->save();
        return view('profile.partials.settings', compact('user', 'settings', 'orderByValue', 'orderPerPageValue', 'orderForValue'));
    }

    public function stripe(Request $request)
    {
        $user = auth()->user();
        $settings = json_decode($user->settings, true);

        if ($request['active'] == null) {
            $request['active'] = 0;
        };
        $requestData = $request->all();


        $active = $requestData['active'];
        $public_key = $requestData['public_key'];
        $private_key = $requestData['private_key'];


        $settings['payMethods']['stripe']['active'] = $active;
        $settings['payMethods']['stripe']['publickey'] = $public_key;
        $settings['payMethods']['stripe']['privateKey'] = $private_key;
        $orderByValue = $settings['orderBy'] ?? null;
        $orderForValue = $settings['orderFor'] ?? null;
        $orderPerPageValue = $settings['perPage'] ?? null;

        $user->settings = json_encode($settings);

        $user->save();
        return view('profile.partials.settings', compact('user', 'settings', 'orderByValue', 'orderPerPageValue', 'orderForValue'));
    }

    public function paypal(Request $request)
    {
        $user = auth()->user();
        $settings = json_decode($user->settings, true);

        if ($request['activePayPal'] == null) {
            $request['activePayPal'] = 0;
        };

        $requestData = $request->all();
     
       

        $active = $request['activePayPal'];
        $client_id = $requestData['PayPalClientId'];
        $client_secret = $requestData['PayPalSecretKey'];

        $settings['payMethods']['paypal']['active'] = $active;
 
        $settings['payMethods']['paypal']['client_id'] = $client_id;
        $settings['payMethods']['paypal']['secret_key'] = $client_secret;
        $orderByValue = $settings['orderBy'] ?? null;
        $orderForValue = $settings['orderFor'] ?? null;
        $orderPerPageValue = $settings['perPage'] ?? null;
        $user->settings = json_encode($settings);

        $user->save();
        return view('profile.partials.settings', compact('user', 'settings', 'active', 'client_id', 'client_secret','orderByValue', 'orderPerPageValue', 'orderForValue'));
    }

    public function updateSettings(Request $request)
    {
        $user = auth()->user();
        $settings = json_decode($user->settings, true);
        $requestData = $request->all();
        $settings['orderBy'] = $requestData['orderBy'];
        $settings['orderFor'] = $requestData['orderFor'];
        $settings['perPage'] = $requestData['perPage'];

        $orderByValue = $settings['orderBy'] ?? null;
        $orderForValue = $settings['orderFor'] ?? null;
        $orderPerPageValue = $settings['perPage'] ?? null;

        $user->settings = json_encode($settings);
        $user->save();
        return view('profile.partials.settings', compact('user', 'settings', 'orderByValue', 'orderPerPageValue', 'orderForValue'));
    }

    public function dashboard(Request $request)
    {
        $user = auth()->user();

        $keyword = $request->input('keyword');
        $dateStart = $request['date_start'];
        $dateEnd = $request['date_end'];
        if (empty($dateStart) && empty($dateEnd)) {
            // Se entrambe le date sono vuote, imposta entrambe le date a oggi
            $dateStart = now()->format('Y-m-d');
            $dateEnd = now()->format('Y-m-d');
        } elseif (empty($dateStart)) {
            // Se solo la data di inizio è vuota, cerca fino alla data di fine e indietro
            $dateStart = $dateEnd;
        } elseif (empty($dateEnd)) {
            // Se solo la data di fine è vuota, cerca dalla data di inizio in poi
            $dateEnd = now()->format('Y-m-d');
        }

        $paymentsQuery = $user->payments()->whereBetween('created_at', [$dateStart, $dateEnd]);
        if (!empty($keyword)) {
            $paymentsQuery->where('description', 'like', "%$keyword%");
        }
        $payments = $paymentsQuery->get();
        $totalPayments = $payments->count();
        $totalPaymentsAmmount = $payments->sum('total_price');
        $totalpaymentsPaid = $user->payments()->where('status', 'paid')->whereBetween('created_at', [$dateStart, $dateEnd])->get();
        $totalPaymentsPaidAmmount = $totalpaymentsPaid->sum('total_price');

        $year = $request->input('year');
        if (empty($year)) {
            $year = now()->year;
        }
        $currentYear = $request['year'];

        $yearPayments = $user->payments()->whereYear('created_at', $year)->get();
        $yearTotalPayments = $yearPayments->count();
        $yearTotalPaymentsAmmount = $yearPayments->sum('total_price');
        $yearTotalPaymentsPaid = $user->payments()->whereYear('created_at', $year)->where('status', 'paid')->get();
        $yearTotalPaymentsPaidAmmount = $yearTotalPaymentsPaid->sum('total_price');

           // Array dei mesi
           $months = [
            '01' => 'Gennaio',
            '02' => 'Febbraio',
            '03' => 'Marzo',
            '04' => 'Aprile',
            '05' => 'Maggio',
            '06' => 'Giugno',
            '07' => 'Luglio',
            '08' => 'Agosto',
            '09' => 'Settembre',
            '10' => 'Ottobre',
            '11' => 'Novembre',
            '12' => 'Dicembre'
        ];
        // Query per ottenere i totali mensili dei pagamenti
        $paymentsQuery2 = $user->payments();
        $monthlyPayments = $paymentsQuery2
            ->select(
                DB::raw('DATE_FORMAT(created_at, "%m") as month'),
                DB::raw('SUM(total_price) as total_amount'),
                DB::raw('SUM(CASE WHEN status = "paid" THEN total_price ELSE 0 END) as total_paid_amount'),
                DB::raw('COUNT(id) as payment_count'),
                DB::raw('SUM(CASE WHEN status = "paid" THEN 1 ELSE 0 END) as paid_payment_count')
            )
            ->whereYear('created_at', $year)
            ->groupBy('month')
            ->get();

        $monthlyData = [];
        foreach ($monthlyPayments as $payment) {
            $monthlyData[$payment->month] = [
                'month' => $months[$payment->month],
                'total_amount' => $payment->total_amount,
                'total_paid_amount' => $payment->total_paid_amount,
                'payment_count' => $payment->payment_count,
            ];
        }

        // Riempimento dei mesi mancanti
        foreach ($months as $key => $month) {
            if (!isset($monthlyData[$key])) {
                $monthlyData[$key] = [
                    'month' => $month,
                    'total_amount' => 0,
                    'total_paid_amount' => 0,
                    'payment_count' => 0,
                ];
            }
        }

        // Ordinamento dei dati per mese
        ksort($monthlyData);
        // dd($monthlyData);
        // Creazione dei dati da passare al grafico
        $labels = array_values($months);
        $totalAmounts = array_column($monthlyData, 'total_amount');
        $totalPaidAmounts = array_column($monthlyData, 'total_paid_amount');
        // for ($month = 1; $month <= 12; $month++) {
        //     // Data di inizio e fine del mese corrente
        //     $startDate = "$currentYear-$month-01";
        //     $endDate = date('Y-m-t', strtotime($startDate));

        //     // Query per ottenere la somma dei totali pagati e non pagati per il mese corrente
        //     $payments = $user->payments()->whereBetween('created_at', [$startDate, $endDate])->get();
        //     $totalPaid = $payments->where('status', 'paid')->sum('total_price');
        //     $totalUnpaid = $payments->sum('total_price');

        //     // Aggiungi i dati al risultato mensile
        //     $monthlyData[] = [
        //         'month' => date('F', strtotime($startDate)),
        //         'totalPaid' => $totalPaid,
        //         'totalUnpaid' => $totalUnpaid
        //     ];
        // }
        // dd($monthlyData);
        $totalPaymentsPaidCount = $totalpaymentsPaid->count();
        $yearTotalPaymentsPaidCount = $yearTotalPaymentsPaid->count();
        return view('dashboard', compact(
            'user',
            'totalPaymentsAmmount',
            'totalPayments',
            'totalPaymentsPaidAmmount',
            'totalPaymentsPaidCount',
            'monthlyData',
            'dateStart',
            'dateEnd',
            'year',
            'yearPayments',
            'yearTotalPayments',
            'yearTotalPaymentsAmmount',
            'yearTotalPaymentsPaidCount',
            'yearTotalPaymentsPaidAmmount',

            'labels',
            'totalAmounts',
            'totalPaidAmounts'
        ));
    }
}
