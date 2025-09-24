<?php
// resources/views/partials/header.php
// Abilita la compressione gzip se supportata dal client
if (function_exists('ob_gzhandler')) {
    ob_start('ob_gzhandler');
}
require_once BASE_PATH . '/app/helpers/html_utils.php';

// Valori di default per SEO e social sharing
$seoConfig = require BASE_PATH . '/config/seo.php';
$defaults = $seoConfig['defaults'];

$pageTitle       = $pageTitle ?? $defaults['title'];
$siteName        = $defaults['site_name'] ?? 'Riuso Sostenibile';
$pageDescription = $pageDescription ?? $defaults['description'];
$pageKeywords    = $pageKeywords ?? $defaults['keywords'];
$pageUrl         = $pageUrl ?? $defaults['url'];
$defaultImage    = $defaults['image'];
if (isset($pageImage)) {
} else {
    $pageImage = (strncmp($defaultImage, 'http', 4) === 0) ? $defaultImage : BASE_URL . $defaultImage;
}

$requiredMap = [
    'title' => $pageTitle,
    'description' => $pageDescription,
    'url' => $pageUrl,
    'image' => $pageImage,
];
$missingSeo = [];
foreach ($seoConfig['required'] as $key) {
    $value = trim((string)($requiredMap[$key] ?? ''));
    if ($value === '') {
        $missingSeo[] = $key;
    }
}
$seoChecklistMessage = $missingSeo ? 'Elementi mancanti: ' . implode(', ', $missingSeo) : 'Completo';
?>
<!doctype html>
<html lang="it">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- SEO Base -->
  <title><?= e($pageTitle) ?></title>
  <meta name="description" content="<?= e($pageDescription) ?>">
  <meta name="keywords" content="<?= e($pageKeywords) ?>">
 <meta name="author" content="Mohamed el Hadi Hadj Mahmoud">
  <link rel="canonical" href="<?= e($pageUrl) ?>">
  
  <!-- Open Graph (per Facebook & LinkedIn) -->
  <meta property="og:title" content="<?= e($pageTitle) ?>">
  <meta property="og:description" content="<?= e($pageDescription) ?>">
  <meta property="og:type" content="website">
  <meta property="og:url" content="<?= e($pageUrl) ?>">
  <meta property="og:image" content="<?= e($pageImage) ?>">

  <!-- Twitter Card -->
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="<?= e($pageTitle) ?>">
  <meta name="twitter:description" content="<?= e($pageDescription) ?>">
  <meta name="twitter:image" content="<?= e($pageImage) ?>">

  <!-- Favicon -->
  <!-- <link rel="icon" href="/img/favicon.ico" type="image/x-icon"> -->
  <link rel="manifest" href="<?= BASE_URL ?>/manifest.json">
  <link rel="icon" type="image/png" sizes="192x192" href="<?= BASE_URL ?>/icons/icon-192x192.png">
  <link rel="icon" type="image/png" sizes="512x512" href="<?= BASE_URL ?>/icons/icon-512x512.png">

  <!-- SEO checklist: <?= e($seoChecklistMessage) ?> -->

  <!-- CSS e JS -->
  <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">
  <link rel="stylesheet" media="print" href="<?= BASE_URL ?>/css/print.css">
  <?php if (!empty($pageStyles)): ?>
    <?php foreach ((array)$pageStyles as $css): ?>
      <link rel="stylesheet" href="<?= BASE_URL ?>/css/<?= e($css) ?>">
    <?php endforeach; ?>
  <?php endif; ?>
  <script src="<?= BASE_URL ?>/js/model.js" defer></script>

</head>
<body>
  <a href="#mainContent" class="skip-link">Salta al contenuto</a>
  <header id="siteHeader" role="banner" class="site-header">
    <nav class="main-nav" role="navigation" aria-label="Main">
        <?php render('partials/menu.php'); ?>
    </nav>
    <div class="logo">
      <?= e($siteName) ?>
    </div>
    <div>
      <?php render('partials/user_status.php'); ?>
    </div>
     <div id="zoomControls">
      <button id="zoomIn" aria-label="Aumenta dimensione del testo">+</button>
      <button id="zoomOut" aria-label="Riduci dimensione del testo">-</button>
    </div>
  </header>
