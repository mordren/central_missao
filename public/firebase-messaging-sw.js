// firebase-messaging-sw.js — ONÇAS DO OESTE
// IMPORTANTE: a config Firebase deve estar hardcoded aqui.
// O SW pode ser acordado pelo browser sem a página aberta,
// por isso não pode depender de postMessage da página.

importScripts('https://www.gstatic.com/firebasejs/12.12.1/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/12.12.1/firebase-messaging-compat.js');

firebase.initializeApp({
    apiKey:            "AIzaSyCl6wDjBt69wm4tLZMlui6B-1QCDtqs58M",
    authDomain:        "central-4a98b.firebaseapp.com",
    projectId:         "central-4a98b",
    storageBucket:     "central-4a98b.firebasestorage.app",
    messagingSenderId: "438092450467",
    appId:             "1:438092450467:web:0458699fb2c5c26d378d16",
});

const messaging = firebase.messaging();

// Notificações em background (aba fechada ou em segundo plano)
messaging.onBackgroundMessage(function (payload) {
    const title = (payload.notification && payload.notification.title) || 'ONÇAS DO OESTE';
    const options = {
        body:  (payload.notification && payload.notification.body) || '',
        icon:  '/images/logo.png',
        badge: '/images/logo.png',
        data:  payload.data || {},
    };
    return self.registration.showNotification(title, options);
});

// Fallback: captura push raw caso o SDK compat não intercepte
self.addEventListener('push', function (event) {
    // O SDK compat já trata pushes do FCM; este fallback cobre payloads customizados
    if (!event.data) return;
    let payload;
    try { payload = event.data.json(); } catch (e) { return; }

    // Só actua se não for um push FCM (que o SDK já tratou)
    if (payload.notification) {
        const title   = payload.notification.title || 'ONÇAS DO OESTE';
        const options = {
            body:  payload.notification.body  || '',
            icon:  '/images/logo.png',
            badge: '/images/logo.png',
            data:  payload.data || {},
        };
        event.waitUntil(self.registration.showNotification(title, options));
    }
});

self.addEventListener('notificationclick', function (event) {
    event.notification.close();
    const url = (event.notification.data && event.notification.data.url) || '/dashboard';
    event.waitUntil(clients.openWindow(url));
});
