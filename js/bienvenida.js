const API_WELCOME_URL = '/autenticacionconjwt/api/api.php?action=welcome'; 

const token = localStorage.getItem('authToken');

async function cargarDatosProtegidos() {

    if (!token) {
        window.location.href = 'no_acceso.html';
        return;
    }

    try {

        const response = await fetch(API_WELCOME_URL, {
            method: 'GET',
            headers: {
                'Authorization': `Bearer ${token}`
            }
        });

        const data = await response.json();

        if (response.ok) {

            document.getElementById('nombreUsuario').textContent = data.username;
            document.getElementById('horaActual').textContent = 'Hora actual: ' + data.hora_actual;
            document.getElementById('mensajeAdicional').textContent = data.mensaje_adicional;
        } else if (response.status === 403) {
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

document.getElementById('logoutButton').addEventListener('click', function() {
    localStorage.removeItem('authToken');
    window.location.href = 'login.html';
});

cargarDatosProtegidos();
