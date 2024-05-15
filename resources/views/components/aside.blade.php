<aside class="side-bar">
    <div class="py-2 px-3">
        <a class="" href="{{ url('/') }}">
            <img src="{{ env('APP_URL') }}/paxy-pay-logo.png" alt="" class="logo">
        </a>
    </div>
    <ul class="d-flex flex-column gap-3">
        <li class="">
            <a class=""
                href="{{ route('admin.payment.index') }}">{{ __('messages.pagamenti') }}</a>
        </li>
        <li class="">
            <a class=""
                href="{{ route('profile.settings') }}">{{ __('messages.settaggi') }}</a>
        </li>
        <li class="">
            <a class=""
                href="{{ url('profile') }}">{{ __('messages.profilo') }}</a>
        </li>
        <li class="">
            <a class=""
                href="{{ url('profile/dashboard') }}">{{ __('messages.statistiche') }}</a>
        </li>
        <li class="">
            <a class="" href="{{ route('logout') }}"
                onclick="event.preventDefault();
                 document.getElementById('logout-form').submit();">
                {{ __('messages.esci') }}
            </a>
        </li>
    </ul>
</aside>

