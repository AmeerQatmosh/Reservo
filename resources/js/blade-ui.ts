/**
 * Blade layouts: Turbo Drive (in-app visits), navigation progress, form busy state.
 * Firefox (Gecko): Turbo Drive navigations cost far more here than WebKit/Chromium; disable Drive so links/forms use full loads.
 */
import { session as turboSession } from '@hotwired/turbo';

if (
    typeof navigator !== 'undefined' &&
    /Firefox\//u.test(navigator.userAgent) &&
    !/Seamonkey/iu.test(navigator.userAgent)
) {
    turboSession.drive = false;
}

const NAV_KEY = 'reservo_nav';

function claimElementUi(el: HTMLElement, token: string): boolean {
    const attr = `data-reservo-ui-${token}`;

    if (el.hasAttribute(attr)) {
        return false;
    }

    el.setAttribute(attr, '');

    return true;
}

function getProgressEl(): HTMLElement | null {
    return document.getElementById('reservo-progress');
}

let sidebarNavTooltipResizeBound = false;
let adminAccountPopoverGlobalsBound = false;
let reservoMobileMenuEscapeBound = false;

function startNavProgress(): void {
    const el = getProgressEl();

    if (!el) {
        return;
    }

    try {
        sessionStorage.setItem(NAV_KEY, '1');
    } catch {
        /* private mode */
    }

    el.dataset.state = 'pending';
}

function hidePageSkeleton(): void {
    document.documentElement.classList.remove('reservo-skeleton-enter');
}

/** After GET ?date= on room show, scroll results into view (sticky header offset via scroll-mt on target). */
function scrollRoomAvailabilityResultsIntoView(): void {
    if (!new URLSearchParams(window.location.search).has('date')) {
        return;
    }

    const el = document.getElementById('room-availability-results');

    if (!el) {
        return;
    }

    const instant =
        typeof window.matchMedia === 'function' &&
        window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    window.requestAnimationFrame(() => {
        el.scrollIntoView({ behavior: instant ? 'auto' : 'smooth', block: 'start' });
        el.focus({ preventScroll: true });
    });
}

function finishNavProgress(): void {
    const el = getProgressEl();
    const pending = sessionStorage.getItem(NAV_KEY) === '1';

    try {
        sessionStorage.removeItem(NAV_KEY);
    } catch {
        /* ignore */
    }

    hidePageSkeleton();

    if (!el) {
        return;
    }

    if (!pending) {
        el.dataset.state = 'idle';

        return;
    }

    el.dataset.state = 'finishing';
    window.setTimeout(() => {
        el.dataset.state = 'idle';
    }, 220);
}

function applySubmitButtonLoading(btn: HTMLButtonElement): void {
    if (btn.querySelector(':scope > .reservo-spinner')) {
        return;
    }

    const label = document.createElement('span');

    label.className = 'reservo-btn-label inline-flex items-center justify-center gap-2';

    while (btn.firstChild) {
        label.appendChild(btn.firstChild);
    }

    const spin = document.createElement('span');

    spin.className = 'reservo-spinner';
    spin.setAttribute('aria-hidden', 'true');
    btn.appendChild(spin);
    btn.appendChild(label);
}

function clearSubmitButtonLoading(btn: HTMLButtonElement): void {
    btn.querySelector(':scope > .reservo-spinner')?.remove();
    const label = btn.querySelector(':scope > .reservo-btn-label');

    if (label) {
        while (label.firstChild) {
            btn.appendChild(label.firstChild);
        }

        label.remove();
    }
}

function applyAnchorLoading(a: HTMLAnchorElement): void {
    if (a.querySelector(':scope > .reservo-spinner')) {
        return;
    }

    const label = document.createElement('span');

    label.className = 'reservo-loading-link-label inline-flex items-center justify-center gap-2';

    while (a.firstChild) {
        label.appendChild(a.firstChild);
    }

    const spin = document.createElement('span');

    spin.className = 'reservo-spinner';
    spin.setAttribute('aria-hidden', 'true');
    a.appendChild(spin);
    a.appendChild(label);
    a.classList.add('is-loading');
}

function clearAnchorLoading(a: HTMLAnchorElement): void {
    a.querySelector(':scope > .reservo-spinner')?.remove();
    const label = a.querySelector(':scope > .reservo-loading-link-label');

    if (label) {
        while (label.firstChild) {
            a.appendChild(label.firstChild);
        }

        label.remove();
    }

    a.classList.remove('is-loading');
}

function restoreAllLoadingUi(): void {
    document.querySelectorAll('form.reservo-form-busy').forEach((form) => {
        form.classList.remove('reservo-form-busy');
    });

    document.querySelectorAll('button[type="submit"][aria-busy="true"]').forEach((btn) => {
        if (btn instanceof HTMLButtonElement) {
            clearSubmitButtonLoading(btn);
            btn.removeAttribute('aria-busy');
            btn.disabled = false;
        }
    });

    document.querySelectorAll('a.reservo-loading-link.is-loading').forEach((a) => {
        if (a instanceof HTMLAnchorElement) {
            clearAnchorLoading(a);
        }
    });

    syncReservoGlobalFormBusyClass();
}

