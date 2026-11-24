import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

const DEFAULT_CENTER = [39.8283, -98.5795];
const DEFAULT_ZOOM = 4;
const APPROXIMATE_ZOOM = 7;
const BOUNDS_DELAY = 250;
const APPROXIMATE_CACHE_KEY = 'mrl:approximate-location';
const APPROXIMATE_CACHE_MS = 60 * 60 * 1000;
const APPROXIMATE_ENDPOINTS = [
    {
        url: 'https://ipinfo.io/json',
        parse(data) {
            const [latitude, longitude] = String(data.loc || '').split(',').map(Number);

            if (!Number.isFinite(latitude) || !Number.isFinite(longitude)) {
                return null;
            }

            return {
                latitude,
                longitude,
                city: data.city || '',
                region: data.region || '',
                country: data.country || '',
            };
        },
    },
    {
        url: 'https://ipwho.is/?fields=success,latitude,longitude,city,region,country_code',
        parse(data) {
            const latitude = Number(data.latitude);
            const longitude = Number(data.longitude);

            if (!data.success || !Number.isFinite(latitude) || !Number.isFinite(longitude)) {
                return null;
            }

            return {
                latitude,
                longitude,
                city: data.city || '',
                region: data.region || '',
                country: data.country_code || '',
            };
        },
    },
];

let layoutMap = null;
let markerLayer = null;
let userLayer = null;
let approximateLayer = null;
let currentUserLocation = null;
let approximateLocation = null;
let approximateAttempted = false;
let suppressBoundsUntil = 0;
let boundsTimer = null;

function parseJson(value, fallback) {
    if (!value) {
        return fallback;
    }

    try {
        return JSON.parse(value);
    } catch {
        return fallback;
    }
}

function escapeHtml(value) {
    const element = document.createElement('div');
    element.textContent = value ?? '';

    return element.innerHTML;
}

function buildPopup(layout) {
    const link = document.createElement('a');
    link.href = layout.url;
    link.className = 'layout-map-popup';

    if (layout.cover) {
        const image = document.createElement('img');
        image.src = layout.cover;
        image.alt = layout.name;
        image.loading = 'lazy';
        link.append(image);
    }

    const body = document.createElement('div');
    body.className = 'layout-map-popup-body';

    const title = document.createElement('p');
    title.className = 'layout-map-popup-title';
    title.textContent = layout.name;
    body.append(title);

    const meta = [layout.scaleLabel || layout.scale, layout.place].filter(Boolean).join(' · ');

    if (meta) {
        const metaLine = document.createElement('p');
        metaLine.className = 'layout-map-popup-meta';
        metaLine.textContent = meta;
        body.append(metaLine);
    }

    if (layout.distance) {
        const distance = document.createElement('p');
        distance.className = 'layout-map-popup-distance';
        distance.textContent = `${layout.distance} away`;
        body.append(distance);
    }

    const callToAction = document.createElement('span');
    callToAction.className = 'layout-map-popup-cta';
    callToAction.textContent = 'View layout →';
    body.append(callToAction);

    link.append(body);

    return link;
}

function createMarker(layout) {
    const icon = L.divIcon({
        className: 'layout-map-marker',
        html: `<span>${escapeHtml(layout.scale || '?')}</span>`,
        iconSize: [36, 36],
        iconAnchor: [18, 18],
        popupAnchor: [0, -22],
    });

    return L.marker([Number(layout.latitude), Number(layout.longitude)], {
        icon,
        title: layout.name,
        alt: layout.name,
    }).bindPopup(buildPopup(layout), {
        maxWidth: 300,
        minWidth: 240,
        autoPanPadding: [24, 24],
    });
}

function setLocationStatus(message, tone = 'muted') {
    const status = document.getElementById('layout-location-status');

    if (!status) {
        return;
    }

    status.textContent = message || '';
    status.dataset.tone = tone;
    status.classList.toggle('hidden', !message);
}

function renderUserLocation(location) {
    if (!userLayer) {
        return;
    }

    userLayer.clearLayers();
    currentUserLocation = null;

    if (!location) {
        return;
    }

    const latitude = Number(location.latitude);
    const longitude = Number(location.longitude);

    if (!Number.isFinite(latitude) || !Number.isFinite(longitude)) {
        return;
    }

    currentUserLocation = { latitude, longitude };

    L.circleMarker([latitude, longitude], {
        radius: 7,
        color: '#ffffff',
        weight: 2,
        fillColor: '#2563eb',
        fillOpacity: 1,
    })
        .addTo(userLayer)
        .bindPopup('Your location');
}

