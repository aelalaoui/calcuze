<?php
// Enable error reporting for debugging (REMOVE IN PRODUCTION AFTER FIXING)
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

// Root index.php with i18n support - handles all languages

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

// Include i18n helper
require_once __DIR__ . '/includes/i18n.php';

// Detect language from URL parameters or browser
if (!isset($lang)) {
    $lang = isset($_GET['lang']) ? strtolower($_GET['lang']) : null;

    // If no lang parameter, try to detect from browser
    if (!$lang && isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
        $browserLang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
        $lang = in_array($browserLang, ['fr', 'en', 'es', 'pt', 'it', 'de', 'sv']) ? $browserLang : 'en';
    } else {
        $lang = $lang ?? 'en';
    }
}

// Validate language
$validLanguages = ['fr', 'en', 'es', 'pt', 'it', 'de', 'sv'];
if (!in_array($lang, $validLanguages)) {
    $lang = 'en';
}

// Initialize i18n
i18n::init($lang, __DIR__ . '/langs/');

// Include the template
$templatePath = __DIR__ . '/templates/index-template.php';
include $templatePath;
