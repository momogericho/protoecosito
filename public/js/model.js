// public/js/model.js
if ('serviceWorker' in navigator) {
  window.addEventListener('load', () => {
    navigator.serviceWorker.register('/sw.js');
  });
}

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

document.addEventListener("DOMContentLoaded", () => {

  // banner stato connessione
  const connectionBanner = document.createElement('div');
  connectionBanner.id = 'connectionBanner';
  Object.assign(connectionBanner.style, {
    position: 'fixed',
    bottom: '0',
    left: '0',
    right: '0',
    padding: '0.5rem',
    textAlign: 'center',
    color: '#fff',
    zIndex: '1000',
    display: 'none'
  });

  function updateConnectionStatus() {
    if (navigator.onLine) {
      connectionBanner.textContent = 'Sei online';
      connectionBanner.style.backgroundColor = '#4caf50';
    } else {
      connectionBanner.textContent = 'Sei offline';
      connectionBanner.style.backgroundColor = '#f44336';
    }
    connectionBanner.style.display = 'block';
  }

  window.addEventListener('online', updateConnectionStatus);
  window.addEventListener('offline', updateConnectionStatus);
  document.body.appendChild(connectionBanner);
  updateConnectionStatus();

  // menu laterale
  const btnMenuToggle = document.getElementById('menuToggle');
  const menu = document.getElementById('sideMenu');
  const overlay = document.getElementById('menuOverlay');
  const focusableSelectors = 'a, button, input, textarea, select, [tabindex]:not([tabindex="-1"])';
  let focusables = [];

  // aggiorna aria-expanded sul bottone
  function setMenuExpanded(expanded) {
    btnMenuToggle.setAttribute('aria-expanded', expanded ? 'true' : 'false');
  }

  // apre menu
  function openMenu() {
    menu.classList.add('active');
    overlay.classList.add('active');
    setMenuExpanded(true);
    focusables = Array.from(menu.querySelectorAll(focusableSelectors));
    focusables[0]?.focus();
  }

  // chiude menu
  function closeMenu() {
    menu.classList.remove('active');
    overlay.classList.remove('active');
    setMenuExpanded(true);
    btnMenuToggle.focus();
  }

  // alterna stato menu
  function toggleMenu() {
    if (menu.classList.contains('active')) {
      closeMenu();
    } else {
      openMenu();
    }
  }

  btnMenuToggle.addEventListener('click', toggleMenu);
  btnMenuToggle.addEventListener('keydown', (e) => {
    if (e.key === 'Enter' || e.key === ' ') {
      e.preventDefault();
      toggleMenu();
    }
  });

  overlay.addEventListener('click', closeMenu);
  
  // gestione focus trap e chiusura con ESC
  document.addEventListener('keydown', (e) => {
    if (!menu.classList.contains('active')) return;
    if (e.key === 'Escape') {
      closeMenu();
      return;
    }
    if (e.key === 'Tab') {
      const first = focusables[0];
      const last = focusables[focusables.length - 1];
      if (e.shiftKey && document.activeElement === first) {
        e.preventDefault();
        last.focus();
      } else if (!e.shiftKey && document.activeElement === last) {
        e.preventDefault();
        first.focus();
      }
    }
  });
  

  // effetto nascondi/mostra header
  let lastScrollY = window.scrollY;
  const siteHeader = document.getElementById('siteHeader');
  window.addEventListener('scroll', () => {
    if (window.scrollY > lastScrollY) {
      siteHeader?.classList.add('hidden');
    } else {
      siteHeader?.classList.remove('hidden');
    }
    lastScrollY = window.scrollY;
  });


  // bottone login/logout
  const logoutBtn = document.getElementById("logoutBtn");
  const loginBtn = document.getElementById("loginBtn");

  loginBtn?.addEventListener("click", () => {
    window.location.href = "/login.php";
  });
  logoutBtn?.addEventListener("click", () => {
    window.location.href = "/logout.php";
  });
  

  // controlli zoom caratteri
  const zoomInBtn = document.getElementById('zoomIn');
  const zoomOutBtn = document.getElementById('zoomOut');
  const root = document.documentElement;
  const savedSize = localStorage.getItem('rootFontSize');
  if (savedSize) {
    root.style.fontSize = savedSize;
  }
  // funzione di utilità per cambiare la dimensione del font
  function adjustFont(delta) {
    const current = parseFloat(getComputedStyle(root).fontSize);
    const newSize = current + delta;
    root.style.fontSize = newSize + 'px';
    localStorage.setItem('rootFontSize', root.style.fontSize);
  }

  zoomInBtn?.addEventListener('click', () => adjustFont(1));
  zoomOutBtn?.addEventListener('click', () => adjustFont(-1));
  

  // validazione form registrazione
  const aziForm = document.querySelector('form input[name="type"][value="azienda"]')?.closest('form');
  const artForm = document.querySelector('form input[name="type"][value="artigiano"]')?.closest('form');

  function clearError(element) {
    const msg = element?.nextElementSibling;
    if (msg && msg.classList.contains('error-message')) {
      msg.remove();
    }
  }

  function showError(message, element) {
    if (!element) return;
    let msg = element.nextElementSibling;
    if (!msg || !msg.classList.contains('error-message')) {
      msg = document.createElement('div');
      msg.className = 'error-message';
      msg.style.color = 'red';
      msg.setAttribute('aria-live', 'polite');
      element.insertAdjacentElement('afterend', msg);
      element.addEventListener('input', () => clearError(element), { once: true });
    }
    msg.textContent = message;
  }

  const re = {
    nick: /^[A-Za-z][A-Za-z0-9_-]{3,9}$/,
    passAllowed: /^[A-Za-z0-9.;+=]{8,16}$/,
    indirizzo: /^(Via|Corso)\s+[\p{L} ]+\s+\d{1,3},\s*[\p{L} ]+$/u,
    ragione: /^\p{Lu}[\p{L}\p{Nd}& ]{0,29}$/u,
    name: /^[\p{L} ]{4,14}$/u,
    surname: /^[\p{L} ']{4,16}$/u,
    birth: /^\d{4}-(0?[1-9]|1[0-2])-(0?[1-9]|[12]\d|3[01])$/
  };

  let aziNickTaken = false;
  let artNickTaken = false;

  // Controllo disponibilità nick via API
  function setupNickCheck(form, setTaken) {
    const input = form?.querySelector('input[name="nick"]');
    const submit = form?.querySelector('button[type="submit"]');
    if (!form || !input || !submit) return;
    

    input.addEventListener('input', async () => {
      const nick = input.value.trim();
      clearError(input);
       setTaken(false);
       submit.disabled = false;
       if (!re.nick.test(nick)) return;
       try {
         const r = await fetch(`/api/check_nick.php?nick=${encodeURIComponent(nick)}`);
         const data = await r.json();
         if (data.exists) {
          showError('Nick già in uso', input);
          setTaken(true);
          submit.disabled = true;
        }
      } catch (e) {
        console.error(e);
      }
    });
  }

  setupNickCheck(aziForm, v => aziNickTaken = v);
  setupNickCheck(artForm, v => artNickTaken = v);


  // Controllo rapido azienda prima dell'invio
  function quickValidateAzienda(e) {
    const f = aziForm;
    if (aziNickTaken) {
      showError('Username già in uso.', f.nick);
      e.preventDefault();
      return;
    }
     let invalid = null;
    if (!re.ragione.test(f.ragione.value)) invalid = f.ragione;
    else if (!re.indirizzo.test(f.address2.value)) invalid = f.address2;
    else if (!re.nick.test(f.nick.value)) invalid = f.nick;
    else if (!(re.passAllowed.test(f.password.value) && /[A-Z]/.test(f.password.value) && /[a-z]/.test(f.password.value) && /\d/.test(f.password.value) && /[.;+=]/.test(f.password.value))) invalid = f.password;
    if (invalid) {
      showError('Controlla i campi Azienda (formato non valido).', invalid);
      e.preventDefault();
    }
  }

  // Controllo rapido artigiano prima dell'invio
  function quickValidateArtigiano(e) {
    const f = artForm;
    const credit = f.credit.valueAsNumber;
    const creditOk = Number.isFinite(credit) && (Math.round(credit * 100) % 5 === 0);

    if (artNickTaken) {
      showError('Username già in uso.', f.nick);
      e.preventDefault();
      return;
    }
    
    let invalid = null;
    if (!re.name.test(f.name.value)) invalid = f.name;
    else if (!re.surname.test(f.surname.value)) invalid = f.surname;
    else if (!re.birth.test(f.birthdate.value)) invalid = f.birthdate;
    else if (!creditOk) invalid = f.credit;
    else if (!re.indirizzo.test(f.address.value)) invalid = f.address;
    else if (!re.nick.test(f.nick.value)) invalid = f.nick;
    else if (!(re.passAllowed.test(f.password.value) && /[A-Z]/.test(f.password.value) && /[a-z]/.test(f.password.value) && /\d/.test(f.password.value) && /[.;+=]/.test(f.password.value))) invalid = f.password;

    if (invalid) {
      showError('Controlla i campi Artigiano (formato non valido).', invalid);
      e.preventDefault();
    }
  }

  aziForm?.addEventListener('submit', quickValidateAzienda);
  artForm?.addEventListener('submit', quickValidateArtigiano);  
});
