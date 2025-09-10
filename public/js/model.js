// public/js/model.js
document.addEventListener("DOMContentLoaded", () => {
  
  // menu laterale
  const btnMenuToggle = document.getElementById('menuToggle');
  const menu = document.getElementById('sideMenu');
  const overlay = document.getElementById('menuOverlay');

  btnMenuToggle.addEventListener('click', () => {
    menu.classList.add('active');
    overlay.classList.add('active');
  });

  overlay.addEventListener('click', () => {
    menu.classList.remove('active');
    overlay.classList.remove('active');
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
