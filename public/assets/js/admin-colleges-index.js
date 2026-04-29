/**
 * Index colleges: filter provinsi (Select2) + kota (Select2 + AJAX per provinsi).
 */
'use strict';

$(function () {
  const $province = $('#filter_province_code');
  const $city = $('#filter_city_code');
  const $form = $('#colleges-index-filter-form');

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
    placeholder: $province.data('placeholder') || 'Semua provinsi',
    allowClear: true,
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
  }

  function initCitySelect2() {
    destroyCity();
    syncCityDisabled();
    if (!$province.val()) {
      return;
    }

    $city.select2({
      placeholder: $province.val() ? 'Semua kota/kabupaten' : 'Pilih provinsi terlebih dahulu untuk memfilter kota/kabupaten.',
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
});
