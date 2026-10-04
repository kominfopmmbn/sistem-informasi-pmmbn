/**
 * Banner admin form: Dropzone gambar (single file), link share live, pratinjau popup.
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
  </div>
  <div class="dz-filename" data-dz-name></div>
  <div class="dz-size" data-dz-size></div>
</div>
</div>`;

/** Mendekati Str::slug() Laravel; nilai final tetap ditentukan server. */
function bannerSlugify(text) {
  return text
    .normalize('NFKD')
    .replace(/[̀-ͯ]/g, '')
    .toLowerCase()
    .replace(/_+/g, '-')
    .replace(/[^a-z0-9\s-]/g, '')
    .trim()
    .replace(/[\s-]+/g, '-')
    .replace(/^-+|-+$/g, '');
}

$(function () {
  const form = document.getElementById('banner-form');
  if (!form) {
    return;
  }
  const isEdit = form.querySelector('input[name="_method"]') !== null;

  const titleInput = document.getElementById('title');
  const linkInput = document.getElementById('link_url');
  const activeInput = document.getElementById('is_active');
  const slugInput = document.getElementById('slug');
  const shareInput = document.getElementById('banner-share-url');
  const shareNote = document.getElementById('banner-share-note');
  const previewImage = document.getElementById('banner-preview-image');
  const previewEmpty = document.getElementById('banner-preview-empty');
  const previewCta = document.getElementById('banner-preview-cta');
  const imageEl = document.getElementById('banner-image-dropzone');
  const imageInput = document.getElementById('banner_image');
  const existingUrl = imageEl ? imageEl.dataset.existingUrl || '' : '';

  // --- Pratinjau popup ---
  let objectUrl = null;
  const setPreviewImage = function (url) {
    if (objectUrl && objectUrl !== url) {
      URL.revokeObjectURL(objectUrl);
      objectUrl = null;
    }
    previewImage.src = url || '';
    previewImage.classList.toggle('d-none', !url);
    previewEmpty.classList.toggle('d-none', !!url);
  };
  const syncPreviewText = function () {
    previewImage.alt = titleInput.value.trim();
    previewCta.classList.toggle('d-none', linkInput.value.trim() === '');
  };
  setPreviewImage(existingUrl);
  syncPreviewText();
  titleInput.addEventListener('input', syncPreviewText);
  linkInput.addEventListener('input', syncPreviewText);

  // --- Link share ---
  const syncShare = function () {
    const typed = bannerSlugify(slugInput.value);
    const slug = typed || bannerSlugify(titleInput.value);
    slugInput.placeholder = bannerSlugify(titleInput.value) || 'dibuat dari judul';
    shareInput.value = slug ? shareInput.dataset.shareBase + '?banner=' + encodeURIComponent(slug) : '';
    shareInput.style.height = 'auto';
    shareInput.style.height = shareInput.scrollHeight + 'px';

    // Catatan info biasa; peringatan (link lama mati, banner nonaktif) diberi warna warning.
    const notes = [];
    if (!isEdit) {
      notes.push({ text: 'Link berfungsi setelah banner disimpan.' });
    }
    if (!typed && slug) {
      notes.push({ text: 'Bila parameter sudah dipakai, server menambah akhiran -2, -3, …' });
    }
    const saved = slugInput.dataset.saved || '';
    if (isEdit && saved && slug !== saved) {
      notes.push({ text: 'Link lama (?banner=' + saved + ') tidak lagi memunculkan banner ini.', warn: true });
    }
    if (!activeInput.checked) {
      notes.push({ text: 'Banner nonaktif: link tetap membuka situs, tetapi popup tidak muncul.', warn: true });
    }
    shareNote.replaceChildren(
      ...notes.map(function (note) {
        const line = document.createElement('div');
        line.textContent = note.text;
        if (note.warn) {
          line.className = 'text-warning-emphasis mt-1';
        }
        return line;
      })
    );
  };
  syncShare();
  titleInput.addEventListener('input', syncShare);
  slugInput.addEventListener('input', syncShare);
  activeInput.addEventListener('change', syncShare);

  // --- Dropzone: gambar single file, disalin ke hidden input agar ikut submit form biasa ---
  if (typeof Dropzone === 'undefined' || !imageEl || !imageInput) {
    return;
  }
  Dropzone.autoDiscover = false;

  new Dropzone(imageEl, {
    url: window.location.pathname,
    autoProcessQueue: false,
    uploadMultiple: false,
    parallelUploads: 1,
    maxFiles: 1,
    maxFilesize: parseFloat(imageEl.dataset.maxFilesizeMb || '10'),
    acceptedFiles: imageEl.dataset.acceptedFiles || 'image/*',
    addRemoveLinks: true,
    dictRemoveFile: 'Hapus',
    dictInvalidFileType: 'Format gambar tidak didukung.',
    dictFileTooBig: 'Ukuran file melebihi {{maxFilesize}} MB.',
    previewTemplate: bannerFormPreviewTemplate,
    init: function () {
      const dz = this;
      let existingMock = null;

      if (dz.hiddenFileInput && dz.hiddenFileInput.parentNode) {
        dz.hiddenFileInput.remove();
      }

      // Gambar tersimpan ditampilkan di dropzone (bukan file baru: tidak ikut submit, tanpa tombol hapus).
      const showExisting = function () {
        if (!existingUrl) {
          return;
        }
        existingMock = {
          name: imageEl.dataset.existingName || 'Gambar saat ini',
          size: parseInt(imageEl.dataset.existingSize || '0', 10),
          isExisting: true
        };
        dz.displayExistingFile(existingMock, existingUrl, null, null, false);
        const removeLink = existingMock.previewElement.querySelector('.dz-remove');
        if (removeLink) {
          removeLink.remove();
        }
        imageEl.classList.add('dz-started');
      };

      dz.on('addedfile', function (file) {
        if (file.isExisting) {
          return;
        }
        if (existingMock && existingMock.previewElement) {
          existingMock.previewElement.remove();
          existingMock = null;
        }
        if (dz.files.length > 1) {
          dz.removeFile(dz.files[0]);
        }
        const dt = new DataTransfer();
        dt.items.add(file);
        imageInput.files = dt.files;
        const url = URL.createObjectURL(file);
        setPreviewImage(url);
        objectUrl = url;
      });

      // File ditolak (format/ukuran): jangan ikut submit, pratinjau kembali ke gambar tersimpan.
      dz.on('error', function (file) {
        if (file.isExisting) {
          return;
        }
        imageInput.value = '';
        setPreviewImage(existingUrl);
      });

      dz.on('removedfile', function (file) {
        if (file.isExisting || dz.files.length > 0) {
          return;
        }
        imageInput.value = '';
        setPreviewImage(existingUrl);
        showExisting();
      });

      showExisting();
    }
  });
});
