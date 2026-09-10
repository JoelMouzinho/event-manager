document.addEventListener('DOMContentLoaded', () => {
    const toggle = document.getElementById('quick-add-toggle');
    const panel = document.getElementById('quick-add-panel');
    const userMenuToggle = document.getElementById('user-menu-toggle');
    const userMenu = document.getElementById('user-menu-dropdown');
    const nameInput = document.getElementById('quick-event-name');

    if (!toggle || !panel) {
        return;
    }

    toggle.addEventListener('click', (event) => {
        event.stopPropagation();

        // Benutzermenü schliessen, falls offen
        if (userMenu && userMenu.classList.contains('open')) {
            userMenu.classList.remove('open');
            userMenuToggle?.setAttribute('aria-expanded', 'false');
        }

        const isOpen = panel.classList.toggle('open');
        toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');

        if (isOpen && nameInput) {
            nameInput.focus();
        }
    });

    document.addEventListener('click', (event) => {
        if (!panel.contains(event.target) && !toggle.contains(event.target)) {
            panel.classList.remove('open');
            toggle.setAttribute('aria-expanded', 'false');
        }
    });

    panel.addEventListener('click', (event) => {
        event.stopPropagation();
    });
});