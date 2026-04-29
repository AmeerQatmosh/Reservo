@props([
    'value' => '',
    'min' => null,
    'inputId' => 'date',
    'name' => 'date',
    /** Rows for marking days + tooltip: date, kind, href, … */
    'bookings' => [],
])

@php
    $minDay = $min ?? now()->toDateString();
    $incoming = trim((string) ($value ?? ''));
    $initialDate = ($incoming !== '' && $incoming >= $minDay) ? $incoming : $minDay;
    $weekCursor = \Illuminate\Support\Carbon::parse($minDay)->startOfWeek(\Illuminate\Support\Carbon::SUNDAY);
    $bookingJsonId = 'mini-cal-json-'.$inputId;
@endphp

<div
    {{
        $attributes->merge([
            'class' =>
                'rounded-3xl border border-slate-200/90 bg-white p-4 shadow-[0_2px_8px_-2px_rgba(15,23,42,0.06),0_12px_32px_-8px_rgba(15,23,42,0.08)] ring-1 ring-slate-950/[0.04]',
        ])
    }}
>
    <input
        type="hidden"
        name="{{ $name }}"
        id="{{ $inputId }}"
        value="{{ $initialDate }}"
        aria-label="{{ __('Reservation date') }}"
    >

    <script type="application/json" id="{{ $bookingJsonId }}">@json($bookings ?? [])</script>

    <div
        class="mini-cal"
        data-mini-cal
        data-mini-cal-target="{{ $inputId }}"
        data-mini-cal-book-json="{{ $bookingJsonId }}"
        data-mini-cal-min="{{ $minDay }}"
        data-mini-cal-selected="{{ $initialDate }}"
        data-mini-cal-heading-locale="{{ str_replace('_', '-', app()->getLocale()) }}"
        data-tip-kind-past="{{ __('Past booking') }}"
        data-tip-kind-up="{{ __('Upcoming') }}"
        data-tip-open="{{ __('Open booking') }}"
    >
        <div class="flex items-stretch gap-2">
            <button
                type="button"
                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full text-slate-500 transition-colors hover:bg-slate-100 hover:text-slate-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500/55 focus-visible:ring-offset-2"
                data-mini-cal-prev
                aria-label="{{ __('Previous month') }}"
            >
                <x-lucide name="chevron-left" class="h-[1.125rem] w-[1.125rem] shrink-0 stroke-[2.25]" />
            </button>
            <div class="flex min-h-10 min-w-0 flex-1 flex-col items-center justify-center px-2 text-center">
                <div class="truncate text-[0.9375rem] font-semibold leading-tight tracking-tight text-slate-900 tabular-nums sm:text-[1rem]" data-mini-cal-heading></div>
            </div>
            <button
                type="button"
                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full text-slate-500 transition-colors hover:bg-slate-100 hover:text-slate-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500/55 focus-visible:ring-offset-2"
                data-mini-cal-next
                aria-label="{{ __('Next month') }}"
            >
                <x-lucide name="chevron-right" class="h-[1.125rem] w-[1.125rem] shrink-0 stroke-[2.25]" />
            </button>
        </div>

        <div
            class="mt-4 grid grid-cols-7 gap-1 border-b border-slate-100 pb-3 text-center text-[0.65rem] font-medium leading-none text-slate-400 tabular-nums"
        >
            @foreach (range(0, 6) as $_)
                @php
                    $w = $weekCursor->copy()->addDays($_);
                @endphp
                <span class="truncate px-px">{{ mb_substr($w->translatedFormat('D'), 0, 3) }}</span>
            @endforeach
        </div>
        <div class="mt-2 grid grid-cols-7 gap-1.5" data-mini-cal-grid></div>
    </div>
</div>

