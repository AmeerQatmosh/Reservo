@props([
    'items' => [],
])
@if (count($items) > 0)
    <nav aria-label="Breadcrumb" {{ $attributes->class('text-sm') }}>
        <ol class="flex flex-wrap items-center gap-x-2 gap-y-1">
            @foreach ($items as $item)
                <li class="flex min-w-0 max-w-full items-center gap-2">
                    @if (! $loop->first)
                        <span class="shrink-0 select-none text-gray-300" aria-hidden="true">&gt;</span>
                    @endif
                    @if (! empty($item['url']))
                        <a
                            href="{{ $item['url'] }}"
                            class="truncate font-medium text-gray-600 transition hover:text-gray-900"
                        >{{ $item['label'] }}</a>
                    @else
                        <span class="truncate font-semibold text-gray-900" @if ($loop->last) aria-current="page" @endif>{{ $item['label'] }}</span>
                    @endif
                </li>
            @endforeach
        </ol>
    </nav>
@endif
