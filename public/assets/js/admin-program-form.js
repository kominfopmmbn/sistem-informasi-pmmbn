/**
 * Program admin form: Dropzone (sampul & galeri), Quill (about), dynamic goals repeater.
 */
'use strict';

var programFormPreviewTemplate = `<div class="dz-preview dz-file-preview">
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
  // --- Dropzone: Sampul (single) & Galeri (multi) ---
  if (typeof Dropzone !== 'undefined') {
    Dropzone.autoDiscover = false;

    // Sampul — single file, disalin ke hidden input agar ikut submit.
    const coverEl = document.querySelector('#program-cover-dropzone');
    const coverInput = document.getElementById('program_cover_photo');
    if (coverEl && coverInput) {
      const coverMaxMb = parseFloat(coverEl.dataset.maxFilesizeMb || '10');
      const coverAccepted = coverEl.dataset.acceptedFiles || 'image/*';
      new Dropzone(coverEl, {
        url: window.location.pathname,
        autoProcessQueue: false,
        uploadMultiple: false,
        parallelUploads: 1,
        maxFiles: 1,
        maxFilesize: coverMaxMb,
        acceptedFiles: coverAccepted,
        addRemoveLinks: true,
        previewTemplate: programFormPreviewTemplate,
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
            coverInput.files = dt.files;
            const rm = document.querySelector('input[name="remove_cover"]');
            if (rm) {
              rm.checked = false;
            }
          });
          this.on('removedfile', function () {
            coverInput.value = '';
          });
        }
      });
    }

    // Galeri — multi file, semua disinkron ke hidden input.
    const galleryEl = document.querySelector('#program-gallery-dropzone');
    const galleryInput = document.getElementById('program_gallery');
    if (galleryEl && galleryInput) {
      const maxFiles = parseInt(galleryEl.dataset.maxFiles || '12', 10);
      const maxFilesizeMb = parseFloat(galleryEl.dataset.maxFilesizeMb || '10');
      const acceptedFiles = galleryEl.dataset.acceptedFiles || 'image/*';
      const galleryDz = new Dropzone(galleryEl, {
        url: window.location.pathname,
        autoProcessQueue: false,
        uploadMultiple: true,
        parallelUploads: Math.max(1, maxFiles),
        maxFiles: maxFiles,
        maxFilesize: maxFilesizeMb,
        acceptedFiles: acceptedFiles,
        addRemoveLinks: true,
        previewTemplate: programFormPreviewTemplate,
        init: function () {
          if (this.hiddenFileInput && this.hiddenFileInput.parentNode) {
            this.hiddenFileInput.remove();
          }
          const syncInput = () => {
            const dt = new DataTransfer();
            galleryDz.files.forEach(function (f) {
              dt.items.add(f);
            });
            galleryInput.files = dt.files;
          };
          this.on('addedfile', syncInput);
          this.on('removedfile', syncInput);
        }
      });
    }
  }

  // --- Quill: Tentang Program ---
  if (typeof Quill !== 'undefined') {
    const aboutEl = document.querySelector('#program-about-editor');
    if (aboutEl) {
      const toolbar = [
        [{ header: ['1', '2', false] }],
        ['bold', 'italic', 'underline', 'strike'],
        [{ list: 'ordered' }, { list: 'bullet' }],
        [{ align: [] }],
        ['link', 'blockquote'],
        ['clean']
      ];
      const quill = new Quill('#program-about-editor', {
        bounds: '#program-about-editor',
        placeholder: 'Tulis penjelasan program…',
        modules: { toolbar: toolbar },
        theme: 'snow'
      });
      window.__programAboutQuill = quill;

      const initialHtml =
        typeof window.__programAboutHtml === 'string' ? window.__programAboutHtml : '';
      if (initialHtml) {
        quill.clipboard.dangerouslyPasteHTML(initialHtml);
      }
    }
  }

  // Tulis isi Quill ke hidden input saat submit.
  $('#program-form').on('submit', function () {
    if (window.__programAboutQuill) {
      const hidden = document.getElementById('program_about_content');
      if (hidden) {
        const quill = window.__programAboutQuill;
        // Anggap kosong bila tak ada teks (hanya markup kosong).
        hidden.value = quill.getText().trim() === '' ? '' : quill.root.innerHTML;
      }
    }
  });

  // --- Repeater tujuan ---
  const list = document.getElementById('program-goals-list');
  const addBtn = document.getElementById('program-goals-add');
  const template = document.getElementById('program-goal-template');

  if (addBtn && list && template) {
    addBtn.addEventListener('click', function () {
      const clone = template.content.firstElementChild.cloneNode(true);
      list.appendChild(clone);
      const input = clone.querySelector('input');
      if (input) {
        input.focus();
      }
    });
  }

  if (list) {
    list.addEventListener('click', function (e) {
      const btn = e.target.closest('.program-goal-remove');
      if (!btn) {
        return;
      }
      const rows = list.querySelectorAll('.program-goal-row');
      if (rows.length > 1) {
        btn.closest('.program-goal-row').remove();
      } else {
        // Sisakan minimal satu baris; cukup kosongkan isinya.
        const input = btn.closest('.program-goal-row').querySelector('input');
        if (input) {
          input.value = '';
        }
      }
    });
  }
});
