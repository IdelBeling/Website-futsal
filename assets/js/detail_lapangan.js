function initMap() {
    var map = new google.maps.Map(document.getElementById("map"), {
        center: lapanganLocation,
        zoom: 14
    });

    var marker = new google.maps.Marker({
        position: lapanganLocation,
        map: map,
        title: "Lapangan Futsal"
    });

    window.map = map;
    window.marker = marker;
}

// Fungsi untuk mendapatkan lokasi pengguna dan menampilkan rute ke lapangan
function getUserLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function (position) {
                var userLocation = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude
                };

                var directionsService = new google.maps.DirectionsService();
                var directionsRenderer = new google.maps.DirectionsRenderer();
                directionsRenderer.setMap(window.map);

                var request = {
                    origin: userLocation,
                    destination: lapanganLocation,
                    travelMode: 'DRIVING'
                };

                directionsService.route(request, function (result, status) {
                    if (status == 'OK') {
                        directionsRenderer.setDirections(result);
                    } else {
                        alert("Gagal mendapatkan rute.");
                    }
                });
            },
            function () {
                alert("Gagal mendapatkan lokasi. Periksa izin lokasi di browser.");
            }
        );
    } else {
        alert("Browser tidak mendukung fitur lokasi.");
    }
}
