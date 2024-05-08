<section>
    <header>
        <h2 class="text-secondary">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-muted">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6 " enctype="multipart/form-data" >
        @csrf
        @method('patch')

        <div class="mb-2">
            <label for="name">{{ __('Name') }}</label>
            <input class="form-control" type="text" name="name" id="name" autocomplete="name"
                value="{{ old('name', $user->name) }}" required autofocus>
            @error('name')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->get('name') }}</strong>
                </span>
            @enderror
        </div>

        <div class="mb-2">
            <label for="company_name">{{ __('Company Name') }}</label>
            <input class="form-control" type="text" name="company_name" id="company_name" autocomplete="company_name"
                value="{{ old('company_name', $user->company_name) }}" required autofocus>
            @error('company_name')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->get('company_name') }}</strong>
                </span>
            @enderror
        </div>

        <div class="mb-2">
            <label for="company_phone">{{ __('Company Phone') }}</label>
            <input class="form-control" type="text" name="company_phone" id="company_phone"
                autocomplete="company_phone" value="{{ old('company_phone', $user->company_phone) }}" required
                autofocus>
            @error('company_phone')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->get('company_phone') }}</strong>
                </span>
            @enderror
        </div>

        <div class="mb-2">
            <label for="company_address">{{ __('Company Address') }}</label>
            <input class="form-control" type="text" name="company_address" id="company_address"
                autocomplete="company_address" value="{{ old('company_address', $user->company_address) }}" required
                autofocus>
            @error('company_address')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->get('company_address') }}</strong>
                </span>
            @enderror
        </div>

        <div class="mb-2">
            <label for="image">{{__('Image')}}</label>
            <input type="file" name="image" class="form-control" accept="image/*" id="image" >
            @error('image')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <div class="mb-2">
            <label for="company_email">{{ __('Company Email') }}</label>
            <input class="form-control" type="text" name="company_email" id="company_email"
                autocomplete="company_email" value="{{ old('company_email', $user->company_email) }}" required
                autofocus>
            @error('company_email')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->get('company_email') }}</strong>
                </span>
            @enderror
        </div>

        <div class="mb-2">
            <label for="vat_number">{{ __('Vat Number') }}</label>
            <input class="form-control" type="text" name="vat_number" id="vat_number" autocomplete="vat_number"
                value="{{ old('vat_number', $user->vat_number) }}" required autofocus>
            @error('vat_number')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->get('vat_number') }}</strong>
                </span>
            @enderror
        </div>
  

        <div class="mb-2">
            <label for="email">
                {{ __('Email') }}
            </label>

            <input id="email" name="email" type="email" class="form-control"
                value="{{ old('email', $user->email) }}" required autocomplete="username" />

            @error('email')
                <span class="alert alert-danger mt-2" role="alert">
                    <strong>{{ $errors->get('email') }}</strong>
                </span>
            @enderror

            @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-muted">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="btn btn-outline-dark">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 text-success">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="d-flex align-items-center gap-4">
            <button class="btn btn-primary" type="submit">{{ __('Save') }}</button>

            @if (session('status') === 'profile-updated')
                <script>
                    const show = true;
                    setTimeout(() => show = false, 2000)
                    const el = document.getElementById('profile-status')
                    if (show) {
                        el.style.display = 'block';
                    }
                </script>
                <p id='profile-status' class="fs-5 text-muted">{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
