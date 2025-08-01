@if ($paginator->hasPages())
    <ul class="pagination" role="navigation">
        {{-- Numbered Links --}}
        @foreach ($elements as $element)
            @if (is_string($element))
                <li class="page-item disabled d-none d-sm-block" aria-disabled="true"><span class="page-link">{{ $element }}</span></li>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <li class="page-item active" aria-current="page"><span class="page-link">{{ $page }}</span></li>
                    @elseif (($page == 1 || $page == $paginator->lastPage()) || ($page >= $paginator->currentPage() - 1 && $page <= $paginator->currentPage() + 1))
                        <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                    @else
                        <li class="page-item d-none d-sm-block"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                    @endif
                @endforeach
            @endif
        @endforeach
    </ul>
@endif