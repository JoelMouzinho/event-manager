"use strict";
// theme-toggle.ts
const toggleButton = document.getElementById('theme-toggle');
function updateButtonIcon() {
    if (!toggleButton)
        return;
    toggleButton.textContent = document.body.classList.contains('dark') ? '☀️' : '🌙';
}
function toggleDarkMode() {
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
