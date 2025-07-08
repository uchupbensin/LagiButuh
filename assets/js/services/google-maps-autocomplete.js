// assets/js/services/autocomplete.js

function setupPhotonAutocomplete({ inputId, latId, lngId, resultListId }) {
  const input = document.getElementById(inputId);
  const latField = document.getElementById(latId);
  const lngField = document.getElementById(lngId);
  const resultList = document.getElementById(resultListId);

  if (!input || !latField || !lngField || !resultList) {
    console.warn(`[Photon] Salah satu elemen tidak ditemukan untuk ${inputId}`);
    return;
  }

  let timeout = null;

  input.addEventListener('input', () => {
    const query = input.value;
    clearTimeout(timeout);
    timeout = setTimeout(() => {
      if (query.length < 3) return;

      fetch(`https://photon.komoot.io/api/?q=${encodeURIComponent(query)}&limit=5&lang=id`)
        .then(res => res.json())
        .then(data => {
          resultList.innerHTML = '';
          data.features.forEach((feature) => {
            const li = document.createElement('li');
            const city = feature.properties.city || feature.properties.state || '';
            li.textContent = `${feature.properties.name}${city ? ', ' + city : ''}`;
            li.classList.add('photon-item');
            li.addEventListener('click', () => {
              input.value = li.textContent;
              latField.value = feature.geometry.coordinates[1];
              lngField.value = feature.geometry.coordinates[0];
              resultList.innerHTML = '';

              console.log(`[Photon] Dipilih: ${li.textContent}`, {
                lat: latField.value,
                lng: lngField.value
              });
            });
            resultList.appendChild(li);
          });
        })
        .catch(err => {
          console.error('[Photon] Error fetch:', err);
        });
    }, 300);
  });

  document.addEventListener('click', (e) => {
    if (!resultList.contains(e.target) && e.target !== input) {
      resultList.innerHTML = '';
    }
  });
}

function validateLatLngFields(formId, fields = []) {
  const form = document.getElementById(formId);
  if (!form) return;

  form.addEventListener('submit', function (e) {
    for (let f of fields) {
      const lat = document.getElementById(f.lat);
      const lng = document.getElementById(f.lng);
      if (!lat?.value || !lng?.value) {
        alert(`Silakan pilih lokasi ${f.label} dari hasil pencarian.`);
        e.preventDefault();
        return;
      }
    }
  });
}
