// URL del endpoint de bienvenida protegido
const API_WELCOME_URL = '/autenticacionconjwt/back/api/bienvenida.php'; 

// Obtener el token del localStorage
const token = localStorage.getItem('authToken');

// Función para cargar los datos protegidos si el token es válido
async function cargarDatosProtegidos() {

    if (!token) {
        // Si no hay token, redirigir a página de acceso denegado
        window.location.href = 'no_acceso.html';
        return;
    }

    try {
        // Enviar solicitud GET con el token en el header Authorization
        const response = await fetch(API_WELCOME_URL, {
            method: 'GET',
            headers: {
                'Authorization': `Bearer ${token}`
            }
        });

        const data = await response.json();

        if (response.ok) {
            // Mostrar datos del usuario
            document.getElementById('nombreUsuario').textContent = data.username;
            document.getElementById('horaActual').textContent = 'Hora actual: ' + data.hora_actual;
            document.getElementById('mensajeAdicional').textContent = data.mensaje_adicional;
        } else if (response.status === 403) {
            // Token inválido o expirado
            console.error('Acceso denegado:', data.message);
            window.location.href = 'no_acceso.html';
        } else {
            console.error('Error al obtener datos:', data.message);
            window.location.href = 'no_acceso.html';
        }

    } catch (error) {
        console.error('Error de red al llamar a la API:', error);
        window.location.href = 'no_acceso.html';
    }
}

// Manejar el click en el botón de logout
document.getElementById('logoutButton').addEventListener('click', function() {
    localStorage.removeItem('authToken');
    window.location.href = 'login.html';
});

// Ejecutar al cargar la página
cargarDatosProtegidos();
