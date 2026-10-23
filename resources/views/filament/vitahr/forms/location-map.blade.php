<div
    wire:ignore
    x-data="locationMap({
        latitude: @js($latitude),
        longitude: @js($longitude),
        component: $wire,
    })"
    x-init="init()"
    class="space-y-3"
>
    {{-- Map --}}
    <div
        x-ref="map"
        style="height: 420px; width: 100%;"
        class="overflow-hidden rounded-xl border border-gray-300 dark:border-gray-700"
    ></div>

    {{-- Coordinates --}}
    <div class="grid grid-cols-1 gap-3 md:grid-cols-3">

        {{-- Latitude --}}
        <div class="rounded-lg border border-gray-200 bg-gray-50 p-3 dark:border-gray-700 dark:bg-gray-800">
            <div class="text-xs font-medium text-gray-500 dark:text-gray-400">
                {{ __('vitahr.latitude') }}
            </div>

            <div
                x-text="latitude !== null ? latitude.toFixed(7) : '—'"
                class="mt-1 font-mono text-sm font-semibold text-gray-900 dark:text-white"
            ></div>
        </div>

        {{-- Longitude --}}
        <div class="rounded-lg border border-gray-200 bg-gray-50 p-3 dark:border-gray-700 dark:bg-gray-800">
            <div class="text-xs font-medium text-gray-500 dark:text-gray-400">
                {{ __('vitahr.longitude') }}
            </div>

            <div
                x-text="longitude !== null ? longitude.toFixed(7) : '—'"
                class="mt-1 font-mono text-sm font-semibold text-gray-900 dark:text-white"
            ></div>
        </div>

        {{-- Location --}}
        <div class="rounded-lg border border-gray-200 bg-gray-50 p-3 dark:border-gray-700 dark:bg-gray-800">
            <div class="text-xs font-medium text-gray-500 dark:text-gray-400">
                {{ __('vitahr.location_on_map') }}
            </div>

            <div class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">
                <span x-show="latitude !== null && longitude !== null">
                    📍
                    <span x-text="latitude.toFixed(5)"></span>,
                    <span x-text="longitude.toFixed(5)"></span>
                </span>

                <span
                    x-show="latitude === null || longitude === null"
                    class="text-gray-500"
                >
                    —
                </span>
            </div>
        </div>
    </div>

    {{-- Help text --}}
    <div class="text-sm text-gray-500 dark:text-gray-400">
        {{ __('vitahr.location_map_help') }}
    </div>
</div>

