@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination" class="flex items-center justify-between">

        {{-- Texto: mostrando X–Y de Z --}}
        <div class="text-xs text-gray-500">
            Mostrando
            <span class="font-semibold text-gray-700">{{ $paginator->firstItem() }}</span>–<span class="font-semibold text-gray-700">{{ $paginator->lastItem() }}</span>
            de
            <span class="font-semibold text-gray-700">{{ $paginator->total() }}</span>
        </div>

        {{-- Botones --}}
        <div class="flex items-center gap-1">

            {{-- Anterior --}}
            @if ($paginator->onFirstPage())
                <span class="px-3 py-1.5 text-xs text-gray-300 bg-gray-50 border border-gray-200 rounded-lg cursor-not-allowed select-none">
                    <i class="fa-solid fa-chevron-left text-[10px]"></i>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}"
                   class="px-3 py-1.5 text-xs text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                    <i class="fa-solid fa-chevron-left text-[10px]"></i>
                </a>
            @endif

            {{-- Páginas --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="px-3 py-1.5 text-xs text-gray-400 select-none">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="px-3 py-1.5 text-xs font-semibold text-white bg-primary border border-primary rounded-lg select-none">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}"
                               class="px-3 py-1.5 text-xs text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Siguiente --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}"
                   class="px-3 py-1.5 text-xs text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                    <i class="fa-solid fa-chevron-right text-[10px]"></i>
                </a>
            @else
                <span class="px-3 py-1.5 text-xs text-gray-300 bg-gray-50 border border-gray-200 rounded-lg cursor-not-allowed select-none">
                    <i class="fa-solid fa-chevron-right text-[10px]"></i>
                </span>
            @endif

        </div>
    </nav>
@endif
