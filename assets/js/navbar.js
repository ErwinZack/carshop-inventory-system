// Toggle profile dropdown
document.addEventListener("DOMContentLoaded", () => {
    const profileBtn = document.getElementById("navbarProfileBtn");
    const profileMenu = document.getElementById("navbarProfileMenu");

    if (profileBtn && profileMenu) {
        profileBtn.addEventListener("click", (e) => {
            e.stopPropagation();
            profileMenu.classList.toggle("show");
        });

        // Close dropdown when clicking outside
        window.addEventListener("click", (e) => {
            if (!e.target.closest(".navbar-profile")) {
                profileMenu.classList.remove("show");
            }
        });
    }
});

// Js for dropdown Profile Icon
document.addEventListener("DOMContentLoaded", () => {
    const profileBtn = document.getElementById("profileMenuBtn");
    const profileMenu = document.getElementById("profileMenu");

    if (profileBtn && profileMenu) {
        profileBtn.addEventListener("click", e => {
            e.stopPropagation();
            profileMenu.classList.toggle("show");
        });

        window.addEventListener("click", e => {
            if (!e.target.closest(".navbar-profile")) {
                profileMenu.classList.remove("show");
            }
        });
    }
});