/** Avoid `html:has(...)` — expensive style recalculation on Firefox while forms submit. */
function syncReservoGlobalFormBusyClass(): void {
    document.documentElement.classList.toggle(
        'reservo-form-busy-active',
        document.querySelector('form.reservo-form-busy') !== null,
    );
}

/** Fortify/Inertia auth pages: full document load so Turbo does not stay active alongside Inertia.js. */
function pathNeedsFullDocumentNavigation(pathname: string): boolean {
    return (
        pathname === '/login' ||
        pathname === '/register' ||
        pathname === '/forgot-password' ||
        pathname === '/two-factor-challenge' ||
        pathname.startsWith('/reset-password/') ||
        pathname === '/email/verify' ||
        pathname.startsWith('/email/verify/') ||
        pathname === '/user/confirm-password' ||
        pathname === '/user/confirmed-password-status'
    );
}

/** When leaving Blade+Turbo pages, force normal navigation to auth routes (Inertia + Turbo conflict). */
function bindTurboFortifyFullDocumentVisits(): void {
    document.addEventListener('turbo:before-visit', (event: Event) => {
        const detail = (event as CustomEvent<{ url?: string }>).detail;
        const raw = detail?.url;

        if (!raw) {
            return;
        }

        let pathname: string;

        try {
            pathname = new URL(raw, window.location.origin).pathname;
        } catch {
            return;
        }

        if (!pathNeedsFullDocumentNavigation(pathname)) {
            return;
        }

        event.preventDefault();
        window.location.assign(raw);
    });
}

function bindReservoAnchorLoadingLinks(): void {
    document.addEventListener(
        'click',
        (e: MouseEvent) => {
            if (e.button !== 0) {
                return;
            }

            const t = (e.target as Element | null)?.closest?.('a.reservo-loading-link');

            if (!(t instanceof HTMLAnchorElement)) {
                return;
            }

            if (t.dataset.noLoading !== undefined) {
                return;
            }

            if (e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) {
                return;
            }

            if (t.target === '_blank') {
                return;
            }

            const href = t.getAttribute('href');

            if (!href || href === '#' || href.startsWith('#')) {
                return;
            }

            applyAnchorLoading(t);
        },
        true,
    );
}

function turboFetchLooksLikePrefetch(event: Event): boolean {
    const detail = (event as CustomEvent<{ fetchOptions?: RequestInit }>).detail;
    const headers = detail?.fetchOptions?.headers;

    if (!headers) {
        return false;
    }

    if (headers instanceof Headers) {
        return headers.get('X-Sec-Purpose') === 'prefetch' || headers.get('Sec-Purpose') === 'prefetch';
    }

    if (Array.isArray(headers)) {
        return headers.some(
            ([k, v]) =>
                k.toLowerCase() === 'x-sec-purpose' && String(v) === 'prefetch',
        );
    }

    return Object.entries(headers as Record<string, string>).some(
        ([k, v]) => k.toLowerCase() === 'x-sec-purpose' && String(v) === 'prefetch',
    );
}

function bindTurboNavigation(): void {
    bindTurboFortifyFullDocumentVisits();

    document.addEventListener('turbo:before-fetch-request', (event: Event) => {
        if (turboFetchLooksLikePrefetch(event)) {
            return;
        }

        startNavProgress();
    });

    document.addEventListener('turbo:load', () => {
        finishNavProgress();
        bindPageScopedEnhancements();
        scrollRoomAvailabilityResultsIntoView();
    });

    document.addEventListener('turbo:fetch-request-error', () => {
        finishNavProgress();
        restoreAllLoadingUi();
    });

    document.addEventListener('turbo:submit-end', () => {
        finishNavProgress();
        restoreAllLoadingUi();
    });
}

function bindPageScopedEnhancements(): void {
    bindRoomSortSelects();
    bindRoomFilterComboboxes();
    bindReservoFormSelects();
    bindAdminSidebarRail();
    bindAdminSidebarNavTooltips();
    bindAdminSidebarAccountPopovers();
    bindAdminRoomFormPreview();
    bindReservoMobileMenu();
}

function bindNavigationProgress(): void {
    window.addEventListener('pageshow', (e: PageTransitionEvent) => {
        finishNavProgress();

        if (e.persisted) {
            restoreAllLoadingUi();
        }
    });
}

