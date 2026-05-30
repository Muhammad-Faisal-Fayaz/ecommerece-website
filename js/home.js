// ShopWave — Homepage hero slider
(function () {
    const slider = document.querySelector('.hero-slider');
    if (!slider) return;

    const slides = slider.querySelectorAll('.hero-slide');
    const dots = slider.querySelectorAll('.hero-dot');
    const prevBtn = slider.querySelector('.hero-prev');
    const nextBtn = slider.querySelector('.hero-next');
    const progress = slider.querySelector('.hero-progress');

    let current = 0;
    let timer = null;
    const interval = 6000;
    let progressTick = null;

    function goTo(index) {
        current = (index + slides.length) % slides.length;
        slides.forEach((s, i) => s.classList.toggle('is-active', i === current));
        dots.forEach((d, i) => d.classList.toggle('is-active', i === current));
        resetProgress();
    }

    function next() { goTo(current + 1); }
    function prev() { goTo(current - 1); }

    function resetProgress() {
        if (!progress) return;
        clearInterval(progressTick);
        progress.style.width = '0%';
        let width = 0;
        const step = 100 / (interval / 50);
        progressTick = setInterval(() => {
            width += step;
            progress.style.width = width + '%';
            if (width >= 100) clearInterval(progressTick);
        }, 50);
    }

    function startAutoplay() {
        stopAutoplay();
        timer = setInterval(next, interval);
        resetProgress();
    }

    function stopAutoplay() {
        clearInterval(timer);
        clearInterval(progressTick);
    }

    dots.forEach((dot, i) => dot.addEventListener('click', () => { goTo(i); startAutoplay(); }));
    if (prevBtn) prevBtn.addEventListener('click', () => { prev(); startAutoplay(); });
    if (nextBtn) nextBtn.addEventListener('click', () => { next(); startAutoplay(); });

    slider.addEventListener('mouseenter', stopAutoplay);
    slider.addEventListener('mouseleave', startAutoplay);

    let touchStartX = 0;
    slider.addEventListener('touchstart', (e) => { touchStartX = e.changedTouches[0].screenX; }, { passive: true });
    slider.addEventListener('touchend', (e) => {
        const diff = touchStartX - e.changedTouches[0].screenX;
        if (Math.abs(diff) > 50) diff > 0 ? next() : prev();
        startAutoplay();
    }, { passive: true });

    goTo(0);
    startAutoplay();
})();
