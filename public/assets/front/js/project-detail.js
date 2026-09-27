/* Project detail only: keyboard/touch friendly gallery lightbox. */
(function () {
    'use strict';

    var gallery = document.querySelector('[data-gallery]');
    if (!gallery) return;

    var items = Array.prototype.slice.call(gallery.querySelectorAll('.gallery__item'));
    var index = 0;
    var lastFocus = null;

    var box = document.createElement('div');
    box.className = 'lightbox';
    box.hidden = true;
    box.setAttribute('role', 'dialog');
    box.setAttribute('aria-modal', 'true');
    box.setAttribute('aria-label', 'Galeri gambar');
    box.innerHTML =
        '<img alt="">' +
        '<p class="lightbox__caption"></p>' +
        '<button type="button" class="lightbox__close" aria-label="Tutup"><i class="fa-solid fa-xmark"></i></button>' +
        '<button type="button" class="lightbox__prev" aria-label="Sebelumnya"><i class="fa-solid fa-chevron-left"></i></button>' +
        '<button type="button" class="lightbox__next" aria-label="Berikutnya"><i class="fa-solid fa-chevron-right"></i></button>';
    document.body.appendChild(box);

    var img = box.querySelector('img');
    var caption = box.querySelector('.lightbox__caption');
    var single = items.length < 2;
    box.querySelector('.lightbox__prev').hidden = single;
    box.querySelector('.lightbox__next').hidden = single;

    function show(i) {
        index = (i + items.length) % items.length;
        var item = items[index];
        img.src = item.getAttribute('href');
        img.alt = item.querySelector('img').alt;
        caption.textContent = item.dataset.caption || '';
    }

    function open(i) {
        lastFocus = document.activeElement;
        show(i);
        box.hidden = false;
        document.body.style.overflow = 'hidden';
        box.querySelector('.lightbox__close').focus();
    }

    function close() {
        box.hidden = true;
        document.body.style.overflow = '';
        if (lastFocus) lastFocus.focus();
    }

    items.forEach(function (item, i) {
        item.addEventListener('click', function (e) { e.preventDefault(); open(i); });
    });

    box.querySelector('.lightbox__close').addEventListener('click', close);
    box.querySelector('.lightbox__prev').addEventListener('click', function () { show(index - 1); });
    box.querySelector('.lightbox__next').addEventListener('click', function () { show(index + 1); });
    box.addEventListener('click', function (e) { if (e.target === box) close(); });

    document.addEventListener('keydown', function (e) {
        if (box.hidden) return;
        if (e.key === 'Escape') close();
        if (e.key === 'ArrowLeft') show(index - 1);
        if (e.key === 'ArrowRight') show(index + 1);
    });

    var startX = null;
    box.addEventListener('touchstart', function (e) { startX = e.touches[0].clientX; }, { passive: true });
    box.addEventListener('touchend', function (e) {
        if (startX === null) return;
        var dx = e.changedTouches[0].clientX - startX;
        if (Math.abs(dx) > 50) show(index + (dx < 0 ? 1 : -1));
        startX = null;
    });
})();
