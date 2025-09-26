// Basic Leaflet map to pick a point and fill lat/lng in the form
document.addEventListener('DOMContentLoaded', function(){
  try {
    var map = L.map('map').setView([48.1486, 17.1077], 13); // Bratislava
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{maxZoom:19}).addTo(map);
    var marker;
    map.on('click', function(e){
      if (marker) map.removeLayer(marker);
      marker = L.marker(e.latlng).addTo(map);
      document.getElementById('lat').value = e.latlng.lat;
      document.getElementById('lng').value = e.latlng.lng;
      // pluscode could be computed server-side; leave blank for now
      document.getElementById('pluscode').value = '';
    });
    // simple species autocomplete from species.json
    fetch('species.json').then(r=>r.json()).then(list=>{
      var inp = document.getElementById('species');
      inp.addEventListener('input', function(){
        var v = this.value.toLowerCase();
        var matches = list.filter(s=>s.sk.toLowerCase().includes(v));
        // naive dropdown by datalist
        var dl = document.getElementById('species_dl') || (function(){
          var d=document.createElement('datalist'); d.id='species_dl'; document.body.appendChild(d); inp.setAttribute('list','species_dl'); return d;
        })();
        dl.innerHTML = '';
        matches.slice(0,10).forEach(m=>{
          var opt = document.createElement('option'); opt.value = m.sk; dl.appendChild(opt);
        });
      });
    });
  } catch (e) {
    console.warn('Map init failed', e);
  }
});
