/**
 * Blade layouts: navigation progress + form submit busy state (no extra dependencies).
 */
const NAV_KEY = 'reservo_nav';

function getProgressEl(): HTMLElement | null {
    return document.getElementById('reservo-progress');
}

function isInternalNavLink(anchor: HTMLAnchorElement): boolean {
    if (anchor.target === '_blank' || anchor.getAttribute('download') !== null) {
        return false;
    }

    const href = anchor.getAttribute('href');

    if (!href || href.startsWith('#') || href.startsWith('javascript:')) {
        return false;
    }

    try {
        const next = new URL(href, window.location.href);

        if (next.origin !== window.location.origin) {
            return false;
        }

        /* Same path + query (hash-only) — no full navigation */
        if (next.pathname === window.location.pathname && next.search === window.location.search) {
            return false;
        }

        return true;
    } catch {
        return false;
    }
}

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
}

function bindNavigationProgress(): void {
    document.addEventListener(
        'click',
        (e: MouseEvent) => {
            if (e.defaultPrevented || e.button !== 0) {
                return;
            }

            if (e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) {
                return;
            }

            const a = (e.target as Element | null)?.closest?.('a');

            if (!(a instanceof HTMLAnchorElement)) {
                return;
            }

            if (a.dataset.noProgress !== undefined) {
                return;
            }

            if (!isInternalNavLink(a)) {
                return;
            }

            startNavProgress();

            if (a.classList.contains('reservo-loading-link')) {
                applyAnchorLoading(a);
            }
        },
        true,
    );

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

function bindRoomFilterComboboxes(): void {
    document.querySelectorAll<HTMLElement>('[data-reservo-combobox]').forEach(bindRoomFilterCombobox);
}

function bindReservoMobileMenu(): void {
    const menu = document.querySelector<HTMLDetailsElement>('.reservo-mobile-menu');

    if (!menu) {
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

    const onDocKey = (e: KeyboardEvent): void => {
        if (e.key === 'Escape' && menu.open) {
            e.preventDefault();
            close();
        }
    };

    document.addEventListener('keydown', onDocKey, true);
}

function boot(): void {
    bindNavigationProgress();
    bindFormBusy();
    bindRoomFilterComboboxes();
    bindReservoMobileMenu();
    finishNavProgress();
    scrollRoomAvailabilityResultsIntoView();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
} else {
    boot();
}
