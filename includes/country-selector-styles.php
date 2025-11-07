<style>
    .dropdown-menu {
        display: none;
        flex-direction: column;
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease-in-out;
    }
    .dropdown-menu.active {
        display: flex;
        max-height: 500px;
    }
    .dropdown-arrow {
        transition: transform 0.3s ease-in-out;
    }
    .dropdown-arrow.active {
        transform: rotate(180deg);
    }
</style>

