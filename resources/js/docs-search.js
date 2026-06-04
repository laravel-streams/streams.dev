import Fuse from 'fuse.js';

const GROUP_LABELS = {
    reference: 'Reference',
    guide: 'Guides',
    page: 'Pages',
};

export function initDocsSearch() {
    const root = document.getElementById('docs-search-root');
    if (!root) {
        return;
    }

    const overlay = root.querySelector('[data-docs-search-overlay]');
    const input = root.querySelector('[data-docs-search-input]');
    const resultsEl = root.querySelector('[data-docs-search-results]');
    const emptyEl = root.querySelector('[data-docs-search-empty]');
    const indexUrl = root.dataset.indexUrl;

    let fuse = null;
    let items = [];
    let activeIndex = -1;

    const loadIndex = async () => {
        if (items.length) {
            return;
        }

        const response = await fetch(indexUrl);
        items = await response.json();
        fuse = new Fuse(items, {
            keys: [
                { name: 'title', weight: 0.45 },
                { name: 'description', weight: 0.25 },
                { name: 'excerpt', weight: 0.2 },
                { name: 'package', weight: 0.1 },
            ],
            threshold: 0.38,
            includeScore: true,
        });
    };

    const open = async () => {
        await loadIndex();
        root.hidden = false;
        root.setAttribute('aria-hidden', 'false');
        document.body.classList.add('overflow-hidden');
        input.value = '';
        activeIndex = -1;
        render([]);
        requestAnimationFrame(() => input.focus());
    };

    const close = () => {
        root.hidden = true;
        root.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('overflow-hidden');
        activeIndex = -1;
    };

    const render = (results) => {
        resultsEl.innerHTML = '';
        emptyEl?.classList.toggle('hidden', results.length > 0);

        if (!results.length) {
            return;
        }

        const grouped = {};
        results.forEach((result) => {
            const section = result.item.section || 'guide';
            if (!grouped[section]) {
                grouped[section] = [];
            }
            grouped[section].push(result);
        });

        Object.keys(grouped).forEach((section) => {
            const heading = document.createElement('li');
            heading.className = 'px-4 py-1.5 text-xs font-semibold uppercase tracking-wide text-[var(--color-text-muted)]';
            heading.textContent = GROUP_LABELS[section] || section;
            resultsEl.appendChild(heading);

            grouped[section].forEach((result, i) => {
                const globalIndex = results.indexOf(result);
                const li = document.createElement('li');
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'docs-search-result w-full text-left px-4 py-2.5 hover:bg-[var(--color-page)]' + (globalIndex === activeIndex ? ' bg-[var(--color-page)]' : '');
                btn.dataset.index = String(globalIndex);
                btn.innerHTML = `
                    <span class="block font-medium text-[var(--color-text)]">${escapeHtml(result.item.title)}</span>
                    <span class="block mt-0.5 text-xs text-[var(--color-text-muted)]">${escapeHtml(result.item.description || result.item.excerpt || '')}</span>
                `;
                btn.addEventListener('click', () => navigate(result.item.url));
                li.appendChild(btn);
                resultsEl.appendChild(li);
            });
        });

        highlightActive();
    };

    const highlightActive = () => {
        resultsEl.querySelectorAll('.docs-search-result').forEach((el) => {
            const idx = Number(el.dataset.index);
            el.classList.toggle('bg-[var(--color-page)]', idx === activeIndex);
        });
    };

    const navigate = (url) => {
        window.location.href = url;
    };

    const search = (query) => {
        if (!fuse || !query.trim()) {
            render([]);
            return;
        }
        render(fuse.search(query, { limit: 12 }));
    };

    document.querySelectorAll('[data-docs-search-open]').forEach((el) => {
        el.addEventListener('click', (e) => {
            e.preventDefault();
            open();
        });
    });

    document.querySelectorAll('[data-docs-search-input-hero]').forEach((el) => {
        el.addEventListener('focus', (e) => {
            e.preventDefault();
            open();
        });
        el.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                open();
            }
        });
    });

    overlay?.addEventListener('click', (e) => {
        if (e.target === overlay) {
            close();
        }
    });

    input?.addEventListener('input', (e) => {
        activeIndex = -1;
        search(e.target.value);
    });

    input?.addEventListener('keydown', (e) => {
        const buttons = [...resultsEl.querySelectorAll('.docs-search-result')];
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            activeIndex = Math.min(activeIndex + 1, buttons.length - 1);
            highlightActive();
            buttons[activeIndex]?.scrollIntoView({ block: 'nearest' });
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            activeIndex = Math.max(activeIndex - 1, 0);
            highlightActive();
            buttons[activeIndex]?.scrollIntoView({ block: 'nearest' });
        } else if (e.key === 'Enter' && activeIndex >= 0) {
            e.preventDefault();
            buttons[activeIndex]?.click();
        } else if (e.key === 'Escape') {
            close();
        }
    });

    document.addEventListener('keydown', (e) => {
        if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
            e.preventDefault();
            if (root.hidden) {
                open();
            } else {
                close();
            }
        }
    });
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
