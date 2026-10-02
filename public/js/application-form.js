// Digital Star - 4-step service application form (services/show.blade.php)
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('service-form');
    if (!form) return;

    const TOTAL = 4;
    const $ = (sel) => form.querySelector(sel);
    const steps = [...form.querySelectorAll('[data-step]')];
    const stepItems = [...document.querySelectorAll('[data-step-item]')];
    const next = $('[data-next]');
    const back = $('[data-back]');
    const submit = $('[data-submit]');
    const message = $('[data-form-message]');
    const progressCount = document.querySelector('[data-progress-count]');
    const progressStep = document.querySelector('[data-progress-step]');
    const progressBar = document.querySelector('[data-progress-bar]');
    const anchor = document.getElementById('application');
    let current = 1;

    const showMessage = (text) => { message.hidden = false; message.textContent = text; };
    const clearMessage = () => { message.hidden = true; message.textContent = ''; };

    const validateStep = (n) => {
        const fields = [...steps[n - 1].querySelectorAll('input, select, textarea')]
            .filter((el) => !el.disabled && el.type !== 'hidden');
        for (const el of fields) {
            if (!el.checkValidity()) {
                el.reportValidity();
                el.focus({ preventScroll: false });
                return false;
            }
        }
        return true;
    };

    // Build a <div><dt>..</dt><dd>..</dd></div> row safely (no innerHTML with user input)
    const row = (label, value) => {
        const div = document.createElement('div');
        const dt = document.createElement('dt');
        const dd = document.createElement('dd');
        dt.textContent = label;
        dd.textContent = value || '—';
        div.append(dt, dd);
        return div;
    };
    const valueOf = (name) => form.elements[name]?.value?.trim() || '';

    const updateReview = () => {
        form.querySelector('[data-review="contact"]').replaceChildren(
            row('Full name', valueOf('customer_name')),
            row('Phone number', valueOf('customer_phone')),
            row('Email address', valueOf('customer_email')),
            row('Preferred date', valueOf('preferred_date')),
            row('Additional notes', valueOf('customer_notes')),
        );

        const dynamic = [...form.querySelectorAll('[name^="fields["]')].filter((el) => el.type !== 'file');
        const seen = new Set();
        const serviceRows = [];
        dynamic.forEach((el) => {
            if (seen.has(el.name)) return;
            seen.add(el.name);
            const label = el.closest('[data-field-label]')?.dataset.fieldLabel || 'Field';
            let value;
            if (el.type === 'radio') value = dynamic.find((x) => x.name === el.name && x.checked)?.value;
            else if (el.type === 'checkbox') value = el.checked ? 'Yes' : 'No';
            else value = el.value;
            serviceRows.push(row(label, value));
        });
        form.querySelector('[data-review="service"]').replaceChildren(
            ...(serviceRows.length ? serviceRows : [row('Additional information', 'No additional fields')])
        );

        const fileRows = [...form.querySelectorAll('input[type="file"]')]
            .map((input) => row(input.dataset.label || 'Document', input.files?.[0]?.name || 'No file selected'));
        form.querySelector('[data-review="files"]').replaceChildren(
            ...(fileRows.length ? fileRows : [row('Documents', 'No documents required')])
        );
    };

    const setStep = (n, scroll = true) => {
        current = n;
        steps.forEach((panel, i) => { panel.hidden = i + 1 !== current; });
        stepItems.forEach((item) => {
            const x = Number(item.dataset.stepItem);
            item.classList.toggle('is-active', x === current);
            item.classList.toggle('is-done', x < current);
        });
        progressCount.textContent = `${current} / ${TOTAL}`;
        progressStep.textContent = current;
        progressBar.style.width = `${(current / TOTAL) * 100}%`;
        back.hidden = current === 1;
        next.hidden = current === TOTAL;
        submit.hidden = current !== TOTAL;
        if (current === TOTAL) updateReview();
        clearMessage();
        if (scroll) anchor?.scrollIntoView({ behavior: 'smooth', block: 'start' });
    };

    next.addEventListener('click', () => { if (validateStep(current)) setStep(Math.min(TOTAL, current + 1)); });
    back.addEventListener('click', () => setStep(Math.max(1, current - 1)));
    form.querySelectorAll('[data-go-step]').forEach((b) => b.addEventListener('click', () => setStep(Number(b.dataset.goStep))));

    // Show the chosen file name on each upload box
    form.querySelectorAll('input[type="file"]').forEach((input) => input.addEventListener('change', () => {
        const target = form.querySelector(`[data-file-name-for="${CSS.escape(input.id)}"]`);
        if (!target) return;
        if (input.files?.length) {
            target.textContent = `Selected: ${input.files[0].name}`;
            target.classList.add('ds-file-name');
        }
    }));

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        if (!validateStep(TOTAL)) return;
        clearMessage();
        const label = submit.innerHTML;
        submit.disabled = true;
        submit.textContent = 'Submitting...';
        try {
            const response = await fetch(form.dataset.submitUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: new FormData(form),
            });
            const data = await response.json().catch(() => ({}));
            if (!response.ok) {
                const firstError = data.errors ? Object.values(data.errors)[0]?.[0] : null;
                throw new Error(firstError || data.message || 'We could not submit your application. Please check your details.');
            }
            const ref = data.data.reference_number;
            steps.forEach((panel) => { panel.hidden = true; });
            $('[data-wizard-nav]').hidden = true;
            $('[data-reference]').textContent = ref;
            $('[data-track-link]').href = `/track/status/${encodeURIComponent(ref)}`;
            $('[data-success]').hidden = false;
            stepItems.forEach((item) => { item.classList.remove('is-active'); item.classList.add('is-done'); });
            progressBar.style.width = '100%';
            anchor?.scrollIntoView({ behavior: 'smooth', block: 'start' });
        } catch (error) {
            showMessage(error.message);
        } finally {
            submit.disabled = false;
            submit.innerHTML = label;
        }
    });

    setStep(1, false);
});
