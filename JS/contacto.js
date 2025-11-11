// ===== EFECTO SCROLL REVEAL =====
document.addEventListener("scroll", () => {
    document.querySelectorAll(".reveal").forEach(el => {
        const top = el.getBoundingClientRect().top;
        const visible = window.innerHeight * 0.8;
        if (top < visible) el.classList.add("activo");
    });
});

// Revelar los visibles al cargar
window.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll(".reveal").forEach(el => {
        const top = el.getBoundingClientRect().top;
        if (top < window.innerHeight * 0.9) el.classList.add("activo");
    });
});
