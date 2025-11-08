<?php
// Detect language and country from URL parameters or browser
if (!isset($lang)) {
    $lang = isset($_GET['lang']) ? strtolower($_GET['lang']) : null;

    // If no lang parameter, try to detect from browser
    if (!$lang && isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
        $browserLang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
        $lang = in_array($browserLang, ['fr', 'en']) ? $browserLang : 'en';
    } else {
        $lang = $lang ?? 'en';
    }
}

if (!isset($country)) {
    $country = isset($_GET['country']) ? strtoupper($_GET['country']) : null;
}

// Validate language
$validLanguages = ['fr', 'en'];
if (!in_array($lang, $validLanguages)) {
    $lang = 'en';
}

// Validate country based on language
$validCountriesByLang = [
    'fr' => ['FR', 'BE', 'CH', 'CA', 'LU', 'MC'],
    'en' => ['US', 'GB', 'AU', 'CA', 'NZ', 'IE']
];

if (!$country || !isset($validCountriesByLang[$lang]) || !in_array($country, $validCountriesByLang[$lang])) {
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

// Country metadata configuration
$countryMetadata = [
    'fr' => [
        'FR' => ['name' => 'France', 'region' => 'FR', 'region_code' => 'FR'],
        'BE' => ['name' => 'Belgique', 'region' => 'BE', 'region_code' => 'BE'],
        'CH' => ['name' => 'Suisse', 'region' => 'CH', 'region_code' => 'CH'],
        'CA' => ['name' => 'Canada', 'region' => 'CA', 'region_code' => 'QC'],
        'LU' => ['name' => 'Luxembourg', 'region' => 'LU', 'region_code' => 'LU'],
        'MC' => ['name' => 'Monaco', 'region' => 'MC', 'region_code' => 'MC']
    ],
    'en' => [
        'US' => ['name' => 'United States', 'region' => 'US', 'region_code' => 'US'],
        'GB' => ['name' => 'United Kingdom', 'region' => 'GB', 'region_code' => 'GB'],
        'AU' => ['name' => 'Australia', 'region' => 'AU', 'region_code' => 'AU'],
        'CA' => ['name' => 'Canada', 'region' => 'CA', 'region_code' => 'ON'],
        'NZ' => ['name' => 'New Zealand', 'region' => 'NZ', 'region_code' => 'NZ'],
        'IE' => ['name' => 'Ireland', 'region' => 'IE', 'region_code' => 'IE']
    ]
];

// Get metadata for current language/country
$metadata = $countryMetadata[$lang][$country];
$geoRegion = $metadata['region'];
$geoRegionCode = $metadata['region_code'];
$geoCountry = $metadata['name'];
$geoPlacename = $metadata['name'];

// JSON-LD content based on language
$jsonldContent = [
    'fr' => [
        'name' => 'Calcuze Calculatrice Universelle',
        'description' => 'Calculatrice professionnelle en ligne avec fonctions scientifiques, économiques et de conversion d\'unités',
        'operatingSystem' => 'Navigateur Web',
        'browserRequirements' => 'Nécessite JavaScript',
        'featureList' => [
            'Opérations arithmétiques de base',
            'Calculs scientifiques',
            'Formules économiques',
            'Conversions d\'unités',
            'Suivi de l\'historique'
        ],
        'priceCurrency' => 'EUR'
    ],
    'en' => [
        'name' => 'Calcuze Universal Calculator',
        'description' => 'Professional online calculator with scientific, economic, and unit conversion functions',
        'operatingSystem' => 'Web Browser',
        'browserRequirements' => 'Requires JavaScript',
        'featureList' => [
            'Basic arithmetic operations',
            'Scientific calculations',
            'Economic formulas',
            'Unit conversions',
            'History tracking'
        ],
        'priceCurrency' => 'USD'
    ]
];

// Currency codes by country
$currencyByCountry = [
    'fr' => [
        'FR' => 'EUR', 'BE' => 'EUR', 'CH' => 'CHF',
        'CA' => 'CAD', 'LU' => 'EUR', 'MC' => 'EUR'
    ],
    'en' => [
        'US' => 'USD', 'GB' => 'GBP', 'AU' => 'AUD',
        'CA' => 'CAD', 'NZ' => 'NZD', 'IE' => 'EUR'
    ]
];

// Get content for current language
$content = $jsonldContent[$lang];
$currency = $currencyByCountry[$lang][$country] ?? 'USD';

// Build the URL
$baseUrl = 'https://calcuze.com';
$url = $baseUrl . '/' . $lang . '/' . $country;

// Convert language code for inLanguage field (fr-FR format)
$inLanguage = str_replace('-', '_', $langAttribute);

// Page titles and meta descriptions based on language
$pageTitles = [
    'fr' => 'Calculatrice Universelle - Calcuze',
    'en' => 'Universal Calculator - Calcuze'
];

$pageDescriptions = [
    'fr' => 'Calculatrice professionnelle en ligne avec fonctions scientifiques, économiques et de conversion d\'unités. Outil de calcul gratuit pour étudiants, professionnels et entreprises.',
    'en' => 'Professional online calculator with scientific, economic, and unit conversion functions. Free calculator tool for students, professionals, and businesses.'
];

$pageKeywords = [
    'fr' => 'calculatrice, calculatrice scientifique, calculatrice économique, convertisseur d\'unités, outil mathématique, calculatrice en ligne, calculatrice gratuite',
    'en' => 'calculator, scientific calculator, economic calculator, unit converter, math tool, online calculator, free calculator'
];

$ogTitles = [
    'fr' => 'Calcuze - Calculatrice Universelle',
    'en' => 'Calcuze - Universal Calculator'
];

$ogDescriptions = [
    'fr' => 'Calculatrice professionnelle avec fonctions avancées incluant calculs scientifiques, calculs économiques et conversions d\'unités.',
    'en' => 'Professional calculator with advanced features including scientific functions, economic calculations, and unit conversions.'
];
?>
<!DOCTYPE html>
<html lang="<?php echo $langAttribute; ?>" id="html-root">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitles[$lang]; ?></title>
    <meta name="description" content="<?php echo $pageDescriptions[$lang]; ?>">
    <meta name="keywords" content="<?php echo $pageKeywords[$lang]; ?>">
    <meta name="robots" content="index, follow">
    <meta name="author" content="Calcuze">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo $url; ?>">
    <meta property="og:title" content="<?php echo $ogTitles[$lang]; ?>">
    <meta property="og:description" content="<?php echo $ogDescriptions[$lang]; ?>">
    <meta property="og:site_name" content="Calcuze">
    <meta property="og:locale" content="<?php echo $langAttribute; ?>">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:title" content="<?php echo $ogTitles[$lang]; ?>">
    <meta property="twitter:description" content="<?php echo $ogDescriptions[$lang]; ?>">

    <!-- Géolocalisation -->
    <meta name="geo.region" content="<?php echo $geoRegion; ?>">
    <meta name="geo.country" content="<?php echo $geoCountry; ?>">
    <meta name="geo.placename" content="<?php echo $geoPlacename; ?>">
    <meta name="language" content="<?php echo $langAttribute; ?>">

    <!-- Canonical URL -->
    <link rel="canonical" href="<?php echo $url; ?>">

    <!-- JSON-LD Structured Data -->
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "WebApplication",
            "name": "<?php echo $content['name']; ?>",
            "description": "<?php echo $content['description']; ?>",
            "url": "<?php echo $url; ?>",
            "applicationCategory": "UtilitiesApplication",
            "operatingSystem": "<?php echo $content['operatingSystem']; ?>",
            "inLanguage": "<?php echo $inLanguage; ?>",
            "availableLanguage": ["<?php echo implode('", "', array_keys($jsonldContent)); ?>"],
            "offers": {
                "@type": "Offer",
                "price": "0",
                "priceCurrency": "<?php echo $currency; ?>"
            },
            "featureList": [
                <?php echo '"' . implode('",
                "', $content['featureList']) . '"'; ?>
            ],
            "browserRequirements": "<?php echo $content['browserRequirements']; ?>"
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

