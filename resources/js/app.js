
import "./src/highlight"
import "./src/highlight-copy"
import DarkAndLightMode from "./src/dark-light-mode"


import "./src/anchor"
import "./src/tocbot"


const bashes = document.querySelectorAll('.language-bash');
bashes.forEach(bash => {
    bash.closest('pre').classList.add('language-bash');
    bash.classList.add('c-scrollbar');
});

DarkAndLightMode({ selector: '[data-streams=toggle-color-mode]' });
