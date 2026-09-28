// Scroll reveal: a subtle fade plus a 10px rise, once per element.
//
// Mark a block with data-reveal. Add data-reveal-stagger to reveal its
// children one after another (70ms apart, capped at 6 steps). Elements that
// are already on screen when the page loads are left alone, so nothing that
// is visible ever blinks out. Skipped entirely under prefers-reduced-motion.
const STAGGER_MS = 70;
const MAX_STEPS = 6;

export function initReveal() {
    const blocks = document.querySelectorAll('[data-reveal]');
    if (!blocks.length || !('IntersectionObserver' in window)) {
        return;
    }
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        return;
    }

    const targets = [];
    blocks.forEach((block) => {
        if (block.hasAttribute('data-reveal-stagger')) {
            [...block.children].forEach((child, i) => targets.push([child, Math.min(i, MAX_STEPS) * STAGGER_MS]));
        } else {
            targets.push([block, 0]);
        }
    });

    const finish = (el) => {
        el.classList.remove('st-reveal-in');
        el.style.removeProperty('--st-reveal-delay');
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) {
                return;
            }
            const el = entry.target;
            observer.unobserve(el);
            el.classList.add('st-reveal-in');
            el.addEventListener('transitionend', function done(event) {
                if (event.target === el && event.propertyName === 'transform') {
                    el.removeEventListener('transitionend', done);
                    finish(el);
                }
            });
            // Safety net if transitionend never fires (tab hidden, etc.).
            setTimeout(() => finish(el), 1600);
            requestAnimationFrame(() => el.classList.remove('st-reveal-pending'));
        });
    }, { rootMargin: '0px 0px -8% 0px', threshold: 0 });

    const fold = window.innerHeight * 0.92;
    targets.forEach(([el, delay]) => {
        if (el.getBoundingClientRect().top < fold) {
            return;
        }
        el.style.setProperty('--st-reveal-delay', `${delay}ms`);
        el.classList.add('st-reveal-pending');
        observer.observe(el);
    });
}
