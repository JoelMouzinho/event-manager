document.addEventListener('DOMContentLoaded', () => {
    const toggle = document.getElementById('user-menu-toggle');
    const menu = document.getElementById('user-menu-dropdown');

    if (!toggle || !menu) {
        return;
    }

    toggle.addEventListener('click', (event) => {
        event.stopPropagation();

        const isOpen = menu.classList.toggle('open');
        toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    });

    document.addEventListener('click', (event) => {
        if (!menu.contains(event.target) && !toggle.contains(event.target)) {
            menu.classList.remove('open');
            toggle.setAttribute('aria-expanded', 'false');
        }
    });
});