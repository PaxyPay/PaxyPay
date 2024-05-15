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
                                {{ __('messages.nome_cliente') }}
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
                                href="{{ route('admin.payment.index', array_merge(request()->query(), ['column' => 'due_date', 'order' => $settings['orderBy'] == 'due_date' && $settings['orderFor'] == 'ASC' ? 'DESC' : 'ASC'])) }}">{{ __('messages.data_scadenza') }}
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
                                href="{{ route('admin.payment.index', array_merge(request()->query(), ['column' => 'created_at', 'order' => $settings['orderBy'] == 'created_at' && $settings['orderFor'] == 'ASC' ? 'DESC' : 'ASC'])) }}">{{ __('messages.data_creazione') }}
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
                                href="{{ route('admin.payment.index', array_merge(request()->query(), ['column' => 'active', 'order' => $settings['orderBy'] == 'active' && $settings['orderFor'] == 'ASC' ? 'DESC' : 'ASC'])) }}">{{ __('messages.attivi') }}
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
                                href="{{ route('admin.payment.index', array_merge(request()->query(), ['column' => 'status', 'order' => $settings['orderBy'] == 'status' && $settings['orderFor'] == 'ASC' ? 'DESC' : 'ASC'])) }}">{{ __('messages.stato') }}
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
                                href="{{ route('admin.payment.index', array_merge(request()->query(), ['column' => 'total_price', 'order' => $settings['orderBy'] == 'total_price' && $settings['orderFor'] == 'ASC' ? 'DESC' : 'ASC'])) }}">{{ __('messages.prezzo_totale') }}
                                €

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
                            {{ __('messages.opzioni') }}
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
                                        <span class="badge bg-success">{{ __('messages.attivo') }}</span>
                                    @else
                                        <span class="badge bg-danger">{{ __('messages.disattivo') }}</span>
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
                                        <span class="badge bg-success">{{ __('messages.pagato') }}</span>
                                    @else
                                        <span class="badge bg-danger">{{ __('messages.non_pagato') }}</span>
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
                                            <a class="btn btn-primary copyButton dropdown-item" onclick="copy(event)"
                                                token="{{ env('APP_URL') }}/pay/{{ $payment->token }}">
                                                <i class="fa-regular fa-copy"></i> {{ __('messages.copia') }}
                                            </a>
                                        </li>
                                        <li>
                                            <a class="btn btn-primary bottoneCondividi dropdown-item"
                                                onclick="copy(event)"
                                                token="{{ env('APP_URL') }}/pay/{{ $payment->token }}">
                                                <i class="fa-solid fa-share-nodes"></i>
                                                {{ __('messages.condividi') }}
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ route('admin.payment.copyCreate', $payment) }}"
                                                class="btn btn-primary dropdown-item"><i
                                                    class="fa-regular fa-clone"></i>
                                                {{ __('messages.clona') }}
                                            </a>
                                        </li>
                                    </ul>
                                </div>

                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
