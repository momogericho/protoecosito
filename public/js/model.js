// public/js/model.js
document.addEventListener("DOMContentLoaded", () => {
  

  // menu laterale
  const btnMenuToggle = document.getElementById('menuToggle');
  const menu = document.getElementById('sideMenu');
  const overlay = document.getElementById('menuOverlay');
  const focusableSelectors = 'a, button, input, textarea, select, [tabindex]:not([tabindex="-1"])';
  let focusables = [];

  // apre menu
  function openMenu() {
    menu.classList.add('active');
    overlay.classList.add('active');
    btnMenuToggle.setAttribute('aria-expanded', 'true');
    focusables = Array.from(menu.querySelectorAll(focusableSelectors));
    focusables[0]?.focus();
  }

  // chiude menu
  function closeMenu() {
    menu.classList.remove('active');
    overlay.classList.remove('active');
    btnMenuToggle.setAttribute('aria-expanded', 'false');
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
    window.location.href = "/public/login.php";
  });
  logoutBtn?.addEventListener("click", () => {
    window.location.href = "/public/logout.php";
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

  const re = {
    nick: /^[A-Za-z][A-Za-z0-9_-]{3,9}$/,
    passAllowed: /^[A-Za-z0-9.;+=]{8,16}$/,
    indirizzo: /^(Via|Corso)\s+[\p{L} ]+\s+\d{1,3},\s*[\p{L} ]+$/u,
    ragione: /^\p{Lu}[\p{L}\p{Nd}& ]{0,29}$/u,
    name: /^[\p{L} ]{4,14}$/u,
    surname: /^[\p{L} ']{4,16}$/u,
    birth: /^\d{4}-(0?[1-9]|1[0-2])-(0?[1-9]|[12]\d|3[01])$/
  };

  // Controllo rapido prima dell'invio
  function quickValidateAzienda(e) {
    const f = aziForm;
    const ok =
      re.ragione.test(f.ragione.value) &&
      re.indirizzo.test(f.address2.value) &&
      re.nick.test(f.nick.value) &&
      re.passAllowed.test(f.password.value) &&
      /[A-Z]/.test(f.password.value) &&
      /[a-z]/.test(f.password.value) &&
      /\d/.test(f.password.value) &&
      /[.;+=]/.test(f.password.value);
    if (!ok) {
      alert('Controlla i campi Azienda (formato non valido).');
      e.preventDefault();
    }
  }

  // Controllo rapido prima dell'invio
  function quickValidateArtigiano(e) {
    const f = artForm;
    const credit = f.credit.value;
    const centsOk = /^\d+(?:\.\d{2})$/.test(credit) && (Math.round(parseFloat(credit) * 100) % 5 === 0);

    const ok =
      re.name.test(f.name.value) &&
      re.surname.test(f.surname.value) &&
      re.birth.test(f.birthdate.value) &&
      centsOk &&
      re.indirizzo.test(f.address.value) &&
      re.nick.test(f.nick.value) &&
      re.passAllowed.test(f.password.value) &&
      /[A-Z]/.test(f.password.value) &&
      /[a-z]/.test(f.password.value) &&
      /\d/.test(f.password.value) &&
      /[.;+=]/.test(f.password.value);

    if (!ok) {
      alert('Controlla i campi Artigiano (formato non valido).');
      e.preventDefault();
    }
  }

  aziForm?.addEventListener('submit', quickValidateAzienda);
  artForm?.addEventListener('submit', quickValidateArtigiano);  
});
