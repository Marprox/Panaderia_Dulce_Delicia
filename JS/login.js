const caja = document.querySelector('.form-caja');
const mostrarRegistro = document.getElementById('mostrarRegistro');
const mostrarLogin = document.getElementById('mostrarLogin');

// Cambiar a formulario de registro
mostrarRegistro.addEventListener('click', e => {
    e.preventDefault();
    caja.classList.add('activo');
});

// Cambiar a formulario de login  
mostrarLogin.addEventListener('click', e => {
    e.preventDefault();
    caja.classList.remove('activo');
});

// Manejar envío de formularios
document.querySelectorAll('form').forEach(form => {
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Mostrar loading en el botón
        const boton = this.querySelector('button[type="submit"]');
        const textoOriginal = boton.textContent;
        boton.textContent = 'Procesando...';
        boton.disabled = true;

        const datos = new FormData(this);

        // IMPORTANTE: Ahora ambos formularios se envían al MISMO archivo
        fetch('login.php', {
            method: 'POST',
            body: datos
        })
        .then(res => res.text())
        .then(data => {
            // Si la respuesta contiene una redirección, el PHP ya la maneja
            if (data.includes('Location:') || data.trim() === '') {
                // El PHP ya redirigió, no hacer nada
                return;
            }
            
            // Si hay un mensaje de error, se mostrará en la página
            console.log('Respuesta del servidor:', data);
        })
        .catch(err => {
            console.error('Error:', err);
            alert('🚨 Error de conexión con el servidor.');
        })
        .finally(() => {
            // Restaurar botón
            boton.textContent = textoOriginal;
            boton.disabled = false;
        });
    });
});