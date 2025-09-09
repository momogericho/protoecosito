// public/js/lista.js
// Piccolo miglioramento UX: se l'utente clicca "Rimuovi filtro",
// puliamo l'input date localmente per coerenza visiva (la pagina ricarica via href).
document.addEventListener('DOMContentLoaded', () => {
  const resetLink = document.getElementById('btn-reset-filtro');
  const dateInput = document.getElementById('after_date');
  if (resetLink && dateInput) {
    resetLink.addEventListener('click', () => {
      dateInput.value = '';
    });
  }
});
