@extends('layouts.app')

@section('content')
    <div class="container p-3">
        <div class="row">

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
                    <a href="{{ route('admin.payment.create') }}" class="btn btn-primary mb-3">+ New Payment</a>
                </div>
            </div>

            {{-- Ricerca per nome/attivo e paginazione --}}
            <form id="form" name="form" action="{{ route('admin.payment.index') }}" method="GET"
                class="shadow search p-3">
                <div class="row g-2">
                    <div class="col-md-3">
                        <label for="keyword" class="form-label">Search</label>
                        <input type="text" class="form-control" placeholder="Search Payments..." id="keyword"
                            name="keyword" value="{{ request()->query('keyword') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="active" class="form-label">Active</label>
                        <select class="form-select" aria-label="Status" id="active" name="active">
                            <option value="" {{ request()->query('active') === null ? 'selected' : '' }}>Tutti
                            </option>
                            <option value="1" {{ request()->query('active') == 1 ? 'selected' : '' }}>Enabled</option>
                            <option value="2" {{ request()->query('active') == 2 ? 'selected' : '' }}>Disabled</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="paid" class="form-label">Status</label>
                        <select class="form-select" aria-label="Paid" id="paid" name="paid">
                            <option value="" {{ request()->query('paid') === null ? 'selected' : '' }}>Tutti
                            </option>
                            <option value="paid" {{ request()->query('paid') == 'paid' ? 'selected' : '' }}>Paid</option>
                            <option value="not_paid" {{ request()->query('paid') == 'not_paid' ? 'selected' : '' }}>Not
                                Paid</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="perPage" class="form-label">Rows for Page</label>
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
                                <i class="fas fa-times-circle"></i> Reset Filters
                            </a>

                            <button class="btn btn-success d-none d-md-inline" type="submit">
                                <i class="fas fa-search"></i> Search

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

            <div class="col-12 p-0">

                {{-- Pagination --}}
                <div class="pagination d-flex justify-content-between p-1 shadow search mt-3 align-items-center">
                    <div class="d-flex gap-2">
                        <div>
                            @if ($payments->currentPage() > 1)
                                <a class="btn btn-primary bh d-flex align-items-center"
                                    href="{{ url()->current() }}?page=1" class="pagination-link">
                                    <i class="fas fa-chevron-left"></i><i class="fas fa-chevron-left"></i> </a>
                            @endif
                        </div>
                        <div>
                            @if ($payments->currentPage() > 1)
                                <a class="btn btn-secondary bh d-flex align-items-center"
                                    href="{{ $payments->appends(request()->query())->previousPageUrl() }}"
                                    class="pagination-link">&laquo; Previous</a>
                            @endif
                        </div>
                    </div>
                    <div class="d-none d-lg-table-cell">
                        <span>Current Page</span>
                        <span class="font-weight-bold">{{ $payments->currentPage() }}</span> /
                        <span class="font-weight-bold">{{ $payments->lastPage() }}</span>
                        <span class="mx-4">Total results : {{ $payments->total() }}</span>
                    </div>

                    <div class="d-flex gap-2">
                        <div>
                            @if ($payments->hasMorePages())
                                <a class="btn btn-secondary bh d-flex align-items-center"
                                    href="{{ $payments->appends(request()->query())->nextPageUrl() }}"
                                    class="pagination-link">Next
                                    &raquo;</a>
                            @endif
                        </div>
                        <div>
                            @if ($payments->currentPage() == $payments->lastPage())
                            @else
                                <a class="btn btn-primary bh d-flex align-items-center"
                                    href="{{ url()->current() }}?page={{ $payments->lastPage() }}"
                                    class="pagination-link"><i class="fas fa-chevron-right"></i><i
                                        class="fas fa-chevron-right"></i></a>
                            @endif
                        </div>
                    </div>
                </div>

            </div>

            <div class="col-12 p-0 my-3">
                {{-- Payment Table --}}
                <div class="card shadow">
                    <div class="m-0 table-striped cursor">
                        <table class="table rounded">
                            <thead>
                                <tr class="">
                                    <th scope="col " class="cursor">
                                        <a class="{{ $settings['orderBy'] == 'client_name' ? 'badge text-bg-primary text-wrap fs-10' : '' }}"
                                            href="{{ route('admin.payment.index', array_merge(request()->query(), ['column' => 'client_name', 'order' => $settings['orderBy'] == 'client_name' && $settings['orderFor'] == 'ASC' ? 'DESC' : 'ASC'])) }}">
                                            Client Name
                                            @if ($settings['orderBy'] == 'client_name')
                                                @if ($settings['orderFor'] == 'ASC')
                                                    <i class="fas fa-arrow-up"></i>
                                                @else
                                                    <i class="fas fa-arrow-down"></i>
                                                @endif
                                            @endif
                                        </a>
                                    </th>
                                    <th scope="col" class="d-none d-md-table-cell">
                                        <a class="{{ $settings['orderBy'] == 'due_date' ? 'badge text-bg-primary text-wrap fs-10' : '' }}"
                                            href="{{ route('admin.payment.index', array_merge(request()->query(), ['column' => 'due_date', 'order' => $settings['orderBy'] == 'due_date' && $settings['orderFor'] == 'ASC' ? 'DESC' : 'ASC'])) }}">Due
                                            Date
                                            @if ($settings['orderBy'] == 'due_date')
                                                @if ($settings['orderFor'] == 'ASC')
                                                    <i class="fas fa-arrow-up"></i>
                                                @else
                                                    <i class="fas fa-arrow-down"></i>
                                                @endif
                                            @endif
                                        </a>
                                    </th>
                                    <th scope="col" class="d-none d-md-table-cell">
                                        <a class="{{ $settings['orderBy'] == 'created_at' ? 'badge text-bg-primary text-wrap fs-10' : '' }}"
                                            href="{{ route('admin.payment.index', array_merge(request()->query(), ['column' => 'created_at', 'order' => $settings['orderBy'] == 'created_at' && $settings['orderFor'] == 'ASC' ? 'DESC' : 'ASC'])) }}">Created
                                            at
                                            @if ($settings['orderBy'] == 'created_at')
                                                @if ($settings['orderFor'] == 'ASC')
                                                    <i class="fas fa-arrow-up"></i>
                                                @else
                                                    <i class="fas fa-arrow-down"></i>
                                                @endif
                                            @endif
                                        </a>
                                    </th>
                                    <th scope="col" class="">
                                        <a class="{{ $settings['orderBy'] == 'active' ? 'badge text-bg-primary text-wrap fs-10' : '' }}"
                                            href="{{ route('admin.payment.index', array_merge(request()->query(), ['column' => 'active', 'order' => $settings['orderBy'] == 'active' && $settings['orderFor'] == 'ASC' ? 'DESC' : 'ASC'])) }}">Active
                                            @if ($settings['orderBy'] == 'active')
                                                @if ($settings['orderFor'] == 'ASC')
                                                    <i class="fas fa-arrow-up"></i>
                                                @else
                                                    <i class="fas fa-arrow-down"></i>
                                                @endif
                                            @endif
                                        </a>
                                    </th>
                                    <th class="">
                                        <a class="{{ $settings['orderBy'] == 'status' ? 'badge text-bg-primary text-wrap fs-10' : '' }}"
                                            href="{{ route('admin.payment.index', array_merge(request()->query(), ['column' => 'status', 'order' => $settings['orderBy'] == 'status' && $settings['orderFor'] == 'ASC' ? 'DESC' : 'ASC'])) }}">Status
                                            @if ($settings['orderBy'] == 'status')
                                                @if ($settings['orderFor'] == 'ASC')
                                                    <i class="fas fa-arrow-up"></i>
                                                @else
                                                    <i class="fas fa-arrow-down"></i>
                                                @endif
                                            @endif
                                        </a>
                                    </th>
                                    <th scope="col" class="text-end ">
                                        <a class="{{ $settings['orderBy'] == 'total_price' ? 'badge text-bg-primary text-wrap fs-10' : '' }}"
                                            href="{{ route('admin.payment.index', array_merge(request()->query(), ['column' => 'total_price', 'order' => $settings['orderBy'] == 'total_price' && $settings['orderFor'] == 'ASC' ? 'DESC' : 'ASC'])) }}">Total
                                            Price €
                                            @if ($settings['orderBy'] == 'total_price')
                                                @if ($settings['orderFor'] == 'ASC')
                                                    <i class="fas fa-arrow-up"></i>
                                                @else
                                                    <i class="fas fa-arrow-down"></i>
                                                @endif
                                            @endif
                                        </a>
                                    </th>
                                    <th class="">
                                        option
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($payments as $payment)
                                    <tr class="db-green">

                                        {{-- Client Name --}}
                                        <td onclick="window.location='{{ route('admin.payment.edit', $payment->id) }}'">
                                            {{ $payment->client_name }}
                                        </td>
                                        {{-- Due Date --}}
                                        <td onclick="window.location='{{ route('admin.payment.edit', $payment->id) }}'"
                                            class="d-none d-md-table-cell">
                                            @if ($payment->due_date)
                                                {{ \Carbon\Carbon::parse($payment->due_date)->format('d/m/Y') }}
                                            @else
                                                ∞
                                            @endif
                                        </td>
                                        {{-- Created At --}}

                                        <td onclick="window.location='{{ route('admin.payment.edit', $payment->id) }}'"
                                            class="d-none d-md-table-cell">
                                            {{ \Carbon\Carbon::parse($payment->created_at)->format('d/m/Y') }}
                                        </td>
                                        <td onclick="window.location='{{ route('admin.payment.edit', $payment->id) }}'">
                                            <div class="d-none d-md-block">
                                                @if ($payment->active == true)
                                                    <span class="badge bg-success">Enabled</span>
                                                @else
                                                    <span class="badge bg-danger">Disabled</span>
                                                @endif
                                            </div>
                                            <div class="d-md-none">
                                                @if ($payment->active == true)
                                                    <i class="fas fa-check-circle text-success"></i>
                                                @else
                                                    <i class="fas fa-times-circle text-danger"></i>
                                                @endif
                                            </div>
                                        </td>
                                        <td onclick="window.location='{{ route('admin.payment.edit', $payment->id) }}'">
                                            <div class="d-none d-md-block">
                                                @if ($payment->status == 'paid')
                                                    <span class="badge bg-success">{{ $payment->status }}</span>
                                                @else
                                                    <span class="badge bg-danger">{{ $payment->status }}</span>
                                                @endif
                                            </div>

                                            <div class="d-md-none">
                                                @if ($payment->status == 'paid')
                                                    <i class="fas fa-check-circle text-success"></i>
                                                @else
                                                    <i class="fas fa-times-circle text-danger"></i>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="price-right fw-bold"
                                            onclick="window.location='{{ route('admin.payment.edit', $payment->id) }}'">
                                            {{ number_format($payment->total_price, 2, ',', '.') }}
                                        </td>

                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-secondary dropdown-toggle" type="button"
                                                    data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="fas fa-key"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <a class="btn btn-primary copyButton dropdown-item"
                                                            onclick="copy(event)"
                                                            token="https://paxypay.com/pay/{{ $payment->token }}">
                                                            <i class="fa-regular fa-copy"></i> Copy
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="btn btn-primary bottoneCondividi dropdown-item"
                                                            onclick="copy(event)"
                                                            token="https://paxypay.com/pay/{{ $payment->token }}">
                                                            <i class="fa-solid fa-share-nodes"></i> Share
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="{{ route('admin.payment.copyCreate', $payment) }}"
                                                            class="btn btn-primary dropdown-item"><i
                                                                class="fa-regular fa-clone"></i> Clone
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                            {{-- <a class="btn btn-primary copyButton" onclick="copy(event)"
                                                token="https://proxy.cmh.it/pay/{{ $payment->token }}">
                                                <i class="fa-regular fa-copy"></i>
                                            </a>
                                            <a class="btn btn-primary bottoneCondividi" onclick="copy(event)"
                                                token="https://proxy.cmh.it/pay/{{ $payment->token }}">
                                                <i class="fa-solid fa-share-nodes"></i>
                                            </a>
                                            <a href="{{ route('admin.payment.copyCreate', $payment) }}"
                                                class="btn btn-primary"><i class="fa-regular fa-clone"></i>
                                            </a> --}}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
            <div class="col-12 p-0">

                {{-- Pagination
                <div class="pagination d-flex justify-content-between p-1 shadow search mt-3 align-items-center">
                    <div class="d-flex gap-2">
                        <div>
                            @if ($payments->currentPage() > 1)
                                <a class="btn btn-primary bh d-flex align-items-center"
                                    href="{{ url()->current() }}?page=1" class="pagination-link">
                                    <i class="fas fa-chevron-left"></i><i class="fas fa-chevron-left"></i> </a>
                            @endif
                        </div>
                        <div>
                            @if ($payments->currentPage() > 1)
                                <a class="btn btn-secondary bh d-flex align-items-center"
                                    href="{{ $payments->appends(request()->query())->previousPageUrl() }}"
                                    class="pagination-link">&laquo; Previous</a>
                            @endif
                        </div>
                    </div>
                    <div class="d-none d-lg-table-cell">
                        <span>Current Page</span>
                        <span class="font-weight-bold">{{ $payments->currentPage() }}</span> /
                        <span class="font-weight-bold">{{ $payments->lastPage() }}</span>
                        <span class="mx-4">Total results : {{ $payments->total() }}</span>
                    </div>

                    <div class="d-flex gap-2">
                        <div>
                            @if ($payments->hasMorePages())
                                <a class="btn btn-secondary bh d-flex align-items-center"
                                    href="{{ $payments->appends(request()->query())->nextPageUrl() }}"
                                    class="pagination-link">Next
                                    &raquo;</a>
                            @endif
                        </div>
                        <div>
                            @if ($payments->currentPage() == $payments->lastPage())
                            @else
                                <a class="btn btn-primary bh d-flex align-items-center"
                                    href="{{ url()->current() }}?page={{ $payments->lastPage() }}"
                                    class="pagination-link"><i class="fas fa-chevron-right"></i><i
                                        class="fas fa-chevron-right"></i></a>
                            @endif
                        </div>
                    </div>
                </div> --}}
                {{-- paginazione  --}}
            <ul class="pagination justify-content-center">

                @if ($payments->currentPage() > 1)
                    <a class="page-link" href="{{ $payments->appends(request()->query())->Url(1) }}">
                        <i class="fa-solid fa-angles-left"></i>
                    </a>
                    <a class="page-link" href="{{ $payments->appends(request()->query())->previousPageUrl() }}"><i
                            class="fa-solid fa-chevron-left"></i></a>
                @endif

                @if ($payments->lastPage() > 2)
                    @if ($payments->currentPage() == $payments->lastPage() && $payments->currentPage() > 1)
                        <a class="page-link"
                            href="{{ $payments->appends(request()->query())->url($payments->currentPage() - 2) }}">{{ $payments->currentPage() - 2 }}</a>
                    @endif
                    @if ($payments->currentPage() > 1)
                        <a class="page-link"
                            href="{{ $payments->appends(request()->query())->url($payments->currentPage() - 1) }}">{{ $payments->currentPage() - 1 }}</a>
                    @endif
                    {{-- elemento sempre presente della current page --}}
                    <a class="page-link {{ $payments->currentPage() ? 'active' : '' }}"
                        href="{{ $payments->url($payments->currentPage()) }}">{{ $payments->currentPage() }}</a>
                    @if ($payments->currentPage() < $payments->lastPage())
                        <a class="page-link"
                            href="{{ $payments->appends(request()->query())->url($payments->currentPage() + 1) }}">{{ $payments->currentPage() + 1 }}</a>
                    @endif
                    @if ($payments->currentPage() == 1 && $payments->lastPage() > 1)
                        <a class="page-link"
                            href="{{ $payments->appends(request()->query())->url($payments->currentPage() + 2) }}">{{ $payments->currentPage() + 2 }}</a>
                    @endif
                @else
                    {{-- se le pagine sono 1 o 2 --}}
                    @for ($i = 1; $i <= $payments->lastPage(); $i++)
                        <a class="page-link  {{ $i == $payments->currentPage() ? 'active' : '' }}"
                            href="{{ $payments->appends(request()->query())->url($i) }}">{{ $i }}</a>
                    @endfor
                @endif


                @if ($payments->hasMorePages())
                    {{-- <a href="{{ $payments->appends(['keyword'=> request()->query('keyword')])->nextPageUrl() }}">Avanti</a> --}}
                    <a class="page-link " href="{{ $payments->appends(request()->query())->nextPageUrl() }}">
                        <i class="fa-solid fa-chevron-right"></i>
                    </a>
                    <a class="page-link" href="{{ $payments->appends(request()->query())->url($payments->lastPage()) }}">
                        <i class="fa-solid fa-angles-right"></i>
                    </a>
                @endif
            </ul>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", (event) => {
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
