/**
 * Calcuze - Progressive Web App Service Worker
 * Handles offline functionality, caching, and background operations
 */

const CACHE_NAME = 'calcuze-v1';
const OFFLINE_PAGE = '/offline.html';

// Assets to cache on install (app shell)
const STATIC_ASSETS = [
  '/',
  '/index.html',
  '/offline.html',
  '/css/styles.css',
  '/scripts/common.js',
  '/scripts/normal.js',
  '/scripts/scientific.js',
  '/scripts/economic.js',
  '/scripts/conversion.js',
  '/scripts/contact.js'
];

/**
 * Install Event - Cache essential assets
 */
self.addEventListener('install', event => {
  console.log('[Service Worker] Installing...');
  
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => {
        console.log('[Service Worker] Caching static assets');
        // Try to cache assets, but don't fail if some aren't available
        return Promise.allSettled(
          STATIC_ASSETS.map(url => 
            fetch(url)
              .then(response => {
                if (response.ok) {
                  return cache.put(url, response);
                }
              })
              .catch(() => {
                // Asset not available during install, will be cached on demand
              })
          )
        );
      })
      .then(() => {
        // Force the waiting service worker to become the active service worker
        return self.skipWaiting();
      })
      .catch(error => {
        console.error('[Service Worker] Installation failed:', error);
      })
  );
});

/**
 * Activate Event - Clean up old caches
 */
self.addEventListener('activate', event => {
  console.log('[Service Worker] Activating...');
  
  event.waitUntil(
    caches.keys()
      .then(cacheNames => {
        return Promise.all(
          cacheNames.map(cacheName => {
            if (cacheName !== CACHE_NAME) {
              console.log('[Service Worker] Deleting old cache:', cacheName);
              return caches.delete(cacheName);
            }
          })
        );
      })
      .then(() => {
        // Take control of all clients immediately
        return self.clients.claim();
      })
  );
});

/**
 * Fetch Event - Network first, fallback to cache
 */
self.addEventListener('fetch', event => {
  const { request } = event;
  const url = new URL(request.url);

  // Skip cross-origin requests and WebSocket
  if (url.origin !== location.origin || request.url.includes('chrome-extension')) {
    return;
  }

  // Handle different request types
  if (request.method === 'GET') {
    event.respondWith(handleGetRequest(request));
  } else {
    // For POST and other methods, just use network
    event.respondWith(fetch(request).catch(() => {
      return createOfflineResponse();
    }));
  }
});

/**
 * Handle GET requests with network-first strategy
 */
async function handleGetRequest(request) {
  try {
    // Try network first
    const response = await fetch(request);
    
    // Cache successful responses
    if (response.ok && isCacheable(request)) {
      const cache = await caches.open(CACHE_NAME);
      cache.put(request, response.clone());
    }
    
    return response;
  } catch (error) {
    // Network failed, try cache
    const cached = await caches.match(request);
    
    if (cached) {
      return cached;
    }
    
    // Neither network nor cache available
    if (isNavigationRequest(request)) {
      return caches.match(OFFLINE_PAGE) || createOfflineResponse();
    }
    
    // For non-navigation requests, return a basic offline response
    return createOfflineResponse();
  }
}

/**
 * Check if a request should be cached
 */
function isCacheable(request) {
  const url = new URL(request.url);
  
  // Cache HTML, CSS, JS, images, fonts
  const cacheableExtensions = [
    '.html', '.css', '.js', '.json',
    '.png', '.jpg', '.jpeg', '.gif', '.svg', '.webp',
    '.woff', '.woff2', '.ttf', '.eot'
  ];
  
  // Don't cache API calls or form submissions
  if (request.method !== 'GET') {
    return false;
  }
  
  const pathname = url.pathname;
  return cacheableExtensions.some(ext => pathname.endsWith(ext)) ||
         pathname === '/' ||
         pathname === '/index.html';
}

/**
 * Check if this is a navigation request
 */
function isNavigationRequest(request) {
  return request.mode === 'navigate' || request.headers.get('accept')?.includes('text/html');
}

/**
 * Create a basic offline response
 */
function createOfflineResponse() {
  return new Response(
    '<html><head><title>Offline</title></head><body><h1>Offline</h1><p>You are currently offline. Please check your connection.</p></body></html>',
    {
      status: 503,
      statusText: 'Service Unavailable',
      headers: new Headers({
        'Content-Type': 'text/html; charset=UTF-8'
      })
    }
  );
}

/**
 * Handle background sync for form submissions (future enhancement)
 */
self.addEventListener('sync', event => {
  if (event.tag === 'sync-contact-form') {
    event.waitUntil(syncContactForm());
  }
});

async function syncContactForm() {
  try {
    // Sync pending form submissions when back online
    console.log('[Service Worker] Syncing contact form...');
  } catch (error) {
    console.error('[Service Worker] Sync failed:', error);
    throw error;
  }
}
