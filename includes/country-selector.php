<!-- Country Selector (Top Middle) -->
<div class="w-full mb-6">
    <div class="relative">
        <!-- Language Selector Button -->
        <button id="dropdownBtn" class="w-full p-3 bg-gray-50 hover:bg-blue-100 rounded-lg text-sm font-medium text-gray-700 hover:text-blue-600 transition flex items-center justify-between border border-gray-200">
            <span id="selectedLang">Select Language</span>
            <span class="dropdown-arrow">▼</span>
        </button>

        <!-- Languages Dropdown Menu -->
        <div id="dropdownMenu" class="dropdown-menu absolute top-full left-0 mt-2 bg-white border border-gray-200 rounded-lg shadow-lg z-10 flex flex-col min-w-max max-h-96 overflow-y-auto">
            <?php
                // Charger tous les fichiers JSON
                $langsDir = __DIR__ . '/../langs/';
                $langFiles = glob($langsDir . '*.json');

                $languages = [];

                foreach ($langFiles as $langFile) {
                    $langData = json_decode(file_get_contents($langFile), true);
                    $langCode = basename($langFile, '.json');

                    // Créer un tableau avec les informations de chaque langue
                    $languages[$langCode] = [
                        'code' => $langCode,
                        'countries' => $langData['validCountries'] ?? [],
                        'countryMetadata' => $langData['countryMetadata'] ?? []
                    ];
                }

                // Trier les langues
                ksort($languages);

                // Afficher chaque langue
                foreach ($languages as $langCode => $langInfo) {
                    $langName = strtoupper($langCode);
                    echo '<div class="lang-option px-4 py-2 text-gray-700 hover:bg-blue-100 hover:text-blue-600 transition text-sm font-medium cursor-pointer" data-lang="' . htmlspecialchars($langCode) . '">';
                    echo htmlspecialchars($langName);
                    echo '</div>';
                }
            ?>
        </div>

        <!-- Countries Dropdown Menu (shown after language selection) -->
        <div id="countriesMenu" class="dropdown-menu absolute top-full left-0 mt-2 bg-white border border-gray-200 rounded-lg shadow-lg z-10 flex flex-col min-w-max max-h-96 overflow-y-auto hidden">
            <?php
                // Afficher les pays pour chaque langue (caché par défaut)
                $langsDir = __DIR__ . '/../langs/';
                $langFiles = glob($langsDir . '*.json');

                foreach ($langFiles as $langFile) {
                    $langData = json_decode(file_get_contents($langFile), true);
                    $langCode = basename($langFile, '.json');

                    if (isset($langData['countryMetadata']) && isset($langData['validCountries'])) {
                        echo '<div class="countries-group hidden" data-lang="' . htmlspecialchars($langCode) . '">';

                        // Bouton retour
                        echo '<button class="back-btn w-full px-4 py-2 text-left text-gray-500 text-xs font-bold uppercase tracking-wide bg-gray-50 hover:bg-gray-100 border-b border-gray-200">';
                        echo '← Back to Languages';
                        echo '</button>';

                        // Afficher chaque pays
                        foreach ($langData['validCountries'] as $countryCode) {
                            if (isset($langData['countryMetadata'][$countryCode])) {
                                $country = $langData['countryMetadata'][$countryCode];
                                $countryName = $country['name'] ?? $countryCode;
                                $url = 'https://calcuze.com/' . $langCode . '/' . $countryCode;

                                echo '<a href="' . htmlspecialchars($url) . '" class="px-4 py-2 text-gray-700 hover:bg-blue-100 hover:text-blue-600 transition text-sm font-medium block">';
                                echo htmlspecialchars($countryName . ' (' . $countryCode . ')');
                                echo '</a>';
                            }
                        }

                        echo '</div>';
                    }
                }
            ?>
        </div>
    </div>
</div>

