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
        var onScroll = function () { nav.classList.toggle('is-scrolled', window.scrollY > 80); };
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

    // --- Theme: light / dark / system (same storage keys as the admin panel) ---
    (function theme() {
        var root = document.documentElement;
        var mq = window.matchMedia('(prefers-color-scheme: dark)');
        var buttons = document.querySelectorAll('[data-theme-set]');
        var meta = document.querySelector('meta[name="theme-color"]');

        function mode() {
            try { return localStorage.getItem('data-bs-theme-mode') || localStorage.getItem('data-bs-theme') || 'dark'; } catch (e) { return 'dark'; }
        }
        function apply(m, persist) {
            var t = m === 'system' ? (mq.matches ? 'dark' : 'light') : (m === 'light' ? 'light' : 'dark');
            root.setAttribute('data-theme', t);
            root.setAttribute('data-theme-mode', m);
            if (persist) { try { localStorage.setItem('data-bs-theme-mode', m); localStorage.setItem('data-bs-theme', t); } catch (e) {} }
            if (meta) meta.setAttribute('content', t === 'light' ? '#e9e9e7' : '#0d0d0f');
            buttons.forEach(function (b) {
                var on = b.getAttribute('data-theme-set') === m;
                b.classList.toggle('is-active', on);
                b.setAttribute('aria-checked', String(on));
            });
        }

        apply(mode(), false);
        buttons.forEach(function (b) { b.addEventListener('click', function () { apply(b.getAttribute('data-theme-set'), true); }); });
        mq.addEventListener('change', function () { if (mode() === 'system') apply('system', false); });
    })();

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
        // Kartu/baris yang bersebelahan muncul bergantian, bukan serempak.
        reveal.forEach(function (el) {
            var i = 0, n = el.previousElementSibling;
            while (n && i < 6) { if (n.hasAttribute('data-reveal')) i++; n = n.previousElementSibling; }
            if (i) el.style.setProperty('--d', (i * 0.07).toFixed(2) + 's');
            io.observe(el);
        });

        // Highlight the nav link of the section in view.
        var links = {};
        document.querySelectorAll('.nav__menu a[href*="#"], .nav__links a[href*="#"]').forEach(function (a) {
            var id = a.getAttribute('href').split('#')[1];
            if (id) links[id] = a;
        });
        var spy = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (!entry.isIntersecting || !links[entry.target.id]) return;
                document.querySelectorAll('.nav__menu a.is-active, .nav__links a.is-active').forEach(function (a) { a.classList.remove('is-active'); });
                document.querySelectorAll('.nav__menu a[href$="#' + entry.target.id + '"], .nav__links a[href$="#' + entry.target.id + '"]').forEach(function (a) { a.classList.add('is-active'); });
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

/* ---------- Deck kartu project (roda) ----------
   Kartu-kartu digantung pada satu poros jauh di bawah deck, digandakan
   sampai rodanya penuh, lalu diputar pelan terus-menerus. Berhenti saat
   kursor di atasnya, bisa diseret (mouse/sentuh) dengan sedikit inersia,
   dan seretan tidak dihitung sebagai klik.                                */
