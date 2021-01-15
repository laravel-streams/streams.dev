import Prism from 'prismjs';
import AnchorJS from 'anchor-js';
import Clipboard from 'clipboard';
import * as tocbot from 'tocbot';

import { ServiceProvider } from '@streams/core';

export class AppServiceProvider extends ServiceProvider {

    public register() {

        // this.app.factory('modal', () => {
        //     return Modals;
        // });
    }

    public boot() {

        /**
         * Setup the code examples
         */
        let examples = Array.prototype.slice.call(
            document.querySelectorAll('pre > code')
        );

        examples.forEach(function (code, index) {

            code.setAttribute('id', 'code-' + (index + 1));

            Prism.highlightElement(code);

            // let copy = document.createElement('button');

            // copy.textContent = 'copy';
            // copy.setAttribute('data-clipboard-target', '#code-' + (index + 1));
            // copy.classList.add('copy-to-clipboard');

            // code.parentNode.insertBefore(copy, code.nextSibling);

            // let clipboard = new ClipboardJS('.copy-to-clipboard');

            // clipboard.on('success', function (event) {
            //     event.trigger.classList.add('copied');
            // });
        });
    }
}
