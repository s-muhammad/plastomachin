@if ($paginator->hasPages())
    <nav role="navigation" aria-label="صفحه‌بندی" class="mt-10 flex w-full justify-center">
        <ul class="flex flex-row flex-wrap items-center justify-center gap-1">
            <li>
                @if ($paginator->onFirstPage())
                    <span class="btn" data-variant="ghost" data-size="icon" aria-disabled="true" aria-label="صفحه قبل">
                        <x-icon name="chevron-right" class="size-4" />
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="btn" data-variant="ghost" data-size="icon" aria-label="صفحه قبل">
                        <x-icon name="chevron-right" class="size-4" />
                    </a>
                @endif
            </li>

            @foreach ($elements as $element)
                @if (is_string($element))
                    <li><span class="flex size-9 items-center justify-center text-sm text-muted-foreground">{{ $element }}</span></li>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        <li>
                            @if ($page == $paginator->currentPage())
                                <span class="btn" data-variant="outline" data-size="icon" aria-current="page">{{ fa_num($page) }}</span>
                            @else
                                <a href="{{ $url }}" class="btn" data-variant="ghost" data-size="icon">{{ fa_num($page) }}</a>
                            @endif
                        </li>
                    @endforeach
                @endif
            @endforeach

            <li>
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="btn" data-variant="ghost" data-size="icon" aria-label="صفحه بعد">
                        <x-icon name="chevron-left" class="size-4" />
                    </a>
                @else
                    <span class="btn" data-variant="ghost" data-size="icon" aria-disabled="true" aria-label="صفحه بعد">
                        <x-icon name="chevron-left" class="size-4" />
                    </span>
                @endif
            </li>
        </ul>
    </nav>
@endif
