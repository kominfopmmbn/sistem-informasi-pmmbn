/**
 * Filter index desa: provinsi + kota + kecamatan (AJAX berantai).
 */
'use strict';

$(function () {
  const $province = $('#filter_province_code');
  const $city = $('#filter_city_code');
  const $district = $('#filter_district_code');
  const $form = $('#villages-index-filter-form');
  const citiesUrl = $city.data('search-url');
  const districtsUrl = $district.data('search-url');

  if (
    !$province.length ||
    !$city.length ||
    !$district.length ||
    typeof $.fn.select2 === 'undefined' ||
    !citiesUrl ||
    !districtsUrl
  ) {
    return;
  }

  $province.wrap('<div class="position-relative"></div>');
  $city.wrap('<div class="position-relative"></div>');
  $district.wrap('<div class="position-relative"></div>');

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

  function destroyDistrict() {
    if ($district.data('select2')) {
      $district.select2('destroy');
    }
  }

  function initCitySelect2() {
    destroyCity();
    $city.prop('disabled', !$province.val());
    if (!$province.val()) {
      $city.val(null).empty().append($('<option></option>', { value: '' }));
      return;
    }

    $city.select2({
      placeholder: 'Semua kota/kabupaten',
      allowClear: true,
      dropdownParent: $city.parent(),
      width: '100%',
      minimumInputLength: 0,
      ajax: {
        url: citiesUrl,
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

  function initDistrictSelect2() {
    destroyDistrict();
    $district.prop('disabled', !$city.val());
    if (!$city.val()) {
      $district.val(null).empty().append($('<option></option>', { value: '' }));
      return;
    }

    $district.select2({
      placeholder: 'Semua kecamatan',
      allowClear: true,
      dropdownParent: $district.parent(),
      width: '100%',
      minimumInputLength: 0,
      ajax: {
        url: districtsUrl,
        dataType: 'json',
        delay: 250,
        data: function (params) {
          return {
            city_code: $city.val(),
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
    destroyDistrict();
    $city.empty().append($('<option></option>', { value: '' }));
    $district.empty().append($('<option></option>', { value: '' }));
    initCitySelect2();
    initDistrictSelect2();
  });

  $city.on('change', function () {
    destroyDistrict();
    $district.empty().append($('<option></option>', { value: '' }));
    initDistrictSelect2();
  });

  if ($form.length) {
    $form.on('submit', function () {
      $city.prop('disabled', false);
      $district.prop('disabled', false);
    });
  }

  if ($province.val()) {
    initCitySelect2();
  }
  if ($province.val() && $city.val()) {
    initDistrictSelect2();
  }
});
