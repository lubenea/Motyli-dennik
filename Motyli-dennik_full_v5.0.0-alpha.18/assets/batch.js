
// Batch page
(function(){
  const qs = s => document.querySelector(s);
  const rows = qs('#rows');
  const addBtn = qs('#addRow');
  const mapEl = qs('#map');

  if (mapEl) {
    var initView = JSON.parse(localStorage.getItem('mapView') || 'null') || {lat:48.1486, lng:17.1077, zoom:12};
    var map = L.map('map').setView([initView.lat, initView.lng], initView.zoom);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{maxZoom:19, attribution:'&copy; OpenStreetMap'}).addTo(map);
    var marker = null;
    function setPoint(latlng){
      if (marker) map.removeLayer(marker);
      marker = L.marker(latlng).addTo(map);
      document.getElementById('lat').value = latlng.lat.toFixed(6);
      document.getElementById('lng').value = latlng.lng.toFixed(6);
    }
    map.on('click', e => setPoint(e.latlng));
    map.on('moveend', () => {
      const c = map.getCenter();
      localStorage.setItem('mapView', JSON.stringify({lat:c.lat, lng:c.lng, zoom:map.getZoom()}));
    });
  }

  // locations
  const locDL = document.getElementById('loc_dl');
  if (locDL) {
    fetch('locations_api.php').then(r=>r.json()).then(names=>{
      names.forEach(n=>{ const o=document.createElement('option'); o.value=n; locDL.appendChild(o); });
    });
  }

  function rowTemplate(){
    const div = document.createElement('div');
    div.className = 'grid g4';
    div.innerHTML = `
      <label>Druh (SK)
        <input name="species_sk[]" class="sk" placeholder="Babočka pávooká">
        <datalist class="sk_dl"></datalist>
      </label>
      <label>Druh (LAT)
        <input name="species_lat[]" class="lat" placeholder="Aglais io">
        <datalist class="lat_dl"></datalist>
      </label>
      <label>Počet
        <input name="count[]" type="number" min="1" step="1" value="1">
      </label>
      <label>Stádium
        <select name="stage[]">
          <option value="imago">imago</option>
          <option value="pupa">pupa</option>
          <option value="larva">larva</option>
          <option value="egg">egg</option>
        </select>
      </label>
    `;
    const sk = div.querySelector('.sk'), lat = div.querySelector('.lat');
    const skDL = div.querySelector('.sk_dl'), latDL = div.querySelector('.lat_dl');
    function populate(inp, dl){
      const q=(inp.value||'').trim();
      fetch('species_api.php' + (q ? ('?q='+encodeURIComponent(q)) : ''))
        .then(r=>r.json()).then(list => {
          dl.innerHTML=''; (list||[]).slice(0,50).forEach(it => {
            if (dl===skDL && it.sk){ const o=document.createElement('option'); o.value=it.sk; dl.appendChild(o); }
            if (dl===latDL && it.lat){ const o=document.createElement('option'); o.value=it.lat; dl.appendChild(o); }
          });
        });
    }
    function linkFill(from, to, param){
      const v=(from.value||'').trim(); if (!v) return;
      fetch('species_api.php?' + param + '=' + encodeURIComponent(v))
        .then(r=>r.json()).then(obj => { if (obj && obj.sk && obj.lat && !(to.value||'').trim()) { to.value = (to===sk?obj.lat:obj.sk); } });
    }
    sk.addEventListener('input', ()=>{ populate(sk, skDL); linkFill(sk, lat, 'sk'); });
    lat.addEventListener('input', ()=>{ populate(lat, latDL); linkFill(lat, sk, 'lat'); });
    populate(sk, skDL); populate(lat, latDL);
    return div;
  }

  function addRow(){ rows.appendChild(rowTemplate()); }
  addBtn.addEventListener('click', addRow);
  for (let i=0;i<6;i++) addRow();
})();
