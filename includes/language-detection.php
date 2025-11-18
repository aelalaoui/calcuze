<?php
/**
 * Language Detection Helper
 *
 * Détection intelligente de la langue du navigateur et du pays
 * Parse Accept-Language header avec support de la qualité
 *
 * Hiérarchie de priorité :
 * 1. Cookie/Session utilisateur (choix précédent)
 * 2. Paramètres URL (?lang=xx&country=XX)
 * 3. Accept-Language header du navigateur
 * 4. Pays par défaut pour la langue détectée
 * 5. Fallback: en/US
 */

class LanguageDetection {

    // Mapping langue → pays par défaut
    private static $defaultCountryByLanguage = [
        'fr' => 'FR',
        'en' => 'US',
        'es' => 'ES',
        'pt' => 'PT',
        'it' => 'IT',
        'de' => 'DE',
        'sv' => 'SE',
        'no' => 'NO',
        'tr' => 'TR',
        'ar' => 'SA'
    ];

    // Cache des langues supportées et leurs pays
    private static $languageCache = null;

    /**
     * Parse l'en-tête Accept-Language et retourne un tableau trié par qualité
     *
     * Format de l'en-tête:
     * Accept-Language: fr-FR,fr;q=0.9,en-US;q=0.8,en;q=0.7
     *
     * @param string $acceptLanguageHeader
     * @return array Format: [
     *     0 => ['lang' => 'fr', 'country' => 'FR', 'quality' => 1.0],
     *     1 => ['lang' => 'fr', 'country' => null, 'quality' => 0.9],
     *     ...
     * ]
     */
    public static function parseAcceptLanguage($acceptLanguageHeader) {
        $languages = [];

        // Nettoyer et diviser par virgule
        $parts = array_filter(array_map('trim', explode(',', $acceptLanguageHeader)));

        foreach ($parts as $part) {
            // Diviser langue et qualité
            $langParts = array_map('trim', explode(';', $part));
            $lang = $langParts[0];
            $quality = 1.0;

            if (isset($langParts[1]) && strpos($langParts[1], 'q=') === 0) {
                $quality = (float)substr($langParts[1], 2);
            }

            // Parser la langue (peut être "fr-FR" ou simplement "fr")
            $langCode = null;
            $country = null;

            if (strpos($lang, '-') !== false) {
                list($langCode, $country) = explode('-', $lang, 2);
                $country = strtoupper($country);
            } else {
                $langCode = $lang;
            }

            $langCode = strtolower($langCode);

            $languages[] = [
                'lang' => $langCode,
                'country' => $country,
                'quality' => $quality
            ];
        }

        // Trier par qualité (décroissant)
        usort($languages, function($a, $b) {
            return $b['quality'] - $a['quality'];
        });

        return $languages;
    }

    /**
     * Charge les métadonnées des langues supportées depuis les fichiers JSON
     *
     * @param string $langsPath Chemin vers le dossier langs/
     * @return array Format: [
     *     'fr' => ['validCountries' => [...], 'meta' => [...], ...],
     *     'en' => [...],
     *     ...
     * ]
     */
    public static function loadLanguageMetadata($langsPath) {
        if (self::$languageCache !== null) {
            return self::$languageCache;
        }

        $translations = [];
        $languages = ['fr', 'en', 'es', 'pt', 'it', 'de', 'sv', 'no', 'tr', 'ar'];

        foreach ($languages as $langCode) {
            $langFile = rtrim($langsPath, '/\\') . '/' . $langCode . '.json';
            if (file_exists($langFile)) {
                $content = file_get_contents($langFile);
                $decoded = json_decode($content, true);
                if ($decoded) {
                    $translations[$langCode] = $decoded;
                }
            }
        }

        self::$languageCache = $translations;
        return $translations;
    }

    /**
     * Récupère les pays valides pour une langue donnée
     *
     * @param string $lang Code de langue (ex: 'fr')
     * @param array $translations Métadonnées des langues (de loadLanguageMetadata)
     * @return array Liste des codes pays valides
     */
    public static function getValidCountriesForLanguage($lang, $translations) {
        if (!isset($translations[$lang])) {
            return [];
        }

        return $translations[$lang]['validCountries'] ?? [];
    }

    /**
     * Récupère le pays par défaut pour une langue
     *
     * @param string $lang Code de langue
     * @return string Code de pays (ex: 'FR', 'US')
     */
    public static function getDefaultCountryForLanguage($lang) {
        $lang = strtolower($lang);
        return self::$defaultCountryByLanguage[$lang] ?? 'US';
    }

    /**
     * Trouve la meilleure correspondance entre les langues préférées du navigateur
     * et les langues supportées de l'application
     *
     * @param array $preferredLanguages Résultat de parseAcceptLanguage()
     * @param array $translations Métadonnées des langues
     * @return array Format: ['lang' => 'fr', 'country' => 'FR']
     */
    public static function selectBestMatch($preferredLanguages, $translations) {
        $supportedLanguages = array_keys($translations);

        foreach ($preferredLanguages as $pref) {
            $lang = $pref['lang'];
            $country = $pref['country'];

            // Vérifier si la langue est supportée
            if (!in_array($lang, $supportedLanguages)) {
                continue;
            }

            $validCountries = self::getValidCountriesForLanguage($lang, $translations);

            // Si un pays spécifique est demandé
            if ($country) {
                // Vérifier si ce pays est valide pour cette langue
                if (in_array($country, $validCountries)) {
                    return [
                        'lang' => $lang,
                        'country' => $country
                    ];
                }
            }

            // Sinon, utiliser le pays par défaut pour cette langue
            $defaultCountry = self::getDefaultCountryForLanguage($lang);

            // Vérifier que le pays par défaut est valide pour cette langue
            if (in_array($defaultCountry, $validCountries)) {
                return [
                    'lang' => $lang,
                    'country' => $defaultCountry
                ];
            }

            // Si le pays par défaut n'est pas valide, utiliser le premier disponible
            if (!empty($validCountries)) {
                return [
                    'lang' => $lang,
                    'country' => $validCountries[0]
                ];
            }
        }

        // Fallback: English (US)
        return [
            'lang' => 'en',
            'country' => 'US'
        ];
    }

