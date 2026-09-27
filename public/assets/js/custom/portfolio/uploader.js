/*
 * Portfolio upload zones (.pf-drop).
 *
 * Files can be added by clicking, drag & drop, or pasting a screenshot /
 * copied image with Ctrl+V. Every zone keeps its own DataTransfer and writes
 * it back into its real <input type="file">, so the form submits normally.
 *
 * Paste goes to the zone that was last clicked/hovered, else the zone marked
 * data-paste-default. Pasting text into an input is never intercepted.
 */
(function () {
    'use strict';

    var zones = [];
    var active = null;

    function human(bytes) {
        return bytes > 1048576 ? (bytes / 1048576).toFixed(1) + ' MB' : Math.max(1, Math.round(bytes / 1024)) + ' KB';
    }

    function notify(msg) {
        if (window.toastr) toastr.warning(msg); else alert(msg);
    }

    function Zone(el) {
        this.el = el;
        this.input = document.querySelector(el.dataset.input);
        this.multiple = el.dataset.multiple === '1';
        this.kind = el.dataset.kind || 'image';
        this.max = parseInt(el.dataset.max || '10485760', 10);
        this.list = el.querySelector('.pf-drop__previews');
        this.dt = new DataTransfer();
        this.bind();
    }

    Zone.prototype.bind = function () {
        var self = this;

        this.el.addEventListener('click', function (e) {
            if (e.target.closest('.pf-drop__remove')) return;
            self.focus();
            self.input.click();
        });
        this.el.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); self.input.click(); }
        });
        this.el.addEventListener('mouseenter', function () { self.focus(); });
        this.el.addEventListener('focus', function () { self.focus(); });

        this.input.addEventListener('change', function () {
            // The picker replaces input.files — merge its picks into our list.
            var picked = Array.prototype.slice.call(self.input.files);
            self.input.files = self.dt.files;
            self.add(picked);
        });

        ['dragenter', 'dragover'].forEach(function (t) {
            self.el.addEventListener(t, function (e) { e.preventDefault(); self.el.classList.add('is-over'); });
        });
        ['dragleave', 'drop'].forEach(function (t) {
            self.el.addEventListener(t, function (e) { e.preventDefault(); self.el.classList.remove('is-over'); });
        });
        this.el.addEventListener('drop', function (e) {
            self.focus();
            self.add(Array.prototype.slice.call(e.dataTransfer.files));
        });
    };

    Zone.prototype.focus = function () {
        zones.forEach(function (z) { z.el.classList.remove('is-active'); });
        this.el.classList.add('is-active');
        active = this;
    };

    Zone.prototype.accepts = function (file) {
        return this.kind !== 'image' || /^image\/(jpeg|png|webp|gif)$/.test(file.type);
    };

    Zone.prototype.add = function (files) {
        var self = this;

        files.forEach(function (file) {
            if (!self.accepts(file)) { notify(file.name + ': format tidak didukung (JPG, PNG, WEBP, GIF).'); return; }
            if (file.size > self.max) { notify(file.name + ' terlalu besar (' + human(file.size) + '). Maks 10 MB.'); return; }

            if (!self.multiple) self.dt = new DataTransfer();
            self.dt.items.add(file);
        });

        this.input.files = this.dt.files;
        this.render();
    };

    Zone.prototype.remove = function (index) {
        var next = new DataTransfer();
        Array.prototype.forEach.call(this.dt.files, function (f, i) { if (i !== index) next.items.add(f); });
        this.dt = next;
        this.input.files = this.dt.files;
        this.render();
    };

    Zone.prototype.render = function () {
        var self = this;
        this.list.querySelectorAll('img').forEach(function (img) { URL.revokeObjectURL(img.src); });
        this.list.innerHTML = '';
        this.el.classList.toggle('has-files', this.dt.files.length > 0);

        Array.prototype.forEach.call(this.dt.files, function (file, i) {
            var item = document.createElement('div');
            item.className = 'pf-drop__item';

            if (/^image\//.test(file.type)) {
                var img = document.createElement('img');
                img.src = URL.createObjectURL(file);
                img.alt = '';
                item.appendChild(img);
            } else {
                var icon = document.createElement('div');
                icon.className = 'pf-drop__file';
                icon.innerHTML = '<i class="ki-outline ki-document fs-2x"></i>';
                item.appendChild(icon);
            }

            var meta = document.createElement('div');
            meta.className = 'pf-drop__meta';
            meta.textContent = file.name + ' · ' + human(file.size);
            item.appendChild(meta);

            var btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'pf-drop__remove';
            btn.setAttribute('aria-label', 'Hapus ' + file.name);
            btn.innerHTML = '&times;';
            btn.addEventListener('click', function (e) { e.stopPropagation(); self.remove(i); });
            item.appendChild(btn);

            self.list.appendChild(item);
        });
    };

    document.addEventListener('paste', function (e) {
        var data = e.clipboardData;
        if (!data) return;

        var files = [];
        Array.prototype.forEach.call(data.items || [], function (item) {
            if (item.kind === 'file') {
                var f = item.getAsFile();
                if (f) files.push(f);
            }
        });
        if (!files.length) return; // plain text paste — leave it alone

        var zone = active || zones.filter(function (z) { return z.el.hasAttribute('data-paste-default'); })[0] || zones[0];
        if (!zone) return;
        e.preventDefault();

        var stamp = Date.now();
        zone.add(files.map(function (f, i) {
            // Screenshots arrive as "image.png" — give each a unique name.
            var ext = (f.type.split('/')[1] || 'png').replace('jpeg', 'jpg');
            return /^image\.\w+$/.test(f.name) || !f.name
                ? new File([f], 'paste-' + stamp + '-' + i + '.' + ext, { type: f.type })
                : f;
        }));
        if (window.toastr) toastr.success(files.length + ' file ditempel.');
    });

    document.querySelectorAll('.pf-drop').forEach(function (el) { zones.push(new Zone(el)); });
})();
