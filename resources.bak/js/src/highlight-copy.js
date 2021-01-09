 

function copyCodeblockToClipboard(button, codeBlock) {
    const clipboard = navigator.clipboard;

    clipboard.writeText(codeBlock.innerText).then(function () {
        button.blur();

        button.classList.add('c-copy-code__hide-button');
        button.closest('pre').classList.add('c-copy-code__preflash--step-1');
        setTimeout(() => {
            button.closest('pre').classList.add('c-copy-code__preflash');
        }, 100);
        setTimeout(function () {
            button.closest('pre').classList.remove('c-copy-code__preflash');
        }, 1300);
        setTimeout(() => {
            button.closest('pre').classList.remove('c-copy-code__preflash--step-1');
            button.classList.remove('c-copy-code__hide-button');
        }, 1475);
    }, function (error) {
        button.innerText = 'Error';
    });
}

if (navigator && navigator.clipboard) {

    document.querySelectorAll('pre > code').forEach(function (codeBlock) {
        const button = document.createElement('button');
        const icon = document.createElement('span');
        icon.classList.add('c-copy-code__icon');

        button.className = 'c-copy-code';
        button.type = 'button';
        button.appendChild(icon);
        codeBlock.parentNode.appendChild(button);

        button.addEventListener('click', copyCodeblockToClipboard.bind(null, button, codeBlock));
        button.closest('pre').addEventListener('dblclick', copyCodeblockToClipboard.bind(null, button, codeBlock));

    });
} else if (window.location.protocol == 'http:') {
    console.info('Copy to clipboard functionality will not work unless you use a secure origin')
}