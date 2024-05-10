@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="card d-flex justify-content-center align-items-center mt-5 shadow">
                    <div class="mt-5 d-flex justify-content-center align-items-center flex-column">
                        <h3>Pagamento creato con successo!</h3>
                        <p>
                            Questo è il tuo link per condividere il tuo pagamento : 
                            <a class="text-primary"
                                href="{{ env('APP_URL') .'/'.'pay/' . $payment->token }}">{{ env('APP_URL').'/'.'pay/' . $payment->token }}</a>
                        </p>
                        <p class="d-flex">
                            <a class="btn btn-primary bottoneCondividi dropdown-item" onclick="copy(event)"
                                token="{{ env('APP_URL').'/'.'pay/' . $payment->token }}">
                                <i class="fa-solid fa-share-nodes"></i> Condividi : Share
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const bottoneCondividi = document.getElementsByClassName('bottoneCondividi');

        // Itera attraverso tutti gli elementi bottoneCondividi
        for (let i = 0; i < bottoneCondividi.length; i++) {
            bottoneCondividi[i].addEventListener('click', function() {
                // Controlla se il browser supporta la funzione di condivisione
                if (navigator.share) {
                    // Ottieni il token dall'attributo "token" dell'elemento cliccato
                    const token = this.getAttribute("token");
                    // Definisci i dati da condividere
                    const datiCondivisione = {
                        title: 'Titolo del link da condividere',
                        text: 'Testo aggiuntivo del link da condividere',
                        url: token
                    };

                    // Utilizza la funzione share per condividere i dati
                    navigator.share(datiCondivisione)
                        .then(function() {
                            console.log('Link condiviso con successo');
                        })
                        .catch(function(error) {
                            console.error('Errore durante la condivisione:', error);
                        });
                } else {
                    // Gestisci il caso in cui il browser non supporta la condivisione
                    console.error('Il browser non supporta la funzione di condivisione');
                    // Puoi fornire un fallback o un messaggio di errore alternativo qui
                }
            });
        }

    </script>
@endsection
