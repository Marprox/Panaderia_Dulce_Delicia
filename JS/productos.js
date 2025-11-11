// ===== EFECTO SCROLL REVEAL =====
document.addEventListener("scroll", () => {
    document.querySelectorAll(".reveal").forEach(el => {
        const top = el.getBoundingClientRect().top;
        const visible = window.innerHeight * 0.8;
        if (top < visible) el.classList.add("activo");
    });
});

// ===== FILTROS DE PRODUCTOS =====
const botones = document.querySelectorAll(".filtro");
const tarjetas = document.querySelectorAll(".tarjeta");

// FUNCIÓN PARA MOSTRAR PRODUCTOS
function mostrarProductos(categoria) {
    tarjetas.forEach((tarjeta, i) => {
        if (categoria === "todos" || tarjeta.dataset.categoria === categoria) {
            tarjeta.style.display = "block";
            setTimeout(() => {
                tarjeta.classList.add("mostrar");
                tarjeta.style.animationDelay = `${i * 0.1}s`;
            }, 50);
        } else {
            tarjeta.classList.remove("mostrar");
            setTimeout(() => {
                tarjeta.style.display = "none";
            }, 300);
        }
    });
}

// MOSTRAR TODOS LOS PRODUCTOS AL CARGAR
window.addEventListener("DOMContentLoaded", () => {
    tarjetas.forEach((tarjeta, i) => {
        tarjeta.style.display = "block";
        setTimeout(() => {
            tarjeta.classList.add("mostrar");
            tarjeta.style.animationDelay = `${i * 0.1}s`;
        }, 150 + i * 120);
    });
});

// EVENTOS DE LOS BOTONES
botones.forEach(boton => {
    boton.addEventListener("click", () => {
        const categoria = boton.dataset.categoria;
        botones.forEach(b => b.classList.remove("activo"));
        boton.classList.add("activo");
        mostrarProductos(categoria);
    });
});
