const STATIC_CACHE = 'static-v1';
const DYNAMIC_CACHE = 'dynamic-v1';

const PRECACHE_URLS = [
  '/css/style.css',
  '/css/print.css',
  '/css/domanda.css',
  '/css/lista.css',
  '/css/offerta.css',
  '/js/model.js',
  '/js/domanda.js',
  '/js/lista.js',
  '/js/offerta.js',
  '/conferma.php',
  '/domanda.php',
  '/fine.php',
  '/home.php',
  '/lista.php',
  '/login.php',
  '/logout.php',
  '/offerta.php',
  '/registra.php',
  '/offline.html'
];

self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(STATIC_CACHE).then(cache => cache.addAll(PRECACHE_URLS))
  );
  self.skipWaiting();
});

self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys().then(keys =>
      Promise.all(
        keys.filter(key => ![STATIC_CACHE, DYNAMIC_CACHE].includes(key))
            .map(key => caches.delete(key))
      )
    )
  );
  self.clients.claim();
});

self.addEventListener('fetch', event => {
  const { request } = event;
  const url = new URL(request.url);

  if (request.mode === 'navigate') {
    event.respondWith(
      fetch(request).catch(() => caches.match('/offline.html'))
    );
    return;
  }

  if (url.pathname.startsWith('/api/')) {
    event.respondWith(
      fetch(request)
        .then(response => {
          const clone = response.clone();
          caches.open(DYNAMIC_CACHE).then(cache => cache.put(request, clone));
          return response;
        })
        .catch(() => caches.match(request))
    );
    return;
  }

  if (
    request.destination === 'style' ||
    request.destination === 'script' ||
    request.destination === 'image'
  ) {
    event.respondWith(
      caches.open(STATIC_CACHE).then(cache =>
        cache.match(request).then(cached => {
          const update = fetch(request).then(response => {
            cache.put(request, response.clone());
            return response;
          });
          event.waitUntil(update);
          return cached || update;
        })
      )
    );
  }
});