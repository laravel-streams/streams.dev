import './bootstrap';
import '../scss/app.scss';


import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();


import Prism from 'prismjs';

Prism.highlightAll();


import AnchorJS from 'anchor-js';

const anchors = new AnchorJS();

anchors.options = {
    placement: 'left',
};

anchors.add('.documentation-content h2, .documentation-content h3, .documentation-content h4');

import * as tocbot from 'tocbot';

tocbot.init({

    // Where to render the table of contents.
    tocSelector: '.documentation__toc',
    // Where to grab the headings to build the table of contents.
    contentSelector: '.documentation-content',
    // Which headings to grab inside of the contentSelector element.
    headingSelector: 'h2,h3',
    // Headings that match the ignoreSelector will be skipped.
    ignoreSelector: '.toc-ignore',
    // For headings inside relative or absolute positioned containers within content.
    hasInnerContainers: false,
    // Main class to add to links.
    linkClass: 'toc-link',
    // The sections that are hidden will open
    // and close as you scroll to headings within them.
    collapseDepth: 5,
    // Smooth scrolling enabled.
    scrollSmooth: true,
    // Smooth scroll duration.
    scrollSmoothDuration: 0,
    // Callback for scroll end.
    // Can also be used to account for scroll height discrepancies from the use of css scroll-padding-top
    headingsOffset: 1,
    // Timeout between events firing to make sure it's
    // not too rapid (for performance reasons).
    throttleTimeout: 50,
    // Element to add the positionFixedClass to.
    positionFixedSelector: null,
    // Fixed position class to add to make sidebar fixed after scrolling
    // down past the fixedSidebarOffset.
    positionFixedClass: 'is-position-fixed',
    // fixedSidebarOffset can be any number but by default is set
    // to auto which sets the fixedSidebarOffset to the sidebar
    // element's offsetTop from the top of the document on init.
    fixedSidebarOffset: 'auto',
    // includeHtml can be set to true to include the HTML markup from the
    // heading node instead of just including the textContent.
    includeHtml: false,
    // If there is a fixed article scroll container, set to calculate titles' offset
    scrollContainer: null,
    // prevent ToC DOM rendering if it's already rendered by an external system
    skipRendering: false,
});
