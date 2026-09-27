/*
 * Live preview receiver — loaded only when the page is opened with
 * ?pf_preview=1 inside the admin's preview panel. The admin form posts
 * { type: 'pf:set', key, value, kind } messages; matching [data-pf*] nodes
 * update instantly. Messages from any other origin are ignored.
 */
(function () {
    'use strict';

    document.documentElement.classList.add('is-preview');

    // Show everything immediately — no scroll-reveal inside the preview.
    document.querySelectorAll('[data-reveal]').forEach(function (el) { el.classList.add('is-visible'); });

    function all(attr, key) {
        return document.querySelectorAll('[' + attr + '="' + key.replace(/"/g, '') + '"]');
    }

    function flash(el) {
        el.classList.remove('pf-flash');
        void el.offsetWidth;
        el.classList.add('pf-flash');
    }

    function scrollTo(target) {
        var el = target && document.querySelector(target);
        if (el) window.scrollTo(0, el.getBoundingClientRect().top + window.scrollY - 70);
    }

    window.addEventListener('message', function (e) {
        if (e.origin !== window.location.origin || !e.data || typeof e.data !== 'object') return;
        var d = e.data;

        if (d.type === 'pf:scroll') { scrollTo(d.target); return; }
        if (d.type !== 'pf:set' || typeof d.key !== 'string') return;

        var value = d.value == null ? '' : String(d.value);
        var first = null;

        all('data-pf', d.key).forEach(function (el) { el.textContent = value; first = first || el; flash(el); });
        all('data-pf-img', d.key).forEach(function (el) { if (value) { el.src = value; el.removeAttribute('srcset'); } first = first || el; flash(el); });
        all('data-pf-icon', d.key).forEach(function (el) { el.className = value || 'fa-solid fa-star'; first = first || el; flash(el); });
        all('data-pf-level', d.key).forEach(function (el) { el.style.setProperty('--lvl', Math.max(0, Math.min(100, parseInt(value, 10) || 0)) + '%'); });

        // Keep the edited element on screen.
        if (first && d.focus) {
            var r = first.getBoundingClientRect();
            if (r.top < 60 || r.bottom > window.innerHeight) window.scrollTo({ top: r.top + window.scrollY - window.innerHeight / 3, behavior: 'smooth' });
        }
    });

    // Tell the admin panel we are ready to receive the current form state.
    if (window.parent !== window) window.parent.postMessage({ type: 'pf:ready' }, window.location.origin);

})();
