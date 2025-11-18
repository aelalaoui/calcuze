<style>
    .dropdown-menu {
        display: none !important;
        opacity: 0;
        visibility: hidden;
        transform: translateY(-10px);
        transition: all 0.3s ease-in-out;
        width: 100%;
        min-width: 250px;
    }

    .dropdown-menu.active {
        display: flex !important;
        flex-direction: column;
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
        max-height: 500px;
    }

    .dropdown-menu.hidden {
        display: none !important;
    }

    .lang-option {
        position: relative;
        padding-right: 24px;
    }

    .lang-option::after {
        content: '›';
        position: absolute;
        right: 8px;
        font-size: 20px;
        color: #9CA3AF;
        transition: color 0.3s ease;
    }

    .lang-option:hover::after {
        color: #2563EB;
    }

    .countries-group {
        display: contents;
    }

    .countries-group.hidden {
        display: none;
    }

    .back-btn {
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 12px;
        padding: 12px 16px;
    }

    .back-btn:hover {
        background-color: #F3F4F6;
        color: #1F2937;
    }

    .back-btn::before {
        content: '';
        display: inline-block;
    }

    .dropdown-arrow {
        display: inline-block;
        transition: transform 0.3s ease-in-out;
    }

    .dropdown-arrow.active {
        transform: rotate(180deg);
    }

    /* Smooth animation for menu transitions */
    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .dropdown-menu.active {
        animation: slideDown 0.3s ease-out;
    }
</style>