(function () {
    'use strict';

    var deck = document.querySelector('[data-deck]');
    if (!deck) return;

    var STEP = 20;                 // derajat antar kartu
    var SPEED = 5;                 // derajat per detik saat auto-putar
    var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    // Gandakan kartu sampai minimal 360 derajat terisi.
    var originals = Array.prototype.slice.call(deck.querySelectorAll('.deck__card'));
    if (!originals.length) return;
    var slots = Math.max(originals.length, Math.ceil(360 / STEP));
    var cards = originals.slice();
    while (cards.length < slots) {
        var clone = originals[cards.length % originals.length].cloneNode(true);
        clone.classList.add('is-clone');
        clone.setAttribute('aria-hidden', 'true');
        clone.setAttribute('tabindex', '-1');
        deck.appendChild(clone);
        cards.push(clone);
    }
    var step = 360 / cards.length;

    var angle = 0, velocity = 0, paused = false, dragging = false, moved = 0, lastX = 0, lastT = 0, raf;

    function render() {
        for (var i = 0; i < cards.length; i++) {
            var a = ((angle + i * step) % 360 + 540) % 360 - 180;   // -180..180
            cards[i].style.setProperty('--a', a.toFixed(2) + 'deg');
            cards[i].style.zIndex = String(Math.round(100 - Math.abs(a)));
            cards[i].style.visibility = Math.abs(a) > 100 ? 'hidden' : '';
        }
    }

    var prev = performance.now();
    function tick(now) {
        var dt = Math.min(0.05, (now - prev) / 1000);
        prev = now;
        if (!dragging) {
            if (Math.abs(velocity) > 0.5) {
                angle += velocity * dt;
                velocity *= Math.pow(0.08, dt);          // inersia meluruh
            } else if (!paused && !reduce) {
                angle += SPEED * dt;
            }
        }
        render();
        raf = requestAnimationFrame(tick);
    }

    function radius() { return cards[0].offsetHeight * 2; }   // = transform-origin 200%

    deck.addEventListener('pointerdown', function (e) {
        if (e.pointerType === 'mouse' && e.button !== 0) return;
        dragging = true; moved = 0; velocity = 0; lastX = e.clientX; lastT = performance.now();
        deck.classList.add('is-dragging');
        try { deck.setPointerCapture(e.pointerId); } catch (err) {}
    });
    deck.addEventListener('pointermove', function (e) {
        if (!dragging) return;
        var dx = e.clientX - lastX, now = performance.now(), dt = Math.max(1, now - lastT) / 1000;
        var deg = dx / radius() * 180 / Math.PI;
        angle += deg; moved += Math.abs(dx);
        velocity = deg / dt;
        lastX = e.clientX; lastT = now;
        render();
    });
    function release() {
        if (!dragging) return;
        dragging = false;
        deck.classList.remove('is-dragging');
        velocity = Math.max(-240, Math.min(240, velocity));
    }
    deck.addEventListener('pointerup', release);
    deck.addEventListener('pointercancel', release);

    // Seretan bukan klik.
    deck.addEventListener('click', function (e) {
        if (moved > 6) { e.preventDefault(); e.stopPropagation(); }
    }, true);

    deck.addEventListener('pointerenter', function () { paused = true; });
    deck.addEventListener('pointerleave', function () { paused = false; });
    deck.addEventListener('focusin', function () { paused = true; });
    deck.addEventListener('focusout', function () { paused = false; });
    deck.addEventListener('keydown', function (e) {
        if (e.key === 'ArrowLeft') { angle -= step; render(); e.preventDefault(); }
        if (e.key === 'ArrowRight') { angle += step; render(); e.preventDefault(); }
    });

    document.addEventListener('visibilitychange', function () {
        cancelAnimationFrame(raf);
        if (!document.hidden) { prev = performance.now(); raf = requestAnimationFrame(tick); }
    });

    render();
    raf = requestAnimationFrame(tick);
})();

/* ---------- Angka berhitung ----------
   Statistik hero, level profil dan kotak stats naik dari 0 ke nilainya saat
   pertama terlihat. Awalan/akhiran (+, %, dsb.) dan jumlah digit dijaga.   */
(function () {
    'use strict';
    if (document.documentElement.classList.contains('is-preview')) return;
    var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    var els = document.querySelectorAll('.hero__stats b, .about__level b, .stats b');
    if (!els.length || !('IntersectionObserver' in window)) return;

    function run(el) {
        var text = el.textContent.trim();
        var m = text.match(/^([^\d]*)(\d+)(.*)$/);
        if (!m) return;
        var target = parseInt(m[2], 10), width = m[2].length, start = performance.now(), dur = 1400;
        if (reduce) return;
        function frame(now) {
            var p = Math.min(1, (now - start) / dur);
            var eased = 1 - Math.pow(1 - p, 3);
            var v = String(Math.round(target * eased));
            while (v.length < width) v = '0' + v;
            el.textContent = m[1] + v + m[3];
            if (p < 1) requestAnimationFrame(frame);
        }
        requestAnimationFrame(frame);
    }

    var io = new IntersectionObserver(function (entries) {
        entries.forEach(function (en) { if (en.isIntersecting) { run(en.target); io.unobserve(en.target); } });
    }, { threshold: 0.4 });
    els.forEach(function (el) { io.observe(el); });
})();

/* ---------- Kursor custom ----------
   Titik merah mengikuti kursor langsung, cincin tipis menyusul dengan
   sedikit jeda. Membesar di atas tautan/tombol, berubah jadi label DRAG di
   atas deck kartu, dan menyingkir di kolom isian. Hanya untuk mouse sungguhan;
   layar sentuh dan pengaturan "kurangi gerakan" memakai kursor biasa.     */
