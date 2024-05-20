<?php

use App\Http\Controllers\Admin\CartController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\PayController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\File;
use App\Http\Controllers\LanguageController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
/*

    File::link($target, $link);
 
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Route::get('/', [PaymentController::class, 'index']);
// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    return redirect('/home');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();

    return back()->with('message', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');


Route::get('/dashboard', [ProfileController::class, 'dashboard'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    // Route::get()
});

Route::middleware(['auth','verified'])->group(function () {
    Route::get('/profile/dashboard', [ProfileController::class, 'dashboard'])->name('profile.dashboard');
    Route::get('/profile/settings', [ProfileController::class, 'settings'])->name('profile.settings');
    Route::post('/profile/stripe', [ProfileController::class, 'stripe'])->name('profile.stripe');
    Route::post('/profile/paypal', [ProfileController::class, 'paypal'])->name('profile.paypal');
    Route::post('/profile/updateSettings', [ProfileController::class, 'updateSettings'])->name('profile.updateSettings');
});

Route::middleware(['auth', 'verified'])
    ->name('admin.')
    ->prefix('admin')
    ->group(function () {
        Route::resource('payment', PaymentController::class);
        Route::get('payment/copyCreate/{payment}', [PaymentController::class, 'copyCreate'])->name('payment.copyCreate');
        Route::get('payment/create_success/{payment}', [PaymentController::class, 'create_success'])->name('payment.create_success');
});

$target = storage_path('app/public');
$link = public_path('storage');

if (!File::exists($link)) {
    // Creazione del collegamento simbolico
}

// Route::post('/api/create-paypal-order', 'PayController@createPayPalOrder');
Route::get('pay/{token}', [PayController::class, 'show'])->name('pay.show');
Route::post('pay/stripe/{payment}', [PayController::class, 'stripe'])->name('pay.stripe');
Route::post('pay/paypal/{payment}', [PayController::class, 'paypal'])->name('pay.paypal');
Route::post('pay/satispay/{payment}', [PayController::class, 'satispay'])->name('pay.satispay');
Route::get('success', [PayController::class, 'success'])->name('success');

// Route::get('email/payment_confirmation', [PaymentReceived::class, 'build'])->name(+)





Route::post('change-language', function (Request $request) {
    $locale = $request->input('locale');
    $lenguageENV = env('APP_LOCALE');
    $lenguage = $locale;
    // Assicurati che il locale sia valido
    if (in_array($locale, ['it', 'en'])) {
        // Imposta il locale nella sessione e nell'applicazione
        session(['locale' => $locale]);
        app()->setLocale($locale);
        $lenguageENV = $locale;
    }
    return back();
})->name('changeLanguage');

require __DIR__.'/auth.php';
