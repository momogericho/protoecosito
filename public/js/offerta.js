(function(){
  const csrf = window.__CSRF__;
  const aziendaId = window.__AZIENDA_ID__;
  const iniziali = Array.isArray(window.__MATERIALI_IDS__) ? window.__MATERIALI_IDS__ : [];

  // Mappa frontend: { [aziendaId]: [ids...] }
  const mappa = { [aziendaId]: [...iniziali] };

  // Helper: salva su storage file via API
  async function salvaMappa() {
    const body = new URLSearchParams();
    body.append('action','set');
    body.append('csrf_token', csrf);
    mappa[aziendaId].forEach((id, idx) => body.append(`materialiIds[${idx}]`, String(id)));
    const res = await fetch('/api/associazioneAzMat.php', { method: 'POST', body, credentials: 'same-origin' });
    return res.json();
  }

  // Esempio: dopo inserimento con redirect la mappa è già aggiornata lato server.
  // Se volessi aggiornarla in-place (senza reload), potresti usare fetch all’endpoint di inserimento
  // e poi pushare il nuovo id qui e chiamare salvaMappa().

  // UX: validazione veloce del form nuovo materiale
  const form = document.getElementById('nuovoMaterialeForm');
  if (form) {
    const nomeEl = document.getElementById('nome');
    const quantitaEl = document.getElementById('quantita');
    const costoEl = document.getElementById('costo');
    const msgNome = nomeEl.nextElementSibling;
    const msgQuantita = quantitaEl.nextElementSibling;
    const msgCosto = costoEl.nextElementSibling;
    form.addEventListener('submit', (e) => {
      msgNome.textContent = '';
      msgQuantita.textContent = '';
      msgCosto.textContent = '';

      const nome = nomeEl.value.trim();
      const costo = parseFloat(costoEl.value || '0');
      const quantita = parseInt(quantitaEl.value || '0', 10);

      let valid = true;

      if (!/^[A-Za-z0-9 ]{10,40}$/.test(nome)) {
        msgNome.textContent = 'Il nome deve avere 10–40 caratteri (lettere/numeri/spazi).';
        valid = false;
      }
      if (!(Number.isInteger(quantita) && quantita > 0)) {
        msgQuantita.textContent = 'Quantità non valida.';
        valid = false;      }
      if (Math.round(costo*100) % 5 !== 0) {
        msgCosto.textContent = 'Il costo deve avere centesimi multipli di 5.';
        valid = false;
      }

      if (!valid) {
        e.preventDefault();      }
    });
  }

  // Espone opzionalmente per future estensioni
  window.AziendaMateriali = { mappa, salvaMappa };
})();
