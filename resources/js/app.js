const nav=document.getElementById('site-nav');
if(nav) window.addEventListener('scroll',()=>nav.classList.toggle('scrolled',window.scrollY>30),{passive:true});
const observer=new IntersectionObserver(entries=>entries.forEach(entry=>{if(entry.isIntersecting){entry.target.classList.add('in');observer.unobserve(entry.target)}}),{threshold:.12});
document.querySelectorAll('.reveal').forEach(el=>observer.observe(el));
document.querySelectorAll('[data-faq]').forEach(item=>item.querySelector('button')?.addEventListener('click',()=>item.classList.toggle('open')));
const canvas=document.getElementById('stars');
if(canvas && !matchMedia('(prefers-reduced-motion: reduce)').matches){const ctx=canvas.getContext('2d');let stars=[];const resize=()=>{canvas.width=innerWidth;canvas.height=innerHeight;stars=Array.from({length:Math.min(150,Math.floor(innerWidth*innerHeight/11000))},()=>({x:Math.random()*canvas.width,y:Math.random()*canvas.height,r:Math.random()*.9+.2,a:Math.random()*.45+.1,p:Math.random()*Math.PI*2}))};resize();addEventListener('resize',resize,{passive:true});const tick=t=>{ctx.clearRect(0,0,canvas.width,canvas.height);stars.forEach(s=>{ctx.globalAlpha=s.a*(.65+.35*Math.sin(t*.001+s.p));ctx.fillStyle='#fff';ctx.beginPath();ctx.arc(s.x,s.y,s.r,0,Math.PI*2);ctx.fill()});requestAnimationFrame(tick)};requestAnimationFrame(tick)}

<<<<<<< HEAD
const dsMenu=document.querySelector('.ds-menu-toggle'); const dsMobile=document.querySelector('.ds-mobile-nav'); if(dsMenu&&dsMobile){dsMenu.addEventListener('click',()=>{const open=dsMobile.classList.toggle('open');dsMenu.setAttribute('aria-expanded',open?'true':'false');}); dsMobile.querySelectorAll('a').forEach(a=>a.addEventListener('click',()=>{dsMobile.classList.remove('open');dsMenu.setAttribute('aria-expanded','false')}));}
=======
const toastStack = () => document.querySelector('[data-toast-stack]');

function showToast(message, tone = 'dark') {
    const stack = toastStack();

    if (!stack || !message) {
        return;
    }

    const toast = document.createElement('div');
    toast.className = 'admin-toast';
    toast.dataset.tone = tone;
    toast.textContent = message;
    stack.appendChild(toast);

    window.setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(-8px)';
        window.setTimeout(() => toast.remove(), 220);
    }, 3600);
}

function closeSidebar() {
    document.body.classList.remove('admin-sidebar-open');
    document.querySelector('[data-sidebar-toggle]')?.setAttribute('aria-expanded', 'false');
}

function setupSidebar() {
    document.querySelector('[data-sidebar-toggle]')?.addEventListener('click', () => {
        const isOpen = document.body.classList.toggle('admin-sidebar-open');
        document.querySelector('[data-sidebar-toggle]')?.setAttribute('aria-expanded', String(isOpen));
    });

    document.querySelectorAll('[data-sidebar-close]').forEach((element) => {
        element.addEventListener('click', closeSidebar);
    });

    window.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closeSidebar();
        }
    });
}

function appendOptionsList(form, formData) {
    const optionsField = form.querySelector('[data-options-list]');

    if (!optionsField || !optionsField.value.trim()) {
        return;
    }

    formData.delete(optionsField.name);

    optionsField.value
        .split(/\r?\n|,/)
        .map((option) => option.trim())
        .filter(Boolean)
        .forEach((option) => {
            const key = option.toLowerCase().replace(/[^a-z0-9]+/g, '_').replace(/^_+|_+$/g, '');
            formData.append(`options[${key || option}]`, option);
        });
}

function setupAjaxForms() {
    document.querySelectorAll('form[data-ajax]').forEach((form) => {
        form.addEventListener('submit', async (event) => {
            event.preventDefault();

            const message = form.dataset.confirm;
            if (message && !window.confirm(message)) {
                return;
            }

            const submitter = event.submitter;
            const originalText = submitter?.textContent;

            if (submitter) {
                submitter.disabled = true;
                submitter.textContent = 'Working...';
            }

            const formData = new FormData(form);
            appendOptionsList(form, formData);

            try {
                const response = await window.axios.post(form.action, formData);
                showToast(response.data?.message || 'Saved successfully');

                if (form.dataset.successRedirect) {
                    window.location.assign(form.dataset.successRedirect);
                    return;
                }

                if ('successReload' in form.dataset) {
                    window.setTimeout(() => window.location.reload(), 450);
                }
            } catch (error) {
                const errors = error.response?.data?.errors;
                const firstError = errors ? Object.values(errors).flat()[0] : null;
                showToast(firstError || error.response?.data?.message || 'Something went wrong', 'danger');
            } finally {
                if (submitter) {
                    submitter.disabled = false;
                    submitter.textContent = originalText;
                }
            }
        });
    });
}

