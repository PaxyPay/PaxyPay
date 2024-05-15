@extends('layouts.app')
@section('content')
    <div class="row p-5">
        @if (session('error'))
            <div class="alert alert-danger d-flex justify-content-center">
                {{ session('error') }}
            </div>
        @endif
        <div class="col-md-6">
            @if ($user->image)
                <img class="logo-pay mb-3" src="{{ Auth::user()->image }}" alt="Immagine del profilo">
            @endif
        </div>
        <div class="col-md-6 d-md-flex justify-content-end">
            <div class="">
                <a href="{{ route('admin.payment.create') }}"
                    class="btn btn-primary mb-3">{{ __('messages.nuovo_pagamento') }}</a>
            </div>
        </div>
        {{-- ricerca --}}
        <x-search :settings="$settings" :user="$user" :payments="$payments"></x-search>
        {{-- paginazione --}}
        <x-paginate :settings="$settings" :user="$user" :payments="$payments"></x-paginate>
        {{-- tabella pagamenti --}}
        <x-table :settings="$settings" :user="$user" :payments="$payments"></x-table>
        {{-- paginazione --}}
        <x-paginate :settings="$settings" :user="$user" :payments="$payments"></x-paginate>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", (event) => {
            function changeLocale(locale) {
                // Aggiorna il valore della select e invia il modulo
                document.getElementById('languageForm').submit();
            }
            document.getElementById('perPage').addEventListener('change', function() {
                form.submit();
            });

            // document.getElementById('active').addEventListener('change', function() {
            //     form.submit();
            // });
            // document.getElementById('paid').addEventListener('change', function() {
            //     form.submit();
            // });

            document.getElementById('resetFilter').addEventListener('click', function() {
                console.log("Pulsante cliccato"); // Aggiunto per verifica 
                window.location.href = '{{ route('admin.payment.index') }}';
            });

            const sortableColumns = document.querySelectorAll('.sortable');


            const btns = document.getElementsByClassName('copyButton');

            for (let b = 0; b < btns.length; b++) {

                btns[b].addEventListener('click', function(e) {
                    event.preventDefault();
                    const token = btns[b].getAttribute("token");
                    link = token.toString()
                    const inputNascosto = document.createElement("input");
                    inputNascosto.value = link;
                    document.body.appendChild(inputNascosto);

                    // Seleziona il testo nell'input
                    inputNascosto.select();
                    // Copia il testo selezionato negli appunti del dispositivo
                    document.execCommand("copy");

                    // Rimuovi l'input nascosto dal documento
                    document.body.removeChild(inputNascosto);
                })
            }

            function copy() {

                console.log('ciao')
            }
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
        });
    </script>
    <style>
        tr {
            cursor: pointer !important;
        }

        td {
            background-color: white !important;
        }

        tr:nth-child(odd) td {
            background-color: lightgray !important;

        }
    </style>
@endsection
