<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {

        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->storePublicly('uploads', 'public');
            $imageUrl = asset('storage/' . str_replace('public/', '', $imagePath));
            $request->user()->image = $imageUrl; // Assicurati che 'image' sia un campo stringa nel modello dell'utente
        }
        // if($request->user()->image = ''){
        //     $request->user()->image = null;
        // }
        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }



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


        $settings['payMethods'][1]['active'] = $active;
        $settings['payMethods'][1]['publickey'] = $public_key;
        $settings['payMethods'][1]['privateKey'] = $private_key;
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

        $settings['payMethods'][0]['active'] = $active;
 
        $settings['payMethods'][0]['client_id'] = $client_id;
        $settings['payMethods'][0]['secret_key'] = $client_secret;
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


        $monthlyData = [];
        for ($month = 1; $month <= 12; $month++) {
            // Data di inizio e fine del mese corrente
            $startDate = "$currentYear-$month-01";
            $endDate = date('Y-m-t', strtotime($startDate));

            // Query per ottenere la somma dei totali pagati e non pagati per il mese corrente
            $payments = $user->payments()->whereBetween('created_at', [$startDate, $endDate])->get();
            $totalPaid = $payments->where('status', 'paid')->sum('total_price');
            $totalUnpaid = $payments->sum('total_price');

            // Aggiungi i dati al risultato mensile
            $monthlyData[] = [
                'month' => date('F', strtotime($startDate)),
                'totalPaid' => $totalPaid,
                'totalUnpaid' => $totalUnpaid
            ];
        }

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
            'yearTotalPaymentsPaidAmmount'
        ));
    }
}
