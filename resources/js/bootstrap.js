import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

const token = localStorage.getItem('AUTH_TOKEN');

const echoConfig = {
    broadcaster: 'pusher',
    key: 'miAppKeyProd',
    wsHost: 'xpressback-production.up.railway.app',
    wsPort: 6001,
    forceTLS: false,
    disableStats: true,
    enabledTransports: ['ws'],
    cluster: 'mt1',
    authEndpoint: 'https://xpressback-production.up.railway.app/broadcasting/auth',
    auth: {
        headers: {
            Authorization: `Bearer ${token}`,
        }
    }
};

window.Echo = new Echo(echoConfig);

window.Echo.connector.pusher.connection.bind('error', (err) => {
    console.error('Connection error:', err);
});

window.Echo.connector.pusher.connection.bind('disconnected', () => {
    console.warn('Disconnected from WebSocket, attempting to reconnect...');
    setTimeout(() => {
        window.Echo.connector.pusher.connect();
    }, 3000);
});

export default window.Echo;
