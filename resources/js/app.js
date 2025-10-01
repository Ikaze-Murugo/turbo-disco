import './bootstrap';

import Alpine from 'alpinejs';
import mapboxgl from 'mapbox-gl';

// Make Mapbox GL available globally
window.mapboxgl = mapboxgl;

window.Alpine = Alpine;

Alpine.start();