function bindFormBusy(): void {
    document.addEventListener('submit', (e: Event) => {
        const form = e.target;

        if (!(form instanceof HTMLFormElement)) {
            return;
        }

        queueMicrotask(() => {
            if (e.defaultPrevented) {
                return;
            }

            if (form.dataset.noLoading !== undefined) {
                return;
            }

            if (form.classList.contains('reservo-form-busy')) {
                return;
            }

            startNavProgress();
            form.classList.add('reservo-form-busy');
            syncReservoGlobalFormBusyClass();
            form.querySelectorAll('button[type="submit"]').forEach((btn) => {
                if (!(btn instanceof HTMLButtonElement)) {
                    return;
                }

                if (btn.dataset.noLoading !== undefined) {
                    return;
                }

                applySubmitButtonLoading(btn);
                btn.disabled = true;
                btn.setAttribute('aria-busy', 'true');
            });
        });
    });
}

let closeActiveRoomCombobox: (() => void) | null = null;
let closeActiveRoomSort: (() => void) | null = null;

function closeAllReservoFormSelects(): void {
    document.querySelectorAll<HTMLElement>('[data-reservo-form-select]').forEach((el) => {
        el.dispatchEvent(new CustomEvent('reservo-close-select', { bubbles: false }));
    });
}

function normalizeComboboxNeedle(s: string): string {
    return s.trim().toLowerCase();
}

function comboboxOptionMatchesFilter(btn: HTMLButtonElement, needle: string): boolean {
    if (needle === '') {
        return true;
    }

    const v = btn.dataset.value ?? '';
    const label = (btn.textContent ?? '').trim().toLowerCase();

    return v.toLowerCase().includes(needle) || label.includes(needle);
}

function applyComboboxFilter(root: HTMLElement, filterInput: HTMLInputElement): void {
    const needle = normalizeComboboxNeedle(filterInput.value);
    const opts = root.querySelectorAll<HTMLButtonElement>('.reservo-combobox__opt');

    opts.forEach((btn) => {
        btn.hidden = !comboboxOptionMatchesFilter(btn, needle);
    });
}

function bindRoomFilterCombobox(root: HTMLElement): void {
    const inputMaybe = root.querySelector<HTMLInputElement>('[data-reservo-combobox-input]');
    const toggleMaybe = root.querySelector<HTMLButtonElement>('[data-reservo-combobox-toggle]');
    const panelMaybe = root.querySelector<HTMLElement>('[data-reservo-combobox-panel]');
    const filterMaybe = root.querySelector<HTMLInputElement>('[data-reservo-combobox-filter]');

    if (!inputMaybe || !toggleMaybe || !panelMaybe || !filterMaybe) {
        return;
    }

    if (!claimElementUi(root, 'combobox')) {
        return;
    }

    const input = inputMaybe;
    const toggle = toggleMaybe;
    const panel = panelMaybe;
    const filterInput = filterMaybe;

    let open = false;

    const detachDocListeners = (onDocPointerDown: (e: PointerEvent) => void, onDocKeyDown: (e: KeyboardEvent) => void): void => {
        document.removeEventListener('pointerdown', onDocPointerDown, true);
        document.removeEventListener('keydown', onDocKeyDown, true);
    };

    const onDocPointerDown = (e: PointerEvent): void => {
        if (!open) {
            return;
        }

        if (e.target instanceof Node && root.contains(e.target)) {
            return;
        }

        setOpen(false);
    };

    const onDocKeyDown = (e: KeyboardEvent): void => {
        if (open && e.key === 'Escape') {
            e.preventDefault();
            setOpen(false);
            input.focus();
        }
    };

    const closeThis = (): void => {
        setOpen(false);
    };

    function setOpen(next: boolean): void {
        if (next === open) {
            return;
        }

        if (next) {
            closeActiveRoomCombobox?.();
            closeActiveRoomSort?.();
            closeAllReservoFormSelects();
        } else {
            detachDocListeners(onDocPointerDown, onDocKeyDown);

            if (closeActiveRoomCombobox === closeThis) {
                closeActiveRoomCombobox = null;
            }
        }

        open = next;
        toggle.setAttribute('aria-expanded', next ? 'true' : 'false');

        if (next) {
            panel.hidden = false;
            panel.classList.add('is-open');
            filterInput.value = '';
            applyComboboxFilter(root, filterInput);
            document.addEventListener('pointerdown', onDocPointerDown, true);
            document.addEventListener('keydown', onDocKeyDown, true);
            closeActiveRoomCombobox = closeThis;
            window.requestAnimationFrame(() => {
                filterInput.removeAttribute('tabindex');
                filterInput.focus();
            });
        } else {
            panel.classList.remove('is-open');
            panel.hidden = true;
            filterInput.setAttribute('tabindex', '-1');
        }
    }

    toggle.addEventListener('click', (e) => {
        e.preventDefault();
        setOpen(!open);
    });

    filterInput.addEventListener('input', () => {
        applyComboboxFilter(root, filterInput);
    });

    filterInput.addEventListener('keydown', (e) => {
        if (e.key === 'Tab' && !e.shiftKey) {
            const visible = Array.from(
                root.querySelectorAll<HTMLButtonElement>('.reservo-combobox__opt:not([hidden])'),
            );

            if (visible.length) {
                e.preventDefault();
                visible[0]?.focus();
            }
        }
    });

    panel.addEventListener('click', (e) => {
        const t = (e.target as Element | null)?.closest?.('button.reservo-combobox__opt');

        if (!(t instanceof HTMLButtonElement)) {
            return;
        }

        e.preventDefault();
        input.value = t.dataset.value ?? '';
        setOpen(false);
        input.focus();
    });
}

