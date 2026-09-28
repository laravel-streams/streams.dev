/*
 * Inline docs filter (the sidebar search box).
 *
 * As you type it narrows the sidebar to matching links (keeping their parent
 * groups, opened), marks the matched text, highlights matches in the page body
 * with the CSS Custom Highlight API, and dims non-matching "On this page"
 * entries. Arrow keys move through the results, Enter follows one, Esc clears.
 * ⌘K is untouched and stays the full-text search dialog.
 */

const DEBOUNCE_MS = 120;
const MAX_PAGE_MATCHES = 400;

/**
 * Lower-case, strip diacritics, and keep a map from each normalized character
 * back to its index in the original string so highlights land on the source.
 */
export function normalizeWithMap(text) {
    let out = '';
    const map = [];
    for (let i = 0; i < text.length; i++) {
        const piece = text[i].normalize('NFD').replace(/\p{M}/gu, '').toLowerCase();
        for (let j = 0; j < piece.length; j++) {
            out += piece[j];
            map.push(i);
        }
    }
    return { text: out, map };
}

export function normalize(text) {
    return normalizeWithMap(text).text;
}

/** All [start, end) ranges of `query` (already normalized) in `text`, in original indices. */
export function findMatches(text, query, limit = Infinity) {
    if (!query) {
        return [];
    }
    const { text: norm, map } = normalizeWithMap(text);
    const ranges = [];
    let from = 0;
    while (ranges.length < limit) {
        const at = norm.indexOf(query, from);
        if (at === -1) {
            break;
        }
        const start = map[at];
        let end = map[at + query.length - 1] + 1;
        // Keep trailing combining marks (decomposed accents) inside the match.
        while (end < text.length && /\p{M}/u.test(text[end])) {
            end++;
        }
        ranges.push([start, end]);
        from = at + query.length;
    }
    return ranges;
}

function escapeHtml(text) {
    return text.replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[c]);
}

/** Replace an element's text with the same text, the matched ranges wrapped in <mark>. */
function markText(el, original, ranges) {
    let html = '';
    let last = 0;
    for (const [start, end] of ranges) {
        html += escapeHtml(original.slice(last, start)) + '<mark class="docs-filter-mark">' + escapeHtml(original.slice(start, end)) + '</mark>';
        last = end;
    }
    el.innerHTML = html + escapeHtml(original.slice(last));
}

