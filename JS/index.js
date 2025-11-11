// animaciones.js
document.addEventListener("scroll", revealOnScroll);

function revealOnScroll() {
    const elementos = document.querySelectorAll(".reveal");

    for (let el of elementos) {
        const distancia = el.getBoundingClientRect().top;
        const altoVentana = window.innerHeight * 0.85;

        if (distancia < altoVentana) {
            el.classList.add("activo");
        }
    }
}

// Revela elementos que ya están visibles al cargar
window.addEventListener("load", revealOnScroll);
