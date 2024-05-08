<div style="max-width: 600px; margin: 0 auto; padding: 20px;">
    <h2 style="text-align: center; color: #333;">Ricevuta di Pagamento</h2>
    <hr style="border-top: 1px solid #ccc;">
    <p style="font-size: 16px;">La tua richiesta di pagamento è stata accolta, {{ $payment->client_name }}.</p>
    <p style="font-size: 16px;">È stata pagata da {{ $payment->customer_name }} / {{ $payment->customer_email }}.</p>
    <hr style="border-top: 1px solid #ccc;">
    <p style="font-size: 14px; color: #666;">Grazie per il tuo pagamento!</p>
</div>