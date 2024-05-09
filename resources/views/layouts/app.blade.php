<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Proxy</title>
    <link rel="icon" href="https://proxy.cmh.it/Immagine.png">
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://kit.fontawesome.com/bce4c6f505.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://proxy.cmh.it/build/assets/app.b0bef6e8.css">  
    <style>td{color: currentColor !important}</style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script type="module" src="https://proxy.cmh.it/build/assets/app.494c25bb.js" onload=""></script>
</head>

<body>
    <div id="app">
        <div class="loading-overlay">
            <div class="loading-spinner"></div>
        </div>
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}">
                    <img src="https://proxy.cmh.it/Immagine.png" alt="" class="logo">
                    {{-- <h1>Proxy</h1> --}}
                    {{-- config('app.name', 'Laravel') --}}
                </a>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">
                        {{-- <li class="nav-item">
                            <a class="nav-link" href="{{url('/') }}">{{ __('Home') }}</a>
                        </li> --}}
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Authentication Links -->
                        @guest
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">Login</a>
                        </li>
                        @if (Route::has('register'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">Register</a>
                        </li>
                        @endif
                        @else
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                {{ Auth::user()->name }}
                            </a>

                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                
                                <a class="dropdown-item" href="{{ route('admin.payment.index') }}">Pagamenti</a>
                                <a  class="dropdown-item" href="{{route('profile.settings')}}">Settaggi</a>
                                <a class="dropdown-item" href="{{ url('profile') }}">Profilo</a>
                                <a class="dropdown-item" href="{{ url('profile/dashboard') }}">Dashboard</a>
                                <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                    Logout
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </div>
                        </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main role="main" class="">
            @yield('content')
        </main>

    </div>
</body>

</html>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Aggiungi una classe al documento quando lo script è in caricamento
        document.documentElement.classList.add('loading');
    });

    // Rimuovi la classe quando lo script è completamente caricato
    document.querySelector('script[src="https://proxy.cmh.it/resources/js/app.js"]').addEventListener('load',
        function() {
            document.documentElement.classList.remove('loading');
        });
    document.addEventListener('DOMContentLoaded', function() {
        // Aggiungi la classe "loaded" quando lo script è completamente caricato
        document.documentElement.classList.add('loaded');
    });

    // Rimuovi la classe "loaded" quando lo script è completamente caricato
    document.querySelector('script[src="https://proxy.cmh.it/resources/js/app.js"]').addEventListener('load',
        function() {
            document.documentElement.classList.remove('loaded');
        });
</script>