function bindRoomSortSelect(root: HTMLElement): void {
    const hidden = root.querySelector<HTMLInputElement>('[data-reservo-room-sort-input]');
    const trigger = root.querySelector<HTMLButtonElement>('[data-reservo-room-sort-trigger]');
    const panel = root.querySelector<HTMLElement>('[data-reservo-room-sort-panel]');
    const labelEl = root.querySelector<HTMLElement>('[data-reservo-room-sort-label]');

    if (!hidden || !trigger || !panel || !labelEl) {
        return;
    }

    if (!claimElementUi(root, 'room-sort')) {
        return;
    }

    let open = false;

    const detachDocListeners = (onDocPointerDown: (e: PointerEvent) => void, onDocKeyDown: (e: KeyboardEvent) => void): void => {
        document.removeEventListener('pointerdown', onDocPointerDown, true);
        document.removeEventListener('keydown', onDocKeyDown, true);
    };

    const onDocPointerDown = (e: PointerEvent): void => {
        if (!open) {
            return;
        }

        if (e.target instanceof Node && root.contains(e.target)) {
            return;
        }

        setOpen(false);
    };

    const onDocKeyDown = (e: KeyboardEvent): void => {
        if (open && e.key === 'Escape') {
            e.preventDefault();
            setOpen(false);
            trigger.focus();
        }
    };

    const closeThis = (): void => {
        setOpen(false);
    };

    function setOpen(next: boolean): void {
        if (next === open) {
            return;
        }

        if (next) {
            closeActiveRoomCombobox?.();
            closeActiveRoomSort?.();
            closeAllReservoFormSelects();
        } else {
            detachDocListeners(onDocPointerDown, onDocKeyDown);

            if (closeActiveRoomSort === closeThis) {
                closeActiveRoomSort = null;
            }
        }

        open = next;
        trigger.setAttribute('aria-expanded', next ? 'true' : 'false');

        if (next) {
            panel.hidden = false;
            panel.classList.add('is-open');
            document.addEventListener('pointerdown', onDocPointerDown, true);
            document.addEventListener('keydown', onDocKeyDown, true);
            closeActiveRoomSort = closeThis;
        } else {
            panel.classList.remove('is-open');
            panel.hidden = true;
        }
    }

    trigger.addEventListener('click', (e) => {
        e.preventDefault();
        setOpen(!open);
    });

    panel.addEventListener('click', (e) => {
        const t = (e.target as Element | null)?.closest?.('button.reservo-room-sort__opt');

        if (!(t instanceof HTMLButtonElement)) {
            return;
        }

        e.preventDefault();
        const val = t.dataset.value ?? 'name';

        hidden.value = val;
        labelEl.textContent = (t.textContent ?? '').trim();

        panel.querySelectorAll<HTMLButtonElement>('.reservo-room-sort__opt').forEach((btn) => {
            const selected = btn.dataset.value === val;

            btn.setAttribute('aria-selected', selected ? 'true' : 'false');
            btn.classList.toggle('bg-gray-100', selected);
            btn.classList.toggle('font-medium', selected);
        });

        setOpen(false);
        trigger.focus();
    });
}

const RESERVO_FORM_SELECT_OPT = 'button.reservo-form-select__opt';

