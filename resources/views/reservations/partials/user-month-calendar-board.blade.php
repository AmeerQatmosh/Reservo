@php
    $weekdayLabels = isset($weeks[0])
        ? collect($weeks[0])->map(fn ($cell) => \Illuminate\Support\Carbon::parse($cell['date'])->translatedFormat('D'))->all()
        : [];
@endphp

<div
    class="overflow-hidden rounded-3xl border border-slate-200/90 bg-white shadow-[0_2px_8px_-2px_rgba(15,23,42,0.06),0_12px_32px_-8px_rgba(15,23,42,0.08)] ring-1 ring-slate-950/[0.04]"
>
    {{-- Month toolbar --}}
    <div class="relative border-b border-slate-100/90 bg-gradient-to-b from-white to-slate-50/60 px-4 py-4 sm:px-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex flex-wrap items-center gap-3">
                <a
                    href="{{ $prevUrl }}"
                    class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-full text-slate-500 shadow-sm ring-1 ring-slate-200/80 transition-colors hover:bg-slate-50 hover:text-slate-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500/50 focus-visible:ring-offset-2"
                    aria-label="{{ __('Previous month') }}"
                >
                    <x-lucide name="chevron-left" class="h-5 w-5 shrink-0 stroke-[2]" />
                </a>
                <h2 class="min-w-0 px-0.5 text-lg font-semibold tracking-tight text-slate-900 sm:text-xl">
                    {{ $calendarHeading }}
                </h2>
                <a
                    href="{{ $nextUrl }}"
                    class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-full text-slate-500 shadow-sm ring-1 ring-slate-200/80 transition-colors hover:bg-slate-50 hover:text-slate-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500/50 focus-visible:ring-offset-2"
                    aria-label="{{ __('Next month') }}"
                >
                    <x-lucide name="chevron-right" class="h-5 w-5 shrink-0 stroke-[2]" />
                </a>
            </div>
            <div class="flex shrink-0 items-center gap-2">
                <a
                    href="{{ $todayUrl }}"
                    class="inline-flex items-center justify-center gap-2 rounded-full bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-md shadow-emerald-700/15 transition hover:bg-emerald-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500/55 focus-visible:ring-offset-2"
                >
                    <x-lucide name="calendar" class="h-4 w-4 shrink-0 opacity-95" aria-hidden="true" />
                    {{ __('Today') }}
                </a>
            </div>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full min-w-[42rem] border-collapse text-left">
            <thead>
                <tr class="bg-slate-50/95 text-[0.65rem] font-semibold uppercase tracking-[0.08em] text-slate-400">
                    @foreach ($weekdayLabels as $abbr)
                        <th scope="col" class="border border-slate-100 px-2 py-3.5 text-center sm:px-3">
                            <span class="inline-block max-w-full truncate">{{ $abbr }}</span>
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="text-sm">
                @foreach ($weeks as $week)
                    <tr>
                        @foreach ($week as $cell)
                            @php
                                /** @var array{date:string,label:int,in_month:bool,is_today:bool} $cell */
                                $dayRows = $reservationsByDate->get($cell['date'], collect());
                                $showLimit = 4;
                            @endphp
                            <td
                                class="@if (! $cell['in_month']) bg-slate-100/55 text-slate-400 @else bg-white @endif @if ($cell['is_today'] && $cell['in_month']) bg-emerald-50/45 ring-[3px] ring-inset ring-emerald-400/35 @elseif ($cell['is_today']) ring-[3px] ring-inset ring-emerald-300/35 @endif relative min-h-[8.75rem] border border-slate-100 align-top sm:min-h-[9.75rem]"
                            >
                                <div class="flex h-full min-h-[inherit] flex-col p-2 sm:p-2.5">
                                    <div class="flex items-start justify-between gap-1">
                                        <span
                                            @class([
                                                'inline-flex min-h-[1.875rem] min-w-[1.875rem] items-center justify-center rounded-full px-1 text-[0.9rem] font-semibold tabular-nums tracking-tight',
                                                'bg-gradient-to-br from-slate-800 to-slate-900 text-white shadow-md shadow-slate-900/20 ring-2 ring-emerald-400/45' => $cell['is_today'],
                                                'text-slate-900' => $cell['in_month'] && ! $cell['is_today'],
                                                'font-medium text-slate-400/90' => ! $cell['in_month'] && ! $cell['is_today'],
                                            ])
                                        >{{ $cell['label'] }}</span>
                                    </div>
                                    <ul class="mt-2 flex min-h-0 flex-1 flex-col gap-1.5">
                                        @foreach ($dayRows->take($showLimit) as $row)
                                            <li class="min-w-0">
                                                <a
                                                    href="{{ $row['href'] }}"
                                                    class="group block rounded-xl border border-slate-200/90 bg-gradient-to-br from-white to-slate-50/90 px-2.5 py-2 text-xs shadow-[0_1px_2px_rgba(15,23,42,0.05)] transition hover:border-emerald-300/70 hover:shadow-md hover:shadow-emerald-900/5 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500/45"
                                                >
                                                    <span class="block tabular-nums text-[0.78rem] font-semibold text-slate-800 group-hover:text-emerald-900">
                                                        {{ $row['start'] }}–{{ $row['end'] }}
                                                    </span>
                                                    <span class="mt-0.5 line-clamp-2 block text-[0.68rem] font-medium leading-snug text-slate-500 group-hover:text-slate-700">
                                                        {{ $row['room_name'] }}
                                                    </span>
                                                </a>
                                            </li>
                                        @endforeach
                                        @if ($dayRows->count() > $showLimit)
                                            <li class="px-0.5 text-center text-[0.65rem] font-medium tabular-nums text-slate-400">
                                                +{{ $dayRows->count() - $showLimit }} {{ __('more') }}
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="border-t border-slate-100/90 bg-slate-50/40 px-4 py-4 sm:px-6">
        <div
            class="rounded-2xl border border-slate-200/70 bg-white/90 px-4 py-3 text-xs text-slate-600 shadow-sm ring-1 ring-slate-950/[0.03] sm:text-sm"
        >
            <p class="leading-relaxed">
                <span class="font-semibold text-slate-800">{{ __('Tip:') }}</span>
                {{ $calendarTip ?? __('Click a booking to open it. Use the History tab to cancel bookings or create a new reservation.') }}
            </p>
        </div>
    </div>
</div>