(function () {
    'use strict';
    var html = document.documentElement;
    if (html.classList.contains('is-preview')) return;
    if (!window.matchMedia('(hover: hover) and (pointer: fine)').matches) return;
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

    var dot = document.createElement('div'); dot.className = 'cursor';
    var ring = document.createElement('div'); ring.className = 'cursor-ring'; ring.setAttribute('data-label', 'Drag');
    document.body.appendChild(dot); document.body.appendChild(ring);

    var x = -100, y = -100, rx = -100, ry = -100, seen = false;

    document.addEventListener('pointermove', function (e) {
        if (e.pointerType && e.pointerType !== 'mouse') { html.classList.remove('has-cursor'); return; }
        x = e.clientX; y = e.clientY;
        if (!seen) { seen = true; rx = x; ry = y; html.classList.add('has-cursor'); }
        html.classList.remove('cursor-hidden');
        var t = e.target;
        var field = t.closest && t.closest('input, textarea, select');
        var drag = t.closest && t.closest('[data-cursor="drag"]');
        var link = t.closest && t.closest('a, button, [role="button"], label, .chip');
        html.classList.toggle('cursor-hidden', !!field);
        html.classList.toggle('cursor-drag', !!drag && !link);
        html.classList.toggle('cursor-link', !!link && !drag);
    }, { passive: true });
    document.addEventListener('pointerleave', function () { html.classList.add('cursor-hidden'); });
    document.addEventListener('pointerenter', function () { html.classList.remove('cursor-hidden'); });

    (function loop() {
        rx += (x - rx) * 0.18; ry += (y - ry) * 0.18;
        dot.style.transform = 'translate(' + x + 'px,' + y + 'px) translate(-50%,-50%)';
        ring.style.transform = 'translate(' + rx + 'px,' + ry + 'px) translate(-50%,-50%)';
        requestAnimationFrame(loop);
    })();
})();

/* ---------- Preloader ----------
   Tirai dengan nama brand + garis progres; hilang begitu halaman termuat
   (minimal 450 ms supaya tidak berkedip, maksimal 2,5 s apa pun yang terjadi). */
(function () {
    'use strict';
    var el = document.querySelector('[data-loader]');
    if (!el) return;
    var started = performance.now(), done = false;
    function finish() {
        if (done) return; done = true;
        var wait = Math.max(0, 450 - (performance.now() - started));
        setTimeout(function () { el.classList.add('is-done'); document.documentElement.classList.add('is-loaded'); }, wait);
    }
    if (document.readyState === 'complete') finish();
    else window.addEventListener('load', finish);
    setTimeout(finish, 2500);
})();

/* ---------- Parallax scroll ----------
   Elemen [data-px="k"] bergeser sejauh k × jarak scroll (positif = lebih
   lambat dari halaman, negatif = lebih cepat). Panggung hero ikut mengecil
   dan memudar saat digulir keluar. Dimatikan untuk "kurangi gerakan".    */
(function () {
    'use strict';
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
    var items = Array.prototype.slice.call(document.querySelectorAll('[data-px]'));
    var stage = document.querySelector('.hero__stage');
    if (!items.length && !stage) return;

    var ticking = false;
    function update() {
        ticking = false;
        var y = window.scrollY || 0, vh = Math.min(window.innerHeight, 1100);
        items.forEach(function (el) {
            var k = parseFloat(el.getAttribute('data-px')) || 0;
            var r = el.getBoundingClientRect();
            // Offset relatif ke tengah layar supaya elemen di bawah hero juga wajar.
            var centre = r.top + r.height / 2 - vh / 2 + (parseFloat(el.style.getPropertyValue('--py')) || 0);
            el.style.setProperty('--py', (-centre * k).toFixed(1) + 'px');
        });
        if (stage) {
            var p = Math.min(1, Math.max(0, y / (vh * 0.9)));
            stage.style.opacity = String(1 - p * 0.9);
            stage.style.transform = 'scale(' + (1 - p * 0.06).toFixed(3) + ')';
        }
    }
    function onScroll() { if (!ticking) { ticking = true; requestAnimationFrame(update); } }
    window.addEventListener('scroll', onScroll, { passive: true });
    window.addEventListener('resize', onScroll, { passive: true });
    update();
})();
