@php
    $isFiltered = $isFiltered ?? false;
    $ctx = $context ?? 'guest';
    $reset = $resetUrl ?? null;
    $add = $addRoomUrl ?? null;
    $isAdmin = $ctx === 'admin' || $ctx === 'demo-admin';
@endphp

{{-- isFiltered, context, resetUrl, addRoomUrl (optional) --}}
<div
    class="max-w-md py-12 text-center sm:mx-auto sm:py-14"
    role="status"
    aria-live="polite"
>
    @if (! $isFiltered)
        <p class="text-sm font-medium text-gray-900">
            @if ($isAdmin)
                No rooms yet
            @else
                No rooms to show
            @endif
        </p>
        <p class="mt-1.5 text-sm leading-relaxed text-gray-600">
            @if ($isAdmin)
                When you add rooms, they will appear in this list.
            @else
                There are no bookable rooms right now. Please check back later.
            @endif
        </p>
        @if ($isAdmin && $add)
            <p class="mt-4 text-sm text-gray-600">
                <a
                    href="{{ $add }}"
                    class="font-medium text-gray-900 underline decoration-gray-300 underline-offset-2 transition hover:decoration-gray-500"
                >Add a room</a>
                <span class="text-gray-500">to get started.</span>
            </p>
        @endif
    @else
        <p class="text-sm font-medium text-gray-900">No matching rooms</p>
        <p class="mt-1.5 text-sm leading-relaxed text-gray-600">
            Adjust your search or clear filters to see the full list.
        </p>
        @if ($reset)
            <p class="mt-5">
                <a
                    href="{{ $reset }}"
                    class="text-sm font-medium text-gray-900 underline decoration-gray-300 underline-offset-2 transition hover:decoration-gray-500"
                >Clear search and filters</a>
            </p>
        @endif
    @endif
</div>
