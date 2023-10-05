@if ($paginator->hasPages())
    <div class="m-3 py-3">
        <nav>
            <ul class="pagination justify-content-center pagination-rs">
                @if ($paginator->onFirstPage())
                    <li class="page-item disabled"><span class="page-link"><i class="bi bi-chevron-left"></i></span></li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $paginator->previousPageUrl() }}">
                            <i class="bi bi-chevron-left"></i>
                        </a>
                    </li>
                @endif

                @foreach ($elements as $element)
                    @if (is_string($element))
                        <li class="page-item disabled"><span class="page-link">{{ $element }}</span></li>
                    @endif

                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                            @else
                                <li class="page-item">
                                    <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                </li>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                @if ($paginator->hasMorePages())
                    <li class="page-item"><a class="page-link" href="{{ $paginator->nextPageUrl() }}"><i
                                class="bi bi-chevron-right"></i></span></a></li>
                @else
                    <li class="page-item disabled"><span class="page-link"><i class="bi bi-chevron-right"></i></span>
                    </li>
                @endif
            </ul>
        </nav>
    </div>
@endif
