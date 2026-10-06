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
            card.classList.toggle('is-hidden', filter !== 'all' && card.dataset.projectCategory !== filter);
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
