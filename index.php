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

// Initialize debug log
$debugLog = [];
$debugLogFile = __DIR__ . '/logs/language-detection-debug.log';
$debugLogDir = dirname($debugLogFile);

// Create logs directory if it doesn't exist
if (!is_dir($debugLogDir)) {
    @mkdir($debugLogDir, 0755, true);
}

// Include language detection helper
require_once __DIR__ . '/includes/language-detection.php';

// Log step 1: Start detection
$debugLog[] = '=== LANGUAGE DETECTION START ===';
$debugLog[] = 'Timestamp: ' . date('Y-m-d H:i:s');
$debugLog[] = 'Request URI: ' . $_SERVER['REQUEST_URI'];
$debugLog[] = 'Accept-Language Header: ' . ($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? 'NOT SET');

// Log step 2: Check cookies
$debugLog[] = '--- STEP 1: Check Existing Cookies ---';
$debugLog[] = 'Cookie calcuze_lang: ' . ($_COOKIE['calcuze_lang'] ?? 'NOT SET');
$debugLog[] = 'Cookie calcuze_country: ' . ($_COOKIE['calcuze_country'] ?? 'NOT SET');

// Log step 3: Check URL parameters
$debugLog[] = '--- STEP 2: Check URL Parameters ---';
$debugLog[] = 'GET lang parameter: ' . ($_GET['lang'] ?? 'NOT SET');
$debugLog[] = 'GET country parameter: ' . ($_GET['country'] ?? 'NOT SET');

// Detect language and country using intelligent hierarchy
$detection = LanguageDetection::detect($langsPath);
$lang = $detection['lang'];
$country = $detection['country'];
$detectionSource = $detection['source'] ?? 'unknown';

$debugLog[] = '--- STEP 3: Detection Result ---';
$debugLog[] = 'Detected Language: ' . $lang;
$debugLog[] = 'Detected Country: ' . $country;
$debugLog[] = 'Detection Source: ' . $detectionSource;

// Load translations to validate
$translations = LanguageDetection::loadLanguageMetadata($langsPath);
$debugLog[] = 'Loaded ' . count($translations) . ' languages from JSON files';

// Validate the detected language/country combination
$isValid = LanguageDetection::isValid($lang, $country, $translations);
$debugLog[] = 'Validation Result: ' . ($isValid ? 'VALID' : 'INVALID');

if (!$isValid) {
    // Fallback if validation fails
    $debugLog[] = 'Applying FALLBACK: en/US (validation failed)';
    $lang = 'en';
    $country = 'US';
}

// Check if we're at the root (/) without lang/country in the URL
// If so, redirect to the detected language/country
$requestUri = $_SERVER['REQUEST_URI'];
$requestPath = parse_url($requestUri, PHP_URL_PATH);

$debugLog[] = '--- STEP 4: Check for Root Request ---';
$debugLog[] = 'Raw Request Path: ' . $requestPath;

// Remove script directory from path if it exists
if (!empty($scriptDir)) {
    $debugLog[] = 'Script Directory: ' . $scriptDir;
    $requestPath = str_replace($scriptDir, '', $requestPath);
    $debugLog[] = 'Path after script dir removal: ' . $requestPath;
}

// Normalize path
$requestPath = trim($requestPath, '/');
$debugLog[] = 'Normalized Request Path: ' . ($requestPath ?: '(empty - ROOT REQUEST)');

// Check if this is a root request (empty or only query string)
$isRootRequest = empty($requestPath) || $requestPath === 'index.php';

$debugLog[] = '--- STEP 5: Redirect Decision ---';
$debugLog[] = 'Is Root Request: ' . ($isRootRequest ? 'YES' : 'NO');
$debugLog[] = 'Detection Source: ' . $detectionSource;
$debugLog[] = 'Should Redirect: ' . (($isRootRequest && $detectionSource !== 'url') ? 'YES' : 'NO');

if ($isRootRequest && $detectionSource !== 'url') {
    // Redirect to detected language/country
    $redirectUrl = $baseUrl . $lang . '/' . $country;

    $debugLog[] = '--- STEP 6: PERFORMING REDIRECT ---';
    $debugLog[] = 'Redirect Target URL: ' . $redirectUrl;
    $debugLog[] = 'HTTP Status: 301 Moved Permanently';

    // Set cookie to remember the choice
    LanguageDetection::setCookie($lang, $country, 365,
        isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on'
    );
    $debugLog[] = 'Cookies Set: calcuze_lang=' . $lang . ', calcuze_country=' . $country;

    // Log the debug information before redirect
    @file_put_contents($debugLogFile, implode("\n", $debugLog) . "\n\nREDIRECT EXECUTED\n" . str_repeat("=", 50) . "\n\n", FILE_APPEND);

    // Permanent redirect (301) for SEO
    header('HTTP/1.1 301 Moved Permanently');
    header('Location: ' . $redirectUrl);
    exit;
} else {
    $debugLog[] = '--- STEP 6: NO REDIRECT NEEDED ---';
    $debugLog[] = 'Reason: ' . ((!$isRootRequest) ? 'Not a root request' : 'Detection source is URL parameter');
    $debugLog[] = 'Continuing with lang=' . $lang . ', country=' . $country;

    // Log the debug information
    @file_put_contents($debugLogFile, implode("\n", $debugLog) . "\n" . str_repeat("=", 50) . "\n\n", FILE_APPEND);
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