function bindReservoFormSelect(root: HTMLElement): void {
    const hidden = root.querySelector<HTMLInputElement>('[data-reservo-form-select-input]');
    const trigger = root.querySelector<HTMLButtonElement>('[data-reservo-form-select-trigger]');
    const panel = root.querySelector<HTMLElement>('[data-reservo-form-select-panel]');
    const labelEl = root.querySelector<HTMLElement>('[data-reservo-form-select-label]');
    const placeholder = (root.dataset.placeholder ?? '').trim() || 'Select';

    if (!hidden || !trigger || !panel || !labelEl) {
        return;
    }

    if (!claimElementUi(root, 'form-select')) {
        return;
    }

    const isRoomField = hidden.name === 'room_id';

    const syncHourlyFromButton = (btn: HTMLButtonElement | null): void => {
        if (!isRoomField) {
            return;
        }

        const raw = btn?.dataset.hourlyRate;

        if (raw === undefined || raw === '') {
            hidden.removeAttribute('data-hourly-rate');
        } else {
            hidden.setAttribute('data-hourly-rate', raw);
        }
    };

    const syncLabelFromValue = (): void => {
        const v = hidden.value;

        if (!v) {
            labelEl.textContent = placeholder;

            if (isRoomField) {
                hidden.removeAttribute('data-hourly-rate');
            }

            return;
        }

        let matched: HTMLButtonElement | null = null;

        for (const b of panel.querySelectorAll<HTMLButtonElement>(RESERVO_FORM_SELECT_OPT)) {
            if ((b.dataset.value ?? '') === v) {
                matched = b;
                break;
            }
        }

        if (matched) {
            labelEl.textContent = (matched.textContent ?? '').trim();
            syncHourlyFromButton(matched);
        } else {
            labelEl.textContent = v;
        }
    };

    const updateOptionState = (): void => {
        const v = hidden.value;
        panel.querySelectorAll<HTMLButtonElement>(RESERVO_FORM_SELECT_OPT).forEach((btn) => {
            const sel = (btn.dataset.value ?? '') === v;
            btn.setAttribute('aria-selected', sel ? 'true' : 'false');
            btn.classList.toggle('bg-gray-100', sel);
            btn.classList.toggle('font-medium', sel);
        });
    };

    let open = false;

    const detachDocListeners = (onDocPointerDown: (e: PointerEvent) => void, onDocKeyDown: (e: KeyboardEvent) => void): void => {
        document.removeEventListener('pointerdown', onDocPointerDown, true);
        document.removeEventListener('keydown', onDocKeyDown, true);
    };

    const onDocPointerDown = (e: PointerEvent): void => {
        if (!open) {
            return;
        }

        if (e.target instanceof Node && root.contains(e.target)) {
            return;
        }

        setOpen(false);
    };

    const onDocKeyDown = (e: KeyboardEvent): void => {
        if (open && e.key === 'Escape') {
            e.preventDefault();
            setOpen(false);
            trigger.focus();
        }
    };

    function setOpen(next: boolean): void {
        if (next === open) {
            return;
        }

        if (next) {
            document.querySelectorAll<HTMLElement>('[data-reservo-form-select]').forEach((el) => {
                if (el !== root) {
                    el.dispatchEvent(new CustomEvent('reservo-close-select', { bubbles: false }));
                }
            });
            closeActiveRoomCombobox?.();
            closeActiveRoomSort?.();
        } else {
            detachDocListeners(onDocPointerDown, onDocKeyDown);
        }

        open = next;
        trigger.setAttribute('aria-expanded', next ? 'true' : 'false');

        if (next) {
            panel.hidden = false;
            panel.classList.add('is-open');
            document.addEventListener('pointerdown', onDocPointerDown, true);
            document.addEventListener('keydown', onDocKeyDown, true);
        } else {
            panel.classList.remove('is-open');
            panel.hidden = true;
        }
    }

    root.addEventListener('reservo-close-select', () => setOpen(false));

    hidden.addEventListener('change', () => {
        syncLabelFromValue();
        updateOptionState();
    });

    trigger.addEventListener('click', (e) => {
        e.preventDefault();
        setOpen(!open);
    });

    panel.addEventListener('click', (e) => {
        const t = (e.target as Element | null)?.closest?.(RESERVO_FORM_SELECT_OPT);

        if (!(t instanceof HTMLButtonElement)) {
            return;
        }

        e.preventDefault();
        const val = t.dataset.value ?? '';

        hidden.value = val;
        syncLabelFromValue();
        updateOptionState();
        setOpen(false);
        trigger.focus();
        hidden.dispatchEvent(new Event('change', { bubbles: true }));
    });

    syncLabelFromValue();
    updateOptionState();
}

function bindRoomFilterComboboxes(): void {
    document.querySelectorAll<HTMLElement>('[data-reservo-combobox]').forEach(bindRoomFilterCombobox);
}

function bindRoomSortSelects(): void {
    document.querySelectorAll<HTMLElement>('[data-reservo-room-sort]').forEach(bindRoomSortSelect);
}

function bindReservoFormSelects(): void {
    document.querySelectorAll<HTMLElement>('[data-reservo-form-select]').forEach(bindReservoFormSelect);
}

function positionAdminSidebarAccountPanel(details: HTMLDetailsElement): void {
    const panel = details.querySelector<HTMLElement>('.reservo-sidebar-account-panel');
    const summary = details.querySelector<HTMLElement>('summary');

    if (!panel || !summary) {
        return;
    }

    const place = (): void => {
        const rect = summary.getBoundingClientRect();
        const pad = 8;
        const gap = 6;
        let left = rect.right + gap;

        const pr = panel.getBoundingClientRect();
        const w = pr.width;
        const h = pr.height;
        const vw = window.innerWidth;
        const vh = window.innerHeight;

        if (left + w > vw - pad) {
            left = Math.max(pad, rect.left - gap - w);
        }

        let top = rect.top + rect.height / 2 - h / 2;
        top = Math.max(pad, Math.min(top, vh - h - pad));

        panel.style.left = `${Math.round(left)}px`;
        panel.style.top = `${Math.round(top)}px`;
    };

    window.requestAnimationFrame(() => {
        place();
        window.requestAnimationFrame(place);
    });
}

function clearAdminSidebarAccountPanelPosition(panel: HTMLElement): void {
    panel.style.removeProperty('left');
    panel.style.removeProperty('top');
}

