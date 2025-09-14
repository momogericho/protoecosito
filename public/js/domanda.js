// public/js/domanda.js

async function savePostRequest(url, body) {
  return new Promise((resolve, reject) => {
    const open = indexedDB.open('offline-posts', 1);
    open.onupgradeneeded = () => {
      open.result.createObjectStore('requests', { autoIncrement: true });
    };
    open.onerror = () => reject(open.error);
    open.onsuccess = async () => {
      const db = open.result;
      const tx = db.transaction('requests', 'readwrite');
      tx.objectStore('requests').add({ url, body, timestamp: Date.now() });
      tx.oncomplete = async () => {
        const reg = await navigator.serviceWorker.ready;
        try { await reg.sync.register('sync-post-requests'); } catch (e) {}
        resolve();
      };
      tx.onerror = () => reject(tx.error);
    };
  });
}

async function postWithFallback(url, body) {
  if (!navigator.onLine) {
    return savePostRequest(url, body);
  }
  try {
    return await fetch(url, { method: 'POST', body, credentials: 'same-origin' });
  } catch (err) {
    return savePostRequest(url, body);
  }
}
document.addEventListener('DOMContentLoaded', () => {
  const items = Array.from(document.querySelectorAll('.material-item'));
  const totalEl = document.getElementById('cart-total');
  const btnCancella = document.getElementById('btn-cancella');

  function fmt(val) {
    return val.toFixed(2).replace('.', ',');
  }

  // Aggiorna il subtotal per una riga e il totale complessivo
  function updateTotals() {
    let total = 0;
    items.forEach(it => {
      const available = parseInt(it.dataset.available, 10);
      const unit = parseFloat(it.dataset.unit);
      const input = it.querySelector('.qty-input');
      let q = parseInt(input.value, 10) || 0;
      if (q < 0) q = 0;
      if (q > available) q = available;
      input.value = q;
      const subtotal = q * unit;
      const subEl = it.querySelector('.line-subtotal');
      subEl.textContent = fmt(subtotal) + ' €';
      total += subtotal;
    });
    totalEl.textContent = fmt(total);
  }

  // Inizializza handlers su ogni item
  items.forEach(it => {
    const btnDec = it.querySelector('.btn-decr');
    const btnInc = it.querySelector('.btn-incr');
    const input  = it.querySelector('.qty-input');
    const errEl  = it.querySelector('.error-msg');
    const available = parseInt(it.dataset.available, 10);

    btnDec.addEventListener('click', () => {
      let v = parseInt(input.value, 10) || 0;
      v = v - 1;
      if (v < 0) v = 0;
      input.value = v;
      errEl.textContent = '';
      updateTotals();
    });

    btnInc.addEventListener('click', () => {
      let v = parseInt(input.value, 10) || 0;
      v = v + 1;
      if (v > available) v = available;
      input.value = v;
      errEl.textContent = '';
      updateTotals();
    });

    // input diretto: valida on input/blur
    input.addEventListener('input', () => {
      let v = input.value;
      // solo cifre intere
      if (!/^\d*$/.test(v)) {
        input.value = v.replace(/[^\d]/g, '');
        errEl.textContent = 'Solo numeri.';
      } else {
        errEl.textContent = '';
      }
    });
    input.addEventListener('blur', () => {
      let v = parseInt(input.value, 10);
      let msg = '';
      if (isNaN(v) || v < 0) {
        v = 0;
        msg = 'Valore non valido.';
      } else if (v > available) {
        v = available;
        msg = `Disponibilità massima: ${available}`;
      }
      input.value = v;
      errEl.textContent = msg;
      updateTotals();
    });
  });

  // Annulla: reset quantità a 0 localmente e invoca endpoint server per svuotare sessione (opzionale)
  btnCancella.addEventListener('click', async () => {
    items.forEach(it => {
      const input = it.querySelector('.qty-input');
      input.value = 0;
    });
    updateTotals();

    // opzionale: svuota server-side (CSRF)
    try {
      const body = new URLSearchParams();
      body.append('csrf_token', window.__CSRF__);
            await postWithFallback('/app/controllers/reset_selection.php', body);
    } catch (err) {
      console.warn('Reset server-side fallito', err);
    }
  });

  // Calcolo iniziale
  updateTotals();
});
