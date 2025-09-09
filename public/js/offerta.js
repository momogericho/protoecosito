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
    const res = await fetch('/api/associazione.php', { method: 'POST', body, credentials: 'same-origin' });
    return res.json();
  }

  // Esempio: dopo inserimento con redirect la mappa è già aggiornata lato server.
  // Se volessi aggiornarla in-place (senza reload), potresti usare fetch all’endpoint di inserimento
  // e poi pushare il nuovo id qui e chiamare salvaMappa().

  // UX: validazione veloce del form nuovo materiale
  const form = document.getElementById('nuovoMaterialeForm');
  if (form) {
    form.addEventListener('submit', (e) => {
      const nome = document.getElementById('nome').value.trim();
      const costo = parseFloat(document.getElementById('costo').value || '0');
      const quantita = parseInt(document.getElementById('quantita').value || '0', 10);

      if (!/^[A-Za-z0-9 ]{10,40}$/.test(nome)) {
        alert('Il nome deve avere 10–40 caratteri (lettere/numeri/spazi).');
        e.preventDefault(); return;
      }
      if (!(Number.isInteger(quantita) && quantita > 0)) {
        alert('Quantità non valida.'); e.preventDefault(); return;
      }
      if (Math.round(costo*100) % 5 !== 0) {
        alert('Il costo deve avere centesimi multipli di 5.'); e.preventDefault(); return;
      }
    });
  }

  // Espone opzionalmente per future estensioni
  window.AziendaMateriali = { mappa, salvaMappa };
})();