    /**
     * Détecte automatiquement la langue et le pays de l'utilisateur
     *
     * Hiérarchie :
     * 1. Paramètres URL (?lang=xx&country=XX) - PRIORITÉ ABSOLUE
     * 2. Cookie/Session utilisateur (SAUF à la racine / - permet re-détection)
     * 3. Accept-Language header du navigateur
     * 4. Fallback: en/US
     *
     * Note: À la racine (/), le cookie est ignoré pour permettre une redirection
     *       automatique basée sur Accept-Language actuel du navigateur.
     *
     * @param string $langsPath Chemin vers le dossier langs/
     * @param bool $ignoreRootCookie Si true, ignore le cookie à la racine (défaut: true)
     * @return array Format: ['lang' => 'fr', 'country' => 'FR']
     */
    public static function detect($langsPath, $ignoreRootCookie = true) {
        $requestUri = $_SERVER['REQUEST_URI'];
        $requestPath = parse_url($requestUri, PHP_URL_PATH);
        $scriptName = $_SERVER['SCRIPT_NAME'];
        $scriptDir = dirname($scriptName);

        if ($scriptDir === '/' || $scriptDir === '\\') {
            $scriptDir = '';
        }

        if (!empty($scriptDir)) {
            $requestPath = str_replace($scriptDir, '', $requestPath);
        }

        $requestPath = trim($requestPath, '/');
        $isRootRequest = empty($requestPath) || $requestPath === 'index.php';

        // 1. Vérifier paramètres URL (PRIORITÉ ABSOLUE)
        if (isset($_GET['lang']) && isset($_GET['country'])) {
            $lang = strtolower($_GET['lang']);
            $country = strtoupper($_GET['country']);

            $translations = self::loadLanguageMetadata($langsPath);
            $validCountries = self::getValidCountriesForLanguage($lang, $translations);

            if (in_array($lang, array_keys($translations)) && in_array($country, $validCountries)) {
                return [
                    'lang' => $lang,
                    'country' => $country,
                    'source' => 'url'
                ];
            }
        }

        // 2. Vérifier cookie (SAUF à la racine si ignoreRootCookie = true)
        // À la racine, on veut re-détecter la langue actuelle du navigateur
        $cookieName = 'calcuze_lang';
        $countryCookieName = 'calcuze_country';

        if (isset($_COOKIE[$cookieName]) && isset($_COOKIE[$countryCookieName])) {
            // Si on est à la racine ET ignoreRootCookie est true, sauter le cookie
            if ($isRootRequest && $ignoreRootCookie) {
                // Ignorer le cookie à la racine - re-détecter avec Accept-Language
            } else {
                // Utiliser le cookie (on est sur une page /[lang]/[country])
                $lang = strtolower($_COOKIE[$cookieName]);
                $country = strtoupper($_COOKIE[$countryCookieName]);

                $translations = self::loadLanguageMetadata($langsPath);
                $validCountries = self::getValidCountriesForLanguage($lang, $translations);

                if (in_array($lang, array_keys($translations)) && in_array($country, $validCountries)) {
                    return [
                        'lang' => $lang,
                        'country' => $country,
                        'source' => 'cookie'
                    ];
                }
            }
        }

        // 3. Parser Accept-Language du navigateur
        $translations = self::loadLanguageMetadata($langsPath);

        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $preferredLanguages = self::parseAcceptLanguage($_SERVER['HTTP_ACCEPT_LANGUAGE']);
            $match = self::selectBestMatch($preferredLanguages, $translations);

            return array_merge($match, ['source' => 'accept-language']);
        }

        // 4. Fallback
        return [
            'lang' => 'en',
            'country' => 'US',
            'source' => 'fallback'
        ];
    }

    /**
     * Valide une paire langue/pays
     *
     * @param string $lang Code de langue
     * @param string $country Code de pays
     * @param array $translations Métadonnées des langues
     * @return bool
     */
    public static function isValid($lang, $country, $translations) {
        $lang = strtolower($lang);
        $country = strtoupper($country);

        if (!isset($translations[$lang])) {
            return false;
        }

        $validCountries = $translations[$lang]['validCountries'] ?? [];
        return in_array($country, $validCountries);
    }

    /**
     * Définit les cookies de langue et de pays
     *
     * @param string $lang Code de langue
     * @param string $country Code de pays
     * @param int $daysExpire Nombre de jours avant expiration du cookie (default: 365 = 1 an)
     * @param bool $secure Si true, utilise HttpOnly (pour HTTPS)
     */
    public static function setCookie($lang, $country, $daysExpire = 365, $secure = true) {
        $lang = strtolower($lang);
        $country = strtoupper($country);

        $expire = time() + ($daysExpire * 86400);

        // Options du cookie
        $options = [
            'expires' => $expire,
            'path' => '/',
            'httponly' => $secure,
            'samesite' => 'Lax'
        ];

        // Pour HTTPS en production
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
            $options['secure'] = true;
        }

        setcookie('calcuze_lang', $lang, $options);
        setcookie('calcuze_country', $country, $options);
    }
}

