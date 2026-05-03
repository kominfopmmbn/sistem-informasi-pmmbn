/**
 * Admin member form: Dropzone dokumen pendukung (sinkron ke input file), Select2 provinsi/kota, gender & wilayah org.
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
    const $orgRegion = $('#member_org_region_id');
    [$gender, $orgRegion].forEach(function ($el) {
      if (!$el.length) {
        return;
      }
      $el.wrap('<div class="position-relative"></div>');
      $el.select2({
        placeholder: $el.data('placeholder') || '—',
        allowClear: true,
        dropdownParent: $el.parent(),
        width: '100%'
      });
    });
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

  const $province = $('#member_province_code');
  const $city = $('#member_place_of_birth_code');
  const $cityHint = $('#member_city_hint');

  function updateCityHint() {
    if ($cityHint.length) {
      $cityHint.toggleClass('d-none', !!$province.val());
    }
  }

  if ($province.length && $city.length && typeof $.fn.select2 !== 'undefined') {
    const searchUrl = $city.data('search-url');
    if (searchUrl) {
      $province.wrap('<div class="position-relative"></div>');
      $city.wrap('<div class="position-relative"></div>');

      $province.select2({
        placeholder: $province.data('placeholder') || 'Pilih provinsi',
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
    }
  }
});