@once

    {{-- Leaflet CSS --}}
    <link
        rel="stylesheet"
        href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        crossorigin=""
    />

    {{-- Leaflet JS --}}
    <script
        src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        crossorigin=""
    ></script>

    <script>
        document.addEventListener('alpine:init', () => {

            Alpine.data('locationMap', (config) => ({

                map: null,
                marker: null,
                initialized: false,

                component: config.component,

                latitude: config.latitude !== null && config.latitude !== ''
                    ? parseFloat(config.latitude)
                    : null,

                longitude: config.longitude !== null && config.longitude !== ''
                    ? parseFloat(config.longitude)
                    : null,

                /*
                 * Map layers
                 */
                baseLayers: {},

                /*
                 * Initialize
                 */
                init() {
                    this.$nextTick(() => {
                        this.initializeMap();
                    });

                    /*
                     * Listen for manually changed coordinates
                     * from Filament latitude/longitude inputs.
                     */
                    window.addEventListener(
                        'work-location-coordinates-updated',
                        (event) => {

                            const latitude = parseFloat(
                                event.detail.latitude
                            );

                            const longitude = parseFloat(
                                event.detail.longitude
                            );

                            if (
                                Number.isNaN(latitude) ||
                                Number.isNaN(longitude)
                            ) {
                                return;
                            }

                            this.latitude = latitude;
                            this.longitude = longitude;

                            this.updateMapLocation(
                                latitude,
                                longitude
                            );
                        }
                    );
                },

                /*
                 * CREATE:
                 *   no coordinates -> try browser geolocation
                 *
                 * EDIT:
                 *   existing coordinates -> use saved coordinates
                 */
                initializeMap() {

                    if (!this.$refs.map) {
                        return;
                    }

                    /*
                     * Prevent Leaflet from being initialized twice.
                     */
                    if (this.$refs.map._leaflet_id) {
                        return;
                    }

                    /*
                     * EDIT MODE
                     *
                     * Existing coordinates are already available.
                     * Do NOT ask for browser location.
                     */
                    if (
                        this.latitude !== null &&
                        this.longitude !== null
                    ) {
                        this.createMap(
                            this.latitude,
                            this.longitude,
                            16
                        );

                        return;
                    }

                    /*
                     * CREATE MODE
                     *
                     * No coordinates yet.
                     * Try to get the browser's current location.
                     */
                    if (navigator.geolocation) {

                        navigator.geolocation.getCurrentPosition(

                            (position) => {

                                const latitude = parseFloat(
                                    position.coords.latitude.toFixed(7)
                                );

                                const longitude = parseFloat(
                                    position.coords.longitude.toFixed(7)
                                );

                                this.latitude = latitude;
                                this.longitude = longitude;

                                this.createMap(
                                    latitude,
                                    longitude,
                                    16
                                );

                                this.createMarker(
                                    latitude,
                                    longitude
                                );

                                this.updateFilamentField(
                                    'latitude',
                                    latitude
                                );

                                this.updateFilamentField(
                                    'longitude',
                                    longitude
                                );
                            },

                            () => {

                                /*
                                 * Location permission denied
                                 * or browser location unavailable.
                                 *
                                 * Show world map and let admin
                                 * choose manually.
                                 */
                                this.createMap(
                                    20,
                                    0,
                                    2
                                );
                            },

                            {
                                enableHighAccuracy: true,
                                timeout: 10000,
                                maximumAge: 0
                            }
                        );

                    } else {

                        /*
                         * Browser does not support geolocation.
                         */
                        this.createMap(
                            20,
                            0,
                            2
                        );
                    }
                },

                /*
                 * Create Leaflet map
                 */
                createMap(
                    latitude,
                    longitude,
                    zoom = 16
                ) {

                    if (this.map) {
                        return;
                    }

                    this.map = L.map(
                        this.$refs.map,
                        {
                            center: [
                                latitude,
                                longitude
                            ],
                            zoom: zoom,

                            /*
                             * Better zoom behavior
                             */
                            zoomControl: true,

                            /*
                             * Allow world wrapping
                             */
                            worldCopyJump: true,
                        }
                    );

                    /*
                     * ==========================================
                     * STANDARD MAP
                     * ==========================================
                     */
                    const standardLayer = L.tileLayer(
                        'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
                        {
                            attribution:
                                '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank">OpenStreetMap</a> contributors',

                            maxZoom: 19,

                            /*
                             * Browser caching
                             */
                            crossOrigin: true,
                        }
                    );

                    /*
                     * ==========================================
                     * TERRAIN
                     * ==========================================
                     *
                     * OpenTopoMap provides terrain/topographic
                     * information and labels.
                     */
                    const terrainLayer = L.tileLayer(
                        'https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png',
                        {
                            attribution:
                                'Map data &copy; <a href="https://www.openstreetmap.org/copyright" target="_blank">OpenStreetMap</a> contributors, <a href="https://opentopomap.org" target="_blank">OpenTopoMap</a>',

                            maxZoom: 17,

                            crossOrigin: true,
                        }
                    );

                    /*
                     * ==========================================
                     * SATELLITE
                     * ==========================================
                     *
                     * Esri World Imagery.
                     *
                     * This is satellite/aerial imagery.
                     */
                    const satelliteLayer = L.tileLayer(
                        'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}',
                        {
                            attribution:
                                'Tiles &copy; Esri',

                            maxZoom: 19,

                            crossOrigin: true,
                        }
                    );

                    /*
                     * ==========================================
                     * SATELLITE LABELS
                     * ==========================================
                     *
                     * Transparent reference layer containing
                     * roads, place names and boundaries.
                     *
                     * This makes the satellite view much easier
                     * to understand.
                     */
                    const labelsLayer = L.tileLayer(
                        'https://{s}.basemaps.cartocdn.com/light_only_labels/{z}/{x}/{y}.png',
                        {
                            attribution:
                                '&copy; <a href="https://carto.com/attributions" target="_blank">CARTO</a>',

                            maxZoom: 19,

                            pane: 'overlayPane',

                            crossOrigin: true,
                        }
                    );

                    /*
                     * ==========================================
                     * ADD DEFAULT LAYER
                     * ==========================================
                     */
                    standardLayer.addTo(this.map);

                    /*
                     * ==========================================
                     * LAYER CONTROL
                     * ==========================================
                     */
                    this.baseLayers = {
                        ['🗺 ' + @js(__('vitahr.map_standard'))]: standardLayer,
                        ['🏔 ' + @js(__('vitahr.map_terrain'))]: terrainLayer,
                        ['🛰 ' + @js(__('vitahr.map_satellite'))]: satelliteLayer,
                    };

                    const overlays = {
                        ['🏷 ' + @js(__('vitahr.map_place_names'))]: labelsLayer,
                    };

                    L.control.layers(
                        this.baseLayers,
                        overlays,
                        {
                            collapsed: false,
                            position: 'topright',
                        }
                    ).addTo(this.map);

                    /*
                     * ==========================================
                     * MARKER
                     * ==========================================
                     */
                    if (
                        this.latitude !== null &&
                        this.longitude !== null
                    ) {
                        this.createMarker(
                            this.latitude,
                            this.longitude
                        );
                    }

                    /*
                     * ==========================================
                     * MAP CLICK
                     * ==========================================
                     */
                    this.map.on(
                        'click',
                        (event) => {

                            this.setLocation(
                                event.latlng.lat,
                                event.latlng.lng
                            );
                        }
                    );

                    /*
                     * ==========================================
                     * FIX MAP SIZE
                     * ==========================================
                     */
                    setTimeout(() => {

                        if (this.map) {
                            this.map.invalidateSize();
                        }

                    }, 200);

                    this.initialized = true;
                },

                /*
                 * Create / move marker
                 */
                createMarker(
                    latitude,
                    longitude
                ) {

                    if (!this.map) {
                        return;
                    }

                    /*
                     * If marker already exists,
                     * simply move it.
                     */
                    if (this.marker) {

                        this.marker.setLatLng([
                            latitude,
                            longitude
                        ]);

                        return;
                    }

                    /*
                     * Create draggable marker.
                     */
                    this.marker = L.marker(
                        [
                            latitude,
                            longitude
                        ],
                        {
                            draggable: true,
                        }
                    ).addTo(this.map);

                    /*
                     * Marker drag event
                     */
                    this.marker.on(
                        'dragend',
                        () => {

                            const position =
                                this.marker.getLatLng();

                            this.setLocation(
                                position.lat,
                                position.lng
                            );
                        }
                    );
                },

                /*
                 * Set selected location
                 */
                setLocation(
                    latitude,
                    longitude
                ) {

                    latitude = parseFloat(
                        latitude.toFixed(7)
                    );

                    longitude = parseFloat(
                        longitude.toFixed(7)
                    );

                    this.latitude = latitude;
                    this.longitude = longitude;

                    /*
                     * Create or move marker.
                     */
                    this.createMarker(
                        latitude,
                        longitude
                    );

                    /*
                     * Center map.
                     */
                    if (this.map) {

                        this.map.setView(
                            [
                                latitude,
                                longitude
                            ],
                            Math.max(
                                this.map.getZoom(),
                                17
                            ),
                            {
                                animate: true,
                            }
                        );
                    }

                    /*
                     * Update Filament fields.
                     */
                    this.updateFilamentField(
                        'latitude',
                        latitude
                    );

                    this.updateFilamentField(
                        'longitude',
                        longitude
                    );
                },

                /*
                 * Update location from manually entered
                 * latitude / longitude.
                 */
                updateMapLocation(
                    latitude,
                    longitude
                ) {

                    if (!this.map) {
                        return;
                    }

                    this.createMarker(
                        latitude,
                        longitude
                    );

                    this.map.setView(
                        [
                            latitude,
                            longitude
                        ],
                        Math.max(
                            this.map.getZoom(),
                            17
                        ),
                        {
                            animate: true,
                        }
                    );
                },

                /*
                 * Update Filament form state
                 */
                updateFilamentField(
                    field,
                    value
                ) {

                    this.component.set(
                        `data.${field}`,
                        value
                    );
                },
            }));
        });
    </script>

@endonce
