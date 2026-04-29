{{--
    Stacked filters (search above refine) — e.g. compact embeds. Main browse UI is built in `rooms.index` with split layout.
    Expects: $action, $resetUrl, $filters, $filterOptions
--}}
<form method="GET" action="{{ $action }}" class="flex flex-col gap-4">
    @include('rooms.partials.room-filters-search', ['filters' => $filters])
    @include('rooms.partials.room-filters-sidebar', [
        'filters' => $filters,
        'filterOptions' => $filterOptions,
        'resetUrl' => $resetUrl,
    ])
</form>
