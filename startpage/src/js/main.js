var $ = require('jquery');

// Simpler access keys
(function(){
  $(document).on('keypress', function (e) {
    if (typeof e.key == 'string') {
      $('a').each(function() {
        if ($(this).attr('accesskey') == e.key) {
          window.location.href = $(this).attr('href');
        }
      })
    }
  });
});

// PWA Offline mode
const $CACHE_STORE = 'benoth-startpage-v1';
const $NAVIGATE_FALLBACK = 'index.html';
const $FILES = [
  '/startpage',
  '/startpage/index.html',
  '/startpage/dist/css/main.css',
  '/startpage/dist/js/main.js'
];

if ('serviceWorker' in navigator) {
  navigator.serviceWorker.register('/startpage/dist/js/main.js')
    .catch(function(err) {
      console.info('Service workers are not supported.');
    });
}

self.addEventListener('install', event => {
  event.waitUntil(
    caches.open($CACHE_STORE)
      .then(cache => {
        return cache.addAll($FILES);
      })
      .then(() => {
        return self.skipWaiting();
      })
  );
});

self.addEventListener('activate', event => {
  event.waitUntil(
    caches.open($CACHE_STORE)
      .then(cache => {
        return cache.keys()
          .then(cacheNames => {
            return Promise.all(
              cacheNames.filter(cacheName => {
                return $FILES.indexOf(cacheName) === -1;
              }).map(cacheName => {
                return caches.delete(cacheName);
              })
            );
          })
          .then(() => {
            return self.clients.claim();
          });
      })
  );
});

self.addEventListener('fetch', event => {
  if (event.request.method === 'GET') {
    let url = event.request.url.indexOf(self.location.origin) !== -1 ?
      event.request.url.split(`${self.location.origin}/`)[1] :
      event.request.url;
    let isFileCached = $FILES.indexOf(url) !== -1;

    // This is important part if your app needs to be available offline
    // If request wasn't found in array of files and this request has navigation mode and there is defined navigation fallback
    // then navigation fallback url is picked istead of real request url
    if (!isFileCached && event.request.mode === 'navigate' && $NAVIGATE_FALLBACK) {
      url = $NAVIGATE_FALLBACK;
      isFileCached = $FILES.indexOf(url) !== -1;
    }

    if (isFileCached) {
      event.respondWith(
        caches.open($CACHE_STORE)
          .then(cache => {
            return cache.match(url)
              .then(response => {
                if (response) {
                  return response;
                }
                throw Error('There is not response for such request', url);
              });
          })
          .catch(error => {
            return fetch(event.request);
          })
      );
    }
  }
});


