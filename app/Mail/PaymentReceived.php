<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class PaymentReceived extends Mailable
{
    use Queueable, SerializesModels;

    public $payment;

    public function __construct($payment)
    {
        $this->payment = $payment;
    }
    public function build()
    {
       
        return $this->view('emails.payment_confirmation')->with('payment', $this->payment);
    }

}
