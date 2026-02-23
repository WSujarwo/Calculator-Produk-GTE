document.addEventListener("DOMContentLoaded", function () {

    const sidebar = document.querySelector(".sidebar");
    const sidebarToggler = document.querySelector(".sidebar-toggler");
    const menuToggler = document.querySelector(".menu-toggler");

    if (!sidebar) return;

    // =========================
    // DESKTOP COLLAPSE
    // =========================
    if (sidebarToggler) {
        sidebarToggler.addEventListener("click", () => {
            sidebar.classList.toggle("collapsed");
        });
    }

    // =========================
    // MOBILE MENU
    // =========================
    const toggleMenu = (isMenuActive) => {
        if (menuToggler) {
            menuToggler.querySelector("span").innerText =
                isMenuActive ? "close" : "menu";
        }
    };

    if (menuToggler) {
        menuToggler.addEventListener("click", () => {
            sidebar.classList.toggle("menu-active");
            toggleMenu(sidebar.classList.contains("menu-active"));
        });
    }

    // =========================
    // WINDOW RESIZE
    // =========================
    window.addEventListener("resize", () => {
        if (window.innerWidth >= 1024) {
            sidebar.classList.remove("menu-active");
        }
    });

});

// Submenu toggle
document.querySelectorAll(".submenu-toggle").forEach(toggle => {
    toggle.addEventListener("click", function (e) {
        e.preventDefault();

        const parent = this.closest(".has-submenu");
        parent.classList.toggle("open");
    });
});


