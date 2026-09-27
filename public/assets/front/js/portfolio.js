/*
 * Portfolio — shared behaviour for every public page:
 * navbar, reveal-on-scroll, active section, live clock, project filter and
 * the AJAX contact form (falls back to a normal POST without JS).
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

    // --- Animated particle background ---
    (function particles() {
        var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        var canvas = document.createElement('canvas');
        canvas.className = 'bg-particles';
        canvas.setAttribute('aria-hidden', 'true');
        document.body.prepend(canvas);
        var ctx = canvas.getContext('2d');
        if (!ctx) return;

        var COLORS = ['255,45,85', '59,108,255', '164,59,255', '255,255,255'];
        var dpr = Math.min(window.devicePixelRatio || 1, 2);
        var w, h, dots = [], link = 130;
        var mouse = { x: -9999, y: -9999 };

        function resize() {
            w = window.innerWidth; h = window.innerHeight;
            canvas.width = w * dpr; canvas.height = h * dpr;
            ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
            // Density scales with screen area, capped for low-end phones.
            var count = Math.min(170, Math.round(w * h / 8500));
            link = w < 700 ? 110 : 150;
            dots = [];
            for (var i = 0; i < count; i++) {
                dots.push({
                    x: Math.random() * w, y: Math.random() * h,
                    vx: (Math.random() - .5) * .35, vy: (Math.random() - .5) * .35,
                    r: Math.random() * 2.2 + .8,
                    c: COLORS[i % COLORS.length === 3 && Math.random() > .4 ? 0 : i % COLORS.length]
                });
            }
        }

        function frame() {
            ctx.clearRect(0, 0, w, h);
            for (var i = 0; i < dots.length; i++) {
                var d = dots[i];
                if (!reduce) {
                    d.x += d.vx; d.y += d.vy;
                    if (d.x < 0 || d.x > w) d.vx *= -1;
                    if (d.y < 0 || d.y > h) d.vy *= -1;

                    // Gentle push away from the cursor.
                    var mx = d.x - mouse.x, my = d.y - mouse.y, md = mx * mx + my * my;
                    if (md < 14400) { var f = (14400 - md) / 14400 * .6; d.x += mx / 120 * f; d.y += my / 120 * f; }
                }

                for (var j = i + 1; j < dots.length; j++) {
                    var e = dots[j], dx = d.x - e.x, dy = d.y - e.y, dist = Math.sqrt(dx * dx + dy * dy);
                    if (dist < link) {
                        ctx.strokeStyle = 'rgba(' + d.c + ',' + (1 - dist / link) * .35 + ')';
                        ctx.lineWidth = .7;
                        ctx.beginPath(); ctx.moveTo(d.x, d.y); ctx.lineTo(e.x, e.y); ctx.stroke();
                    }
                }

                ctx.fillStyle = 'rgba(' + d.c + ',.85)';
                ctx.shadowColor = 'rgba(' + d.c + ',.9)';
                ctx.shadowBlur = 8;
                ctx.beginPath(); ctx.arc(d.x, d.y, d.r, 0, Math.PI * 2); ctx.fill();
                ctx.shadowBlur = 0;
            }
            if (!reduce && !document.hidden) raf = requestAnimationFrame(frame);
        }

        var raf;
        resize();
        frame();
        window.addEventListener('resize', function () { resize(); if (reduce) frame(); }, { passive: true });
        window.addEventListener('pointermove', function (e) { mouse.x = e.clientX; mouse.y = e.clientY; }, { passive: true });
        document.addEventListener('pointerleave', function () { mouse.x = mouse.y = -9999; });
        document.addEventListener('visibilitychange', function () {
            cancelAnimationFrame(raf);
            if (!document.hidden && !reduce) raf = requestAnimationFrame(frame);
        });
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
