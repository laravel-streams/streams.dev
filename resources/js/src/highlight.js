import hljs from 'highlight.js';
import php from 'highlight.js/lib/languages/php';

hljs.registerLanguage('php', php);
document.addEventListener('DOMContentLoaded', (event) => {
    document.querySelectorAll('pre code').forEach((block) => {
        hljs.highlightBlock(block);
    });
});