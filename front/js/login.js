// Manejar el envío del formulario de login
document.getElementById('loginForm').addEventListener('submit', async function(event) {
    event.preventDefault();

    // Obtener valores del formulario
    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;
    const mensajeDiv = document.getElementById('mensaje');
    mensajeDiv.textContent = '';

    try {
        // Enviar solicitud POST a la API de login
        const response = await fetch('/autenticacionconjwt/back/api/login.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ username, password })
        });

        const data = await response.json();

        if (response.ok) {
            // Login exitoso, guardar token en localStorage
            localStorage.setItem('authToken', data.token);
            window.location.href = 'bienvenida.html';
        } else if (response.status === 401) {
            // Mostrar error de credenciales inválidas
            mensajeDiv.textContent = 'Error: ' + (data.message || 'Credenciales inválidas.');
        } else {
            mensajeDiv.textContent = 'Error desconocido en el servidor.';
        }

    } catch (error) {
        console.error('Error de red:', error);
        mensajeDiv.textContent = 'Error de conexión con la API.';
    }
});
