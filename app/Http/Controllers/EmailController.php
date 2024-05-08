<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class EmailController extends Controller
{
    public function sendEmail(Request $request)
    {
        $data = [
            'subject' => 'Test Email',
            'content' => 'This is a test email from Laravel using Mailgun!',
        ];

        Mail::send('emails.test', $data, function($message) {
            $message->to('recipient@example.com', 'Recipient Name')
                    ->subject('Test Email');
        });

        return 'Email sent successfully!';
    }
}
