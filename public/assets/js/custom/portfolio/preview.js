/*
 * Admin → website live preview.
 *
 * Every field of the form that owns the preview panel is streamed to the
 * iframe as { type: 'pf:set', key, value }. Keys are the field names,
 * prefixed for list items ("services.5." + "title"). Picked / pasted images
 * are sent as blob: URLs so they appear before the form is saved.
 */
(function () {
    'use strict';

    var panel = document.querySelector('[data-pf-preview]');
    if (!panel) return;

    var frame = panel.querySelector('[data-frame]');
    var stage = panel.querySelector('[data-stage]');
    var form = panel.closest('form') || document.querySelector('form[enctype]') || document.querySelector('.app-content form[method="POST"]');
    var prefix = panel.dataset.prefix || '';
    var target = panel.dataset.target || '';
    var origin = window.location.origin;
    var device = 'desktop';
    var ready = false;
    var pending = {};

    // --- Scale a desktop-width page down into the panel ---
    function layout() {
        var width = device === 'mobile' ? 390 : 1280;
        var scale = Math.min(1, stage.clientWidth / width);
        frame.style.width = width + 'px';
        frame.style.height = Math.round(stage.clientHeight / scale) + 'px';
        frame.style.transform = 'scale(' + scale + ')';
        stage.classList.toggle('is-mobile', device === 'mobile');
    }
    window.addEventListener('resize', layout);
    layout();

    panel.querySelectorAll('[data-device]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            device = btn.dataset.device;
            panel.querySelectorAll('[data-device]').forEach(function (b) { b.classList.toggle('is-active', b === btn); });
            layout();
        });
    });

    panel.querySelector('[data-preview-reload]').addEventListener('click', function () {
        ready = false;
        frame.contentWindow.location.reload();
    });

    // --- Messaging ---
    function send(key, value, focus) {
        var msg = { type: 'pf:set', key: prefix + key, value: value, focus: !!focus };
        if (!ready) { pending[msg.key] = msg; return; }
        frame.contentWindow.postMessage(msg, origin);
    }

    window.addEventListener('message', function (e) {
        if (e.origin !== origin || e.source !== frame.contentWindow || !e.data || e.data.type !== 'pf:ready') return;
        ready = true;
        if (target) frame.contentWindow.postMessage({ type: 'pf:scroll', target: target }, origin);
        Object.keys(pending).forEach(function (k) { frame.contentWindow.postMessage(pending[k], origin); });
        pending = {};
    });

    if (!form) return;

    function fieldName(el) {
        // links[0][url] etc. are not previewed; plain names only.
        return /^[a-z0-9_]+$/i.test(el.name || '') ? el.name : null;
    }

    form.addEventListener('input', function (e) {
        var el = e.target, name = fieldName(el);
        if (!name || el.type === 'file' || el.type === 'hidden') return;
        send(name, el.type === 'checkbox' ? (el.checked ? '1' : '0') : el.value, true);
    });

    form.addEventListener('change', function (e) {
        var el = e.target;
        if (el.type !== 'file') return;
        var name = (el.name || '').replace(/\[\]$/, '');
        if (!el.files || !el.files[0] || !/^image\//.test(el.files[0].type)) return;
        // Gallery uploads preview as the project's card image.
        send(name === 'images' ? 'cover' : name, URL.createObjectURL(el.files[0]), true);
    });

    // The paste/drop uploader writes input.files programmatically, which does
    // not fire "change" — watch the preview grid instead.
    form.querySelectorAll('.pf-drop').forEach(function (zone) {
        var input = document.querySelector(zone.dataset.input);
        new MutationObserver(function () {
            if (!input || !input.files || !input.files[0] || !/^image\//.test(input.files[0].type)) return;
            var name = (input.name || '').replace(/\[\]$/, '');
            send(name === 'images' ? 'cover' : name, URL.createObjectURL(input.files[0]), true);
        }).observe(zone.querySelector('.pf-drop__previews'), { childList: true });
    });
})();
