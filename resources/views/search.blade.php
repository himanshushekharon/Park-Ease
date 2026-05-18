@extends('layouts.app')

@section('title', 'Find Parking Hubs')

@push('styles')
<style>
    .search-layout {
        height: calc(100vh - 81px); /* Navbar height */
        overflow: hidden;
        background: var(--bg-base);
    }

    #map {
        height: 100%;
        width: 100%;
        z-index: 1;
    }

    .search-overlay-refined {
        position: absolute;
        top: var(--space-6);
        left: var(--space-6);
        z-index: 1000;
        width: 420px;
        max-height: calc(100vh - 130px);
        display: flex;
        flex-direction: column;
    }

    .glass-search-results {
        padding: var(--space-6);
        overflow-y: auto;
        display: flex;
        flex-direction: column;
    }

    .result-item-premium {
        margin-bottom: var(--space-4);
        cursor: pointer;
        border: 2px solid transparent;
        padding: var(--space-4);
    }

    .result-item-premium:hover {
        border-color: var(--border-strong);
    }

    .result-item-premium.active {
        border-color: var(--brand-aqua);
        box-shadow: 0 0 0 4px rgba(46, 196, 182, 0.1);
        background: var(--bg-hover);
    }

    .price-chip {
        background: var(--text-primary);
        color: var(--bg-base);
        padding: var(--space-1) var(--space-3);
        border-radius: var(--radius-sm);
        font-weight: 800;
        font-size: 1.1rem;
        box-shadow: var(--shadow-sm);
    }

    .distance-tag {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--text-muted);
        background: var(--bg-elevated);
        padding: var(--space-1) var(--space-3);
        border-radius: var(--radius-sm);
        border: 1px solid var(--border-default);
    }

    /* Scrollbar */
    .glass-search-results::-webkit-scrollbar { width: 6px; }
    .glass-search-results::-webkit-scrollbar-track { background: transparent; }
    .glass-search-results::-webkit-scrollbar-thumb { background: var(--border-strong); border-radius: 10px; }

    .leaflet-popup-content-wrapper {
        background: var(--bg-surface) !important;
        color: var(--text-primary) !important;
        border-radius: var(--radius-card) !important;
        padding: 0 !important;
        overflow: hidden;
        border: 1px solid var(--border-default);
        box-shadow: var(--shadow-lg) !important;
    }
    .leaflet-popup-tip { background: var(--bg-surface) !important; }

    @media (max-width: 768px) {
        .search-overlay-refined {
            width: calc(100% - var(--space-8));
            left: var(--space-4);
            top: var(--space-4);
        }
    }
</style>
@endpush

