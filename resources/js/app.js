import './bootstrap';
import '../css/app.css';

import Alpine from 'alpinejs';
import Prism from 'prismjs';
import 'prismjs/components/prism-bash';
// prism-php tokenizes through markup-templating; without it highlightAll() throws on the first PHP block.
import 'prismjs/components/prism-markup-templating';
import 'prismjs/components/prism-php';
import 'prismjs/components/prism-json';
import 'prismjs/components/prism-yaml';
import 'prismjs/components/prism-markup';

import AnchorJS from 'anchor-js';
import * as tocbot from 'tocbot';
import { initDocsSearch } from './docs-search';
import { initReveal } from './reveal';
import { initDocsFilter } from './docs-filter';

// Livewire 3 ships its own Alpine. Start ours only on pages that never load it,
// and only after parse, so a late Livewire script is visible to the check.
function startAlpine() {
    if (window.Livewire || window.Alpine) {
        return;
    }

    window.Alpine = Alpine;
    Alpine.start();
}

function highlightCode(root = document) {
    if (root === document) {
        Prism.highlightAll();

        return;
    }

    root.querySelectorAll('pre code[class*="language-"]').forEach((block) => {
        Prism.highlightElement(block);
    });
}

function syncPlaygroundConfigHighlight(textarea) {
    const shell = textarea.closest('[data-playground-config-shell]');

    if (!shell) {
        return;
    }

    const block = shell.querySelector('[data-playground-config-highlight]');

    if (!block) {
        return;
    }

    block.textContent = textarea.value;
    Prism.highlightElement(block);
}

function initPlaygroundConfigHighlight(root = document) {
    root.querySelectorAll('[data-playground-config]').forEach(syncPlaygroundConfigHighlight);
}

function initPlaygroundConfigEditors(root = document) {
    root.querySelectorAll('[data-playground-config]').forEach((textarea) => {
        if (textarea.dataset.playgroundHighlightBound === 'true') {
            return;
        }

        textarea.dataset.playgroundHighlightBound = 'true';
        textarea.addEventListener('input', () => syncPlaygroundConfigHighlight(textarea));
        textarea.addEventListener('scroll', () => {
            const shell = textarea.closest('[data-playground-config-shell]');
            const pre = shell?.querySelector('.playground-config__highlight');

            if (pre) {
                pre.scrollTop = textarea.scrollTop;
                pre.scrollLeft = textarea.scrollLeft;
            }
        });
    });
}

function afterLivewireMorph(el) {
    highlightCode(el);
    initPlaygroundConfigHighlight(el);
    initPlaygroundConfigEditors(el);
}

function initLivewireHighlight() {
    document.addEventListener('livewire:init', () => {
        window.Livewire.hook('morph.updated', ({ el }) => {
            requestAnimationFrame(() => afterLivewireMorph(el));
        });

        window.Livewire.hook('commit', ({ succeed }) => {
            succeed(() => {
                queueMicrotask(() => {
                    afterLivewireMorph(document);
                });
            });
        });
    });
}

window.StreamsHighlightCode = highlightCode;

function initAnchors() {
    const anchors = new AnchorJS();
    anchors.options = {
        placement: 'right',
        icon: '#',
    };
    anchors.add('.documentation-content h2, .documentation-content h3, .documentation-content h4');
}

function initTocbot() {
    const tocEl = document.querySelector('.documentation__toc');
    const contentEl = document.querySelector('.documentation-content');

    if (!tocEl || !contentEl) {
        return;
    }

    tocbot.destroy();

    tocbot.init({
        tocSelector: '.documentation__toc',
        contentSelector: '.documentation-content',
        headingSelector: 'h2,h3',
        hasInnerContainers: false,
        linkClass: 'toc-link',
        collapseDepth: 3,
        scrollSmooth: true,
        scrollSmoothDuration: 0,
        headingsOffset: (document.querySelector('.st-header')?.offsetHeight ?? 72) + 24,
        throttleTimeout: 50,
    });
}

// Run each step on its own so one failure (a Prism grammar, say) cannot skip search or the TOC.
function safely(step) {
    try {
        step();
    } catch (error) {
        console.error(error);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    [
        startAlpine,
        highlightCode,
        initPlaygroundConfigHighlight,
        initPlaygroundConfigEditors,
        initLivewireHighlight,
        initAnchors,
        initTocbot,
        initDocsSearch,
        initDocsFilter,
        initReveal,
    ].forEach(safely);
});
