function initMap() {
    var defaultLocation = { lat: -6.914744, lng: 107.609810 }; // Default ke Bandung
    var map = new google.maps.Map(document.getElementById('map'), {
        center: defaultLocation,
        zoom: 12
    });

    if (!lapanganList || lapanganList.length === 0) {
        console.warn("Tidak ada data lapangan.");
        return;
    }

    var markers = [];
    lapanganList.forEach(function (lapangan) {
        var marker = new google.maps.Marker({
            position: { lat: parseFloat(lapangan.latitude), lng: parseFloat(lapangan.longitude) },
            map: map,
            title: lapangan.nama
        });

        var infoWindow = new google.maps.InfoWindow({
            content: '<div style="max-width: 250px;">' +
                     '<h6>' + lapangan.nama + '</h6>' +
                     '<p><strong>Lokasi:</strong> ' + lapangan.lokasi + '</p>' +
                     '<p><strong>Harga:</strong> Rp ' + new Intl.NumberFormat('id-ID').format(lapangan.harga_per_jam) + ' / jam</p>' +
                     '<p><strong>Jam Operasional:</strong> ' + lapangan.jam_mulai + ' - ' + lapangan.jam_selesai + '</p>' +
                     '<a href="detail_lapangan.php?id=' + lapangan.id + '" class="btn btn-primary btn-sm">Lihat Detail</a>' +
                     '</div>'
        });

        marker.addListener('click', function () {
            infoWindow.open(map, marker);
        });

        markers.push(marker);
    });

    // Fungsi untuk fokus ke marker tertentu saat tombol "Lihat di Peta" ditekan
    window.focusMarker = function (lat, lng) {
        map.setCenter({ lat: lat, lng: lng });
        map.setZoom(15);
    };
}
