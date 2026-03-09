<?php

namespace ProcessWire;

$seo =  $page->seo;

?>

<meta charset="utf-8">
<meta http-equiv="x-ua-compatible" content="ie=edge">
<meta http-equiv="Referrer-Policy" content="no-referrer, strict-origin-when-cross-origin">
<meta name='viewport' content='width=device-width, initial-scale=1' />
<meta name="url" content="<?= $page->httpUrl() ?>">
<?= $seo ?>
<link rel="apple-touch-icon" sizes="180x180" href="<?= $config->urls->templates ?>icons/apple-touch-icon.png?v=xQ7X0KXdyG">
<link rel="icon" type="image/png" sizes="32x32" href="<?= $config->urls->templates ?>icons/favicon-32x32.png?v=xQ7X0KXdyG">
<link rel="icon" type="image/png" sizes="16x16" href="<?= $config->urls->templates ?>icons/favicon-16x16.png?v=xQ7X0KXdyG">
<link rel="manifest" href="<?= $config->urls->templates ?>icons/site.webmanifest?v=xQ7X0KXdyG">
<link rel="mask-icon" href="<?= $config->urls->templates ?>icons/safari-pinned-tab.svg?v=xQ7X0KXdyG" color="#5bbad5">
<link rel="shortcut icon" href="<?= $config->urls->templates ?>icons/favicon.ico?v=xQ7X0KXdyG">
<meta name="apple-mobile-web-app-title" content="Интернет магазин Berkytt">
<meta name="application-name" content="Интернет магазин Berkytt">
<meta name="msapplication-TileColor" content="#00aba9">
<meta name="msapplication-TileImage" content="<?= $config->urls->templates ?>icons/mstile-144x144.png?v=xQ7X0KXdyG">
<meta name="msapplication-config" content="<?= $config->urls->templates ?>icons/browserconfig.xml?v=xQ7X0KXdyG">
<meta name="theme-color" content="#ffffff">