function clearApproximateLocation() {
    if (approximateLayer) {
        approximateLayer.clearLayers();
    }
}

function renderApproximateLocation(location) {
    if (!approximateLayer) {
        return;
    }

    clearApproximateLocation();

    const places = [location.city, location.region].filter(Boolean).join(', ');
    const popup = document.createElement('div');
    const heading = document.createElement('p');
    heading.className = 'font-semibold';
    heading.textContent = 'Approximate area';
    popup.append(heading);

    const detail = document.createElement('p');
    detail.className = 'mt-1 text-xs text-stone-600';
    detail.textContent = places
        ? `Centered near ${places} from your network. Choose "Use my location" for precise distances.`
        : 'Centered from your network. Choose "Use my location" for precise distances.';
    popup.append(detail);

    L.circleMarker([location.latitude, location.longitude], {
        radius: 8,
        color: '#b45309',
        weight: 2,
        fillColor: '#f59e0b',
        fillOpacity: 0.9,
    })
        .addTo(approximateLayer)
        .bindPopup(popup);
}

function isMapVisible() {
    return Boolean(layoutMap && layoutMap.getContainer().getClientRects().length);
}

function fitToLayouts(latLngs, userLocation) {
    suppressBoundsUntil = Date.now() + 1000;

    if (latLngs.length === 1) {
        layoutMap.setView(latLngs[0], 10);

        return;
    }

    if (latLngs.length > 1) {
        layoutMap.fitBounds(L.latLngBounds(latLngs), {
            padding: [40, 40],
            maxZoom: 12,
        });

        return;
    }

    if (userLocation) {
        layoutMap.setView([Number(userLocation.latitude), Number(userLocation.longitude)], 9);
    }
}

function renderMarkers(layouts, options = {}) {
    if (!layoutMap || !markerLayer) {
        return;
    }

    markerLayer.clearLayers();
    const latLngs = [];

    layouts.forEach((layout) => {
        const latitude = Number(layout.latitude);
        const longitude = Number(layout.longitude);

        if (!Number.isFinite(latitude) || !Number.isFinite(longitude)) {
            return;
        }

        createMarker(layout).addTo(markerLayer);
        latLngs.push([latitude, longitude]);
    });

    if (options.fit) {
        fitToLayouts(latLngs, options.userLocation);
    }
}

function readCachedApproximateLocation() {
    try {
        const cached = JSON.parse(sessionStorage.getItem(APPROXIMATE_CACHE_KEY) || 'null');

        if (
            cached
            && Number.isFinite(Number(cached.latitude))
            && Number.isFinite(Number(cached.longitude))
            && Date.now() - Number(cached.savedAt) < APPROXIMATE_CACHE_MS
        ) {
            return cached;
        }
    } catch {
        // Ignore unavailable storage or malformed cache entries.
    }

    return null;
}

function cacheApproximateLocation(location) {
    try {
        sessionStorage.setItem(APPROXIMATE_CACHE_KEY, JSON.stringify({
            ...location,
            savedAt: Date.now(),
        }));
    } catch {
        // Storage is optional.
    }
}

async function fetchApproximateLocation() {
    const cached = readCachedApproximateLocation();

    if (cached) {
        return cached;
    }

    for (const endpoint of APPROXIMATE_ENDPOINTS) {
        const controller = new AbortController();
        const timeout = setTimeout(() => controller.abort(), 5000);

        try {
            const response = await fetch(endpoint.url, {
                signal: controller.signal,
                headers: { Accept: 'application/json' },
            });

            if (!response.ok) {
                continue;
            }

            const location = endpoint.parse(await response.json());

            if (location) {
                cacheApproximateLocation(location);

                return location;
            }
        } catch {
            // Try the next endpoint.
        } finally {
            clearTimeout(timeout);
        }
    }

    return null;
}

async function applyApproximateCenter(force = false, reason = '') {
    if (!layoutMap) {
        return;
    }

    if (!force && (approximateAttempted || currentUserLocation)) {
        return;
    }

    if (!isMapVisible()) {
        return;
    }

    approximateAttempted = true;
    setLocationStatus('Finding an approximate area from your network…');

    const location = approximateLocation || await fetchApproximateLocation();

    if (!location) {
        setLocationStatus(`${reason} We could not determine an approximate area. Showing all layouts.`.trim());

        return;
    }

    approximateLocation = location;
    renderApproximateLocation(location);

    suppressBoundsUntil = 0;
    layoutMap.setView([location.latitude, location.longitude], APPROXIMATE_ZOOM);

    const place = [location.city, location.region].filter(Boolean).join(', ');
    setLocationStatus(
        `${reason} ${place
            ? `Centered near ${place} using an approximate location from your network.`
            : 'Centered using an approximate location from your network.'}`.trim(),
        'success',
    );
}

