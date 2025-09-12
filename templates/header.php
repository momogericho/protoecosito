<?php
// templates/header.php
// Abilita la compressione gzip se supportata dal client
if (function_exists('ob_gzhandler')) {
    ob_start('ob_gzhandler');
}
// Valori di default per SEO e social sharing
$pageTitle       = $pageTitle ?? "Riuso Sostenibile - Marketplace di materiali riciclati";
$siteName        = "Riuso Sostenibile";
$pageDescription = $pageDescription ?? "Connetti aziende che scartano materiali con artigiani, designer e startup sostenibili. Riduci gli sprechi e promuovi il riciclo creativo.";
$pageKeywords    = $pageKeywords ?? "riciclo, riuso, sostenibilità, materiali, artigiani, startup, economia circolare, green";
$pageUrl         = $pageUrl ?? "https://www.riusosostenibile.it";
$pageImage       = $pageImage ?? "/img/logo.png"; // immagine per social sharing
?>
<!doctype html>
<html lang="it">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- SEO Base -->
  <title><?= htmlspecialchars($pageTitle) ?></title>
  <meta name="description" content="<?= htmlspecialchars($pageDescription) ?>">
  <meta name="keywords" content="<?= htmlspecialchars($pageKeywords) ?>">
  <meta name="author" content="Nome Autore">

  <!-- Open Graph (per Facebook & LinkedIn) -->
  <meta property="og:title" content="<?= htmlspecialchars($pageTitle) ?>">
  <meta property="og:description" content="<?= htmlspecialchars($pageDescription) ?>">
  <meta property="og:type" content="website">
  <meta property="og:url" content="<?= htmlspecialchars($pageUrl) ?>">
  <meta property="og:image" content="<?= htmlspecialchars($pageImage) ?>">

  <!-- Twitter Card -->
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="<?= htmlspecialchars($pageTitle) ?>">
  <meta name="twitter:description" content="<?= htmlspecialchars($pageDescription) ?>">
  <meta name="twitter:image" content="<?= htmlspecialchars($pageImage) ?>">

  <!-- Favicon -->
  <link rel="icon" href="/img/favicon.ico" type="image/x-icon">
  
  <!-- CSS e JS -->
  <link rel="stylesheet" href="/css/style.css">
  <link rel="stylesheet" media="print" href="/css/print.css">
  <script src="/js/model.js" defer></script>
</head>
<body>
  <a href="#mainContent" class="skip-link">Salta al contenuto</a>
  <header id="siteHeader" role="banner" class="site-header"></head"er>
    <nav class="site-nav" role="navigation" aria-label="Main"> 
        <?php require __DIR__.'/menu.php'; ?>
    </nav>
    <div class="site-logo">
      <?= htmlspecialchars($siteName) ?>
    </div>
    <div>
      <?php require __DIR__.'/user_status.php'; ?>
    </div>
     <div id="zoomControls">
      <button id="zoomIn" aria-label="Aumenta dimensione del testo">+</button>
      <button id="zoomOut" aria-label="Riduci dimensione del testo">&minus;</button>
    </div>
  </header>
