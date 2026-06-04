import './bootstrap';
import '../scss/app.scss';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

import Prism from 'prismjs';
import 'prismjs/components/prism-bash';
import 'prismjs/components/prism-php';
import 'prismjs/components/prism-json';
import 'prismjs/components/prism-yaml';
import 'prismjs/components/prism-markup';

import AnchorJS from 'anchor-js';
import * as tocbot from 'tocbot';
import { initDocsSearch } from './docs-search';

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
        headingsOffset: 80,
        throttleTimeout: 50,
    });
}

document.addEventListener('DOMContentLoaded', () => {
    highlightCode();
    initAnchors();
    initTocbot();
    initDocsSearch();
});
