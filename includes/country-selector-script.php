<script>
    // ============================================
    // Language Detector - Update lang attribute
    // ============================================
    (function() {
        const urlParams = new URLSearchParams(window.location.search);
        const country = urlParams.get('country');
        const htmlElement = document.getElementById('html-root');

        if (htmlElement && country) {
            const validCountries = ['FR', 'BE', 'CH', 'CA', 'LU', 'MC'];
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
    })();

    // ============================================
    // Country Selector Dropdown Script
    // ============================================
    const dropdownBtn = document.getElementById('dropdownBtn');
    const dropdownMenu = document.getElementById('dropdownMenu');
    const arrow = dropdownBtn.querySelector('.dropdown-arrow');

    dropdownBtn.addEventListener('click', () => {
        dropdownMenu.classList.toggle('active');
        arrow.classList.toggle('active');
    });

    // Close dropdown when a link is clicked
    dropdownMenu.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', () => {
            dropdownMenu.classList.remove('active');
            arrow.classList.remove('active');
        });
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', (e) => {
        if (!e.target.closest('.relative')) {
            dropdownMenu.classList.remove('active');
            arrow.classList.remove('active');
        }
    });
</script>