const RESERVO_SIDEBAR_NAV_TOOLTIP_ID = 'reservo-nav-tooltip-floater';

function hideSidebarNavTooltip(): void {
    const el = document.getElementById(RESERVO_SIDEBAR_NAV_TOOLTIP_ID);

    if (!el) {
        return;
    }

    el.classList.remove('is-visible');
    el.textContent = '';
    el.style.removeProperty('left');
    el.style.removeProperty('top');
}

function sidebarNavTooltipShouldShow(target: HTMLElement): boolean {
    if (target.closest('.admin-sidebar-footer-inner')) {
        return true;
    }

    return document.documentElement.classList.contains('admin-sidebar-rail-collapsed');
}

function formatHourlyRateLabel(raw: string): string | null {
    const t = raw.trim();

    if (!t) {
        return null;
    }

    const n = Number.parseFloat(t.replace(',', '.'));

    if (!Number.isFinite(n) || n < 0) {
        return null;
    }

    return `$${n.toFixed(2)}/hr`;
}

function parseAmenityLines(text: string): string[] {
    return text
        .split(/\s*[\n,]+\s*/u)
        .map((l) => l.trim())
        .filter(Boolean)
        .slice(0, 30);
}

function bindAdminRoomFormPreview(): void {
    const form = document.querySelector<HTMLFormElement>('form[data-admin-room-form]');
    const previewRoot = document.getElementById('admin-room-preview');

    if (!form || !previewRoot) {
        return;
    }

    if (!claimElementUi(form, 'admin-room-preview')) {
        return;
    }

    const q = (sel: string): HTMLInputElement | HTMLTextAreaElement | null =>
        form.querySelector<HTMLInputElement | HTMLTextAreaElement>(sel);

    const nameIn = q('#name');
    const capIn = q('#capacity');
    const descIn = q('#description');
    const locIn = q('#location');
    const sizeIn = q('#size_sqm');
    const hourlyIn = q('#hourly_rate');
    const amenIn = q('#amenities_text');
    const imgUrlIn = q('#image_url');

    const nameEl = document.getElementById('admin-room-preview-name');
    const locEl = document.getElementById('admin-room-preview-location');
    const metaEl = document.getElementById('admin-room-preview-meta');
    const amenEl = document.getElementById('admin-room-preview-amenities');
    const descEl = document.getElementById('admin-room-preview-description');
    const imgEl = document.getElementById('admin-room-preview-img') as HTMLImageElement | null;
    const phEl = document.getElementById('admin-room-preview-photo-placeholder');

    if (!nameEl || !locEl || !metaEl || !amenEl || !descEl || !imgEl || !phEl) {
        return;
    }

    const defaultName = previewRoot.dataset.defaultName ?? 'Room name';
    const upToTemplate = previewRoot.dataset.upToTemplate ?? 'Up to {n} people';
    const moreLabel = previewRoot.dataset.moreLabel ?? 'more';

    let lastImgUrl = '';

    const showPlaceholder = (): void => {
        imgEl.classList.add('hidden');
        phEl.classList.remove('hidden');
        imgEl.removeAttribute('src');
    };

    const syncImage = (url: string): void => {
        const u = url.trim();

        if (!u) {
            lastImgUrl = '';
            showPlaceholder();

            return;
        }

        if (u !== lastImgUrl) {
            lastImgUrl = u;
            imgEl.classList.add('hidden');
            phEl.classList.remove('hidden');
            imgEl.src = u;
        }
    };

    imgEl.addEventListener('load', () => {
        if (!imgEl.getAttribute('src')) {
            return;
        }

        imgEl.classList.remove('hidden');
        phEl.classList.add('hidden');
    });

    imgEl.addEventListener('error', () => {
        lastImgUrl = '';
        showPlaceholder();
    });

    const makePill = (classes: string, text: string): HTMLSpanElement => {
        const span = document.createElement('span');
        span.className = classes;
        span.textContent = text;

        return span;
    };

    const update = (): void => {
        const name = nameIn?.value.trim() ?? '';
        nameEl.textContent = name || defaultName;

        const loc = locIn?.value.trim() ?? '';

        if (loc) {
            locEl.textContent = loc;
            locEl.classList.remove('hidden');
        } else {
            locEl.textContent = '';
            locEl.classList.add('hidden');
        }

        const capRaw = capIn?.value ?? '1';
        let cap = Number.parseInt(capRaw, 10);

        if (!Number.isFinite(cap) || cap < 1) {
            cap = 1;
        }

        metaEl.textContent = '';

        const capText = upToTemplate.replace(/\{n\}/g, String(cap));
        metaEl.appendChild(
            makePill(
                'inline-flex rounded-full border border-gray-200 bg-gray-50 px-2.5 py-0.5 text-xs font-medium text-gray-700',
                capText,
            ),
        );

        const sizeRaw = sizeIn?.value.trim() ?? '';
        const sizeNum = sizeRaw === '' ? Number.NaN : Number.parseInt(sizeRaw, 10);

        if (sizeRaw !== '' && Number.isFinite(sizeNum) && sizeNum > 0) {
            metaEl.appendChild(
                makePill(
                    'inline-flex rounded-full border border-gray-200 bg-white px-2.5 py-0.5 text-xs font-medium text-gray-600',
                    `${sizeNum} m²`,
                ),
            );
        }

        const hrLabel = hourlyIn ? formatHourlyRateLabel(hourlyIn.value) : null;

        if (hrLabel) {
            metaEl.appendChild(
                makePill(
                    'inline-flex rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-0.5 text-xs font-medium text-emerald-800',
                    hrLabel,
                ),
            );
        }

        const amenities = amenIn ? parseAmenityLines(amenIn.value) : [];
        amenEl.textContent = '';

        if (amenities.length > 0) {
            amenEl.classList.remove('hidden');
            const show = amenities.slice(0, 3);

            for (const a of show) {
                amenEl.appendChild(
                    makePill(
                        'rounded-md bg-slate-100 px-2 py-0.5 text-[11px] font-medium text-slate-700',
                        a,
                    ),
                );
            }

            if (amenities.length > 3) {
                amenEl.appendChild(
                    makePill(
                        'rounded-md bg-slate-50 px-2 py-0.5 text-[11px] text-slate-500',
                        `+${amenities.length - 3} ${moreLabel}`,
                    ),
                );
            }
        } else {
            amenEl.classList.add('hidden');
        }

        const desc = descIn?.value.trim() ?? '';

        if (desc) {
            descEl.textContent = desc;
            descEl.classList.remove('hidden');
        } else {
            descEl.textContent = '';
            descEl.classList.add('hidden');
        }

        syncImage(imgUrlIn?.value ?? '');
    };

    form.addEventListener('input', update);
    form.addEventListener('change', update);
    update();
}

