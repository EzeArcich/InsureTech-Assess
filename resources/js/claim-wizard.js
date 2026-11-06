const form = document.querySelector('[data-claim-wizard]');
if (form) {
    const tabs = [...form.querySelectorAll('[data-wizard-tab]')];
    const panels = [...form.querySelectorAll('[data-wizard-panel]')];
    const previous = form.querySelector('[data-wizard-previous]');
    const next = form.querySelector('[data-wizard-next]');
    let active;
    const show = (step, focus = false) => {
        const tab = tabs.find(tab => tab.dataset.wizardTab === step);
        if (!tab) return;
        active = step;
        tabs.forEach(item => { const selected = item === tab; item.setAttribute('aria-selected', String(selected)); item.tabIndex = selected ? 0 : -1; item.classList.toggle('is-active', selected); });
        panels.forEach(panel => { panel.hidden = panel.dataset.wizardPanel !== step; });
        previous.hidden = step === 'claim'; next.hidden = step === 'coordination';
        if (focus) tab.focus();
        form.dispatchEvent(new CustomEvent('claim-step-shown', { detail: { step } }));
    };
    tabs.forEach((tab, index) => {
        tab.addEventListener('click', () => show(tab.dataset.wizardTab));
        tab.addEventListener('keydown', event => {
            let target;
            if (event.key === 'ArrowRight') target = tabs[(index + 1) % tabs.length];
            if (event.key === 'ArrowLeft') target = tabs[(index + tabs.length - 1) % tabs.length];
            if (event.key === 'Home') target = tabs[0];
            if (event.key === 'End') target = tabs[tabs.length - 1];
            if (target) { event.preventDefault(); show(target.dataset.wizardTab, true); }
        });
    });
    next.addEventListener('click', () => {
        const invalid = panels.find(panel => panel.dataset.wizardPanel === active).querySelector(':invalid');
        if (invalid) { invalid.reportValidity(); return; }
        show('coordination', true);
    });
    previous.addEventListener('click', () => show('claim', true));
    form.addEventListener('invalid', event => {
        const panel = event.target.closest('[data-wizard-panel]');
        if (panel) show(panel.dataset.wizardPanel);
    }, true);
    form.addEventListener('claim-wizard-show', event => show(event.detail.step));
    form.querySelectorAll('[data-wizard-navigation]').forEach(element => { element.hidden = false; });
    show(form.dataset.initialStep || 'claim');
}