function maybeApplyApproximateCenter() {
    if (currentUserLocation || approximateAttempted || !isMapVisible()) {
        return;
    }

    applyApproximateCenter();
}

function requestBrowserLocation(wire) {
    if (!navigator.geolocation || !window.isSecureContext) {
        setLocationStatus(
            'Precise location needs HTTPS or localhost. Looking up an approximate area instead.',
            'error',
        );
        applyApproximateCenter(true, 'Precise location needs HTTPS or localhost.');

        return;
    }

    setLocationStatus('Requesting your location…');

    navigator.geolocation.getCurrentPosition(
        (position) => {
            approximateAttempted = true;
            clearApproximateLocation();
            setLocationStatus('Using your device location.', 'success');
            wire.setCoordinates(position.coords.latitude, position.coords.longitude);
        },
        (error) => {
            const reason = error?.code === 1
                ? 'Location permission was denied.'
                : 'We could not get your precise location.';

            setLocationStatus(`${reason} Looking up an approximate area instead.`, 'error');
            applyApproximateCenter(true, reason);
        },
        { enableHighAccuracy: false, timeout: 10000, maximumAge: 300000 },
    );
}

function emitBounds() {
    if (!layoutMap || Date.now() < suppressBoundsUntil) {
        return;
    }

    const bounds = layoutMap.getBounds();

    window.dispatchEvent(new CustomEvent('layout-map-bounds', {
        detail: {
            north: bounds.getNorth(),
            south: bounds.getSouth(),
            east: bounds.getEast(),
            west: bounds.getWest(),
        },
    }));
}

function initLayoutMap() {
    const element = document.getElementById('layout-map');

    if (!element || element.dataset.mapReady === 'true') {
        return;
    }

    layoutMap = L.map(element, {
        scrollWheelZoom: false,
        worldCopyJump: true,
        minZoom: 2,
        maxZoom: 18,
    }).setView(DEFAULT_CENTER, DEFAULT_ZOOM);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
    }).addTo(layoutMap);

    markerLayer = L.layerGroup().addTo(layoutMap);
    userLayer = L.layerGroup().addTo(layoutMap);
    approximateLayer = L.layerGroup().addTo(layoutMap);

    const layouts = parseJson(element.dataset.layouts, []);
    const userLocation = parseJson(element.dataset.userLocation, null);

    renderUserLocation(userLocation);
    renderMarkers(layouts, { fit: true, userLocation });

    element.dataset.mapReady = 'true';

    layoutMap.on('moveend', () => {
        clearTimeout(boundsTimer);
        boundsTimer = setTimeout(emitBounds, BOUNDS_DELAY);
    });

    const resizeObserver = new ResizeObserver(() => {
        if (layoutMap) {
            layoutMap.invalidateSize({ pan: false });
        }
    });
    resizeObserver.observe(element);

    if ('IntersectionObserver' in window) {
        const visibilityObserver = new IntersectionObserver((entries) => {
            if (entries.some((entry) => entry.isIntersecting)) {
                maybeApplyApproximateCenter();
            }
        }, { threshold: 0.15 });

        visibilityObserver.observe(element);
    }

    setTimeout(maybeApplyApproximateCenter, 400);
}

window.mrlUseMyLocation = requestBrowserLocation;

window.addEventListener('layout-map-updated', (event) => {
    const detail = event.detail || {};

    if (!layoutMap) {
        return;
    }

    const applyUpdate = () => {
        layoutMap.invalidateSize({ pan: false });
        renderUserLocation(detail.userLocation);

        if (detail.userLocation) {
            clearApproximateLocation();
        }

        renderMarkers(detail.layouts || [], {
            fit: Boolean(detail.fit),
            userLocation: detail.userLocation,
        });

        setTimeout(maybeApplyApproximateCenter, 250);
    };

    requestAnimationFrame(() => setTimeout(applyUpdate, 0));
});

document.addEventListener('livewire:init', () => {
    if (window.Livewire?.hook) {
        window.Livewire.hook('morph.updated', () => initLayoutMap());
    }
});

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initLayoutMap);
} else {
    initLayoutMap();
}

document.addEventListener('livewire:navigated', initLayoutMap);
