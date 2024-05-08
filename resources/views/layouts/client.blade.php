<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Proxy</title>


    <!-- Fonts -->
    <link rel="icon" href="https://proxy.cmh.it/Immagine.png">
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <!-- CSS di Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- JavaScript di Bootstrap (opzionale, se vuoi utilizzare componenti interattivi) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://proxy.cmh.it/build/assets/app.b0bef6e8.css">       
    <script type="module" src="https://proxy.cmh.it/build/assets/app.494c25bb.js" onload=""></script>
    @if(isset($user))
    <meta property="og:title" content="{{$user->name}}">
    <meta property="og:description" content="{{$payment->description}}">
    <meta property="og:image" content="{{$user->image}}">
    <meta property="og:url" content="https://proxy.cmh.it/pay/{{$payment->token}}">
   @endif

    <style lang="scss">
       
    </style>

</head>
<body>
    <div id="app">
        @yield('content')
            
    </div>
</body>