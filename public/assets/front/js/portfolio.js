/*
 * Portfolio — shared behaviour for every public page:
 * navbar, theme toggle, reveal-on-scroll, active section, live clock, project
 * filter and the AJAX contact form (falls back to a normal POST without JS).
 */
(function () {
    'use strict';

    var nav = document.querySelector('[data-nav]');
    var toggle = document.querySelector('[data-nav-toggle]');

    // --- Navbar: scrolled state + mobile menu ---
    if (nav) {
        var onScroll = function () { nav.classList.toggle('is-scrolled', window.scrollY > 20); };
        onScroll();
        window.addEventListener('scroll', onScroll, { passive: true });

        var setOpen = function (open) {
            nav.classList.toggle('is-open', open);
            document.body.classList.toggle('nav-open', open);
            if (toggle) toggle.setAttribute('aria-expanded', String(open));
        };
        if (toggle) toggle.addEventListener('click', function () { setOpen(!nav.classList.contains('is-open')); });
        nav.querySelectorAll('.nav__menu a').forEach(function (a) { a.addEventListener('click', function () { setOpen(false); }); });
        document.addEventListener('keydown', function (e) { if (e.key === 'Escape') setOpen(false); });
        window.matchMedia('(min-width: 901px)').addEventListener('change', function (m) { if (m.matches) setOpen(false); });
    }

    // --- Light / dark theme (same storage key as the admin panel) ---
    document.querySelectorAll('[data-theme-toggle]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var next = document.documentElement.getAttribute('data-theme') === 'light' ? 'dark' : 'light';
            document.documentElement.setAttribute('data-theme', next);
            try { localStorage.setItem('data-bs-theme', next); } catch (e) {}
            var meta = document.querySelector('meta[name="theme-color"]');
            if (meta) meta.setAttribute('content', next === 'light' ? '#f6f6f4' : '#0d0d0f');
        });
    });

    // --- Navbar dropdown (Category) ---
    document.querySelectorAll('[data-dropdown]').forEach(function (dd) {
        var btn = dd.querySelector('[data-dropdown-btn]');
        var set = function (open) { dd.classList.toggle('is-open', open); btn.setAttribute('aria-expanded', String(open)); };
        btn.addEventListener('click', function (e) { e.stopPropagation(); set(!dd.classList.contains('is-open')); });
        document.addEventListener('click', function (e) { if (!dd.contains(e.target)) set(false); });
        document.addEventListener('keydown', function (e) { if (e.key === 'Escape') set(false); });
    });

    // --- Reveal on scroll ---
    var reveal = document.querySelectorAll('[data-reveal]');
    if ('IntersectionObserver' in window) {
        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) { entry.target.classList.add('is-visible'); io.unobserve(entry.target); }
            });
        }, { rootMargin: '0px 0px -8% 0px', threshold: 0.08 });
        reveal.forEach(function (el) { io.observe(el); });

        // Highlight the nav link of the section in view.
        var links = {};
        document.querySelectorAll('.nav__menu a[href*="#"]').forEach(function (a) {
            var id = a.getAttribute('href').split('#')[1];
            if (id) links[id] = a;
        });
        var spy = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                var link = links[entry.target.id];
                if (!link || !entry.isIntersecting) return;
                Object.keys(links).forEach(function (k) { links[k].classList.remove('is-active'); });
                link.classList.add('is-active');
            });
        }, { rootMargin: '-45% 0px -50% 0px' });
        Object.keys(links).forEach(function (id) { var s = document.getElementById(id); if (s) spy.observe(s); });
    } else {
        reveal.forEach(function (el) { el.classList.add('is-visible'); });
    }

    // --- Live clock in the profile bar ---
    document.querySelectorAll('[data-clock]').forEach(function (el) {
        var tz = el.dataset.tz || 'Asia/Jakarta';
        var fmt;
        try { fmt = new Intl.DateTimeFormat('en-US', { hour: '2-digit', minute: '2-digit', hour12: true, timeZone: tz }); }
        catch (e) { fmt = new Intl.DateTimeFormat('en-US', { hour: '2-digit', minute: '2-digit', hour12: true }); }
        var tick = function () { el.textContent = fmt.format(new Date()); };
        tick();
        setInterval(tick, 30000);
    });

    // --- Project category filter ---
    document.querySelectorAll('[data-filters]').forEach(function (bar) {
        var grid = bar.parentElement.querySelector('[data-projects]');
        if (!grid) return;
        bar.addEventListener('click', function (e) {
            var btn = e.target.closest('[data-filter]');
            if (!btn) return;
            bar.querySelectorAll('[data-filter]').forEach(function (b) {
                b.classList.toggle('is-active', b === btn);
                b.setAttribute('aria-selected', String(b === btn));
            });
            var f = btn.dataset.filter;
            grid.querySelectorAll('.project').forEach(function (card) {
                card.hidden = !(f === '*' || card.dataset.category === f);
            });
        });
    });

    // --- Contact form (progressive enhancement) ---
    var form = document.querySelector('[data-contact-form]');
    if (form && window.fetch) {
        var status = form.querySelector('[data-form-status]');
        var submit = form.querySelector('[data-submit]');

        var show = function (type, text) {
            status.innerHTML = '';
            var p = document.createElement('p');
            p.className = type;
            p.textContent = text;
            status.appendChild(p);
        };

        form.addEventListener('submit', function (e) {
            if (!form.checkValidity()) {
                e.preventDefault();
                form.reportValidity();
                return;
            }
            e.preventDefault();
            submit.disabled = true;
            form.querySelectorAll('.has-error').forEach(function (f) { f.classList.remove('has-error'); });

            fetch(form.action, {
                method: 'POST',
                body: new FormData(form),
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin'
            }).then(function (res) {
                return res.json().catch(function () { return {}; }).then(function (data) { return { ok: res.ok, status: res.status, data: data }; });
            }).then(function (r) {
                if (r.ok) {
                    show('ok', r.data.message || 'Pesan terkirim.');
                    form.reset();
                    return;
                }
                if (r.status === 422 && r.data.errors) {
                    Object.keys(r.data.errors).forEach(function (name) {
                        var input = form.querySelector('[name="' + name + '"]');
                        if (input && input.closest('.field')) input.closest('.field').classList.add('has-error');
                    });
                    show('err', r.data.errors[Object.keys(r.data.errors)[0]][0]);
                    return;
                }
                if (r.status === 419) { show('err', 'Sesi kedaluwarsa, halaman dimuat ulang...'); setTimeout(function () { location.reload(); }, 1200); return; }
                show('err', r.data.message || 'Gagal mengirim pesan. Coba lagi atau hubungi via WhatsApp.');
            }).catch(function () {
                show('err', 'Koneksi bermasalah. Coba lagi sebentar lagi.');
            }).then(function () {
                submit.disabled = false;
            });
        });
    }
})();

