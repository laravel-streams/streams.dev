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

function highlightCode() {
    Prism.highlightAll();
}

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
    [startAlpine, highlightCode, initAnchors, initTocbot, initDocsSearch, initDocsFilter, initReveal].forEach(safely);
});
