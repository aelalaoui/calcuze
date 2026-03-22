<?php
/**
 * sitemap.php — Sitemap XML dynamique avec hreflang pour Calcuze
 *
 * Génère automatiquement les entrées <url> pour chaque combinaison lang/country
 * avec les balises xhtml:link hreflang correctes (format sitemap hreflang).
 *
 * Accès : https://calcuze.com/sitemap.xml (via règle .htaccess) ou directement.
 */

header('Content-Type: application/xml; charset=UTF-8');

$baseUrl  = 'https://calcuze.com';
$langsDir = __DIR__ . '/langs/';
$today    = date('Y-m-d');

// ============================================================================
// CHARGEMENT DE TOUTES LES LANGUES ET LEURS PAYS
// ============================================================================
$allLanguages = [];
$langFiles = glob($langsDir . '*.json');

foreach ($langFiles as $file) {
    $langCode = basename($file, '.json');
    $data = json_decode(file_get_contents($file), true);
    if ($data && isset($data['validCountries'])) {
        $allLanguages[$langCode] = [
            'countries'         => $data['validCountries'],
            'countryMetadata'   => $data['countryMetadata'] ?? [],
            'currencyByCountry' => $data['currencyByCountry'] ?? [],
        ];
    }
}

// Ordre préféré pour l'affichage
$langOrder = ['en', 'fr', 'es', 'pt', 'it', 'de', 'sv', 'no', 'tr', 'ar'];
$orderedLangs = [];
foreach ($langOrder as $l) {
    if (isset($allLanguages[$l])) $orderedLangs[$l] = $allLanguages[$l];
}
foreach ($allLanguages as $l => $d) {
    if (!isset($orderedLangs[$l])) $orderedLangs[$l] = $d;
}

// ============================================================================
// CONSTRUCTION DE LA LISTE COMPLÈTE DES PAGES lang/country
// ============================================================================
$allPages = [];
foreach ($orderedLangs as $langCode => $langData) {
    foreach ($langData['countries'] as $countryCode) {
        $allPages[] = ['lang' => $langCode, 'country' => $countryCode];
    }
}

// ============================================================================
// PRIORITÉS PAR PAGE
// ============================================================================
$highPriorityCountries = [
    'en' => ['US', 'GB', 'AU', 'CA', 'IE', 'NZ'],
    'fr' => ['FR', 'BE', 'CH', 'CA'],
    'es' => ['ES', 'MX', 'AR', 'CO', 'CL'],
    'pt' => ['BR', 'PT'],
    'de' => ['DE', 'AT', 'CH'],
    'it' => ['IT'],
    'ar' => ['SA', 'AE', 'EG', 'MA'],
    'tr' => ['TR'],
    'sv' => ['SE'],
    'no' => ['NO'],
];

function getPagePriority(string $lang, string $country, array $high): string {
    // Page principale — priorité maximale
    if ($lang === 'en' && $country === 'US') return '1.0';
    // Pays majeurs par langue
    if (isset($high[$lang]) && in_array($country, $high[$lang])) return '0.9';
    // Tous les autres pays
    return '0.7';
}

// ============================================================================
// SORTIE XML
// ============================================================================
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:xhtml="http://www.w3.org/1999/xhtml">

    <!--
        SITEMAP DYNAMIQUE CALCUZE
        Généré le : <?php echo $today; ?>

        Pages : <?php echo count($allPages) + 1; ?> (<?php echo count($allPages); ?> lang/country + contact)
    -->

    <!-- ================================================================ -->
    <!-- PAGE CONTACT                                                       -->
    <!-- ================================================================ -->
    <url>
        <loc><?php echo $baseUrl; ?>/contact</loc>
        <lastmod>2025-11-15</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.5</priority>
        <xhtml:link rel="alternate" hreflang="x-default" href="<?php echo $baseUrl; ?>/contact" />
        <xhtml:link rel="alternate" hreflang="en" href="<?php echo $baseUrl; ?>/contact" />
        <xhtml:link rel="alternate" hreflang="fr" href="<?php echo $baseUrl; ?>/contact" />
    </url>

    <!-- ================================================================ -->
    <!-- PAGES LANG/COUNTRY — avec hreflang croisés entre pays de la même langue -->
    <!-- ================================================================ -->
    <?php
    foreach ($orderedLangs as $langCode => $langData) {
        $countries = $langData['countries'];

        echo "\n    <!-- Langue : " . strtoupper($langCode) . " (" . count($countries) . " pays) -->\n";

        foreach ($countries as $countryCode) {
            $loc      = $baseUrl . '/' . $langCode . '/' . $countryCode;
            $priority = getPagePriority($langCode, $countryCode, $highPriorityCountries);

            echo '    <url>' . "\n";
            echo '        <loc>' . $loc . '</loc>' . "\n";
            echo '        <lastmod>' . $today . '</lastmod>' . "\n";
            echo '        <changefreq>weekly</changefreq>' . "\n";
            echo '        <priority>' . $priority . '</priority>' . "\n";

            // Hreflang self-referencing (obligatoire)
            echo '        <xhtml:link rel="alternate" hreflang="' . $langCode . '-' . $countryCode . '" href="' . $loc . '" />' . "\n";

            // Hreflang croisés : tous les autres pays de la MÊME langue
            foreach ($countries as $otherCountry) {
                if ($otherCountry !== $countryCode) {
                    echo '        <xhtml:link rel="alternate" hreflang="' . $langCode . '-' . $otherCountry . '" href="' . $baseUrl . '/' . $langCode . '/' . $otherCountry . '" />' . "\n";
                }
            }

            // x-default → en/US
            echo '        <xhtml:link rel="alternate" hreflang="x-default" href="' . $baseUrl . '/en/US" />' . "\n";
            echo '    </url>' . "\n";
        }
    }
    ?>

</urlset>
