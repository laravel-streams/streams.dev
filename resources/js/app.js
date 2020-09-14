
import "./src/highlight"
import "./src/highlight-copy"
import "./src/tocbot"

const bashes = document.querySelectorAll('.language-bash');
bashes.forEach(bash => {
    bash.closest('pre').classList.add('language-bash');
    bash.classList.add('c-scrollbar');
});

