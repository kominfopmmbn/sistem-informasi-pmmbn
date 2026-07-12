/**
 * Admin college form: provinsi (Select2) + kota (Select2 + AJAX per provinsi).
 */
'use strict';

$(function () {
  initCollegeLocationMap();

  const $province = $('#college_province_code');
  const $city = $('#college_city_code');
  const $form = $('#college-form');
  const $cityHint = $('#college_city_hint');

  function updateCityHint() {
    if ($cityHint.length) {
      $cityHint.toggleClass('d-none', !!$province.val());
    }
  }

  if (!$province.length || !$city.length || typeof $.fn.select2 === 'undefined') {
    return;
  }

  const searchUrl = $city.data('search-url');
  if (!searchUrl) {
    return;
  }

  $province.wrap('<div class="position-relative"></div>');
  $city.wrap('<div class="position-relative"></div>');

  $province.select2({
    placeholder: $province.data('placeholder') || 'Pilih provinsi',
    allowClear: false,
    dropdownParent: $province.parent(),
    width: '100%'
  });

  function destroyCity() {
    if ($city.data('select2')) {
      $city.select2('destroy');
    }
  }

  function syncCityDisabled() {
    const hasProvince = !!$province.val();
    $city.prop('disabled', !hasProvince);
    if (!hasProvince) {
      $city.val(null);
    }
    updateCityHint();
  }

  function initCitySelect2() {
    destroyCity();
    syncCityDisabled();
    if (!$province.val()) {
      return;
    }

    $city.select2({
      placeholder: $city.data('placeholder') || 'Pilih kota/kabupaten',
      allowClear: true,
      dropdownParent: $city.parent(),
      width: '100%',
      minimumInputLength: 0,
      ajax: {
        url: searchUrl,
        dataType: 'json',
        delay: 250,
        data: function (params) {
          return {
            province_code: $province.val(),
            q: params.term,
            page: params.page || 1
          };
        },
        processResults: function (data) {
          const rows = (data.results || []).map(function (item) {
            return { id: item.code, text: item.text };
          });
          return {
            results: rows,
            pagination: { more: !!(data.pagination && data.pagination.more) }
          };
        }
      }
    });
  }

  $province.on('change', function () {
    destroyCity();
    $city.empty().append($('<option></option>', { value: '' }));
    syncCityDisabled();
    if ($province.val()) {
      initCitySelect2();
    }
  });

  if ($form.length) {
    $form.on('submit', function () {
      $city.prop('disabled', false);
    });
  }

  if ($province.val()) {
    initCitySelect2();
  }

  updateCityHint();

  function initCollegeLocationMap() {
    if (typeof L === 'undefined' || !document.getElementById('college_map')) {
      return;
    }

    const $lat = $('#college_lat');
    const $long = $('#college_long');
    L.Icon.Default.imagePath = '/assets/vendor/libs/leaflet/images/';

    function parseCoord(value, min, max) {
      const num = parseFloat(value);
      if (isNaN(num) || num < min || num > max) {
        return null;
      }
      return num;
    }

    function currentLatLng() {
      const lat = parseCoord($lat.val(), -90, 90);
      const lng = parseCoord($long.val(), -180, 180);
      if (lat === null || lng === null) {
        return null;
      }
      return { lat: lat, lng: lng };
    }

    const initial = currentLatLng();
    const center = initial ? [initial.lat, initial.lng] : [-2.5, 118];
    const zoom = initial ? 15 : 5;

    const map = L.map('college_map').setView(center, zoom);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 19,
      attribution: '&copy; OpenStreetMap'
    }).addTo(map);

    let marker = null;

    function placeMarker(latlng) {
      if (marker) {
        marker.setLatLng(latlng);
      } else {
        marker = L.marker(latlng, { draggable: true }).addTo(map);
        marker.on('dragend', function () {
          setInputs(marker.getLatLng());
        });
      }
    }

    function setInputs(latlng) {
      $lat.val(latlng.lat.toFixed(7));
      $long.val(latlng.lng.toFixed(7));
    }

    if (initial) {
      placeMarker(initial);
    }

    map.on('click', function (e) {
      placeMarker(e.latlng);
      setInputs(e.latlng);
    });

    $lat.add($long).on('input change', function () {
      const latlng = currentLatLng();
      if (latlng) {
        placeMarker(latlng);
        map.panTo([latlng.lat, latlng.lng]);
      }
    });

    const $search = $('#college_map_search');
    const $searchBtn = $('#college_map_search_btn');
    const $results = $('#college_map_search_results');

    function renderResults(rows) {
      $results.empty();
      if (!rows.length) {
        $results.append(
          $('<li class="list-group-item text-body-secondary small mb-0"></li>').text('Tidak ada hasil ditemukan.')
        );
        $results.removeClass('d-none');
        return;
      }
      rows.forEach(function (row) {
        const $item = $('<button type="button" class="list-group-item list-group-item-action small text-wrap"></button>')
          .text(row.display_name);
        $item.on('click', function () {
          const latlng = { lat: parseFloat(row.lat), lng: parseFloat(row.lon) };
          map.setView([latlng.lat, latlng.lng], 16);
          placeMarker(latlng);
          setInputs(latlng);
          $search.val(row.display_name);
          $results.addClass('d-none').empty();
        });
        $results.append($item);
      });
      $results.removeClass('d-none');
    }

    function doSearch() {
      const q = $.trim($search.val());
      if (!q) {
        return;
      }
      $searchBtn.prop('disabled', true);
      $.getJSON('https://nominatim.openstreetmap.org/search', {
        format: 'json',
        limit: 5,
        countrycodes: 'id',
        q: q
      })
        .done(function (data) {
          renderResults(data || []);
        })
        .fail(function () {
          renderResults([]);
        })
        .always(function () {
          $searchBtn.prop('disabled', false);
        });
    }

    $searchBtn.on('click', doSearch);
    $search.on('keydown', function (e) {
      if (e.key === 'Enter') {
        e.preventDefault();
        doSearch();
      }
    });
    $(document).on('click', function (e) {
      if (!$(e.target).closest('#college_map_search_results, #college_map_search, #college_map_search_btn').length) {
        $results.addClass('d-none');
      }
    });

    setTimeout(function () {
      map.invalidateSize();
    }, 200);
  }
});