/* ---------- Modal detail pengalaman ----------
   Penanda timeline (belah ketupat merah, berfoto atau tidak) membuka <dialog>
   milik item itu. Dipakai event delegation supaya tetap jalan walau item
   pengalaman bertambah/berkurang.                                            */
(function () {
    'use strict';

    var opener = null;

    document.addEventListener('click', function (e) {
        var open = e.target.closest('[data-exp-open]');
        if (open) {
            var dlg = document.getElementById(open.getAttribute('data-exp-open'));
            if (dlg && typeof dlg.showModal === 'function') {
                opener = open;
                dlg.showModal();
            }
            return;
        }

        var close = e.target.closest('[data-exp-close]');
        if (close) {
            var owner = close.closest('dialog');
            if (owner) { owner.close(); }
            return;
        }

        // Klik di area gelap (backdrop) menutup modal.
        if (e.target.tagName === 'DIALOG' && e.target.classList.contains('expdlg')) {
            e.target.close();
        }
    });

    // Kembalikan fokus ke penanda yang tadi diklik, supaya navigasi keyboard
    // tidak "tersesat" setelah modal ditutup.
    document.addEventListener('close', function (e) {
        if (e.target.classList && e.target.classList.contains('expdlg') && opener) {
            opener.focus();
            opener = null;
        }
    }, true);
})();
