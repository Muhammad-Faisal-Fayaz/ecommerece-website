// ShopWave — Global UI interactions
document.addEventListener('DOMContentLoaded', () => {
    const toggle = document.querySelector('.nav-toggle');
    const navLinks = document.querySelector('.nav-links');
    if (toggle && navLinks) {
        toggle.addEventListener('click', () => {
            navLinks.classList.toggle('is-open');
            toggle.setAttribute('aria-expanded', navLinks.classList.contains('is-open'));
        });
        document.addEventListener('click', (e) => {
            if (!toggle.contains(e.target) && !navLinks.contains(e.target)) {
                navLinks.classList.remove('open', 'is-open');
                toggle.setAttribute('aria-expanded', 'false');
            }
        });
    }

    const main = document.querySelector('.main-animate');
    if (main) {
        main.style.opacity = '0';
        requestAnimationFrame(() => {
            main.style.opacity = '1';
        });
    }

    document.querySelectorAll('a[href^="#"]').forEach((a) => {
        a.addEventListener('click', (e) => {
            const id = a.getAttribute('href');
            if (id.length > 1) {
                const el = document.querySelector(id);
                if (el) {
                    e.preventDefault();
                    el.scrollIntoView({ behavior: 'smooth' });
                }
            }
        });
    });
});
