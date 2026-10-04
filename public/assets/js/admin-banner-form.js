/**
 * Banner admin form: Dropzone gambar (single file).
 */
'use strict';

var bannerFormPreviewTemplate = `<div class="dz-preview dz-file-preview">
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
  if (typeof Dropzone === 'undefined') {
    return;
  }
  Dropzone.autoDiscover = false;

  // Gambar — single file, disalin ke hidden input agar ikut submit form biasa.
  const imageEl = document.querySelector('#banner-image-dropzone');
  const imageInput = document.getElementById('banner_image');
  if (!imageEl || !imageInput) {
    return;
  }

  new Dropzone(imageEl, {
    url: window.location.pathname,
    autoProcessQueue: false,
    uploadMultiple: false,
    parallelUploads: 1,
    maxFiles: 1,
    maxFilesize: parseFloat(imageEl.dataset.maxFilesizeMb || '10'),
    acceptedFiles: imageEl.dataset.acceptedFiles || 'image/*',
    addRemoveLinks: true,
    previewTemplate: bannerFormPreviewTemplate,
    init: function () {
      if (this.hiddenFileInput && this.hiddenFileInput.parentNode) {
        this.hiddenFileInput.remove();
      }
      this.on('addedfile', function (file) {
        if (this.files.length > 1) {
          this.removeFile(this.files[0]);
        }
        const dt = new DataTransfer();
        dt.items.add(file);
        imageInput.files = dt.files;
      });
      this.on('removedfile', function () {
        if (this.files.length === 0) {
          imageInput.value = '';
        }
      });
    }
  });
});
