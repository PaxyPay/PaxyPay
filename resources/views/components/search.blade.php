<form id="form" name="form" action="{{ route('admin.payment.index') }}" method="GET"
            class="shadow search p-3">
            <div class="row g-2">
                <div class="col-md-3">
                    <label for="keyword" class="form-label">{{ __('messages.cerca') }}</label>
                    <input type="text" class="form-control" placeholder="Search Payments..." id="keyword"
                        name="keyword" value="{{ request()->query('keyword') }}">
                </div>
                <div class="col-md-3">
                    <label for="active" class="form-label">{{ __('messages.attivi') }}</label>
                    <select class="form-select" aria-label="Status" id="active" name="active">
                        <option value="" {{ request()->query('active') === null ? 'selected' : '' }}>
                            {{ __('messages.tutti') }}
                        </option>
                        <option value="1" {{ request()->query('active') == 1 ? 'selected' : '' }}>
                            {{ __('messages.attivi') }}</option>
                        <option value="2" {{ request()->query('active') == 2 ? 'selected' : '' }}>
                            {{ __('messages.disattivi') }}</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="paid" class="form-label">{{ __('messages.stato') }}</label>
                    <select class="form-select" aria-label="Paid" id="paid" name="paid">
                        <option value="" {{ request()->query('paid') === null ? 'selected' : '' }}>
                            {{ __('messages.tutti') }}
                        </option>
                        <option value="paid" {{ request()->query('paid') == 'paid' ? 'selected' : '' }}>
                            {{ __('messages.pagati') }}</option>
                        <option value="not_paid" {{ request()->query('paid') == 'not_paid' ? 'selected' : '' }}>
                            {{ __('messages.non_pagati') }}</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="perPage" class="form-label">{{ __('messages.righe_per_pagine') }}</label>
                    <select class="form-select" id="perPage" name="perPage">
                        <option value="10" {{ $settings['perPage'] == 10 ? 'selected' : '' }}>10</option>
                        <option value="20" {{ $settings['perPage'] == 20 ? 'selected' : '' }}>20</option>
                        <option value="50" {{ $settings['perPage'] == 50 ? 'selected' : '' }}>50</option>
                    </select>
                </div>
                <div class="col-md-12">
                    <div class="d-flex justify-content-between align-items-center">

                        <a href="{{ route('admin.payment.index') }}" class="btn btn-danger d-none d-md-inline"
                            id="resetFilter">
                            <i class="fas fa-times-circle"></i> {{ __('messages.resetta_filtri') }}
                        </a>

                        <button class="btn btn-success d-none d-md-inline" type="submit">
                            <i class="fas fa-search"></i> {{ __('messages.cerca') }}

                        </button>

                        <a href="{{ route('admin.payment.index') }}" class="btn btn-danger d-inline d-md-none"
                            id="resetFilter">
                            <i class="fas fa-times-circle"></i>
                        </a>
                        <button class="btn btn-success d-inline d-md-none" type="submit">
                            <i class="fas fa-search"></i>
                        </button>


                    </div>
                </div>
            </div>
        </form>