<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debug Map</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        #map { height: 400px; width: 100%; }
        .debug { padding: 20px; background: #f0f0f0; margin: 10px 0; }
    </style>
</head>
<body>
    <h1>Debug Map Test</h1>
    
    <div class="debug">
        <h3>API Test:</h3>
        <div id="api-status">Testing API...</div>
    </div>
    
    <div class="debug">
        <h3>Map Container:</h3>
        <div id="map"></div>
    </div>
    
    <div class="debug">
        <h3>Console Log:</h3>
        <div id="console-log"></div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        // Override console.log to show in page
        const originalLog = console.log;
        const originalError = console.error;
        const logDiv = document.getElementById('console-log');
        
        console.log = function(...args) {
            originalLog.apply(console, args);
            logDiv.innerHTML += '<div style="color: green;">LOG: ' + args.join(' ') + '</div>';
        };
        
        console.error = function(...args) {
            originalError.apply(console, args);
            logDiv.innerHTML += '<div style="color: red;">ERROR: ' + args.join(' ') + '</div>';
        };

        // Test API
        fetch('/api/properties/geojson')
            .then(response => {
                document.getElementById('api-status').innerHTML = 'API Status: ' + response.status + ' ' + response.statusText;
                return response.json();
            })
            .then(data => {
                console.log('API Response:', data);
                document.getElementById('api-status').innerHTML += '<br>Properties found: ' + data.features.length;
                
                // Initialize map
                const map = L.map('map').setView([-1.9441, 30.0619], 13);
                
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap contributors'
                }).addTo(map);
                
                // Add markers
                data.features.forEach(feature => {
                    const coords = feature.geometry.coordinates;
                    const property = feature.properties;
                    
                    L.marker([coords[1], coords[0]])
                        .addTo(map)
                        .bindPopup(`
                            <h3>${property.title}</h3>
                            <p>Price: ${property.price} RWF</p>
                            <p>Type: ${property.type}</p>
                        `);
                });
                
                console.log('Map initialized successfully');
            })
            .catch(error => {
                console.error('API Error:', error);
                document.getElementById('api-status').innerHTML = 'API Error: ' + error.message;
            });
    </script>
</body>
</html>
