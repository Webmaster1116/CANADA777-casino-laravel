'use strict';

/*
 * ==========================================================
 * SERVICE WORKER
 * ==========================================================
 *
 * Service Worker of Support Board admin area
 *
*/

const SB_CACHE_NAME = 'sb-3-3-3';
const SB_OFFLINE = 'resources/pwa/offline.html';
var sb_push_link;
var sb_push_conversation_id;
var sb_push_user_conversation_id;

importScripts('https://js.pusher.com/beams/service-worker.js');

self.addEventListener('install', (event) => {
    event.waitUntil((async () => {
        const cache = await caches.open(SB_CACHE_NAME);
        await cache.add(new Request(SB_OFFLINE, { cache: 'reload' }));
    })());
});

self.addEventListener('activate', (event) => {
    event.waitUntil((async () => {
        if ('navigationPreload' in self.registration) {
            await self.registration.navigationPreload.enable();
        }
    })());
    self.clients.claim();
});

self.addEventListener('fetch', (event) => {
    if (event.request.mode === 'navigate') {
        event.respondWith((async () => {
            try {
                const preloadResponse = await event.preloadResponse;
                if (preloadResponse) {
                    return preloadResponse;
                }
                const networkResponse = await fetch(event.request);
                return networkResponse;
            } catch (error) {
                const cache = await caches.open(SB_CACHE_NAME);
                const cachedResponse = await cache.match(SB_OFFLINE);
                return cachedResponse;
            }
        })());
    }
});

// Pusher
PusherPushNotifications.onNotificationReceived = ({ pushEvent, payload }) => {
    sb_push_link = payload.notification.deep_link;
    sb_push_conversation_id = payload.data.conversation_id;
    sb_push_user_conversation_id = payload.data.user_id;
    pushEvent.waitUntil(self.registration.showNotification(payload.notification.title, { body: payload.notification.body, icon: payload.notification.icon, data: payload.data }));
};

self.addEventListener('notificationclick', function (event) {
    event.notification.close();
    event.waitUntil(clients.matchAll({
        type: 'window',
        includeUncontrolled: true
    }).then((clientList) => {
        for (var i = 0; i < clientList.length; i++) {
            if (clientList[i].url.split('?')[0] == sb_push_link) {
                clientList[i].postMessage({ 'conversation_id': sb_push_conversation_id, 'user_id': sb_push_user_conversation_id });
                return clientList[i].focus();
            }
        }
        if (sb_push_link && clients.openWindow) return clients.openWindow(sb_push_link + '?conversation=' + sb_push_conversation_id);
    }));
});
