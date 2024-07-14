import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

const token = localStorage.getItem('AUTH_TOKEN');

const echoConfig = {
    broadcaster: 'pusher',
    key: 'miAppKeyProd', // Puede ser cualquier string ya que no estamos usando Pusher
    wsHost: 'xpressback-production.up.railway.app', // Apunta a tu dominio de WebSockets
    wsPort: 6001, // Puerto del servidor WebSockets
    forceTLS: false, // Asegúrate de que esté en false si no estás usando SSL
    disableStats: true,
    enabledTransports: ['ws'], // Solo habilitar WebSocket sin SSL
    cluster: 'mt1', // Asegúrate de incluir el cluster
    authEndpoint: 'https://xpressback-production.up.railway.app/broadcasting/auth', // Cambia a tu endpoint de autenticación
    auth: {
        headers: {
            Authorization: `Bearer ${token}`,
        }
    }
};

window.Echo = new Echo(echoConfig);

export default window.Echo;
