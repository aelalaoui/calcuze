# 🧮 Calcuze - Professional Calculator

A professional and multifunctional web calculator with multilingual and multi-currency support.

## 📋 Project Overview

**Calcuze** is an advanced web calculator application designed to offer multiple calculation modes (Normal, Scientific, Conversion, Economic) with an elegant and reactive user interface. The application supports multiple languages (French, English) and multiple currencies/countries.

### Key Features

- 🔢 **Multiple Modes** : Normal, Scientific, Unit Conversion, Economic Calculations
- 🌍 **Multilingual** : Support for French and English with automatic detection
- 🌐 **i18n System** : JSON-based internationalization with centralized translations
- 📱 **Responsive Design** : Interface optimized for mobile, tablet, and desktop
- 💾 **History** : Calculation history storage
- ⌨️ **Keyboard Support** : Full keyboard navigation
- 🎨 **Modern Design** : Elegant and intuitive interface

---

## 🌐 Internationalization (i18n)

Calcuze uses a modern JSON-based internationalization system for managing translations.

### Structure

```
calcuze/
├── langs/
│   ├── fr.json          # French translations
│   └── en.json          # English translations
├── includes/
│   └── i18n.php         # i18n helper functions
├── templates/
│   ├── index-template.php         # Template for /fr/ and /en/
│   └── index-template-root.php    # Template for root
├── fr/index.php         # French entry point (5 lines)
└── en/index.php         # English entry point (5 lines)
```

### Usage

```php
// Get a translation
$title = __('logo.title');

// Display a translation (HTML escaped)
_e('calculator.title');

// Loop through an array of translations
foreach(__('ads.sidebar_features') as $feature) {
    echo htmlspecialchars($feature);
}
```

### Adding a New Language

1. Create `langs/xx.json` (copy and translate from fr.json)
2. Create folder `xx/`
3. Create `xx/index.php`:
   ```php
   <?php
   $lang = 'xx';
   include __DIR__ . '/../templates/index-template.php';
   ```
4. Add 'xx' to `$validLanguages` in `includes/header.php`

### Documentation

- 📖 [Complete i18n Guide](docs/i18n-README.md)
- 🎯 [Demo Page](demo-i18n.php)
- 🧪 [Test File](test-i18n.php)

---

## 🛠️ Technology Stack

### Backend

| Technology | Version | Usage |
|-----------|---------|-------|
| **PHP** | 7.4+ | Server logic, language/country detection, dynamic rendering |
| **Apache** | 2.4+ | Web server (WAMP/LAMP) |
| **.htaccess** | - | URL rewriting and server configuration |

### Frontend

| Technology | Version | Usage |
|-----------|---------|-------|
| **HTML5** | 5 | Semantic structure |
| **CSS3** | 3 | Styles and animations |
| **JavaScript** | ES6+ | Interactive calculator logic |
| **Tailwind CSS** | 3.x | Utility-first CSS framework (via CDN) |
| **Font Awesome** | 6.4.0 | Icons and symbols (via CDN) |

### External Services

| Service | Usage |
|---------|-------|
| **Google Analytics** | Traffic tracking and analysis |
| **Google Tag Manager** | Tag and event management |
| **EmailJS** | Email sending service from contact.html |

---

## 📁 Project Structure

```
calcuze/
├── index.php                  # Main entry point
├── index.html                 # Redirect to index.php
├── contact.html               # Contact page
├── robots.txt                 # Configuration for crawlers
├── sitemap.xml                # Site map for SEO
├── .htaccess                  # URL rewriting
│
├── css/
│   └── styles.css             # Custom styles
│
├── scripts/
│   ├── common.js              # Common calculator logic
│   ├── normal.js              # Normal calculator mode
│   ├── scientific.js          # Scientific calculator mode
│   ├── conversion.js          # Unit conversion mode
│   ├── economic.js            # Economic calculations mode
│   └── contact.js             # Contact form logic
│
├── includes/
│   ├── header.php             # Common header (HTML <head> + meta + styles)
│   ├── country-selector.php   # Country/currency selector
│   ├── country-selector-styles.php  # Selector styles
│   └── country-selector-script.php  # Selector scripts
│
├── fr/
│   └── index.php              # French version
│
├── en/
│   └── index.php              # English version
│
└── .git/                       # Git repository
```

