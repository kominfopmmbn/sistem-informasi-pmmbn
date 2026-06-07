/**
 * Admin member form: Dropzone dokumen pendukung (sinkron ke input file), Select2 kota tempat lahir & gender.
 */
'use strict';

var memberSupportingPreviewTemplate = `<div class="dz-preview dz-file-preview">
<div class="dz-details">
  <div class="dz-thumbnail">
    <img data-dz-thumbnail>
    <span class="dz-nopreview">No preview</span>
    <div class="dz-success-mark"></div>
    <div class="dz-error-mark"></div>
    <div class="dz-error-message"><span data-dz-errormessage></span></div>
    <div class="progress">
      <div class="progress-bar progress-bar-primary" role="progressbar" aria-valuemin="0" aria-valuemax="100" data-dz-uploadprogress></div>
    </div>
  </div>
  <div class="dz-filename" data-dz-name></div>
  <div class="dz-size" data-dz-size></div>
</div>
</div>`;

$(function () {
  const $form = $('#member-form');

  if (typeof $.fn.select2 !== 'undefined') {
    const $gender = $('#member_gender_id');
    if ($gender.length) {
      $gender.wrap('<div class="position-relative"></div>');
      $gender.select2({
        placeholder: $gender.data('placeholder') || '—',
        allowClear: true,
        dropdownParent: $gender.parent(),
        width: '100%'
      });
    }
  }

  if (typeof Dropzone !== 'undefined') {
    Dropzone.autoDiscover = false;
    const dzEl = document.querySelector('#member-supporting-dropzone');
    const fileInput = document.getElementById('member_supporting_documents');
    if (dzEl && fileInput) {
      const maxFiles = parseInt(dzEl.dataset.maxFiles || '10', 10);
      const maxFilesizeMb = parseFloat(dzEl.dataset.maxFilesizeMb || '10');
      const acceptedFiles = dzEl.dataset.acceptedFiles || '';

      const dz = new Dropzone(dzEl, {
        url: window.location.pathname,
        autoProcessQueue: false,
        uploadMultiple: true,
        parallelUploads: Math.max(1, maxFiles),
        maxFiles: maxFiles,
        maxFilesize: maxFilesizeMb,
        acceptedFiles: acceptedFiles,
        addRemoveLinks: true,
        previewTemplate: memberSupportingPreviewTemplate,
        init: function () {
          if (this.hiddenFileInput && this.hiddenFileInput.parentNode) {
            this.hiddenFileInput.remove();
          }
          const syncInput = () => {
            const dt = new DataTransfer();
            dz.files.forEach(function (f) {
              dt.items.add(f);
            });
            fileInput.files = dt.files;
          };
          this.on('addedfile', syncInput);
          this.on('removedfile', syncInput);
        }
      });
    }
  }

  const $city = $('#member_place_of_birth_code');
  if ($city.length && typeof $.fn.select2 !== 'undefined') {
    const searchUrl = $city.data('search-url');
    if (searchUrl) {
      $city.wrap('<div class="position-relative"></div>');
      $city.select2({
        placeholder: $city.data('placeholder') || 'Pilih kota/kabupaten',
        allowClear: !$city.prop('required'),
        dropdownParent: $city.parent(),
        width: '100%',
        minimumInputLength: 0,
        ajax: {
          url: searchUrl,
          dataType: 'json',
          delay: 250,
          data: function (params) {
            return {
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
  }

  const $village = $('#member_village_code');
  if ($village.length && typeof $.fn.select2 !== 'undefined') {
    const villageSearchUrl = $village.data('search-url');
    if (villageSearchUrl) {
      $village.wrap('<div class="position-relative"></div>');
      $village.select2({
        placeholder: $village.data('placeholder') || 'Cari desa/kelurahan (nama atau kode pos)',
        allowClear: !$village.prop('required'),
        dropdownParent: $village.parent(),
        width: '100%',
        minimumInputLength: 0,
        ajax: {
          url: villageSearchUrl,
          dataType: 'json',
          delay: 250,
          data: function (params) {
            return {
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
  }

  const $college = $('#member_college_id');
  if ($college.length && typeof $.fn.select2 !== 'undefined') {
    const collegeSearchUrl = $college.data('search-url');
    if (collegeSearchUrl) {
      $college.wrap('<div class="position-relative"></div>');
      $college.select2({
        placeholder: $college.data('placeholder') || 'Pilih perguruan tinggi',
        allowClear: !$college.prop('required'),
        dropdownParent: $college.parent(),
        width: '100%',
        minimumInputLength: 0,
        ajax: {
          url: collegeSearchUrl,
          dataType: 'json',
          delay: 250,
          data: function (params) {
            return {
              q: params.term,
              page: params.page || 1
            };
          },
          processResults: function (data) {
            const rows = (data.results || []).map(function (item) {
              return { id: item.id, text: item.text };
            });
            return {
              results: rows,
              pagination: { more: !!(data.pagination && data.pagination.more) }
            };
          }
        }
      });
    }
  }
});