function bindAdminSidebarNavTooltips(): void {
    const sidebar = document.getElementById('reservo-admin-sidebar');

    if (!sidebar) {
        return;
    }

    let tipEl: HTMLDivElement | null = null;
    let showTimer: number | undefined;

    const getTipEl = (): HTMLDivElement => {
        if (!tipEl) {
            tipEl = document.createElement('div');
            tipEl.id = RESERVO_SIDEBAR_NAV_TOOLTIP_ID;
            tipEl.setAttribute('role', 'tooltip');
            tipEl.setAttribute('aria-hidden', 'true');
            document.body.appendChild(tipEl);
        }

        return tipEl;
    };

    const tipDelayMs = (): number =>
        window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 0 : 280;

    const showAtTarget = (target: HTMLElement, text: string): void => {
        if (!sidebarNavTooltipShouldShow(target)) {
            hideSidebarNavTooltip();

            return;
        }

        const el = getTipEl();
        el.textContent = text;

        const place = (): void => {
            const rect = target.getBoundingClientRect();
            const pad = 8;
            const gap = 10;
            const tw = el.offsetWidth;
            const th = el.offsetHeight;
            let left = rect.right + gap;
            const vw = window.innerWidth;
            const vh = window.innerHeight;

            if (left + tw > vw - pad) {
                left = Math.max(pad, rect.left - gap - tw);
            }

            let top = rect.top + rect.height / 2 - th / 2;
            top = Math.max(pad, Math.min(top, vh - th - pad));
            el.style.left = `${Math.round(left)}px`;
            el.style.top = `${Math.round(top)}px`;
            el.classList.add('is-visible');
        };

        window.requestAnimationFrame(() => {
            place();
            window.requestAnimationFrame(place);
        });
    };

    const onEnter = (e: Event): void => {
        const t = e.currentTarget as HTMLElement;
        const text = t.dataset.sidebarTooltip?.trim();

        if (!text) {
            return;
        }

        if (!sidebarNavTooltipShouldShow(t)) {
            return;
        }

        window.clearTimeout(showTimer);
        showTimer = window.setTimeout(() => showAtTarget(t, text), tipDelayMs());
    };

    const onLeave = (): void => {
        window.clearTimeout(showTimer);
        hideSidebarNavTooltip();
    };

    sidebar.querySelectorAll<HTMLElement>('[data-sidebar-tooltip]').forEach((el) => {
        if (!claimElementUi(el, 'sidebar-tooltip')) {
            return;
        }

        el.addEventListener('mouseenter', onEnter);
        el.addEventListener('mouseleave', onLeave);
        el.addEventListener('focusin', onEnter);
        el.addEventListener('focusout', onLeave);
    });

    if (claimElementUi(sidebar, 'sidebar-tooltip-scroll')) {
        sidebar.addEventListener('scroll', onLeave, { capture: true, passive: true });
    }

    if (!sidebarNavTooltipResizeBound) {
        sidebarNavTooltipResizeBound = true;
        window.addEventListener('resize', () => hideSidebarNavTooltip(), { passive: true });
    }
}

