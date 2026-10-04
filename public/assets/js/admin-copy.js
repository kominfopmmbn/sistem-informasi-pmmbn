/**
 * Tombol salin: `<button data-copy-target="<id input>">` menyalin nilai input tersebut.
 */
'use strict';

document.addEventListener('click', function (e) {
  const btn = e.target.closest('[data-copy-target]');
  if (!btn) {
    return;
  }
  const input = document.getElementById(btn.dataset.copyTarget);
  if (!input || input.value === '') {
    return;
  }
  const label = btn.dataset.copyLabel || (btn.dataset.copyLabel = btn.textContent);
  const done = function () {
    btn.textContent = 'Tersalin';
    setTimeout(function () {
      btn.textContent = label;
    }, 1500);
  };
  input.select();
  // Clipboard API hanya tersedia di HTTPS/localhost; selain itu pakai execCommand.
  if (navigator.clipboard && window.isSecureContext) {
    navigator.clipboard.writeText(input.value).then(done);
  } else {
    document.execCommand('copy');
    done();
  }
});
