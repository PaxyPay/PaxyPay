@extends('layouts.app')

@section('content')
    <form id="form" name="form" action="{{ route('profile.dashboard') }}" method="GET" class="shadow search p-3">
        <div class="container">

            <div class="row justify-content-center card mt-4 shadow">
                <div class="card-header shadow">
                    <h2 class="fs-4 text-secondary my-4">
                        {{ __('Dashboard') }}
                    </h2>
                </div>
                <div class="col-12 card-body shadow">

                    <div class="row g-2">
                        <div class="col-md-4">
                            <label for="keyword" class="form-label">Search</label>
                            <input type="text" class="form-control" placeholder="Search" id="keyword" name="keyword"
                                value="{{ old('keyword') }}">
                        </div>
                        <div class="col-md-4">
                            <label for="date_start" class="form-label">Da</label>
                            <input type="date" class="form-control" placeholder="date_start" id="date_start"
                                name="date_start" value="{{ old('date_start', $dateStart) }}">
                        </div>
                        <div class="col-md-4">
                            <label for="date_end" class="form-label">fino a</label>
                            <input type="date" class="form-control" placeholder="date_end" id="date_end" name="date_end"
                                value="{{ old('date_end', $dateEnd) }}">
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

                    <div class="row mt-3 p-0 py-2 flex-row  row-gap-4">
                        <div class="col-12 col-md-4">
                            <div class="card width-md shadow">
                                <div class="card-header">
                                    Pagamenti Emessi Totali
                                </div>
                                <div class="card-body d-flex justify-content-between align-items-end">
                                    <h3><span class="text-primary">{{ $totalPayments }} </span>Totali</h3>
                                    <h3><span
                                            class="text-danger">{{ number_format($totalPaymentsAmmount, 2, ',', '.') }}€</span>
                                    </h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="card width-md shadow">
                                <div class="card-header">
                                    Pagamenti Da Sadare
                                </div>
                                <div class="card-body d-flex justify-content-between align-items-end">
                                    <h3>
                                        <span class="text-primary">
                                            @if ($totalPayments - $totalPaymentsPaidCount < 0)
                                                0
                                            @else
                                            {{number_format($totalPayments - $totalPaymentsPaidCount, 2, ',', '.')}}€
                                            @endif
                                        </span>Totali
                                    </h3>
                                    <h3>
                                        <span class="text-danger">
                                            @if($totalPaymentsAmmount - $totalPaymentsPaidAmmount < 0)
                                            0
                                            @else
                                            {{ number_format($totalPaymentsAmmount - $totalPaymentsPaidAmmount, 2, ',', '.') }}€
                                            @endif
                                        </span>
                                    </h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="card shadow">
                                <div class="card-header">
                                    Pagamenti Saldati Totali
                                </div>
                                <div class="card-body  d-flex justify-content-between align-items-end">
                                    <h3><span class="text-primary">{{ $totalPaymentsPaidCount }} </span>Totali</h3>
                                    <h3><span
                                            class="text-danger">{{ number_format($totalPaymentsPaidAmmount, 2, ',', '.') }}€</span>
                                    </h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-3 flex-row shadow card">
                <div class="card-header">
                    <div class="col-12 col-md-3 ">
                        <select class="form-select" aria-label="Default select example" name="year" id="yearSelect">
                            <option selected>Seleziona l'anno</option>
                            <option value="2022" {{ old('year', $year) == '2022' ? 'selected' : '' }}>2022</option>
                            <option value="2023" {{ old('year', $year) == '2023' ? 'selected' : '' }}>2023</option>
                            <option value="2024" {{ old('year', $year) == '2024' ? 'selected' : '' }}>2024</option>
                        </select>
                    </div>
                </div>
                <div class="shadow col-12 card-body">
                    <canvas id="myChart" width="1200" height="600" class="p-3"></canvas>
                </div>
            </div>
            <div class="row mt-3 flex-row row-gap-4 card py-3 shadow">
                <div class="col-12 col-md-4">
                    <div class="card width-md shadow">
                        <div class="card-header">
                            Pagamenti Emessi Totali
                        </div>
                        <div class="card-body d-flex justify-content-between align-items-end">
                            <h3><span class="text-primary">{{ $yearTotalPayments }} </span>Totali</h3>
                            <h3><span
                                    class="text-danger">{{ number_format($yearTotalPaymentsAmmount, 2, ',', '.') }}€</span>
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="card width-md shadow">
                        <div class="card-header">
                            Pagamenti Da Sadare
                        </div>
                        <div class="card-body d-flex justify-content-between align-items-end">
                            <h3><span class="text-primary">{{ $yearTotalPayments - $yearTotalPaymentsPaidCount }}
                                </span>Totali</h3>
                            <h3><span
                                    class="text-danger">{{ number_format($yearTotalPaymentsAmmount - $yearTotalPaymentsPaidAmmount, 2, ',', '.') }}€</span>
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="card shadow">
                        <div class="card-header">
                            Pagamenti Saldati Totali
                        </div>
                        <div class="card-body  d-flex justify-content-between align-items-end">
                            <h3><span class="text-primary">{{ $yearTotalPaymentsPaidCount }} </span>Totali</h3>
                            <h3><span
                                    class="text-danger">{{ number_format($yearTotalPaymentsPaidAmmount, 2, ',', '.') }}€</span>
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center align-items-center my-4">
                <div class="col-12 col-md-6 ">
                    <div class="row mt-3 flex-row shadow card">
                        <div class="card-header">
                            <h4 class="text-center">Pagamenti Saldati Totali per l'Anno Selezionato</h4>
                        </div>
                        <div class="col-12 card-body">
                            <canvas id="pieChart" width="400" height="400"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <style>
        #myChart {
            width: 100%;


            margin: 0 auto;

        }

        @media (max-width: 767px) {


            #myChart {
                max-width: none;
                padding: 0 10px;
            }
        }
    </style>
    <script>
        let mesi = ['Gennaio', 'Febbraio', 'Marzo', 'Aprile', 'Maggio', 'Giugno', 'Luglio', 'Agosto', 'Settembre',
            'Ottobre', 'Novembre', 'Dicembre'
        ];
        let pagamentiRicevuti = [
            {{ $monthlyData[0]['totalPaid'] }}, {{ $monthlyData[1]['totalPaid'] }},
            {{ $monthlyData[2]['totalPaid'] }}, {{ $monthlyData[3]['totalPaid'] }},
            {{ $monthlyData[4]['totalPaid'] }}, {{ $monthlyData[5]['totalPaid'] }},
            {{ $monthlyData[6]['totalPaid'] }}, {{ $monthlyData[7]['totalPaid'] }},
            {{ $monthlyData[8]['totalPaid'] }}, {{ $monthlyData[9]['totalPaid'] }},
            {{ $monthlyData[10]['totalPaid'] }}, {{ $monthlyData[11]['totalPaid'] }}
        ];
        let speseSostenute = [
            {{ $monthlyData[0]['totalUnpaid'] }}, {{ $monthlyData[1]['totalUnpaid'] }},
            {{ $monthlyData[2]['totalUnpaid'] }}, {{ $monthlyData[3]['totalUnpaid'] }},
            {{ $monthlyData[4]['totalUnpaid'] }}, {{ $monthlyData[5]['totalUnpaid'] }},
            {{ $monthlyData[6]['totalUnpaid'] }}, {{ $monthlyData[7]['totalUnpaid'] }},
            {{ $monthlyData[8]['totalUnpaid'] }}, {{ $monthlyData[9]['totalUnpaid'] }},
            {{ $monthlyData[10]['totalUnpaid'] }}, {{ $monthlyData[11]['totalUnpaid'] }}
        ];

        let ctx = document.getElementById('myChart').getContext('2d');
        let myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: mesi,
                datasets: [{
                    label: 'Pagamenti Ricevuti',
                    data: pagamentiRicevuti,
                    backgroundColor: 'rgba(54, 162, 235, 0.5)', // Colore per le barre dei pagamenti ricevuti
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }, {
                    label: 'Spese Sostenute',
                    data: speseSostenute,
                    backgroundColor: 'rgba(255, 99, 132, 0.5)', // Colore per le barre delle spese sostenute
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            }
        });
        let ctxPie = document.getElementById('pieChart').getContext('2d');
        let pieChart = new Chart(ctxPie, {
            type: 'pie',
            data: {
                labels: ['Pagamenti Saldati', 'Pagamenti Non Saldati'],
                datasets: [{
                    label: 'Pagamenti Saldati',
                    data: [
                        {{ $yearTotalPaymentsPaidCount }},
                        {{ $yearTotalPayments - $yearTotalPaymentsPaidCount }}
                    ],
                    backgroundColor: [
                        'rgba(75, 192, 192, 0.5)', // Color for payments paid
                        'rgba(255, 99, 132, 0.5)' // Color for payments due
                    ],
                    borderColor: [
                        'rgba(75, 192, 192, 1)',
                        'rgba(255, 99, 132, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                legend: {
                    position: 'bottom'
                },
                title: {
                    display: true,
                    text: 'Pagamenti Saldati Totali per l\'Anno Selezionato'
                }
            }
        });
        document.getElementById('yearSelect').addEventListener('change', function() {
            document.getElementById('form').submit();
        });
    </script>
@endsection
