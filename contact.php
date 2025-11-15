<?php
// Include i18n helper
require_once __DIR__ . '/includes/i18n.php';

// Detect language from URL parameters or browser
$lang = isset($_GET['lang']) ? strtolower($_GET['lang']) : null;

// If no lang parameter, try to detect from browser
if (!$lang && isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
    $browserLang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
    $lang = in_array($browserLang, ['fr', 'en', 'es', 'pt', 'it']) ? $browserLang : 'en';
} else {
    $lang = $lang ?? 'en';
}

// Validate language
$validLanguages = ['fr', 'en', 'es', 'pt', 'it'];
if (!in_array($lang, $validLanguages)) {
    $lang = 'en';
}

// Detect base URL dynamically
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$scriptName = $_SERVER['SCRIPT_NAME'];
$scriptDir = dirname($scriptName);
if ($scriptDir === '/' || $scriptDir === '\\') {
    $scriptDir = '';
}
$baseUrl = $protocol . '://' . $host . $scriptDir;
if (substr($baseUrl, -1) !== '/') {
    $baseUrl .= '/';
}

// Configuration
$cssPath = $baseUrl . 'css/styles.css';
$includesPath = __DIR__ . '/includes/';
$scriptsPath = $baseUrl . 'scripts/';

// Initialize i18n
i18n::init($lang, __DIR__ . '/langs/');

// Build canonical URL
$canonicalUrl = 'https://calcuze.com/contact';
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Calcuze Calculator</title>
    <meta name="description" content="Contact Calcuze - Get in touch with our team for support, partnerships, or inquiries about our online calculator.">
    <meta name="robots" content="index, follow">

    <!-- Canonical URL -->
    <link rel="canonical" href="<?php echo $canonicalUrl; ?>">

    <!-- Hreflang Alternate URLs -->
    <link rel="alternate" hreflang="en" href="https://calcuze.com/contact?lang=en" />
    <link rel="alternate" hreflang="fr" href="https://calcuze.com/contact?lang=fr" />
    <link rel="alternate" hreflang="x-default" href="https://calcuze.com/contact" />

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo $cssPath; ?>">
    <!-- EmailJS SDK -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/@emailjs/browser@3/dist/email.min.js"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <!-- Main Container -->
    <div class="min-h-screen grid grid-rows-[auto_1fr_auto] grid-cols-[300px_1fr_300px] gap-4 p-4">

        <!-- Top Header with Logo and Navigation -->
        <div class="col-span-3 grid grid-cols-[300px_1fr] gap-4">
            <!-- Logo Section -->
            <div class="logo-section rounded-xl p-4 flex items-center justify-center">
                <div class="text-white text-center">
                    <i class="fas fa-calculator text-4xl mb-2"></i>
                    <h1 class="text-xl font-bold">Calcuze</h1>
                    <p class="text-sm opacity-80">Professional Calculator</p>
                </div>
            </div>

            <!-- Navigation Header -->
            <div class="bg-white rounded-xl p-6 flex items-center justify-between shadow-lg">
                <div class="flex items-center space-x-4">
                    <a href="index.php" class="flex items-center text-blue-600 hover:text-blue-800 transition">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Calculator
                    </a>
                </div>
                <div class="text-center">
                    <h1 class="text-2xl font-bold text-gray-800">Contact Us</h1>
                    <p class="text-gray-600">Get in touch with our team</p>
                </div>
                <div class="w-32"></div> <!-- Spacer for centering -->
            </div>
        </div>

        <!-- Left Sidebar - Contact Info -->
        <div class="ad-banner rounded-xl p-4 flex flex-col justify-center">
            <div class="text-white text-center">
                <i class="fas fa-envelope text-4xl mb-4"></i>
                <h3 class="text-lg font-bold mb-4">Get In Touch</h3>
                <div class="space-y-3 text-sm opacity-90">
                    <div class="flex items-center justify-center">
                        <i class="fas fa-envelope mr-2"></i>
                        <span>adil.ksjo@gmail.com</span>
                    </div>
                    <div class="flex items-center justify-center">
                        <i class="fas fa-clock mr-2"></i>
                        <span>24/7 Support</span>
                    </div>
                    <div class="flex items-center justify-center">
                        <i class="fas fa-reply mr-2"></i>
                        <span>Quick Response</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Form Section (Main Content) -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <div class="bg-blue-600 p-4 text-white">
                <h2 class="text-xl font-bold text-center">Contact Form</h2>
                <p class="text-center text-sm opacity-90 mt-1">Send us a message and we'll get back to you</p>
            </div>

            <div class="p-6">
                <form id="contact-form" class="space-y-6">
                    <!-- Name Field -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-user mr-2 text-blue-600"></i>
                            Full Name *
                        </label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                            placeholder="Enter your full name"
                        >
                    </div>

                    <!-- Email Field -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-envelope mr-2 text-blue-600"></i>
                            Email Address *
                        </label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                            placeholder="Enter your email address"
                        >
                    </div>

                    <!-- Subject Field -->
                    <div>
                        <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-tag mr-2 text-blue-600"></i>
                            Subject *
                        </label>
                        <select
                            id="subject"
                            name="subject"
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                        >
                            <option value="">Select a subject</option>
                            <option value="General Inquiry">General Inquiry</option>
                            <option value="Bug Report">Bug Report</option>
                            <option value="Feature Request">Feature Request</option>
                            <option value="Technical Support">Technical Support</option>
                            <option value="Business Partnership">Business Partnership</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <!-- Message Field -->
                    <div>
                        <label for="message" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-comment mr-2 text-blue-600"></i>
                            Message *
                        </label>
                        <textarea
                            id="message"
                            name="message"
                            required
                            rows="6"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition resize-none"
                            placeholder="Enter your message here..."
                        ></textarea>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex flex-col sm:flex-row gap-4">
                        <button
                            type="submit"
                            id="submit-btn"
                            class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition flex items-center justify-center"
                        >
                            <i class="fas fa-paper-plane mr-2"></i>
                            Send Message
                        </button>
                        <button
                            type="reset"
                            class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-700 font-semibold py-3 px-6 rounded-lg transition"
                        >
                            <i class="fas fa-redo mr-2"></i>
                            Reset Form
                        </button>
                    </div>

                    <!-- Status Messages -->
                    <div id="form-status" class="hidden"></div>
                </form>
            </div>
        </div>

        <!-- Right Sidebar Ad -->
        <div class="ad-banner rounded-xl p-4 flex flex-col items-center justify-center">
            <div class="text-white text-center">
                <i class="fas fa-question-circle text-3xl mb-3"></i>
                <h3 class="text-lg font-bold mb-2">Need Help?</h3>
                <p class="text-sm mb-4 opacity-90">Check out our FAQ section or send us a message</p>
                <div class="space-y-2 text-xs opacity-75">
                    <p>• Quick responses</p>
                    <p>• Expert support</p>
                    <p>• 24/7 availability</p>
                </div>
            </div>
        </div>

        <!-- Bottom Footer -->
        <div class="col-span-3 text-center text-gray-600 text-sm py-4">
            <p>&copy; 2025 Calcuze - All rights reserved</p>
        </div>
    </div>

    <script src="<?php echo $scriptsPath; ?>contact.js"></script>
</body>
</html>

