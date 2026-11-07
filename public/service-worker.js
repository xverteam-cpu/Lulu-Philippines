self.addEventListener('push', function (event) {
  var payload = {};

  if (event.data) {
    try {
      payload = event.data.json();
    } catch (error) {
      console.error('Unable to parse the push notification payload.', error);
    }
  }

  var title = payload.title || 'Lulu';
  var options = {
    body: payload.body || 'You have a new update.',
    icon: '/icons/lulu-192.png',
    badge: '/icons/lulu-192.png',
    data: { url: payload.url || '/dashboard' }
  };

  event.waitUntil(self.registration.showNotification(title, options));
});

self.addEventListener('notificationclick', function (event) {
  event.notification.close();

  var targetUrl = new URL(event.notification.data.url || '/dashboard', self.location.origin).href;
  event.waitUntil(
    clients.matchAll({ type: 'window', includeUncontrolled: true }).then(function (windowClients) {
      for (var index = 0; index < windowClients.length; index += 1) {
        var client = windowClients[index];

        if (client.url === targetUrl && 'focus' in client) {
          return client.focus();
        }
      }

      return clients.openWindow(targetUrl);
    })
  );
});
