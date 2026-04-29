/**
 * Form wilayah admin: Select2 default + cascade provinsi → kota (kode) untuk kecamatan,
 * dan provinsi → kota → kecamatan (kode) untuk desa.
 */
'use strict';

$(function () {
  if (typeof $.fn.select2 === 'undefined') {
    return;
  }

  const skipGlobalSelect2Ids = [
    'region_district_city',
    'region_district_province_helper',
    'region_village_city',
    'region_village_district',
    'region_village_province_helper'
  ];

  $('.select2').each(function () {
    const $el = $(this);
    const id = $el.attr('id');
    if (id && skipGlobalSelect2Ids.indexOf(id) >= 0) {
      return;
    }
    if ($el.data('regions-forms-inited')) {
      return;
    }
    $el.wrap('<div class="position-relative"></div>');
    $el.select2({
      placeholder: $el.data('placeholder') || '',
      allowClear: !!$el.data('allow-clear'),
      dropdownParent: $el.parent(),
      width: '100%'
    });
    $el.data('regions-forms-inited', true);
  });

  initDistrictCityCascade();
  initVillageCascade();
});

function initDistrictCityCascade() {
  const $province = $('#region_district_province_helper');
  const $city = $('#region_district_city');
  const citiesUrl = $city.data('search-url');
  if (!$province.length || !$city.length || !citiesUrl) {
    return;
  }

  $province.wrap('<div class="position-relative"></div>');
  $city.wrap('<div class="position-relative"></div>');

  $province.select2({
    placeholder: 'Pilih provinsi',
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
  }

  function initCitySelect2() {
    destroyCity();
    syncCityDisabled();
    if (!$province.val()) {
      return;
    }

    $city.select2({
      placeholder: 'Pilih kota/kabupaten',
      allowClear: false,
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

  $province.on('change', function () {
    destroyCity();
    $city.empty().append($('<option></option>', { value: '' }));
    syncCityDisabled();
    if ($province.val()) {
      initCitySelect2();
    }
  });

  if ($province.val()) {
    initCitySelect2();
  }
}

function initVillageCascade() {
  const $province = $('#region_village_province_helper');
  const $city = $('#region_village_city');
  const $district = $('#region_village_district');
  const citiesUrl = $city.data('search-url');
  const districtsUrl = $district.data('search-url');
  if (!$province.length || !$city.length || !$district.length || !citiesUrl || !districtsUrl) {
    return;
  }

  $province.wrap('<div class="position-relative"></div>');
  $city.wrap('<div class="position-relative"></div>');
  $district.wrap('<div class="position-relative"></div>');

  $province.select2({
    placeholder: 'Pilih provinsi',
    allowClear: false,
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
      placeholder: 'Pilih kota/kabupaten',
      allowClear: false,
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
      placeholder: 'Pilih kecamatan',
      allowClear: false,
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

  if ($province.val()) {
    initCitySelect2();
  }
  if ($city.val()) {
    initDistrictSelect2();
  }
}
