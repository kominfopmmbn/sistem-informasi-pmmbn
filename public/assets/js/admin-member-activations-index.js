/**
 * Filter index aktivasi anggota: Select2 untuk status (nilai = MemberActivationStatus->value).
 */
'use strict';

$(function () {
  const $status = $('#filter_status');
  if (!$status.length || typeof $.fn.select2 === 'undefined') {
    return;
  }

  $status.wrap('<div class="position-relative"></div>');
  $status.select2({
    placeholder: $status.data('placeholder') || 'Semua status',
    allowClear: true,
    dropdownParent: $status.parent(),
    width: '100%'
  });
});
