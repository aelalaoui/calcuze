<script>
    // ============================================
    // Language Detector - Update lang attribute
    // ============================================
    (function() {
        const urlParams = new URLSearchParams(window.location.search);
        const country = urlParams.get('country');
        const htmlElement = document.getElementById('html-root');

        // Load validCountries from fr.json
        fetch('/langs/fr.json')
            .then(response => response.json())
            .then(data => {
                const validCountries = data.validCountries;

                if (htmlElement && country) {
                    if (validCountries.includes(country.toUpperCase())) {
                        htmlElement.lang = `fr-${country.toUpperCase()}`;
                        sessionStorage.setItem('selectedCountry', country.toUpperCase());
                    }
                } else if (htmlElement) {
                    const storedCountry = sessionStorage.getItem('selectedCountry');
                    if (storedCountry) {
                        htmlElement.lang = `fr-${storedCountry}`;
                    }
                }
            })
            .catch(error => {
                console.warn('Erreur lors du chargement de fr.json:', error);
                // Fallback en cas d'erreur
                const validCountries = ['FR', 'BE', 'CH', 'CA', 'LU', 'MC'];
                if (htmlElement && country) {
                    if (validCountries.includes(country.toUpperCase())) {
                        htmlElement.lang = `fr-${country.toUpperCase()}`;
                        sessionStorage.setItem('selectedCountry', country.toUpperCase());
                    }
                }
            });
    })();

    // ============================================
    // Country Selector Dropdown Script
    // ============================================
    const dropdownBtn = document.getElementById('dropdownBtn');
    const dropdownMenu = document.getElementById('dropdownMenu');
    const countriesMenu = document.getElementById('countriesMenu');
    const arrow = dropdownBtn.querySelector('.dropdown-arrow');
    const selectedLangSpan = document.getElementById('selectedLang');

    // Open/Close language dropdown
    dropdownBtn.addEventListener('click', () => {
        dropdownMenu.classList.toggle('active');
        countriesMenu.classList.add('hidden');
        countriesMenu.classList.remove('active');
        arrow.classList.toggle('active');
    });

    // Handle language selection
    document.querySelectorAll('.lang-option').forEach(langOption => {
        langOption.addEventListener('click', (e) => {
            e.stopPropagation();
            const selectedLang = langOption.dataset.lang;
            const langName = langOption.textContent.trim();

            // Update button text
            selectedLangSpan.textContent = langName;

            // Hide language menu and show countries menu
            dropdownMenu.classList.remove('active');
            countriesMenu.classList.remove('hidden');
            countriesMenu.classList.add('active');

            // Show countries for selected language
            document.querySelectorAll('.countries-group').forEach(group => {
                group.classList.add('hidden');
            });
            document.querySelector(`.countries-group[data-lang="${selectedLang}"]`).classList.remove('hidden');
        });
    });

    // Handle back button in countries menu
    document.querySelectorAll('.back-btn').forEach(backBtn => {
        backBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            countriesMenu.classList.add('hidden');
            countriesMenu.classList.remove('active');
            dropdownMenu.classList.add('active');
            selectedLangSpan.textContent = 'Select Language';
        });
    });

    // Close dropdown when a country link is clicked
    countriesMenu.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', () => {
            dropdownBtn.classList.remove('active');
            arrow.classList.remove('active');
            countriesMenu.classList.remove('active');
            countriesMenu.classList.add('hidden');
            dropdownMenu.classList.remove('active');
            selectedLangSpan.textContent = 'Select Language';
        });
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', (e) => {
        if (!e.target.closest('.relative')) {
            dropdownMenu.classList.remove('active');
            countriesMenu.classList.remove('active');
            countriesMenu.classList.add('hidden');
            arrow.classList.remove('active');
            selectedLangSpan.textContent = 'Select Language';
        }
    });
</script>