<script>
(function () {
    function pad2(n) {
        return String(n).padStart(2, '0');
    }
    function formatYMD(d) {
        return d.getFullYear() + '-' + pad2(d.getMonth() + 1) + '-' + pad2(d.getDate());
    }
    function parseYMD(s) {
        var p = String(s).split('-');
        if (p.length !== 3) return null;
        var y = Number(p[0]),
            mo = Number(p[1]),
            day = Number(p[2]);
        if (!(y >= 1900 && mo >= 1 && mo <= 12 && day >= 1 && day <= 31)) return null;
        var dt = new Date(y, mo - 1, day, 12, 0, 0);
        if (
            dt.getFullYear() !== y ||
            dt.getMonth() !== mo - 1 ||
            dt.getDate() !== day
        ) {
            return null;
        }
        return dt;
    }
    function cmpYMD(a, b) {
        if (a < b) return -1;
        if (a > b) return 1;
        return 0;
    }

    function monthCells(year, mo1to12) {
        var monthStart = new Date(year, mo1to12 - 1, 1, 12, 0, 0);
        var gridStart = new Date(monthStart);
        gridStart.setDate(monthStart.getDate() - monthStart.getDay());
        var lastOfMonth = new Date(year, mo1to12, 0, 12, 0, 0);
        var gridEnd = new Date(lastOfMonth);
        gridEnd.setDate(lastOfMonth.getDate() + (6 - lastOfMonth.getDay()));
        var cur = new Date(gridStart);
        var out = [];
        while (cur <= gridEnd) {
            out.push(new Date(cur));
            cur.setDate(cur.getDate() + 1);
        }
        return out;
    }

    function triggerChange(el) {
        el.dispatchEvent(new Event('input', { bubbles: true }));
        el.dispatchEvent(new Event('change', { bubbles: true }));
    }

    function readBookingRows(bookJsonId) {
        var el = bookJsonId ? document.getElementById(bookJsonId) : null;
        if (!el) return [];
        try {
            return JSON.parse(el.textContent || '[]');
        } catch (_) {
            return [];
        }
    }

    function indexByDate(rows) {
        var ix = {};
        (rows || []).forEach(function (b) {
            if (!b || !b.date) return;
            if (!ix[b.date]) ix[b.date] = [];
            ix[b.date].push(b);
        });
        return ix;
    }

    /** @returns {HTMLElement} */
    function tipLayer() {
        if (!window.__miniCalTipEl) {
            var d = document.createElement('div');
            d.setAttribute('role', 'tooltip');
            d.className =
                'mini-cal-tip fixed z-[110] hidden w-[min(18.5rem,calc(100vw-1.75rem))] overflow-hidden rounded-2xl border border-slate-200/90 bg-white/95 p-3.5 text-[0.8125rem] leading-snug text-slate-800 shadow-[0_12px_40px_-12px_rgba(15,23,42,0.18)] ring-1 ring-slate-950/[0.06] backdrop-blur-sm';
            d.addEventListener('mouseenter', function () {
                tipCancelHide();
            });
            d.addEventListener('mouseleave', function () {
                tipScheduleHide();
            });
            document.body.appendChild(d);
            /** @type {HTMLElement} */
            window.__miniCalTipEl = d;
        }
        /** @type {HTMLElement} */
        return /** @type {HTMLElement} */ (window.__miniCalTipEl);
    }

    var tipHideT = null;
    function tipCancelHide() {
        if (tipHideT) {
            window.clearTimeout(tipHideT);
            tipHideT = null;
        }
    }

    function tipScheduleHide() {
        tipHideT && window.clearTimeout(tipHideT);
        tipHideT = window.setTimeout(function () {
            tipLayer().classList.add('hidden');
            tipHideT = null;
        }, 140);
    }

    function tipHideImmediate() {
        tipCancelHide();
        tipLayer().classList.add('hidden');
    }

    function tipPosition(btn) {
        var tip = tipLayer();
        var r = btn.getBoundingClientRect();
        var tw = tip.offsetWidth;
        var th = tip.offsetHeight;
        var top = r.bottom + 6;
        var left = r.left;

        tip.classList.remove('hidden');
        tw = tip.offsetWidth;
        th = tip.offsetHeight;

        if (top + th > window.innerHeight - 8) {
            top = r.top - th - 6;
        }
        if (top < 8) top = 8;

        if (left + tw > window.innerWidth - 8) {
            left = window.innerWidth - tw - 8;
        }
        if (left < 8) left = 8;

        tip.style.left = left + 'px';
        tip.style.top = top + 'px';
    }

    function tipPopulate(cell, dayRows, wrap) {
        var tip = tipLayer();
        tip.innerHTML = '';

        var locale = wrap.getAttribute('data-mini-cal-heading-locale') || 'en-US';
        var labelPast = wrap.getAttribute('data-tip-kind-past') || 'Past';
        var labelUp = wrap.getAttribute('data-tip-kind-up') || 'Upcoming';
        var openText = wrap.getAttribute('data-tip-open') || 'Open';

        var head = document.createElement('div');
        head.className =
            'mb-2.5 border-b border-slate-100 pb-2.5 text-[0.6875rem] font-semibold uppercase tracking-[0.06em] text-slate-400';
        head.textContent = new Intl.DateTimeFormat(locale, {
            weekday: 'long',
            month: 'long',
            day: 'numeric',
            year: 'numeric',
        }).format(cell);
        tip.appendChild(head);

        dayRows.forEach(function (b) {
            var row = document.createElement('div');
            row.className = 'mb-2 rounded-xl border border-slate-100/90 bg-slate-50/80 p-2.5 last:mb-0';

            var topRow = document.createElement('div');
            topRow.className = 'flex flex-wrap items-center gap-x-2 gap-y-1';

            var badge = document.createElement('span');
            badge.className =
                'inline-flex rounded-lg px-2 py-0.5 text-[0.62rem] font-semibold uppercase tracking-wide ' +
                (b.kind === 'past'
                    ? 'bg-slate-200/90 text-slate-700'
                    : 'bg-emerald-100 text-emerald-900 ring-1 ring-emerald-200/60');
            badge.textContent = b.kind === 'past' ? labelPast : labelUp;
            topRow.appendChild(badge);

            var time = document.createElement('span');
            time.className = 'tabular-nums text-[0.75rem] font-medium text-slate-700';
            time.textContent = (b.start || '') + '–' + (b.end || '');
            topRow.appendChild(time);

            row.appendChild(topRow);

            var linkWrap = document.createElement('div');
            linkWrap.className = 'mt-1.5 border-t border-slate-100/90 pt-1.5';

            var link = document.createElement('a');
            link.href = b.href || '#';
            link.className =
                'inline-flex items-center gap-1 text-[0.8rem] font-semibold text-emerald-700 transition hover:text-emerald-900';
            link.textContent = (b.room_name || '') + ' · ' + openText;
            link.setAttribute('rel', 'noopener noreferrer');
            link.addEventListener('mousedown', function (e) {
                e.stopPropagation();
            });

            linkWrap.appendChild(link);
            row.appendChild(linkWrap);
            tip.appendChild(row);
        });
    }

    document.querySelectorAll('[data-mini-cal]').forEach(function (wrap) {
        if (wrap.getAttribute('data-mini-cal-done') === '1') return;
        wrap.setAttribute('data-mini-cal-done', '1');

        var targetId = wrap.getAttribute('data-mini-cal-target');
        var jsonId = wrap.getAttribute('data-mini-cal-book-json');
        var inputEl = targetId ? document.getElementById(targetId) : null;
        var gridEl = wrap.querySelector('[data-mini-cal-grid]');
        var heading = wrap.querySelector('[data-mini-cal-heading]');
        var btnPrev = wrap.querySelector('[data-mini-cal-prev]');
        var btnNext = wrap.querySelector('[data-mini-cal-next]');
        var localeHead =
            wrap.getAttribute('data-mini-cal-heading-locale') ||
            document.documentElement.lang ||
            'en-US';
        var minStr = wrap.getAttribute('data-mini-cal-min') || formatYMD(new Date());

        if (!inputEl || !gridEl) return;

        var selected = (
            wrap.getAttribute('data-mini-cal-selected') ||
            inputEl.value ||
            ''
        ).trim();

        function bookingIndex() {
            return indexByDate(readBookingRows(jsonId));
        }

        function viewFrom(y) {
            var d =
                parseYMD(y) ||
                parseYMD(minStr) ||
                parseYMD(formatYMD(new Date()));
            return { y: d.getFullYear(), mo: d.getMonth() + 1 };
        }

        var view = viewFrom(selected || minStr);

        function renderHeading() {
            if (!heading) return;
            var hd = new Date(view.y, view.mo - 1, 1, 12, 0, 0);
            heading.textContent = new Intl.DateTimeFormat(localeHead, {
                month: 'long',
                year: 'numeric',
            }).format(hd);
        }

        function todayStr() {
            return formatYMD(new Date());
        }

        function paint() {
            tipHideImmediate();
            var ix = bookingIndex();
            gridEl.innerHTML = '';
            monthCells(view.y, view.mo).forEach(function (cell) {
                var ymd = formatYMD(cell);
                var inMonth = cell.getMonth() + 1 === view.mo;
                var selectable = cmpYMD(ymd, minStr) >= 0;
                var sel = !!selected && ymd === selected;
                var isToday = ymd === todayStr();
                var dayRows = ix[ymd] || [];
                var hasPast = dayRows.some(function (b) {
                    return b.kind === 'past';
                });
                var hasUpcoming = dayRows.some(function (b) {
                    return b.kind === 'upcoming';
                });

                var btn = document.createElement('button');
                btn.type = 'button';
                btn.setAttribute('aria-pressed', sel ? 'true' : 'false');
                var aria = ymd + (!selectable ? ' — unavailable' : '');
                if (dayRows.length) {
                    aria += ' — ' + dayRows.length + ' booking' + (dayRows.length === 1 ? '' : 's');
                }
                btn.setAttribute('aria-label', aria);

                btn.className =
                    'mini-cal-cell group relative flex min-h-[2.65rem] w-full flex-col items-center justify-center gap-0.5 overflow-visible rounded-2xl px-0.5 pb-1 pt-1 text-[0.8125rem] font-semibold tabular-nums tracking-tight transition-colors duration-150 ' +
                    (inMonth ? 'text-slate-800' : 'text-slate-300') +
                    (!selectable
                        ? ' cursor-not-allowed opacity-40'
                        : ' cursor-pointer focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500/50 focus-visible:ring-offset-2') +
                    (!selectable ? '' : inMonth ? ' hover:bg-slate-100/95' : ' hover:bg-slate-50/80') +
                    (sel
                        ? ' !bg-gradient-to-br !from-slate-900 !to-slate-800 !text-white shadow-md shadow-slate-900/25 ring-1 ring-white/10 hover:!from-slate-800 hover:!to-slate-900'
                        : selectable && !sel && isToday
                          ? ' border border-emerald-400/50 bg-emerald-50 text-emerald-950 shadow-[inset_0_1px_0_0_rgba(255,255,255,0.85)] hover:border-emerald-500/55 hover:bg-emerald-50/90'
                          : selectable && !sel
                            ? ' border border-transparent'
                            : '');

                var dayNum = document.createElement('span');
                dayNum.className = 'leading-none ' + (sel ? 'drop-shadow-[0_1px_0_rgba(0,0,0,0.2)]' : '');
                dayNum.textContent = String(cell.getDate());
                btn.appendChild(dayNum);

                if (dayRows.length && selectable) {
                    var dotRow = document.createElement('span');
                    dotRow.className =
                        'pointer-events-none mt-0 flex h-[0.4375rem] w-full shrink-0 flex-wrap items-center justify-center gap-[3px]';
                    if (hasUpcoming) {
                        var du = document.createElement('span');
                        du.className =
                            'h-1.5 w-1.5 shrink-0 rounded-full ' +
                            (sel ? 'bg-emerald-400 ring-2 ring-white/30' : 'bg-emerald-500 shadow-sm shadow-emerald-600/25 ring-[1.5px] ring-white');
                        du.setAttribute('aria-hidden', 'true');
                        dotRow.appendChild(du);
                    }
                    if (hasPast) {
                        var dp = document.createElement('span');
                        dp.className =
                            'h-1.5 w-1.5 shrink-0 rounded-full ' +
                            (sel ? 'bg-slate-300 ring-2 ring-white/25' : 'bg-slate-400 ring-[1px] ring-slate-200/70');
                        dp.setAttribute('aria-hidden', 'true');
                        dotRow.appendChild(dp);
                    }
                    btn.appendChild(dotRow);
                }

                if (!selectable) {
                    btn.disabled = true;
                    gridEl.appendChild(btn);
                    return;
                }
                btn.disabled = false;

                btn.addEventListener('click', function () {
                    tipHideImmediate();
                    selected = ymd;
                    inputEl.value = selected;
                    wrap.setAttribute('data-mini-cal-selected', selected);
                    triggerChange(inputEl);
                    paint();
                });

                if (dayRows.length) {
                    btn.addEventListener('mouseenter', function () {
                        tipCancelHide();
                        tipPopulate(cell, dayRows, wrap);
                        tipPosition(btn);
                    });
                    btn.addEventListener('mouseleave', function () {
                        tipScheduleHide();
                    });
                }

                gridEl.appendChild(btn);
            });
            renderHeading();
        }

        btnPrev?.addEventListener('click', function () {
            tipHideImmediate();
            if (view.mo <= 1) {
                view.y -= 1;
                view.mo = 12;
            } else {
                view.mo -= 1;
            }
            paint();
        });
        btnNext?.addEventListener('click', function () {
            tipHideImmediate();
            if (view.mo >= 12) {
                view.y += 1;
                view.mo = 1;
            } else {
                view.mo += 1;
            }
            paint();
        });

        inputEl.addEventListener('change', function () {
            var v = (inputEl.value || '').trim();
            if (!v || cmpYMD(v, minStr) < 0) return;
            selected = v;
            wrap.setAttribute('data-mini-cal-selected', selected);
            view = viewFrom(v);
            paint();
        });

        paint();
    });
})();
</script>