@section('content')
<div class="search-layout position-relative">
    <div id="map"></div>

    <div class="search-overlay-refined">
        <div class="surface-glass glass-search-results flex-grow-1">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="text-h4 mb-0">System Nodes</h3>
                <span id="resultCount" class="badge bg-elevated border text-muted px-3 py-2 rounded-pill">Scanning...</span>
            </div>

            <div id="loadingIndicator" class="text-center py-5">
                <div class="spinner-border spinner-border-sm text-primary mb-3"></div>
                <div class="text-h6">Optimizing Grid View...</div>
            </div>

            <div id="resultsContainer"></div>

            <div id="noResults" class="empty-state d-none my-4">
                <i class="bi bi-geo-alt empty-state-icon"></i>
                <h4 class="text-h4 mb-2">No Hubs Detected</h4>
                <p class="text-secondary mb-4 text-center">Adjust your search parameters or explore another sector.</p>
                <a href="/" class="btn btn-secondary w-100">Reset Search</a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const urlParams = new URLSearchParams(window.location.search);
    const pincode = urlParams.get('pincode');
    const lat = urlParams.get('lat');
    const lng = urlParams.get('lng');
    const isLoggedIn = @json(Auth::check());

    let currentTileLayer = null;
    function setMapTheme() {
        const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
        const tileUrl = isDark 
            ? 'https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png'
            : 'https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png';
        
        if (currentTileLayer) map.removeLayer(currentTileLayer);
        currentTileLayer = L.tileLayer(tileUrl, { attribution: '&copy; CARTO' }).addTo(map);
    }

    let map = L.map('map', { zoomControl: false }).setView([20.5937, 78.9629], 5);
    setMapTheme();

    // Listen for theme changes dynamically
    const observer = new MutationObserver(mutations => {
        mutations.forEach(mutation => {
            if (mutation.attributeName === 'data-theme') setMapTheme();
        });
    });
    observer.observe(document.documentElement, { attributes: true });

    L.control.zoom({ position: 'bottomright' }).addTo(map);

    let markers = {};
    let bounds = new L.LatLngBounds();

    if (lat && lng) {
        L.circleMarker([lat, lng], {
            color: '#2EC4B6',
            fillColor: '#2EC4B6',
            fillOpacity: 0.3,
            radius: 12,
            weight: 2
        }).addTo(map).bindPopup("<b style='padding: 8px;'>Target Location</b>");
        bounds.extend([lat, lng]);
    }

    // API Fetch
    let apiUrl = '/api/search?';
    if (pincode) apiUrl += `pincode=${pincode}`;
    if (lat && lng) apiUrl += `lat=${lat}&lng=${lng}`;

    fetch(apiUrl)
        .then(res => res.json())
        .then(data => {
            document.getElementById('loadingIndicator').classList.add('d-none');
            const hubs = data.data;
            document.getElementById('resultCount').innerText = `${hubs.length} Active Hubs`;

            if (hubs.length === 0) {
                document.getElementById('noResults').classList.remove('d-none');
                if (lat && lng) map.setView([lat, lng], 14);
                return;
            }

            const container = document.getElementById('resultsContainer');

            hubs.forEach(hub => {
                const id = hub._id || hub.id;
                
                // Premium Marker
                const marker = L.marker([hub.latitude, hub.longitude]).addTo(map);
                marker.bindPopup(`
                    <div class="p-4 text-center">
                        <div class="text-h6 mb-2">${hub.city} Hub</div>
                        <h4 class="text-h4 mb-3">${hub.name}</h4>
                        <div class="d-flex justify-content-between align-items-center gap-4 border-top pt-3" style="border-color: var(--border-default) !important;">
                            <span class="text-h4 text-primary mb-0">₹${hub.car_price}<span class="text-small fw-normal text-muted">/hr</span></span>
                            <a href="/parking/${id}" class="btn btn-brand btn-sm px-4">Select</a>
                        </div>
                    </div>
                `);
                markers[id] = marker;
                bounds.extend([hub.latitude, hub.longitude]);

                // Result Card
                const item = document.createElement('div');
                item.className = 'surface-card result-item-premium hover-lift';
                item.id = `card-${id}`;
                item.innerHTML = `
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <span class="distance-tag">${hub.city || 'Regional Hub'}</span>
                        <div class="price-chip">₹${hub.car_price}</div>
                    </div>
                    <h4 class="text-h4 mb-1">${hub.name}</h4>
                    <p class="text-secondary mb-4 text-truncate">${hub.address}</p>
                    <div class="d-flex gap-2">
                        <a href="${isLoggedIn ? '/parking/' + id : '/login?intended=/parking/' + id}" class="btn btn-brand flex-grow-1 text-decoration-none">Reserve</a>
                        <button class="btn btn-secondary px-3" onclick="focusParking('${id}')"><i class="bi bi-geo"></i></button>
                    </div>
                `;
                
                item.addEventListener('mouseenter', () => {
                    item.classList.add('active');
                    marker.openPopup();
                });
                item.addEventListener('mouseleave', () => item.classList.remove('active'));
                
                container.appendChild(item);
            });

            if(hubs.length > 0) {
                map.fitBounds(bounds, {padding: [50, 50], maxZoom: 14});
            }
        });

    window.focusParking = function(id) {
        const marker = markers[id];
        if (marker) {
            map.setView(marker.getLatLng(), 16);
            marker.openPopup();
            document.getElementById(`card-${id}`).scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    };
</script>
@endpush
