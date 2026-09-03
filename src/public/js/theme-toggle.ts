// theme-toggle.ts
const toggleButton = document.getElementById('theme-toggle') as HTMLButtonElement | null;

function updateButtonIcon(): void {
    if (!toggleButton) return;
    toggleButton.textContent = document.body.classList.contains('dark') ? '☀️' : '🌙';
}

function toggleDarkMode(): void {
    document.body.classList.toggle('dark');
    localStorage.setItem('darkMode', document.body.classList.contains('dark') ? 'true' : 'false');
    updateButtonIcon();
}

// Prüfen, ob Dark Mode gespeichert ist
if (localStorage.getItem('darkMode') === 'true') {
    document.body.classList.add('dark');
}
updateButtonIcon();

toggleButton?.addEventListener('click', toggleDarkMode);