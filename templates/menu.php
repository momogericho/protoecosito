<?php
// templates/menu.php
session_start();

// Recupero stato utente
$isLogged    = !empty($_SESSION['user_id']);
$isAzienda   = $isLogged && isset($_SESSION['$isArtigiano']) && $_SESSION['$isArtigiano'] === False;
$isArtigiano   = $isLogged && isset($_SESSION['$isArtigiano']) && $_SESSION['$isArtigiano'] === True;

$menuItems = [
    ['label' => 'Home',    'url' => '/home.php',    'enabled' => true],
    ['label' => 'Login',   'url' => '/login.php',   'enabled' => !$isLogged],
    ['label' => 'Registra','url' => '/registra.php','enabled' => true],
    ['label' => 'Lista',   'url' => '/lista.php',   'enabled' => true],
    ['label' => 'Offerta', 'url' => '/offerta.php', 'enabled' => $isAzienda],
    ['label' => 'Domanda', 'url' => '/domanda.php', 'enabled' => $isArtigiano],
    ['label' => 'Logout',  'url' => '/logout.php',  'enabled' => $isLogged],
];
?>

<!-- Bottone hamburger -->
<div class="menu-btn">
  <button id="menuToggle">☰</button>
</div>

<!-- Overlay -->
<div id="menuOverlay" class="overlay"></div>

<!-- Menù laterale -->
<nav id="sideMenu" class="side-menu">
  <h2>Menu</h2>
  <ul>
    <?php foreach ($menuItems as $item): ?>
      <li class="<?= $item['enabled'] ? '' : 'disabled' ?>">
        <?php if ($item['enabled']): ?>
          <a href="<?= htmlspecialchars($item['url']) ?>">
            <?= htmlspecialchars($item['label']) ?>
          </a>
        <?php else: ?>
          <span><?= htmlspecialchars($item['label']) ?></span>
        <?php endif; ?>
      </li>
    <?php endforeach; ?>
  </ul>
</nav>

<script>
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
</script>

