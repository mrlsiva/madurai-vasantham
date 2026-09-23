{{--
    Punch location popup, opened by admin.partials.location-button.
    Map: Leaflet + OpenStreetMap tiles. Address: OpenStreetMap Nominatim
    reverse geocoding, looked up in the browser when the popup opens.
--}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.css">
<script src="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.js"></script>

<style>
    .loc-map { height: 320px; border-radius: 12px; }
    .loc-card { border: 1px solid #e5e7eb; border-radius: 12px; padding: 14px 16px; margin-top: 12px; }
    .loc-card .loc-label { color: #6b7280; font-size: 13px; }
    .loc-box { background: #f3f4f6; border-radius: 10px; padding: 10px; text-align: center; }
    .loc-box .loc-label { display: block; }
    .loc-box strong { font-size: 17px; }
    .loc-photo { width: 64px; height: 64px; object-fit: cover; border-radius: 8px; }
    .loc-dot { display: inline-block; width: 10px; height: 10px; border-radius: 50%; margin-right: 4px; }
</style>

<div class="modal fade" id="locationModal" tabindex="-1" aria-labelledby="locationModalTitle" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="locationModalTitle">Location</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="locMap" class="loc-map"></div>
        <div class="small text-muted mt-1">
            <span class="loc-dot" style="background:#16a34a"></span>Punch in
            <span class="loc-dot ms-3" style="background:#dc2626"></span>Punch out
        </div>

        <div class="loc-card">
            <div class="d-flex">
                <i class="fa-solid fa-location-dot text-success fs-4 me-3 mt-1"></i>
                <div>
                    <div class="loc-label">Nearest address</div>
                    <div id="locAddress1" class="fw-bold">Looking up address…</div>
                    <div id="locAddress2"></div>
                </div>
            </div>
            <div class="row g-2 mt-2">
                <div class="col-6"><div class="loc-box"><span class="loc-label">Latitude</span><strong id="locLat"></strong></div></div>
                <div class="col-6"><div class="loc-box"><span class="loc-label">Longitude</span><strong id="locLng"></strong></div></div>
            </div>
        </div>

        <div class="loc-card">
            <div class="d-flex align-items-center">
                <img id="locPhoto" class="loc-photo me-3" src="" alt="Selfie" onerror="this.style.display='none'">
                <div class="row flex-grow-1 g-2">
                    <div class="col-6 col-md-4"><div class="loc-label">Staff</div><div id="locName" class="fw-semibold"></div></div>
                    <div class="col-6 col-md-4"><div class="loc-label">Date</div><div id="locDate"></div></div>
                    <div class="col-6 col-md-4"><div class="loc-label" id="locTimeLabel">Time</div><div id="locTime"></div></div>
                    <div class="col-6 col-md-4"><div class="loc-label">Shift</div><div id="locShift"></div></div>
                    <div class="col-12 col-md-8"><div class="loc-label" id="locDistanceLabel">Distance</div><div id="locDistance"></div></div>
                </div>
            </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary btn-sm" id="locCopy"><i class="fa-regular fa-copy"></i> Copy coordinates</button>
        <a href="#" target="_blank" rel="noopener" class="btn btn-primary btn-sm" id="locGoogle"><i class="fa-solid fa-map-location-dot"></i> Open in Google Maps</a>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var modalEl = document.getElementById('locationModal');
    var map = null, layer = null, current = null;
    var addressCache = {};

    function el(id) { return document.getElementById(id); }

    /* Straight-line distance in metres between two points */
    function distance(lat1, lng1, lat2, lng2) {
        var R = 6371000, toRad = Math.PI / 180;
        var dLat = (lat2 - lat1) * toRad, dLng = (lng2 - lng1) * toRad;
        var a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                Math.cos(lat1 * toRad) * Math.cos(lat2 * toRad) * Math.sin(dLng / 2) * Math.sin(dLng / 2);
        return 2 * R * Math.asin(Math.sqrt(a));
    }

    function formatDistance(m) {
        return m < 1000 ? Math.round(m) + ' m' : (m / 1000).toFixed(2) + ' km';
    }

    function showAddress(key, data) {
        var a = (data && data.address) || {};
        var line1 = [a.road || a.neighbourhood || a.hamlet, a.suburb || a.village || a.town].filter(Boolean).join(', ');
        var line2 = [a.city || a.town || a.county || a.state_district, a.state, a.postcode].filter(Boolean).join(', ') + (a.country ? ', ' + a.country : '');
        if (current !== key) return; // popup moved on to another punch
        el('locAddress1').textContent = line1 || (data && data.display_name) || 'Address not found';
        el('locAddress2').textContent = line1 ? line2 : '';
    }

    function lookupAddress(lat, lng) {
        var key = lat + ',' + lng;
        if (addressCache[key]) { showAddress(key, addressCache[key]); return; }
        el('locAddress1').textContent = 'Looking up address…';
        el('locAddress2').textContent = '';
        fetch('https://nominatim.openstreetmap.org/reverse?format=jsonv2&zoom=18&addressdetails=1&lat=' + lat + '&lon=' + lng, { headers: { 'Accept': 'application/json' } })
            .then(function (r) { return r.json(); })
            .then(function (data) { addressCache[key] = data; showAddress(key, data); })
            .catch(function () { if (current === key) el('locAddress1').textContent = 'Address lookup failed'; });
    }

    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.show-location');
        if (!btn) return;

        var d = btn.dataset;
        var isIn = d.kind === 'in';
        var lat = parseFloat(d.lat), lng = parseFloat(d.lng);
        var oLat = parseFloat(d.otherLat), oLng = parseFloat(d.otherLng);
        var hasOther = !isNaN(oLat) && !isNaN(oLng);
        current = lat + ',' + lng;

        el('locationModalTitle').textContent = (isIn ? 'Punch in' : 'Punch out') + ' location - ' + d.name;
        el('locLat').textContent = lat.toFixed(6);
        el('locLng').textContent = lng.toFixed(6);
        el('locName').textContent = d.name || '-';
        el('locDate').textContent = d.date || '-';
        el('locTimeLabel').textContent = isIn ? 'Punch in time' : 'Punch out time';
        el('locTime').textContent = d.time || '-';
        el('locShift').textContent = d.shift || '-';
        el('locDistanceLabel').textContent = isIn ? 'Punch out was' : 'Punch in was';
        el('locDistance').textContent = hasOther ? formatDistance(distance(lat, lng, oLat, oLng)) + ' away from here' : 'No location recorded';
        el('locPhoto').src = d.photo || '';
        el('locPhoto').style.display = d.photo ? '' : 'none';
        el('locGoogle').href = 'https://www.google.com/maps/search/?api=1&query=' + lat + ',' + lng;
        el('locCopy').dataset.coords = lat.toFixed(6) + ', ' + lng.toFixed(6);

        lookupAddress(lat, lng);

        modalEl.dataset.lat = lat; modalEl.dataset.lng = lng;
        modalEl.dataset.oLat = hasOther ? oLat : ''; modalEl.dataset.oLng = hasOther ? oLng : '';
        modalEl.dataset.isIn = isIn ? '1' : '';
        bootstrap.Modal.getOrCreateInstance(modalEl).show();
    });

    /* Draw the map once the popup is visible, so Leaflet can measure it */
    modalEl.addEventListener('shown.bs.modal', function () {
        var ds = modalEl.dataset;
        var here = [parseFloat(ds.lat), parseFloat(ds.lng)];
        var isIn = ds.isIn === '1';

        if (!map) {
            map = L.map('locMap');
            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
            }).addTo(map);
        }
        if (layer) layer.remove();
        layer = L.layerGroup().addTo(map);
        map.invalidateSize();

        var mark = function (pos, punchIn, open) {
            var m = L.circleMarker(pos, { radius: 9, weight: 3, color: '#fff', fillColor: punchIn ? '#16a34a' : '#dc2626', fillOpacity: 1 })
                .bindTooltip(punchIn ? 'Punch in' : 'Punch out', { direction: 'top', offset: [0, -8], permanent: open })
                .addTo(layer);
            return m;
        };
        mark(here, isIn, true);

        if (ds.oLat !== '' && ds.oLng !== '') {
            var other = [parseFloat(ds.oLat), parseFloat(ds.oLng)];
            mark(other, !isIn, false);
            L.polyline([here, other], { color: '#6b7280', weight: 2, dashArray: '4 6' }).addTo(layer);
            map.fitBounds([here, other], { padding: [50, 50], maxZoom: 17 });
        } else {
            map.setView(here, 16);
        }
    });

    el('locCopy').addEventListener('click', function () {
        var btn = this, text = btn.dataset.coords;
        var done = function () {
            btn.innerHTML = '<i class="fa-solid fa-check"></i> Copied';
            setTimeout(function () { btn.innerHTML = '<i class="fa-regular fa-copy"></i> Copy coordinates'; }, 1200);
        };
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(text).then(done);
        } else {
            var tmp = document.createElement('textarea');
            tmp.value = text; document.body.appendChild(tmp); tmp.select();
            document.execCommand('copy'); document.body.removeChild(tmp);
            done();
        }
    });
});
</script>
