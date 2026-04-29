/**
 * Filter index kota/kabupaten: Select2 untuk provinsi (nilai = kode).
 */
'use strict';

$(function () {
  const $province = $('#filter_province_code');
  if (!$province.length || typeof $.fn.select2 === 'undefined') {
    return;
  }

  $province.wrap('<div class="position-relative"></div>');
  $province.select2({
    placeholder: $province.data('placeholder') || 'Semua provinsi',
    allowClear: true,
    dropdownParent: $province.parent(),
    width: '100%'
  });
});