export function initDocsFilter() {
    const input = document.querySelector('[data-docs-filter]');
    const nav = document.querySelector('[data-docs-nav]');
    if (!input || !nav) {
        return;
    }

    const sidebar = input.closest('.docs-sidebar') ?? nav;
    const meta = sidebar.querySelector('[data-docs-filter-meta]');
    const status = sidebar.querySelector('[data-docs-filter-status]');
    const nextBtn = sidebar.querySelector('[data-docs-filter-next]');
    const empty = sidebar.querySelector('[data-docs-filter-empty]');
    const emptyQuery = sidebar.querySelector('[data-docs-filter-query]');
    const emptySearch = sidebar.querySelector('[data-docs-filter-search]');
    const content = document.querySelector('.documentation-content');
    const supportsHighlights = typeof CSS !== 'undefined' && 'highlights' in CSS && typeof Highlight !== 'undefined';

    // Every link and group label, with its original text cached once.
    const links = [...nav.querySelectorAll('a.docs-nav-link')];
    links.forEach((a, i) => {
        a.dataset.filterText = a.textContent.trim();
        a.id ||= `docs-nav-item-${i}`;
    });
    const labels = [...nav.querySelectorAll('[data-filter-label]')].map((el) => {
        el.dataset.filterText ??= el.textContent.trim();
        return el;
    });

    let timer = null;
    let results = [];
    let active = -1;
    let pageRanges = [];
    let pageIndex = -1;
    let openedDetails = [];

    const tocLinks = () => [...document.querySelectorAll('.documentation__toc a.toc-link')];

    const setActive = (index) => {
        results.forEach((a) => a.classList.remove('is-filter-active'));
        active = index;
        const current = results[active];
        if (current) {
            current.classList.add('is-filter-active');
            current.scrollIntoView({ block: 'nearest' });
            input.setAttribute('aria-activedescendant', current.id);
        } else {
            input.removeAttribute('aria-activedescendant');
        }
    };

    const clearPage = () => {
        pageRanges = [];
        pageIndex = -1;
        if (supportsHighlights) {
            CSS.highlights.delete('docs-filter');
            CSS.highlights.delete('docs-filter-current');
        }
    };

    const reset = () => {
        sidebar.classList.remove('is-filtering');
        nav.querySelectorAll('.is-filter-hidden, .is-filter-open, .is-filter-match').forEach((el) => {
            el.classList.remove('is-filter-hidden', 'is-filter-open', 'is-filter-match');
        });
        links.forEach((a) => {
            a.classList.remove('is-filter-active');
            if (a.querySelector('mark')) {
                a.textContent = a.dataset.filterText;
            }
        });
        labels.forEach((el) => {
            if (el.querySelector('mark')) {
                el.textContent = el.dataset.filterText;
            }
        });
        openedDetails.forEach((d) => d.removeAttribute('open'));
        openedDetails = [];
        tocLinks().forEach((a) => {
            a.classList.remove('is-filter-hit');
            if (a.dataset.filterText !== undefined && a.querySelector('mark')) {
                a.textContent = a.dataset.filterText;
            }
        });
        clearPage();
        results = [];
        active = -1;
        input.removeAttribute('aria-activedescendant');
        meta.hidden = true;
        empty.hidden = true;
    };

    const highlightPage = (query) => {
        clearPage();
        if (!content) {
            return;
        }
        const walker = document.createTreeWalker(content, NodeFilter.SHOW_TEXT, {
            acceptNode: (node) => (node.parentElement?.closest('.anchorjs-link, script, style') ? NodeFilter.FILTER_REJECT : NodeFilter.FILTER_ACCEPT),
        });
        for (let node = walker.nextNode(); node && pageRanges.length < MAX_PAGE_MATCHES; node = walker.nextNode()) {
            for (const [start, end] of findMatches(node.nodeValue, query, MAX_PAGE_MATCHES - pageRanges.length)) {
                const range = document.createRange();
                range.setStart(node, start);
                range.setEnd(node, end);
                pageRanges.push(range);
            }
        }
        if (supportsHighlights && pageRanges.length) {
            CSS.highlights.set('docs-filter', new Highlight(...pageRanges));
        }
    };

    const filterToc = (query) => {
        tocLinks().forEach((a) => {
            a.dataset.filterText ??= a.textContent.trim();
            const ranges = findMatches(a.dataset.filterText, query);
            if (ranges.length) {
                markText(a, a.dataset.filterText, ranges);
                a.classList.add('is-filter-hit');
            } else {
                a.textContent = a.dataset.filterText;
                a.classList.remove('is-filter-hit');
            }
        });
    };

    const apply = () => {
        const raw = input.value.trim();
        reset();
        if (!raw) {
            return;
        }
        const query = normalize(raw);
        sidebar.classList.add('is-filtering');

        const shown = new Set();
        // A matching group label (package or category) keeps its whole group.
        labels.forEach((label) => {
            const ranges = findMatches(label.dataset.filterText, query);
            if (!ranges.length) {
                return;
            }
            markText(label, label.dataset.filterText, ranges);
            const group = label.closest('[data-filter-group]');
            group?.querySelectorAll('a.docs-nav-link').forEach((a) => shown.add(a));
        });
        links.forEach((a) => {
            const ranges = findMatches(a.dataset.filterText, query);
            if (ranges.length) {
                if (!a.querySelector('mark')) {
                    markText(a, a.dataset.filterText, ranges);
                }
                shown.add(a);
            }
        });

        results = links.filter((a) => shown.has(a));
        results.forEach((a) => a.classList.add('is-filter-match'));

        // Keep each result's ancestors (parent group links, sections), open collapsed containers, hide the rest.
        const keep = new Set();
        results.forEach((a) => {
            for (let el = a; el && el !== nav; el = el.parentElement) {
                keep.add(el);
                if (el.tagName === 'DETAILS' && !el.open) {
                    el.setAttribute('open', '');
                    openedDetails.push(el);
                }
                if (el.matches('.docs-nav-collapsed, [x-show]')) {
                    el.classList.add('is-filter-open');
                }
            }
        });
        nav.querySelectorAll('[data-filter-section], [data-filter-group], li, a.docs-nav-link').forEach((el) => {
            if (!keep.has(el)) {
                el.classList.add('is-filter-hidden');
            }
        });
        // The group label link of a shown group stays visible as context.
        nav.querySelectorAll('[data-filter-group]').forEach((group) => {
            if (keep.has(group)) {
                group.querySelector(':scope > a.docs-nav-link')?.classList.remove('is-filter-hidden');
            }
        });

        highlightPage(query);
        filterToc(query);

        const pages = results.length;
        const onPage = pageRanges.length;
        const parts = [`${pages} ${pages === 1 ? 'page' : 'pages'}`];
        if (content) {
            parts.push(`${onPage}${onPage >= MAX_PAGE_MATCHES ? '+' : ''} in page`);
        }
        status.textContent = parts.join(' · ');
        meta.hidden = false;
        nextBtn.hidden = !(supportsHighlights && onPage);
        nextBtn.setAttribute('aria-label', 'Go to the first match in the page');
        nextBtn.textContent = 'Next ↓';
        empty.hidden = pages > 0;
        emptyQuery.textContent = `“${raw}”`;
    };

    const schedule = () => {
        clearTimeout(timer);
        timer = setTimeout(apply, DEBOUNCE_MS);
    };

    const clear = () => {
        clearTimeout(timer);
        input.value = '';
        reset();
    };

    const goToPageMatch = () => {
        if (!pageRanges.length) {
            return;
        }
        pageIndex = (pageIndex + 1) % pageRanges.length;
        const range = pageRanges[pageIndex];
        CSS.highlights.set('docs-filter-current', new Highlight(range));
        const target = range.startContainer.parentElement;
        target?.scrollIntoView({ block: 'center', behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth' });
        nextBtn.textContent = `${pageIndex + 1} / ${pageRanges.length} ↓`;
        nextBtn.setAttribute('aria-label', `Match ${pageIndex + 1} of ${pageRanges.length}, go to next`);
    };

    input.addEventListener('input', schedule);
    input.addEventListener('search', () => {
        if (!input.value) {
            clear();
        }
    });

    input.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
            if (timer && input.value.trim()) {
                clearTimeout(timer);
                apply();
            }
            if (!results.length) {
                return;
            }
            e.preventDefault();
            const step = e.key === 'ArrowDown' ? 1 : -1;
            setActive(active === -1 ? (step > 0 ? 0 : results.length - 1) : (active + step + results.length) % results.length);
        } else if (e.key === 'Enter') {
            clearTimeout(timer);
            apply();
            const target = results[active] ?? results[0];
            if (target) {
                e.preventDefault();
                window.location.href = target.href;
            }
        } else if (e.key === 'Escape') {
            if (input.value) {
                // Clear first; a second Esc falls through (closes the phone drawer).
                e.preventDefault();
                e.stopPropagation();
                clear();
            } else {
                input.blur();
            }
        }
    });

    nextBtn?.addEventListener('click', goToPageMatch);
    emptySearch?.addEventListener('click', () => {
        const query = input.value.trim();
        window.StreamsDocsSearch?.open(query);
    });

    // "/" focuses the filter when the sidebar is on screen (desktop) and focus is not in a field.
    document.addEventListener('keydown', (e) => {
        if (e.key !== '/' || e.metaKey || e.ctrlKey || e.altKey) {
            return;
        }
        const t = e.target;
        if (t instanceof HTMLElement && (t.isContentEditable || t.closest('input, textarea, select'))) {
            return;
        }
        if (input.offsetParent === null || input.closest('[inert]')) {
            return;
        }
        e.preventDefault();
        input.focus();
        input.select();
    });
}
