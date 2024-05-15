<div class="col-12 p-0">

    {{-- Pagination --}}
    <div class="pagination d-flex justify-content-between p-1 shadow search mt-3 align-items-center">
        <div class="d-flex gap-2">
            <div>
                @if ($payments->currentPage() > 1)
                    <a class="btn btn-primary bh d-flex align-items-center" href="{{ url()->current() }}?page=1"
                        class="pagination-link">
                        <i class="fas fa-chevron-left"></i><i class="fas fa-chevron-left"></i> </a>
                @endif
            </div>
            <div>
                @if ($payments->currentPage() > 1)
                    <a class="btn btn-secondary bh d-flex align-items-center"
                        href="{{ $payments->appends(request()->query())->previousPageUrl() }}"
                        class="pagination-link">&laquo; {{ __('messages.precedente') }}</a>
                @endif
            </div>
        </div>
        <div class="d-none d-lg-table-cell">
            <span>{{ __('messages.pagina_corrente') }}</span>
            <span class="font-weight-bold">{{ $payments->currentPage() }}</span> /
            <span class="font-weight-bold">{{ $payments->lastPage() }}</span>
            <span class="mx-4">{{ __('messages.risultati_totali') }} : {{ $payments->total() }}</span>
        </div>

        <div class="d-flex gap-2">
            <div>
                @if ($payments->hasMorePages())
                    <a class="btn btn-secondary bh d-flex align-items-center"
                        href="{{ $payments->appends(request()->query())->nextPageUrl() }}"
                        class="pagination-link">{{ __('messages.prossimo') }}
                        &raquo;</a>
                @endif
            </div>
            <div>
                @if ($payments->currentPage() == $payments->lastPage())
                @else
                    <a class="btn btn-primary bh d-flex align-items-center"
                        href="{{ url()->current() }}?page={{ $payments->lastPage() }}" class="pagination-link"><i
                            class="fas fa-chevron-right"></i><i class="fas fa-chevron-right"></i></a>
                @endif
            </div>
        </div>
    </div>
    {{-- <div class="col-12 p-0">
        <div class="pagination d-flex justify-content-between p-1 shadow search mt-3 align-items-center">
            <div class="d-flex gap-2">
                <div>
                    @if ($payments->currentPage() > 1)
                        <a class="btn btn-primary bh d-flex align-items-center" href="{{ url()->current() }}?page=1"
                            class="pagination-link">
                            <i class="fas fa-chevron-left"></i><i class="fas fa-chevron-left"></i> </a>
                    @endif
                </div>
                <div>
                    @if ($payments->currentPage() > 1)
                        <a class="btn btn-secondary bh d-flex align-items-center"
                            href="{{ $payments->appends(request()->query())->previousPageUrl() }}"
                            class="pagination-link">&laquo; {{ __('messages.precedente') }}</a>
                    @endif
                </div>
            </div>
            <div class="d-flex gap-2">
                @if ($payments->currentPage() == $payments->lastPage() && $payments->currentPage() > 1)
                    <a class="btn btn-secondary"
                        href="{{ $payments->appends(request()->query())->url($payments->currentPage() - 2) }}">{{ $payments->currentPage() - 2 }}</a>
                @endif
                @if ($payments->currentPage() > 1)
                    <a class="btn btn-secondary"
                        href="{{ $payments->appends(request()->query())->url($payments->currentPage() - 1) }}">{{ $payments->currentPage() - 1 }}</a>
                @endif
                <a class="btn btn-secondary {{ $payments->currentPage() ? 'active' : '' }}"
                    href="{{ $payments->url($payments->currentPage()) }}">{{ $payments->currentPage() }}</a>
                @if ($payments->currentPage() < $payments->lastPage())
                    <a class="btn btn-secondary"
                        href="{{ $payments->appends(request()->query())->url($payments->currentPage() + 1) }}">{{ $payments->currentPage() + 1 }}</a>
                @endif
                @if ($payments->currentPage() == 1 && $payments->lastPage() > 1)
                    <a class="btn btn-secondary"
                        href="{{ $payments->appends(request()->query())->url($payments->currentPage() + 2) }}">{{ $payments->currentPage() + 2 }}</a>
                @endif
            </div>
            <div class="d-flex gap-2">
                <div>
                    @if ($payments->hasMorePages())
                        <a class="btn btn-secondary bh d-flex align-items-center"
                            href="{{ $payments->appends(request()->query())->nextPageUrl() }}"
                            class="pagination-link">{{ __('messages.prossimo') }}
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
    </div> --}}
</div>