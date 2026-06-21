<?php
/** @var string $metaTitle */
/** @var string $metaDescription */
/** @var string $canonical */
$metaTitle = $metaTitle ?? 'Invoxaco - Free Online Business Document Generator';
$metaDescription = $metaDescription ?? 'Create invoices, quotations, contracts, and 100+ business documents online. Generate, save, and download as PDF or Word in minutes.';
$canonical = $canonical ?? (url() . \App\Core\Request::path());
$ogImage = $ogImage ?? asset('img/og-default.png');
$robotsMeta = $robotsMeta ?? 'index,follow';
?>
<title><?= e($metaTitle) ?></title>
<meta name="description" content="<?= e($metaDescription) ?>">
<meta name="robots" content="<?= e($robotsMeta) ?>">
<link rel="canonical" href="<?= e($canonical) ?>">

<meta property="og:type" content="website">
<meta property="og:title" content="<?= e($metaTitle) ?>">
<meta property="og:description" content="<?= e($metaDescription) ?>">
<meta property="og:url" content="<?= e($canonical) ?>">
<meta property="og:image" content="<?= e($ogImage) ?>">
<meta property="og:site_name" content="Invoxaco">

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?= e($metaTitle) ?>">
<meta name="twitter:description" content="<?= e($metaDescription) ?>">
<meta name="twitter:image" content="<?= e($ogImage) ?>">

<?= \App\Services\SeoService::render(\App\Services\SeoService::organizationSchema()) ?>
<?= \App\Services\SeoService::render(\App\Services\SeoService::websiteSchema()) ?>
<?php if (!empty($jsonLd)): foreach ((array) $jsonLd as $schema): ?>
<?= \App\Services\SeoService::render($schema) ?>
<?php endforeach; endif; ?>
