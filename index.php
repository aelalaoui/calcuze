<?php
// Enable error reporting for debugging (REMOVE IN PRODUCTION AFTER FIXING)
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

/**
 * Root index.php with Intelligent Language Detection & Redirection
 *
 * Handles all languages with automatic redirection based on:
 * 1. User's browser language (Accept-Language header)
 * 2. Cookie/Session (if user has previously chosen a language)
 * 3. URL parameters (?lang=xx&country=XX)
 * 4. Country extraction from Accept-Language (e.g., fr-FR, en-GB)
 * 5. Fallback: English (US)
 *
 * NO IP geolocation - ONLY browser data
 * NO external APIs - Pure PHP solution
 */

// Detect base URL dynamically (works both in local /calcuze/ and production root)
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$scriptName = $_SERVER['SCRIPT_NAME'];

// Get directory of the script (empty string if at root, or /calcuze if in subdirectory)
$scriptDir = dirname($scriptName);
if ($scriptDir === '/' || $scriptDir === '\\') {
    $scriptDir = '';
}

$baseUrl = $protocol . '://' . $host . $scriptDir;
if (substr($baseUrl, -1) !== '/') {
    $baseUrl .= '/';
}

// Configuration with absolute paths from base URL
$cssPath = $baseUrl . 'css/styles.css';
$includesPath = __DIR__ . '/includes/';
$scriptsPath = $baseUrl . 'scripts/';
$langsPath = __DIR__ . '/langs/';

// ============================================================================
// INTELLIGENT LANGUAGE DETECTION & REDIRECTION
// ============================================================================

// Include language detection helper
require_once __DIR__ . '/includes/language-detection.php';

// Detect language and country using intelligent hierarchy
$detection = LanguageDetection::detect($langsPath);
$lang = $detection['lang'];
$country = $detection['country'];
$detectionSource = $detection['source'] ?? 'unknown';

// Load translations to validate
$translations = LanguageDetection::loadLanguageMetadata($langsPath);

// Validate the detected language/country combination
if (!LanguageDetection::isValid($lang, $country, $translations)) {
    // Fallback if validation fails
    $lang = 'en';
    $country = 'US';
}

// Check if we're at the root (/) without lang/country in the URL
// If so, redirect to the detected language/country
$requestUri = $_SERVER['REQUEST_URI'];
$requestPath = parse_url($requestUri, PHP_URL_PATH);

// Remove script directory from path if it exists
if (!empty($scriptDir)) {
    $requestPath = str_replace($scriptDir, '', $requestPath);
}

// Normalize path
$requestPath = trim($requestPath, '/');

// Check if this is a root request (empty or only query string)
$isRootRequest = empty($requestPath) || $requestPath === 'index.php';

if ($isRootRequest && $detectionSource !== 'url') {
    // Redirect to detected language/country
    $redirectUrl = $baseUrl . $lang . '/' . $country;

    // Set cookie to remember the choice
    LanguageDetection::setCookie($lang, $country, 365,
        isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on'
    );

    // Permanent redirect (301) for SEO
    header('HTTP/1.1 301 Moved Permanently');
    header('Location: ' . $redirectUrl);
    exit;
}

// ============================================================================
// NORMAL FLOW: Language/Country are set, continue with content
// ============================================================================

// Include i18n helper
require_once __DIR__ . '/includes/i18n.php';

// Initialize i18n
i18n::init($lang, $langsPath);

// Set cookie to remember this language/country choice
LanguageDetection::setCookie($lang, $country, 365,
    isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on'
);

// Include the template
$templatePath = __DIR__ . '/templates/index-template.php';
include $templatePath;
