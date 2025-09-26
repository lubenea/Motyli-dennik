
// Quick form logic
(function(){
  const qs = s => document.querySelector(s);
  const mapEl = qs('#map');
  const form = qs('#obsForm');

  // Stage pills
  document.querySelectorAll('.pill').forEach(p => {
    p.addEventListener('click', () => {
      document.querySelectorAll('.pill').forEach(x=>x.classList.remove('active'));
      p.classList.add('active');
      const r = p.querySelector('input'); if (r) r.checked = true;
    });
  });

  // Map with memory + fill lat/lng
  if (mapEl) {
    var initView = JSON.parse(localStorage.getItem('mapView') || 'null') || {lat:48.1486, lng:17.1077, zoom:12};
    var map = L.map('map').setView([initView.lat, initView.lng], initView.zoom);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{maxZoom:19, attribution:'&copy; OpenStreetMap'}).addTo(map);
    var marker = null;
    function setPoint(latlng){
      if (marker) map.removeLayer(marker);
      marker = L.marker(latlng).addTo(map);
      qs('#lat').value = latlng.lat.toFixed(6);
      qs('#lng').value = latlng.lng.toFixed(6);
    }
    map.on('click', e => setPoint(e.latlng));
    map.on('moveend', () => {
      const c = map.getCenter();
      localStorage.setItem('mapView', JSON.stringify({lat:c.lat, lng:c.lng, zoom:map.getZoom()}));
    });
  }

  // Locations datalist
  const locDL = qs('#loc_dl');
  if (locDL) {
    fetch('locations_api.php').then(r=>r.json()).then(names=>{
      names.forEach(n=>{ const o=document.createElement('option'); o.value=n; locDL.appendChild(o); });
    });
  }

  // Species SK/LAT bidirectional
  const sk = qs('#species_sk'), lat = qs('#species_lat');
  const skDL = qs('#species_sk_dl'), latDL = qs('#species_lat_dl');
  function populate(inp, dl){
    const q = (inp.value||'').trim();
    fetch('species_api.php' + (q ? ('?q='+encodeURIComponent(q)) : ''))
      .then(r=>r.json()).then(list => {
        dl.innerHTML='';
        (list||[]).slice(0,50).forEach(it => {
          if (dl===skDL && it.sk){ const o=document.createElement('option'); o.value=it.sk; dl.appendChild(o); }
          if (dl===latDL && it.lat){ const o=document.createElement('option'); o.value=it.lat; dl.appendChild(o); }
        });
      }).catch(()=>{});
  }
  function linkFill(from, to, param){
    const v=(from.value||'').trim(); if (!v) return;
    fetch('species_api.php?' + param + '=' + encodeURIComponent(v))
      .then(r=>r.json()).then(obj => { if (obj && obj.sk && obj.lat && !(to.value||'').trim()) { to.value = (to===sk?obj.lat:obj.sk); } })
      .catch(()=>{});
  }
  if (sk && lat) {
    sk.addEventListener('input', ()=>{ populate(sk, skDL); linkFill(sk, lat, 'sk'); });
    lat.addEventListener('input', ()=>{ populate(lat, latDL); linkFill(lat, sk, 'lat'); });
    populate(sk, skDL); populate(lat, latDL);
  }

  // Duplicate check
  if (form) {
    form.addEventListener('submit', function(ev){
      if (!form.reportValidity || !form.reportValidity()) { ev.preventDefault(); return; }
      ev.preventDefault();
      const fd = new FormData(form);
      fetch('check_duplicate.php', {method:'POST', body:fd})
        .then(r=>r.json()).then(j=>{
          if (j && j.duplicate && !j.ok) {
            if (confirm('Vyzerá to ako duplikát (deň/miesto/druh). Uložiť aj tak?')) { document.getElementById('dup_ok').value='1'; form.submit(); }
          } else { form.submit(); }
        }).catch(()=>form.submit());
    });
    document.addEventListener('keydown', function(e){
      if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
        const btn=document.querySelector('button[name="next"]'); if (btn) btn.click();
      }
    });
  }
})();
