<script>
    // Country Selector Dropdown Script
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

