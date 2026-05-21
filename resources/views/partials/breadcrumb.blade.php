<nav class="mb-4 md:mb-6" aria-label="Breadcrumb">
    <ol class="inline-flex flex-wrap items-center gap-x-2 gap-y-1 text-[10px] md:text-xs font-semibold text-gray-400">
        @foreach($items as $item)
            @php
                $label = is_array($item) ? ($item['label'] ?? '') : $item;
                $url = is_array($item) ? ($item['url'] ?? null) : null;
                $icon = is_array($item) ? ($item['icon'] ?? null) : null;
                $isLast = $loop->last;
            @endphp

            <li class="inline-flex items-center">
                @if(!$loop->first)
                    <i class="fa-solid fa-chevron-right text-[8px] md:text-[9px] mx-1 md:mx-2 text-gray-300"></i>
                @endif

                @if($icon)
                    <i class="{{ $icon }} mr-2 text-[10px] md:text-xs"></i>
                @endif

                @if($url && !$isLast)
                    <a href="{{ $url }}" class="hover:text-[#091E6E] transition-colors">{{ $label }}</a>
                @else
                    <span class="{{ $isLast ? 'text-[#091E6E] font-bold' : '' }}">{{ $label }}</span>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
