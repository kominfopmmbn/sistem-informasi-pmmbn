/**
 * Program detail gallery lightbox: Swiper inside a Bootstrap modal.
 * Click a photo -> fullscreen popup; swipe / arrows / keyboard to browse the program's photos.
 */
'use strict';

document.addEventListener('DOMContentLoaded', function () {
  var modalEl = document.getElementById('galeriLightbox');
  if (!modalEl || typeof Swiper === 'undefined' || typeof bootstrap === 'undefined') return;

  var swiper = new Swiper(modalEl.querySelector('.galeri-swiper'), {
    spaceBetween: 24,
    observer: true,
    observeParents: true,
    keyboard: { enabled: true },
    navigation: {
      nextEl: modalEl.querySelector('.swiper-button-next'),
      prevEl: modalEl.querySelector('.swiper-button-prev')
    },
    pagination: { el: modalEl.querySelector('.swiper-pagination'), type: 'fraction' }
  });

  var modal = bootstrap.Modal.getOrCreateInstance(modalEl);
  var pendingIndex = 0;

  document.querySelectorAll('[data-galeri-index]').forEach(function (trigger) {
    trigger.addEventListener('click', function () {
      pendingIndex = parseInt(this.getAttribute('data-galeri-index'), 10) || 0;
      modal.show();
    });
  });

  modalEl.addEventListener('shown.bs.modal', function () {
    swiper.update();
    swiper.slideTo(pendingIndex, 0);
  });
});
