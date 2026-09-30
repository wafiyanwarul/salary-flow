document.addEventListener('DOMContentLoaded', () => {
    // 1. Auto-dismiss flash alerts
    document.querySelectorAll('[data-auto-dismiss]').forEach((el) => {
        window.setTimeout(() => {
            el.classList.add('fade-out');
            window.setTimeout(() => el.remove(), 320);
        }, 3800);
    });

    // 2. Rupiah / Money input mask
    document.querySelectorAll('[data-money-input]').forEach((input) => {
        const formatValue = () => {
            const raw = input.value.replace(/\D/g, '');
            input.value = raw ? new Intl.NumberFormat('id-ID').format(Number(raw)) : '';
        };

        if (input.value) {
            formatValue();
        }

        input.addEventListener('input', formatValue);
    });

    // 3. Tab Switcher
    const tabButtons = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');

    const switchTab = (targetId) => {
        tabButtons.forEach((btn) => {
            btn.classList.toggle('active', btn.getAttribute('data-tab') === targetId);
        });
        tabContents.forEach((content) => {
            content.classList.toggle('active', content.id === targetId);
        });
    };

    tabButtons.forEach((btn) => {
        btn.addEventListener('click', () => {
            const targetId = btn.getAttribute('data-tab');
            if (targetId) {
                switchTab(targetId);

                const url = new URL(window.location.href);
                const tabParam = targetId.replace('tab-', '');
                url.searchParams.set('tab', tabParam);
                window.history.replaceState({}, '', url.toString());
            }
        });
    });

    const urlParams = new URLSearchParams(window.location.search);
    const tabFromUrl = urlParams.get('tab');
    if (tabFromUrl) {
        const targetTabId = 'tab-' + tabFromUrl;
        if (document.getElementById(targetTabId)) {
            switchTab(targetTabId);
        }
    }

    // 4. Custom Select Dropdowns (Shadcn / Mantine Style)
    const customSelects = document.querySelectorAll('[data-custom-select]');
    customSelects.forEach((cs) => {
        const trigger = cs.querySelector('.custom-select-trigger');
        const hiddenInput = cs.querySelector('input[type="hidden"]');
        const valueDisplay = cs.querySelector('.custom-select-value');
        const options = cs.querySelectorAll('.custom-select-option');
        const autoSubmit = cs.getAttribute('data-auto-submit') === 'true';

        trigger.addEventListener('click', (e) => {
            e.stopPropagation();
            // Close other open selects
            customSelects.forEach((other) => {
                if (other !== cs) other.classList.remove('open');
            });
            cs.classList.toggle('open');
            trigger.setAttribute('aria-expanded', cs.classList.contains('open') ? 'true' : 'false');
        });

        options.forEach((opt) => {
            opt.addEventListener('click', (e) => {
                e.stopPropagation();
                const val = opt.getAttribute('data-value');
                const label = opt.querySelector('span:first-child')?.textContent || val;

                hiddenInput.value = val;
                valueDisplay.textContent = label;

                options.forEach((o) => o.classList.remove('selected'));
                opt.classList.add('selected');

                cs.classList.remove('open');
                trigger.setAttribute('aria-expanded', 'false');

                // Dispatch change event on hidden input
                hiddenInput.dispatchEvent(new Event('change', { bubbles: true }));

                if (autoSubmit) {
                    const form = cs.closest('form');
                    if (form) form.submit();
                }
            });
        });
    });

    // Close dropdowns when clicking outside
    document.addEventListener('click', () => {
        customSelects.forEach((cs) => {
            cs.classList.remove('open');
            const trigger = cs.querySelector('.custom-select-trigger');
            if (trigger) trigger.setAttribute('aria-expanded', 'false');
        });
    });

    // 5. Expandable Month Item Management Drawer in Tab 1
    document.querySelectorAll('.toggle-month-items').forEach((btn) => {
        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            const targetId = btn.getAttribute('data-target');
            const drawerRow = document.getElementById(targetId);
            const arrow = btn.querySelector('.toggle-arrow');

            if (drawerRow) {
                const isHidden = drawerRow.style.display === 'none';
                drawerRow.style.display = isHidden ? 'table-row' : 'none';
                if (arrow) arrow.classList.toggle('open', isHidden);
            }
        });
    });

    // 6. Mobile touch chart popovers
    const chartCols = document.querySelectorAll('.chart-col');
    chartCols.forEach((col) => {
        col.addEventListener('click', (e) => {
            chartCols.forEach((other) => {
                if (other !== col) {
                    const pop = other.querySelector('.chart-popover');
                    if (pop) pop.style.display = '';
                }
            });

            const popover = col.querySelector('.chart-popover');
            if (popover) {
                const isVisible = window.getComputedStyle(popover).display === 'block';
                popover.style.display = isVisible ? 'none' : 'block';
                e.stopPropagation();
            }
        });
    });

    document.addEventListener('click', () => {
        document.querySelectorAll('.chart-popover').forEach((pop) => {
            pop.style.display = '';
        });
    });
});