---

## 🔧 Technologies in Detail

### Backend (PHP)

- **Automatic Language Detection** : Based on URL parameters or HTTP `Accept-Language` header
- **Country/Currency Management** : Support for 6 French-speaking and 6 English-speaking countries
- **Dynamic Inclusion** : PHP includes for modularity
- **Centralized Templates** : Header and metadata managed from `includes/header.php`

### Frontend (JavaScript)

#### common.js
- Calculator state management
- Basic mathematical operations
- Calculation history
- Keyboard support
- Utility functions

#### Specialized Scripts
- **normal.js** : Basic calculator functionality
- **scientific.js** : Trigonometric, logarithmic functions, etc.
- **conversion.js** : Unit conversion (length, mass, temperature, etc.)
- **economic.js** : Financial and economic calculations
- **contact.js** : Contact form management with EmailJS

### CSS & Styling

- **Tailwind CSS** : Utility-first CSS framework for rapid development
- **Font Awesome** : Vector icon library
- **Custom CSS** : Specific animations and styles for buttons and calculator
- **Responsive Design** : Breakpoints for mobile, tablet, desktop

---

## 🌐 Multilingual and Multi-Regional

### Supported Languages
- 🇫🇷 **French** : France, Belgium, Switzerland, Canada, Luxembourg, Monaco
- 🇬🇧 **English** : United States, United Kingdom, Australia, Canada, New Zealand, Ireland

### Automatic Detection
```php
// Detection hierarchy:
1. URL parameter (?lang=fr or ?lang=en)
2. HTTP Accept-Language header
3. Default: English
```

---

## 📦 Dependencies

### CDN

```html
<!-- Tailwind CSS -->
<script src="https://cdn.tailwindcss.com"></script>

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<!-- EmailJS -->
<script src="https://cdn.jsdelivr.net/npm/@emailjs/browser@3/dist/email.min.js"></script>
```

**No local dependencies** - Everything is delivered via CDN for simplified installation and deployment.

---

## 🚀 Installation and Deployment

### Requirements
- PHP 7.4 or higher
- Apache server with mod_rewrite enabled
- Modern web browser

### Local Installation (WAMP/LAMP)

1. **Clone the repository**
   ```bash
   git clone <repository-url> calcuze
   cd calcuze
   ```

2. **Access via browser**
   ```
   http://localhost/calcuze/
   ```

3. **Verify structure**
   - All PHP files and folders should be in place
   - CSS and JS paths should be accessible

### Server Deployment (Hostinger/OVH)

1. Create a folder `/public_html/calcuze/` or `/public_html/`
2. Upload all files via FTP/SFTP
3. Ensure mod_rewrite is enabled
4. Test access at `https://yoursite.com/calcuze/`

**See** : `HOSTINGER_INSTRUCTIONS.md` for detailed instructions

---

## 🎯 Entry Points

| URL | Description |
|-----|-----------|
| `/index.php` | Main home (automatic language detection) |
| `/fr/index.php` | French version |
| `/en/index.php` | English version |
| `/contact.html` | Contact form |

---

## 📊 SEO Optimization

- Dynamic metadata by language and country
- JSON-LD structured data
- Open Graph and Twitter Cards
- XML Sitemap
- Configured Robots.txt
- SEO-friendly URLs with .htaccess

---

## 🔐 Security

- Validation of language and country parameters
- Client-side input sanitization
- EmailJS for secure email sending
- Security headers via .htaccess

---

## 👨‍💻 Development

### Modular Architecture

- Separation of backend (PHP) / frontend (JavaScript)
- Reusable components (header, country-selector)
- Specialized scripts per calculator mode
- Centralized and customizable styles

### Extensibility

- Add a new language: modify `header.php`
- Add a new mode: create `scripts/newmode.js`
- Add a country: update lists in `header.php`

---

**Last Updated** : November 8, 2025  
**Version** : 1.0.0

