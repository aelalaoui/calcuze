<?php
/**
 * i18n.php - Internationalization Helper
 * Loads and provides translations from JSON language files
 */

class i18n {
    private static $translations = null;
    private static $currentLang = null;
    private static $fallbackLang = 'en';

    /**
     * Initialize the i18n system
     * @param string $lang Language code (e.g., 'fr', 'en')
     * @param string $langsPath Path to the langs directory
     */
    public static function init($lang = 'en', $langsPath = __DIR__ . '/../langs/') {
        self::$currentLang = $lang;
        self::loadTranslations($lang, $langsPath);
    }

    /**
     * Load translations from JSON file
     */
    private static function loadTranslations($lang, $langsPath) {
        $langFile = $langsPath . $lang . '.json';

        if (!file_exists($langFile)) {
            // Fallback to English if language file doesn't exist
            $langFile = $langsPath . self::$fallbackLang . '.json';
            self::$currentLang = self::$fallbackLang;
        }

        $jsonContent = file_get_contents($langFile);
        self::$translations = json_decode($jsonContent, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Error parsing language file: ' . json_last_error_msg());
        }
    }

    /**
     * Get a translation by key path (e.g., 'logo.title')
     * @param string $keyPath Dot-separated path to translation
     * @param mixed $default Default value if translation not found
     * @return mixed Translation value
     */
    public static function get($keyPath, $default = '') {
        if (self::$translations === null) {
            self::init();
        }

        $keys = explode('.', $keyPath);
        $value = self::$translations;

        foreach ($keys as $key) {
            if (isset($value[$key])) {
                $value = $value[$key];
            } else {
                return $default;
            }
        }

        return $value;
    }

    /**
     * Alias for get() - shorter syntax
     */
    public static function t($keyPath, $default = '') {
        return self::get($keyPath, $default);
    }

    /**
     * Get all translations
     */
    public static function getAll() {
        if (self::$translations === null) {
            self::init();
        }
        return self::$translations;
    }

    /**
     * Get current language
     */
    public static function getCurrentLang() {
        return self::$currentLang;
    }

    /**
     * Echo a translation (convenience method)
     */
    public static function e($keyPath, $default = '') {
        echo htmlspecialchars(self::get($keyPath, $default), ENT_QUOTES, 'UTF-8');
    }
}

/**
 * Helper function for quick access to translations
 * Usage: __('logo.title')
 */
function __($keyPath, $default = '') {
    return i18n::get($keyPath, $default);
}

/**
 * Helper function to echo translation
 * Usage: _e('logo.title')
 */
function _e($keyPath, $default = '') {
    i18n::e($keyPath, $default);
}

