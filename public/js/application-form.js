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
    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    let current = 1;

    // Localised strings injected by services/show.blade.php (falls back to English).
    const T = Object.assign({
        full_name: 'Full name',
        phone: 'Phone number',
        email: 'Email address',
        preferred_date: 'Preferred date',
        notes: 'Additional notes',
        field: 'Field',
        yes: 'Yes',
        no: 'No',
        additional_info: 'Additional information',
        no_additional_fields: 'No additional fields',
        document: 'Document',
        no_file_selected: 'No file selected',
        documents: 'Documents',
        no_documents_required: 'No documents required',
        selected_prefix: 'Selected:',
        submitting: 'Submitting...',
        error: 'We could not submit your application. Please check your details.',
    }, window.DS_FORM_I18N || {});

    // Conditions change only which preparation items apply; they never make
    // documents mandatory for submitting an assistance request.
    const conditionalItems = [...document.querySelectorAll('[data-service-condition]')];
    const syncConditions = () => {
        const answers = {};
        form.querySelectorAll('[name^="fields["]').forEach((input) => {
            const key = input.name.match(/^fields\[([^\]]+)\]$/)?.[1];
            if (key && input.type !== 'file' && (!['radio', 'checkbox'].includes(input.type) || input.checked)) {
                answers[key] = input.value;
            }
        });
        conditionalItems.forEach((item) => {
            const conditions = JSON.parse(item.dataset.serviceCondition || '{}');
            const applies = Object.entries(conditions).every(([key, allowed]) => allowed.includes(answers[key]));
            item.hidden = !applies;
            item.querySelectorAll('input,select,textarea').forEach((input) => { input.disabled = !applies; });
        });
    };
    form.addEventListener('change', syncConditions);
    form.addEventListener('input', syncConditions);
    syncConditions();

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
            row(T.full_name, valueOf('customer_name')),
            row(T.phone, valueOf('customer_phone')),
            row(T.email, valueOf('customer_email')),
            row(T.preferred_date, valueOf('preferred_date')),
            row(T.notes, valueOf('customer_notes')),
        );

        const dynamic = [...form.querySelectorAll('[name^="fields["]')].filter((el) => el.type !== 'file' && !el.disabled);
        const seen = new Set();
        const serviceRows = [];
        dynamic.forEach((el) => {
            if (seen.has(el.name)) return;
            seen.add(el.name);
            const label = el.closest('[data-field-label]')?.dataset.fieldLabel || T.field;
            let value;
            if (el.type === 'radio') value = dynamic.find((x) => x.name === el.name && x.checked)?.value;
            else if (el.type === 'checkbox') value = el.checked ? T.yes : T.no;
            else value = el.value;
            serviceRows.push(row(label, value));
        });
        form.querySelector('[data-review="service"]').replaceChildren(
            ...(serviceRows.length ? serviceRows : [row(T.additional_info, T.no_additional_fields)])
        );

        const fileRows = [...form.querySelectorAll('input[type="file"]')]
            .filter((input) => !input.disabled)
            .map((input) => row(input.dataset.label || T.document, input.files?.[0]?.name || T.no_file_selected));
        form.querySelector('[data-review="files"]').replaceChildren(
            ...(fileRows.length ? fileRows : [row(T.documents, T.no_documents_required)])
        );
    };

    const setStep = (n, scroll = true) => {
        current = n;
        steps.forEach((panel, i) => {
            const active = i + 1 === current;
            panel.hidden = !active;
            if (active && !reduceMotion) { panel.classList.remove('is-enter'); void panel.offsetWidth; panel.classList.add('is-enter'); }
        });
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
            target.textContent = `${T.selected_prefix} ${input.files[0].name}`;
            target.classList.add('ds-file-name');
        }
    }));

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        if (!validateStep(TOTAL)) return;
        clearMessage();
        const label = submit.innerHTML;
        submit.disabled = true;
        submit.classList.add('is-loading');
        submit.setAttribute('aria-busy', 'true');
        submit.textContent = T.submitting;
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
                throw new Error(firstError || data.message || T.error);
            }
            const ref = data.data.reference_number;
            const successCopy = $('[data-success-message]');
            if (successCopy && data.message) successCopy.textContent = data.message;
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
            submit.classList.remove('is-loading');
            submit.removeAttribute('aria-busy');
            submit.innerHTML = label;
        }
    });

    setStep(1, false);
});