function setupLoginForm() {
    document.querySelectorAll('form[data-login]').forEach((form) => {
        form.addEventListener('submit', async (event) => {
            event.preventDefault();

            const submitter = event.submitter;
            const originalText = submitter?.textContent;

            if (submitter) {
                submitter.disabled = true;
                submitter.textContent = 'Signing in...';
            }

            try {
                const response = await window.axios.post(form.action, new FormData(form));
                const redirect = response.data?.data?.redirect || response.data?.redirect || '/admin/dashboard';
                showToast(response.data?.message || 'Logged in successfully');
                window.location.assign(redirect);
            } catch (error) {
                const errors = error.response?.data?.errors;
                const firstError = errors ? Object.values(errors).flat()[0] : null;
                showToast(firstError || error.response?.data?.message || 'Login failed', 'danger');
            } finally {
                if (submitter) {
                    submitter.disabled = false;
                    submitter.textContent = originalText;
                }
            }
        });
    });
}

function metricCard(label, value, tone = 'blue') {
    return `
        <article class="admin-stat-card">
            <span class="admin-stat-dot is-${tone}"></span>
            <p class="text-sm font-semibold text-muted">${label}</p>
            <p class="mt-4 text-3xl font-black text-ink">${value}</p>
        </article>
    `;
}

function escapeHtml(value) {
    return String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

function table(headers, rows, emptyMessage) {
    if (!rows.length) {
        return `<div class="admin-empty"><div class="admin-empty-mark">-</div><p>${emptyMessage}</p></div>`;
    }

    return `
        <table class="admin-table">
            <thead><tr>${headers.map((header) => `<th>${header}</th>`).join('')}</tr></thead>
            <tbody>${rows.join('')}</tbody>
        </table>
    `;
}

function setupReports() {
    const form = document.querySelector('[data-report-filters]');

    if (!form) {
        return;
    }

    const overview = document.querySelector('[data-report-overview]');
    const staffTarget = document.querySelector('[data-report-staff]');
    const servicesTarget = document.querySelector('[data-report-services]');

    const load = async () => {
        const params = new URLSearchParams(new FormData(form));

        try {
            const [overviewResponse, staffResponse, servicesResponse] = await Promise.all([
                window.axios.get(`${form.dataset.overviewUrl}?${params}`),
                window.axios.get(`${form.dataset.staffUrl}?${params}`),
                window.axios.get(`${form.dataset.servicesUrl}?${params}`),
            ]);

            const data = overviewResponse.data.data;
            overview.innerHTML = [
                metricCard('Submissions', data.total_submissions, 'blue'),
                metricCard('Completed', data.completed_submissions, 'green'),
                metricCard('Pending', data.pending_submissions, 'gold'),
                metricCard('Completion rate', `${data.completion_rate}%`, 'cyan'),
            ].join('');

            staffTarget.innerHTML = table(
                ['Name', 'Role', 'Processed', 'Completed', 'Rate'],
                staffResponse.data.data.staff.map((member) => `
                    <tr>
                        <td>${escapeHtml(member.name)}</td>
                        <td>${escapeHtml(member.role_label || member.role)}</td>
                        <td>${member.total_processed}</td>
                        <td>${member.completed}</td>
                        <td>${member.completion_rate}%</td>
                    </tr>
                `),
                'No staff activity in this range.',
            );

            servicesTarget.innerHTML = table(
                ['Service', 'Category', 'Total', 'Completed', 'Rate'],
                servicesResponse.data.data.services.map((service) => `
                    <tr>
                        <td>${escapeHtml(service.name)}</td>
                        <td>${escapeHtml(service.category_name || 'N/A')}</td>
                        <td>${service.total}</td>
                        <td>${service.completed}</td>
                        <td>${service.completion_rate}%</td>
                    </tr>
                `),
                'No service usage in this range.',
            );
        } catch (error) {
            showToast(error.response?.data?.message || 'Could not load reports', 'danger');
        }
    };

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        load();
    });

    load();
}

document.addEventListener('DOMContentLoaded', () => {
    setupSidebar();
    setupAjaxForms();
    setupLoginForm();
    setupReports();

    if (document.getElementById('hero-3d-canvas-container')) {
        import('./hero-3d').then(({ initHero3D }) => {
            initHero3D();
        });
    }
});
>>>>>>> origin/digital-star-consultants
