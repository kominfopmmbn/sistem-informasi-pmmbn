/**
 * Article admin: Select2, Flatpickr, Dropzone, Quill (Sneat patterns).
 */
'use strict';

var articleFormPreviewTemplate = `<div class="dz-preview dz-file-preview">
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
  const $category = $('#category_id');
  if ($category.length) {
    $category.wrap('<div class="position-relative"></div>');
    $category.select2({
      placeholder: 'Pilih kategori',
      allowClear: false,
      dropdownParent: $category.parent(),
      width: '100%'
    });
  }

  const $tags = $('#article_tags');
  if ($tags.length) {
    $tags.wrap('<div class="position-relative"></div>');
    $tags.select2({
      tags: true,
      tokenSeparators: [','],
      placeholder: 'Pilih atau ketik tag baru',
      allowClear: true,
      closeOnSelect: false,
      dropdownParent: $tags.parent(),
      width: '100%'
    });
  }

  const publishedEl = document.querySelector('#published_at');
  if (publishedEl && typeof flatpickr !== 'undefined') {
    flatpickr(publishedEl, {
      enableTime: true,
      dateFormat: 'Y-m-d H:i',
      static: true,
      monthSelectorType: 'static',
      time_24hr: true,
      allowInput: true
    });
  }

  if (typeof Dropzone !== 'undefined') {
    Dropzone.autoDiscover = false;
    const dzEl = document.querySelector('#article-cover-dropzone');
    const fileInput = document.getElementById('article_cover_photo');
    if (dzEl && fileInput) {
      const dz = new Dropzone(dzEl, {
        url: window.location.pathname,
        autoProcessQueue: false,
        uploadMultiple: false,
        parallelUploads: 1,
        maxFiles: 1,
        maxFilesize: 5,
        acceptedFiles: 'image/*',
        addRemoveLinks: true,
        previewTemplate: articleFormPreviewTemplate,
        dictDefaultMessage:
          'Seret gambar ke sini atau klik untuk memilih<br><small class="text-body-secondary">Format gambar, maks. 5 MB</small>',
        init: function () {
          if (this.hiddenFileInput && this.hiddenFileInput.parentNode) {
            this.hiddenFileInput.remove();
          }
          this.on('addedfile', function (file) {
            if (this.files.length > 1) {
              this.removeFile(this.files[0]);
            }
            if (file && fileInput) {
              const dt = new DataTransfer();
              dt.items.add(file);
              fileInput.files = dt.files;
            }
            const rm = document.querySelector('input[name="remove_cover"]');
            if (rm) {
              rm.checked = false;
            }
          });
          this.on('removedfile', function () {
            if (fileInput) {
              fileInput.value = '';
            }
          });
        }
      });
    }
  }

  if (typeof Quill !== 'undefined') {
    const fullEditorEl = document.querySelector('#full-editor');
    if (fullEditorEl) {
      const fullToolbar = [
        [{ font: [] }, { size: [] }],
        ['bold', 'italic', 'underline', 'strike'],
        [{ color: [] }, { background: [] }],
        [{ script: 'super' }, { script: 'sub' }],
        [{ header: '1' }, { header: '2' }, 'blockquote', 'code-block'],
        [{ list: 'ordered' }, { indent: '-1' }, { indent: '+1' }],
        [{ direction: 'rtl' }, { align: [] }],
        ['link', 'image', 'video', 'formula'],
        ['clean']
      ];
      const quill = new Quill('#full-editor', {
        bounds: '#full-editor',
        placeholder: 'Tulis konten artikel…',
        modules: {
          syntax: true,
          toolbar: fullToolbar
        },
        theme: 'snow'
      });
      window.__articleQuill = quill;

      const initialHtml =
        typeof window.__articleContentHtml === 'string' ? window.__articleContentHtml : '';
      if (initialHtml) {
        quill.clipboard.dangerouslyPasteHTML(initialHtml);
      }
    }
  }

  $('#article-form').on('submit', function () {
    if (window.__articleQuill) {
      const hidden = document.getElementById('article_content');
      if (hidden) {
        hidden.value = window.__articleQuill.root.innerHTML;
      }
    }
  });
});
