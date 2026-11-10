// Digital Star Consultants - site interactions (menu, scroll reveals, work filters, case-study dialog)
document.addEventListener('DOMContentLoaded', () => {
    if (window.lucide) window.lucide.createIcons();

    // Mobile menu
    const body = document.body;
    document.querySelectorAll('[data-menu-open]').forEach((btn) =>
        btn.addEventListener('click', () => body.classList.add('ds-drawer-open'))
    );
    document.querySelectorAll('[data-menu-close]').forEach((btn) =>
        btn.addEventListener('click', () => body.classList.remove('ds-drawer-open'))
    );
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') body.classList.remove('ds-drawer-open');
    });

    // Fade sections in as they scroll into view
    const revealEls = document.querySelectorAll('.ds-reveal');
    if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });
        revealEls.forEach((el) => observer.observe(el));
    } else {
        revealEls.forEach((el) => el.classList.add('is-visible'));
    }

    // Count-up animation for stat numbers (runs once when scrolled into view)
    const countUp = (el) => {
        const raw = (el.dataset.count || el.textContent).trim();
        if (raw.includes('/')) return; // skip ratios like "24/7" — counting them looks wrong
        const match = raw.match(/^([\d.,]+)(\D*)$/);
        if (!match) return; // skip values like "24/7" that aren't countable
        const suffix = match[2];
        const target = parseFloat(match[1].replace(/,/g, ''));
        if (!isFinite(target) || target === 0) return;
        const decimals = (match[1].split('.')[1] || '').length;
        const duration = 1400;
        const start = performance.now();
        const tick = (now) => {
            const p = Math.min((now - start) / duration, 1);
            const eased = 1 - Math.pow(1 - p, 3); // ease-out cubic
            const current = target * eased;
            const shown = decimals ? current.toFixed(decimals) : Math.round(current).toLocaleString('en-US');
            el.textContent = shown + suffix;
            if (p < 1) requestAnimationFrame(tick);
            else el.textContent = match[1] + suffix; // restore exact original
        };
        requestAnimationFrame(tick);
    };

    const statNums = document.querySelectorAll('.ds-stat strong[data-count]');
    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (statNums.length && !reduceMotion && 'IntersectionObserver' in window) {
        const countObserver = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    countUp(entry.target);
                    countObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });
        statNums.forEach((el) => countObserver.observe(el));
    }

    // Work page: category filters
    const tabs = [...document.querySelectorAll('[data-work-filter]')];
    const cards = [...document.querySelectorAll('[data-project-category]')];
    tabs.forEach((tab) => tab.addEventListener('click', () => {
        tabs.forEach((t) => {
            t.classList.toggle('is-active', t === tab);
            t.setAttribute('aria-pressed', String(t === tab));
        });
        const filter = tab.dataset.workFilter;
        cards.forEach((card) => {
            const show = filter === 'all' || card.dataset.projectCategory === filter;
            card.classList.toggle('is-hidden', !show);
            // Brief pop-in so the switch feels deliberate instead of abrupt
            if (show && !reduceMotion) {
                card.classList.remove('is-pop');
                void card.offsetWidth; // restart the animation
                card.classList.add('is-pop');
            }
        });
    }));

    // Work page: case-study dialog
    const modal = document.querySelector('[data-work-modal]');
    const projects = window.digitalStarProjects || [];
    if (modal && typeof modal.showModal === 'function') {
        const q = (sel) => modal.querySelector(sel);
        const video = q('[data-modal-video]');
        const stopVideo = () => { if (video) { video.pause(); video.removeAttribute('src'); video.removeAttribute('poster'); video.load(); } };
        modal.addEventListener('close', stopVideo);
        document.querySelectorAll('[data-project-id]').forEach((btn) => btn.addEventListener('click', () => {
            const p = projects[Number(btn.dataset.projectId)];
            if (!p) return;
            stopVideo();
            const isVideo = p.type === 'video' && p.video;
            const img = q('[data-modal-image]');
            img.hidden = Boolean(isVideo);
            if (video) video.hidden = !isVideo;
            if (isVideo && video) { video.src = p.video; video.poster = p.image; video.setAttribute('aria-label', p.title); img.removeAttribute('src'); }
            else { img.src = p.image; img.alt = p.alt || p.title; }
            q('[data-modal-category]').textContent = p.category;
            q('[data-modal-title]').textContent = p.title;
            q('[data-modal-description]').textContent = p.description;
            const meta = q('[data-modal-meta]');
            meta.replaceChildren(...(p.meta || []).map((m) => {
                const span = document.createElement('span');
                span.className = 'ds-badge';
                span.textContent = m;
                return span;
            }));
            modal.showModal();
        }));
        modal.querySelectorAll('[data-modal-close]').forEach((b) => b.addEventListener('click', () => modal.close()));
        // Close when clicking the backdrop
        modal.addEventListener('click', (e) => { const b = modal.getBoundingClientRect(); if (e.target === modal && (e.clientX < b.left || e.clientX > b.right || e.clientY < b.top || e.clientY > b.bottom)) modal.close(); });
    }
});
