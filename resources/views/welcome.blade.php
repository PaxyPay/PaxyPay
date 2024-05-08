@extends('layouts.app')
@section('content')

<div class="container text-center p-5">
    <div class="row justify-content-center">
        <div class="col-4 mt-5 ">
            <div class="mt-5">
                <h1>Welcome to PROXY</h4>
                <p>Con la nostra applicazione potrai creare i tuoi pagamenti PAY-BY-Link in modo intuitivo e veloce</p>
                <p>Inizia subito a creare i tuoi pagamenti</p>
                <a href="{{route('admin.payment.create')}}" class="btn btn-primary mx-5 text-primary-emphasis">New Payment</a>
            </div>
        </div>
    </div>
</div>
@endsection