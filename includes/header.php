<?php
// Include i18n helper if not already loaded
if (!class_exists('i18n')) {
    require_once __DIR__ . '/i18n.php';
}

// Detect language and country from URL parameters or browser
if (!isset($lang)) {
    $lang = isset($_GET['lang']) ? strtolower($_GET['lang']) : null;

    // If no lang parameter, try to detect from browser
    if (!$lang && isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
        $browserLang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
        $lang = in_array($browserLang, ['fr', 'en', 'es', 'pt', 'it', 'de', 'sv', 'no']) ? $browserLang : 'en';
    } else {
        $lang = $lang ?? 'en';
    }
}

if (!isset($country)) {
    $country = isset($_GET['country']) ? strtoupper($_GET['country']) : null;
}

// Validate language
$validLanguages = ['fr', 'en', 'es', 'pt', 'it', 'de', 'sv', 'no'];
if (!in_array($lang, $validLanguages)) {
    $lang = 'en';
}

// Initialize i18n system only if not already initialized
if (i18n::getCurrentLang() === null || i18n::getCurrentLang() !== $lang) {
    i18n::init($lang, __DIR__ . '/../langs/');
}

// Load translations for all languages to extract metadata
$langsPath = __DIR__ . '/../langs/';
$translations = [];
foreach (['fr', 'en', 'es', 'pt', 'it', 'de', 'sv', 'no'] as $langCode) {
    $langFile = $langsPath . $langCode . '.json';
    if (file_exists($langFile)) {
        $translations[$langCode] = json_decode(file_get_contents($langFile), true);
    }
}

// Get metadata from current language JSON
$currentTranslations = $translations[$lang] ?? $translations['en'];

// Load valid countries for current language from JSON
$validCountries = $currentTranslations['validCountries'] ?? [];

// Validate country based on language
if (!$country || !in_array($country, $validCountries)) {
    $country = $lang === 'en' ? 'US' : 'FR';
}

// Set the lang attribute
$langAttribute = $lang . '-' . $country;

// Store in session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$_SESSION['lang'] = $lang;
$_SESSION['country'] = $country;

// Load country metadata and currency from JSON for current language
$countryMetadata = $currentTranslations['countryMetadata'] ?? [];
$currencyByCountry = $currentTranslations['currencyByCountry'] ?? [];

// Get metadata for current language/country
$metadata = $countryMetadata[$country] ?? [];
$geoRegion = $metadata['region'] ?? $country;
$geoRegionCode = $metadata['region_code'] ?? $country;
$geoCountry = $metadata['name'] ?? $country;
$geoPlacename = $metadata['name'] ?? $country;
$metaData = $currentTranslations['meta'] ?? [];
$seoData = $currentTranslations['seo'] ?? [];
$features = $seoData['features']['items'] ?? [];

// Extract page title from meta
$pageTitle = $metaData['title'] ?? 'Calcuze';

// Extract page description from meta
$pageDescription = $metaData['description'] ?? '';

// Extract keywords from meta
$pageKeywords = $metaData['keywords'] ?? '';

// OG Title and Description from SEO data
$ogTitle = $seoData['main_title'] ?? 'Calcuze';
$ogDescription = $seoData['main_subtitle'] ?? '';

// Build the URL
$baseUrl = 'https://calcuze.com';
$url = $baseUrl . '/' . $lang . '/' . $country;

// Convert language code for inLanguage field (fr-FR format)
$inLanguage = str_replace('-', '_', $langAttribute);

// Get currency for current country
$currency = $currencyByCountry[$country] ?? 'USD';

// Get decimal separator from current language
$decimalSeparator = $currentTranslations['decimal_separator'] ?? '.';
?>
<!DOCTYPE html>
<html lang="<?php echo $langAttribute; ?>" id="html-root">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($pageDescription, ENT_QUOTES, 'UTF-8'); ?>">
    <meta name="keywords" content="<?php echo htmlspecialchars($pageKeywords, ENT_QUOTES, 'UTF-8'); ?>">
    <meta name="robots" content="index, follow">
    <meta name="author" content="Calcuze">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo $url; ?>">
    <meta property="og:title" content="<?php echo htmlspecialchars($ogTitle, ENT_QUOTES, 'UTF-8'); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($ogDescription, ENT_QUOTES, 'UTF-8'); ?>">
    <meta property="og:site_name" content="Calcuze">
    <meta property="og:locale" content="<?php echo $langAttribute; ?>">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:title" content="<?php echo htmlspecialchars($ogTitle, ENT_QUOTES, 'UTF-8'); ?>">
    <meta property="twitter:description" content="<?php echo htmlspecialchars($ogDescription, ENT_QUOTES, 'UTF-8'); ?>">

    <!-- Géolocalisation -->
    <meta name="geo.region" content="<?php echo $geoRegion; ?>">
    <meta name="geo.country" content="<?php echo $geoCountry; ?>">
    <meta name="geo.placename" content="<?php echo $geoPlacename; ?>">
    <meta name="language" content="<?php echo $langAttribute; ?>">

    <!-- Canonical URL -->
    <link rel="canonical" href="<?php echo $url; ?>">

    <!-- Hreflang Alternate URLs for SEO -->
    <?php
    // Generate hreflang links dynamically from all available language files
    if (isset($translations) && is_array($translations)) {
        foreach ($translations as $langCode => $langData) {
            if (isset($langData['validCountries']) && is_array($langData['validCountries'])) {
                foreach ($langData['validCountries'] as $countryCode) {
                    $hreflangUrl = $baseUrl . '/' . $langCode . '/' . $countryCode;
                    echo '<link rel="alternate" hreflang="' . $langCode . '-' . $countryCode . '" href="' . $hreflangUrl . '" />' . "\n    ";
                }
            }
        }
    }

    // X-Default for default language
    echo '<link rel="alternate" hreflang="x-default" href="' . $baseUrl . '/en/US" />' . "\n    ";
    ?>

    <!-- JSON-LD Structured Data -->
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "WebApplication",
            "name": "Calcuze",
            "description": "<?php echo htmlspecialchars($pageDescription, ENT_QUOTES, 'UTF-8'); ?>",
            "url": "<?php echo $url; ?>",
            "applicationCategory": "UtilitiesApplication",
            "operatingSystem": "Any",
            "inLanguage": "<?php echo $inLanguage; ?>",
            "availableLanguage": ["en", "fr"],
            "offers": {
                "@type": "Offer",
                "price": "0",
                "priceCurrency": "<?php echo $currency; ?>"
            },
            "featureList": [
                <?php
                    if (!empty($features)) {
                        echo '"' . implode('", "', array_map(function($f) {
                            return htmlspecialchars($f, ENT_QUOTES, 'UTF-8');
                        }, $features)) . '"';
                    }
                ?>
            ],
            "browserRequirements": "Requires JavaScript"
        }
    </script>

    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-N8S3ZX8V');</script>
    <!-- End Google Tag Manager -->

    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-0FNDY41NJH"></script>
    <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'G-0FNDY41NJH');
    </script>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo isset($cssPath) ? $cssPath : '../css/styles.css'; ?>">
    <?php
    $selectorStylesPath = isset($includesPath) ? $includesPath . 'country-selector-styles.php' : '../includes/country-selector-styles.php';
    include $selectorStylesPath;
    ?>
</head>

