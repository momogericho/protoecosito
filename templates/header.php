<?php
// templates/header.php
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
  <script src="/js/model.js" defer></script>
</head>
<body>
  <header class="site-header">
    <div class="site-logo">
      <?= htmlspecialchars($siteName) ?>
    </div>
    <nav class="site-nav">
        <?php require __DIR__.'/menu.php'; ?>
    </nav>
    <div> 
      <?php require __DIR__.'/user_status.php'; ?>
    </div>
  </header>
