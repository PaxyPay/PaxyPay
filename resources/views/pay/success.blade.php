@extends('layouts.client')
@section('content')
    <div class="container max-container mt-5">
        <div class="row p-3 glass ">
            <div class="d-flex justify-content-center align-items-center">
                <img class="logo-pay" src="http://192.168.1.8:8000/immagine.png" alt="">
            </div>
            <div class="p-3 my-2 bg-viola d-flex justify-content-center align-items-center flex-column">
                <h3> Pagamento avvenuto con successo!</h3>
                <p>Grazie per aver usato la nostra applicazione.</p>
            </div>
        </div>
    </div>
    <style>
        body{
            background-image: url('https://images.unsplash.com/photo-1545579133-99bb5ab189bd?q=80&w=2070&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D') !important;
        }
        .glass {
                background: rgba(255, 255, 255, 0.1);
                border-radius: 20px;
                box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
                -webkit-backdrop-filter: blur(10px);
                backdrop-filter: blur(5px);
                border: 1px solid rgba(255, 255, 255, 0.3);
                max-height: 750px;
                margin-bottom: 100px;
                padding-top: 20px;
                /* Aggiunge uno spazio sopra il contenuto */
            }
    </style>
@endsection