function bindAdminSidebarAccountPopovers(): void {
    const menus = document.querySelectorAll<HTMLDetailsElement>('.admin-sidebar-account-menu');

    if (!menus.length) {
        return;
    }

    let repositionRaf = 0;

    const repositionOpen = (): void => {
        if (repositionRaf) {
            window.cancelAnimationFrame(repositionRaf);
        }

        repositionRaf = window.requestAnimationFrame(() => {
            repositionRaf = 0;
            document.querySelectorAll<HTMLDetailsElement>('.admin-sidebar-account-menu').forEach((details) => {
                if (details.open) {
                    positionAdminSidebarAccountPanel(details);
                }
            });
        });
    };

    menus.forEach((details) => {
        if (!claimElementUi(details, 'account-menu')) {
            return;
        }

        const panel = details.querySelector<HTMLElement>('.reservo-sidebar-account-panel');

        if (!panel) {
            return;
        }

        details.addEventListener('toggle', () => {
            if (!details.open) {
                clearAdminSidebarAccountPanelPosition(panel);

                return;
            }

            positionAdminSidebarAccountPanel(details);
        });
    });

    if (!adminAccountPopoverGlobalsBound) {
        adminAccountPopoverGlobalsBound = true;
        window.addEventListener('resize', repositionOpen, { passive: true });
        document.addEventListener('scroll', repositionOpen, { capture: true, passive: true });
    }
}

function bindAdminSidebarRail(): void {
    const toggle = document.getElementById('reservo-admin-sidebar-toggle');

    if (!toggle) {
        return;
    }

    if (!claimElementUi(toggle, 'sidebar-rail')) {
        return;
    }

    const sync = (): void => {
        const collapsed = document.documentElement.classList.contains('admin-sidebar-rail-collapsed');

        toggle.setAttribute('aria-expanded', collapsed ? 'false' : 'true');

        const expand = toggle.dataset.sidebarLabelExpand;
        const collapse = toggle.dataset.sidebarLabelCollapse;

        if (expand && collapse) {
            toggle.setAttribute('aria-label', collapsed ? expand : collapse);
            toggle.dataset.sidebarTooltip = collapsed ? expand : collapse;
        }
    };

    sync();

    toggle.addEventListener('click', () => {
        hideSidebarNavTooltip();

        if (document.documentElement.classList.contains('admin-sidebar-rail-collapsed')) {
            document.documentElement.classList.remove('admin-sidebar-rail-collapsed');

            try {
                localStorage.removeItem('reservo_admin_sidebar_rail');
            } catch {
                /* private mode */
            }
        } else {
            document.documentElement.classList.add('admin-sidebar-rail-collapsed');

            try {
                localStorage.setItem('reservo_admin_sidebar_rail', '1');
            } catch {
                /* private mode */
            }
        }

        sync();

        window.requestAnimationFrame(() => {
            document.querySelectorAll<HTMLDetailsElement>('.admin-sidebar-account-menu').forEach((details) => {
                if (details.open) {
                    positionAdminSidebarAccountPanel(details);
                }
            });
        });
    });
}

function bindReservoMobileMenu(): void {
    const menu = document.querySelector<HTMLDetailsElement>('.reservo-mobile-menu');

    if (!menu) {
        return;
    }

    if (!claimElementUi(menu, 'mobile-menu')) {
        return;
    }

    const closers = menu.querySelectorAll<HTMLButtonElement>('.reservo-mobile-menu__close');
    const summary = menu.querySelector<HTMLElement>('summary');

    const close = (): void => {
        if (menu.open) {
            menu.removeAttribute('open');
        }
    };

    menu.addEventListener('toggle', () => {
        /* Do not lock body scroll: lets the page scroll with the menu open, and avoids scrollbar "shake". */
        summary?.setAttribute('aria-expanded', menu.open ? 'true' : 'false');

        if (menu.open) {
            window.requestAnimationFrame(() => {
                closers[0]?.focus({ preventScroll: true });
            });
        } else {
            summary?.focus({ preventScroll: true });
        }
    });

    /* Backdrop is pointer-events: none (see app.css) so the page can scroll; close with X or Escape. */

    closers.forEach((btn) => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            close();
        });
    });

    if (!reservoMobileMenuEscapeBound) {
        reservoMobileMenuEscapeBound = true;

        document.addEventListener(
            'keydown',
            (e: KeyboardEvent) => {
                if (e.key !== 'Escape') {
                    return;
                }

                document.querySelectorAll<HTMLDetailsElement>('.reservo-mobile-menu').forEach((m) => {
                    if (m.open) {
                        m.removeAttribute('open');
                    }
                });
            },
            true,
        );
    }
}

function boot(): void {
    bindReservoAnchorLoadingLinks();
    bindTurboNavigation();
    bindNavigationProgress();
    bindFormBusy();
    bindPageScopedEnhancements();
    finishNavProgress();
    scrollRoomAvailabilityResultsIntoView();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
} else {
    boot();
}
