/**
 * Admin college form: provinsi (Select2) + kota (Select2 + AJAX per provinsi).
 */
'use strict';

$(function () {
  const $province = $('#college_province_id');
  const $city = $('#college_city_id');
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
            province_id: $province.val(),
            term: params.term,
            page: params.page || 1
          };
        },
        processResults: function (data) {
          return {
            results: data.results || [],
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
});
