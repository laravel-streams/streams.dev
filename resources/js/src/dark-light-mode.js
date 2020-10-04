let selectors;
let modeEl = document.body;

function changeDarkLightMode(e) {
    e.preventDefault();

    if (modeEl.classList.contains('o-mode__light')) {
        modeEl.classList.remove('o-mode__light')
        modeEl.classList.add('o-mode__dark')
        localStorage.setItem('mode', 'dark')
    } else if (modeEl.classList.contains('o-mode__dark')) {
        modeEl.classList.remove('o-mode__dark')
        modeEl.classList.add('o-mode__light')
        localStorage.setItem('mode', 'light')
    }
}

export default function (config = {}) {

    console.log(localStorage.getItem('mode'));
    modeEl.classList.add('o-mode');

    // If we are previously set prefered mode.
    if (localStorage.getItem('mode')) {
        if (localStorage.getItem('mode') === "dark") { modeEl.classList.add('o-mode__dark') }
        else { modeEl.classList.add('o-mode__light') }
    }
    // If user prefers light mode
    else if (window.matchMedia("(prefers-color-scheme: light)").matches) {
        modeEl.classList.add('o-mode__light')
    }
    // If user prefers dark mode
    else if (window.matchMedia("(prefers-color-scheme: dark)").matches) {
        modeEl.classList.add('o-mode__light')
    }
    else if (!modeEl.classList.contains('o-mode__light') && !modeEl.classList.contains('o-mode__dark')) {
        modeEl.classList.add('o-mode__dark')
    }

    if (config.selector) {
        selectors = document.querySelectorAll(config.selector)
    } else {
        selectors = document.querySelectorAll('[data-streams=toggle-color-mode]')
    }
    selectors.forEach(el => {
        el.addEventListener('click', changeDarkLightMode)
    });
}