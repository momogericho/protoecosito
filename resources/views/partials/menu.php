<?php
// resources/views/partials/user_status.php

// Recupero stato utente
$isLogged = !empty($_SESSION['user_id']);
$isAzienda = $isLogged && isset($_SESSION['artigiano']) && !$_SESSION['artigiano'];
$isArtigiano = $isLogged && !empty($_SESSION['artigiano']);

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
  <button id="menuToggle"  aria-label="Apri menu" aria-controls="sideMenu" aria-expanded="false">☰</button>
</div>

<!-- Overlay -->
<div id="menuOverlay" class="overlay"></div>

<!-- Menù laterale -->
<nav id="sideMenu" class="side-menu"  role="dialog" aria-modal="true">
  <h2>Menu</h2>
  <ul>
    <?php foreach ($menuItems as $item): ?>
      <li class="<?= $item['enabled'] ? '' : 'disabled' ?>">
        <?php if ($item['enabled']): ?>
          <a href="<?= e($item['url']) ?>">
            <?= e($item['label']) ?>
          </a>
        <?php else: ?>
          <span><?= e($item['label']) ?></span>
        <?php endif; ?>
      </li>
    <?php endforeach; ?>
  </ul>
</nav>

