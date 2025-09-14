const STATIC_CACHE = 'static-v1';
const DYNAMIC_CACHE = 'dynamic-v1';
const POST_DB = 'offline-posts';

// Apri il database IndexedDB per memorizzare le richieste POST
function openPostDB() {
  return new Promise((resolve, reject) => {
    const request = indexedDB.open(POST_DB, 1);
    request.onupgradeneeded = () => {
      request.result.createObjectStore('requests', { autoIncrement: true });
    };
    request.onsuccess = () => resolve(request.result);
    request.onerror = () => reject(request.error);
  });
}

async function storePostRequest(url, body) {
  const db = await openPostDB();
  const tx = db.transaction('requests', 'readwrite');
  tx.objectStore('requests').add({ url, body, timestamp: Date.now() });
  return tx.complete;
}

async function getStoredRequests() {
  const db = await openPostDB();
  const tx = db.transaction('requests', 'readonly');
  const store = tx.objectStore('requests');
  return new Promise((resolve, reject) => {
    const req = store.openCursor();
    const all = [];
    req.onsuccess = e => {
      const cursor = e.target.result;
      if (cursor) {
        all.push({ id: cursor.key, ...cursor.value });
        cursor.continue();
      } else {
        resolve(all);
      }
    };
    req.onerror = () => reject(req.error);
  });
}

async function deleteStoredRequest(id) {
  const db = await openPostDB();
  const tx = db.transaction('requests', 'readwrite');
  tx.objectStore('requests').delete(id);
  return tx.complete;
}

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

  if (request.method === 'POST') {
    event.respondWith(
      fetch(request.clone()).catch(() =>
        request.clone().text().then(body =>
          storePostRequest(url.pathname + url.search, body)
            .then(() => self.registration.sync.register('sync-post-requests'))
            .then(() => new Response(JSON.stringify({ ok: false, offline: true }), {
              headers: { 'Content-Type': 'application/json' }
            }))
        )
      )
    );
    return;
  }

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

self.addEventListener('sync', event => {
  if (event.tag === 'sync-post-requests') {
    event.waitUntil(processQueue());
  }
});

async function processQueue() {
  const items = await getStoredRequests();
  for (const item of items) {
    try {
      const res = await fetch(item.url, {
        method: 'POST',
        body: item.body,
        credentials: 'same-origin',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
      });
      if (res.status === 401 || res.status === 403) {
        const clients = await self.clients.matchAll();
        clients.forEach(c => c.postMessage({ type: 'auth-error', status: res.status, url: item.url }));
        await deleteStoredRequest(item.id);
      } else if (res.ok) {
        await deleteStoredRequest(item.id);
      }
    } catch (err) {
      // rete ancora non disponibile: ritenteremo al prossimo sync
    }
  }
}