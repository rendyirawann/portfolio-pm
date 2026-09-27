{{-- Gaya lembar cetak A4, dipakai bersama oleh keempat versi export
     (portfolio, portfolio ringkas, CV, resume). --}}
    <style>
        :root {
            --ink: #0f172a;
            --text: #1e293b;
            --muted: #64748b;
            --line: #e2e8f0;
            --soft: #f1f5f9;
            --accent: #c8102e;
            --accent-soft: #fdecef;
        }
        * { box-sizing: border-box; }
        html, body { margin: 0; }
        body { background: #e5e7eb; color: var(--text); font: 400 10pt/1.55 'Plus Jakarta Sans', 'Segoe UI', Arial, sans-serif; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        h1, h2, h3, h4, p { margin: 0; }
        ul { margin: 0; padding: 0; list-style: none; }
        a { color: inherit; text-decoration: none; }

        /* ---------- Screen toolbar ---------- */
        .toolbar { position: sticky; top: 0; z-index: 5; display: flex; align-items: center; gap: 12px; padding: 12px 24px; background: var(--ink); color: #fff; font-size: 13px; }
        .toolbar__title { font-weight: 700; }
        .toolbar__hint { color: #94a3b8; }
        .toolbar__actions { margin-left: auto; display: flex; gap: 8px; }
        .btn { display: inline-flex; align-items: center; gap: 8px; padding: 9px 16px; border-radius: 8px; border: 1px solid #334155; background: transparent; color: #fff; font: 600 13px 'Plus Jakarta Sans', sans-serif; cursor: pointer; }
        .btn--primary { background: var(--accent); border-color: var(--accent); }

        /* ---------- A4 sheets ---------- */
        .sheet { position: relative; width: 210mm; min-height: 297mm; margin: 24px auto; background: #fff; box-shadow: 0 10px 40px rgba(15, 23, 42, .15); display: flex; flex-direction: column; }
        .sheet__body { flex: 1; padding: 14mm 16mm 0; display: flex; flex-direction: column; min-height: 0; }
        .sheet__foot { display: flex; justify-content: space-between; padding: 6mm 16mm 8mm; font-size: 7.5pt; color: var(--muted); letter-spacing: .02em; }
        .sheet__foot b { color: var(--ink); font-weight: 600; }

        .eyebrow { font-size: 7.5pt; font-weight: 700; letter-spacing: .16em; text-transform: uppercase; color: var(--accent); }
        .section-title { display: flex; align-items: center; gap: 10px; margin: 0 0 4mm; font-size: 8pt; font-weight: 700; letter-spacing: .16em; text-transform: uppercase; color: var(--ink); }
        .section-title::after { content: ''; flex: 1; height: 1px; background: var(--line); }

        /* ---------- Page 1: profile ---------- */
        .hero { display: grid; grid-template-columns: 52mm 1fr; background: var(--ink); color: #fff; }
        .hero__photo { height: 64mm; background: #1e293b; overflow: hidden; display: flex; align-items: flex-end; justify-content: center; }
        .hero__photo img { width: 100%; height: 100%; object-fit: cover; object-position: top center; }
        .hero__info { padding: 10mm 12mm; display: flex; flex-direction: column; justify-content: center; gap: 2.5mm; border-left: 3px solid var(--accent); }
        .hero__name { font-size: 26pt; font-weight: 800; letter-spacing: -.02em; line-height: 1.05; }
        .hero__role { font-size: 11pt; font-weight: 500; color: #cbd5e1; }
        .hero__contact { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5mm 6mm; margin-top: 4mm; font-size: 8.5pt; color: #e2e8f0; }
        .hero__contact span { display: flex; gap: 6px; }
        .hero__contact em { font-style: normal; color: #94a3b8; min-width: 16mm; }

        .intro { padding: 9mm 16mm 0; }
        .intro p { font-size: 10.5pt; line-height: 1.7; color: var(--text); }
        .stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 4mm; margin-top: 6mm; }
        .stats li { padding: 3.5mm 4mm; background: var(--soft); border-radius: 2mm; border-left: 2px solid var(--accent); }
        .stats b { display: block; font-size: 15pt; font-weight: 800; color: var(--ink); line-height: 1.1; }
        .stats span { font-size: 7.5pt; color: var(--muted); }

        .cols { display: grid; grid-template-columns: 1fr 1.15fr; gap: 10mm; padding: 9mm 16mm 0; flex: 1; min-height: 0; }
        .skill-group + .skill-group { margin-top: 4mm; }
        .skill-group h4 { font-size: 8pt; font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: .08em; margin-bottom: 2mm; }
        .skill { display: grid; grid-template-columns: 1fr 24mm; align-items: center; gap: 4mm; font-size: 9pt; padding: .9mm 0; }
        .skill__bar { height: 1.4mm; background: var(--line); border-radius: 1mm; overflow: hidden; }
        .skill__bar i { display: block; height: 100%; background: var(--ink); }
        .services { margin-top: 7mm; display: grid; gap: 2.5mm; }
        .services li { font-size: 9pt; }
        .services b { color: var(--ink); font-weight: 700; }

        .timeline li { position: relative; padding: 0 0 5mm 6mm; border-left: 1px solid var(--line); }
        .timeline li:last-child { padding-bottom: 0; }
        .timeline li::before { content: ''; position: absolute; left: -1.3mm; top: 1.2mm; width: 2.4mm; height: 2.4mm; border-radius: 50%; background: #fff; border: 2px solid var(--accent); }
        .timeline__period { font-size: 7.5pt; font-weight: 700; color: var(--accent); letter-spacing: .06em; text-transform: uppercase; }
        .timeline h3 { font-size: 10.5pt; font-weight: 700; color: var(--ink); margin-top: .5mm; }
        .timeline__company { font-size: 8.5pt; font-weight: 600; color: var(--muted); }
        .timeline p { font-size: 8.8pt; margin-top: 1mm; }

        /* ---------- Project pages ---------- */
        .project__head { display: flex; justify-content: space-between; align-items: flex-end; gap: 8mm; padding-bottom: 5mm; border-bottom: 1px solid var(--line); }
        .project__num { font-size: 34pt; font-weight: 800; color: var(--line); line-height: .9; }
        .project__title { font-size: 20pt; font-weight: 800; color: var(--ink); letter-spacing: -.01em; line-height: 1.15; margin-top: 1.5mm; }
        .project__cover { margin-top: 6mm; height: 92mm; border-radius: 2.5mm; overflow: hidden; background: var(--soft); }
        .project__cover img { width: 100%; height: 100%; object-fit: cover; }
        .project__grid { display: grid; grid-template-columns: 1fr 52mm; gap: 9mm; margin-top: 7mm; flex: 1; min-height: 0; }
        .project__summary { font-size: 11pt; font-weight: 600; color: var(--ink); line-height: 1.5; }
        .project__desc { margin-top: 4mm; font-size: 9.2pt; line-height: 1.7; white-space: pre-line; color: var(--text); }
        .meta { display: grid; gap: 3.5mm; align-content: start; }
        .meta dt { font-size: 7pt; font-weight: 700; letter-spacing: .14em; text-transform: uppercase; color: var(--muted); }
        .meta dd { margin: .6mm 0 0; font-size: 9pt; font-weight: 600; color: var(--ink); word-break: break-word; }
        .tags { display: flex; flex-wrap: wrap; gap: 1.5mm; margin-top: 1mm; }
        .tags li { font-size: 7.5pt; font-weight: 600; padding: 1mm 2.4mm; border-radius: 1mm; background: var(--accent-soft); color: var(--accent); }
        .thumbs { display: grid; grid-template-columns: repeat(3, 1fr); gap: 3mm; margin-top: 6mm; }
        .thumbs img { width: 100%; height: 26mm; object-fit: cover; border-radius: 1.5mm; background: var(--soft); }

        @page { size: A4; margin: 0; }
        @media print {
            body { background: #fff; }
            .toolbar { display: none; }
            /* Lembar tidak lagi dipaksa setinggi 297mm: kalau isinya pendek,
               halamannya berhenti di situ (tidak ada ruang kosong dipaksakan);
               kalau isinya panjang, ia MENGALIR ke halaman berikutnya alih-alih
               terpotong seperti sebelumnya. */
            .sheet { margin: 0; box-shadow: none; min-height: 0; }
            .sheet + .sheet { page-break-before: always; break-before: page; }

            /* Versi teks (ringkas / CV / resume) dibiarkan menyambung supaya
               tidak menyisakan halaman setengah kosong. */
            .sheet--flow + .sheet--flow { page-break-before: auto; break-before: auto; padding-top: 10mm; }

            /* ---- Jangan pernah memotong satu blok utuh ---- */
            .lite__item, .timeline li, .skill-group, .doc__quotes li,
            .services li, .resume__proj li, .stats, .thumbs,
            .project__cover, .hero, .intro, .doc__table tr {
                page-break-inside: avoid; break-inside: avoid;
            }

            /* Judul tidak boleh tertinggal sendirian di dasar halaman. */
            h1, h2, h3, h4, .section-title {
                page-break-after: avoid; break-after: avoid;
            }

            /* Paragraf: minimal 3 baris tersisa di tiap sisi pemenggalan. */
            p, li { orphans: 3; widows: 3; }

            /* Tabel panjang: header ikut berulang di halaman berikutnya. */
            .doc__table thead { display: table-header-group; }
            .doc__table tr { page-break-inside: avoid; break-inside: avoid; }

            /* Footer menempel di akhir isi, bukan dipaksa ke dasar kertas. */
            .sheet__foot { margin-top: auto; }
        }
        @media screen and (max-width: 860px) {
            .sheet { transform-origin: top center; zoom: .45; }
            .toolbar__hint { display: none; }
        }

        /* ---------- Versi ringkas / CV / resume ---------- */
        .hero--doc { padding-bottom: 6mm; }
        .hero--doc .hero__info { padding-left: 0; }
        .doc__lead { color: var(--muted); }
        .doc__gap { margin-top: 7mm; }

        .doc__table { width: 100%; border-collapse: collapse; margin-top: 3mm; font-size: 8.6pt; }
        .doc__table th { text-align: left; font-size: 7.6pt; letter-spacing: .08em; text-transform: uppercase; color: var(--muted); border-bottom: 1px solid var(--line); padding: 2mm 3mm 2mm 0; }
        .doc__table td { padding: 2.4mm 3mm 2.4mm 0; border-bottom: 1px solid var(--line); vertical-align: top; }
        .doc__table small { color: var(--muted); }

        .doc__quotes { list-style: none; display: grid; gap: 3mm; margin-top: 3mm; }
        .doc__quotes li { padding: 3mm 4mm; border-left: 2px solid var(--accent); background: var(--accent-soft); }
        .doc__quotes p { font-style: italic; }
        .doc__quotes span { display: block; margin-top: 1.5mm; font-size: 8.4pt; color: var(--muted); }

        .lite__lead { color: var(--muted); margin-bottom: 4mm; }
        .lite__cont { color: var(--muted); font-weight: 400; }
        .lite { list-style: decimal; padding-left: 5mm; display: grid; gap: 5mm; }
        .lite__item { break-inside: avoid; }
        .lite__head { display: flex; align-items: baseline; gap: 3mm; flex-wrap: wrap; }
        .lite__title { font-size: 11.5pt; font-weight: 700; }
        .lite__type { font-size: 7.8pt; letter-spacing: .08em; text-transform: uppercase; color: var(--muted); }
        .lite__summary { margin-top: 1mm; font-weight: 500; }
        .lite__desc { margin-top: 1.5mm; color: var(--muted); }
        .lite__meta { margin-top: 2mm; display: grid; gap: 1.2mm; font-size: 8.6pt; }
        .lite__meta > div { display: grid; grid-template-columns: 22mm 1fr; gap: 3mm; }
        .lite__meta dt { color: var(--muted); }

        .resume__skills { color: var(--muted); font-size: 9pt; }
        .resume__proj { list-style: none; display: grid; gap: 3mm; margin-top: 2mm; }
        .resume__proj b { display: block; }
        .resume__proj span { font-size: 7.8pt; letter-spacing: .06em; text-transform: uppercase; color: var(--muted); }
        .resume__proj p { color: var(--muted); font-size: 8.6pt; margin-top: .8mm; }
        .resume__stats { margin-top: 6mm; }
        .resume__note { margin-top: 6mm; padding-top: 3mm; border-top: 1px solid var(--line); font-size: 8.4pt; color: var(--muted); }

        /* ================= CV & RESUME (tata letak baru) =================
           Dua kolom: sidebar sempit untuk data ringkas, kolom utama untuk
           narasi. Jarak antar bagian sengaja dilonggarkan supaya tidak padat.
           Paragraf dibuat rata kanan-kiri. */

        .doc2 { display: flex; flex-direction: column; flex: 1; }

        .doc2__head { display: grid; grid-template-columns: auto 1fr; gap: 9mm; align-items: center;
                      padding: 14mm 16mm 10mm; background: var(--ink); color: #fff; }
        .doc2__head--nophoto { grid-template-columns: 1fr; }
        .doc2__photo { width: 34mm; height: 34mm; border-radius: 50%; overflow: hidden;
                       background: #1e293b; border: 1.2mm solid rgba(255,255,255,.14); }
        .doc2__photo img { width: 100%; height: 100%; object-fit: cover; object-position: top center; }
        .doc2__kicker { font-size: 7.6pt; letter-spacing: .22em; text-transform: uppercase; color: var(--accent); font-weight: 700; }
        .doc2__name { font-size: 25pt; font-weight: 800; letter-spacing: -.02em; line-height: 1.08; margin-top: 2mm; color: #fff; }
        .doc2__role { font-size: 11pt; color: #cbd5e1; margin-top: 1.5mm; }
        .doc2__line { display: flex; flex-wrap: wrap; gap: 3mm 7mm; margin-top: 5mm; font-size: 8.6pt; color: #e2e8f0; }
        .doc2__line span { display: flex; gap: 2mm; align-items: baseline; }
        .doc2__line em { font-style: normal; color: #94a3b8; font-size: 7.4pt;
                         letter-spacing: .1em; text-transform: uppercase; }

        .doc2__body { display: grid; grid-template-columns: 58mm 1fr; gap: 11mm; padding: 11mm 16mm 0; flex: 1; }
        .doc2__body--wide { grid-template-columns: 1fr; }

        .doc2__h { font-size: 8.4pt; font-weight: 700; letter-spacing: .18em; text-transform: uppercase;
                   color: var(--ink); padding-bottom: 2mm; border-bottom: .4mm solid var(--accent); margin-bottom: 4.5mm; }
        .doc2__h + * { margin-top: 0; }
        .doc2__sec { margin-bottom: 9mm; }
        .doc2__sec:last-child { margin-bottom: 0; }

        /* Paragraf rata kanan-kiri, dengan jarak baris yang lega. */
        .doc2 p, .doc2 li { text-align: justify; hyphens: auto; -webkit-hyphens: auto; }
        .doc2__lead { font-size: 10pt; line-height: 1.85; color: var(--text); }

        /* Sidebar */
        .doc2__meta { display: grid; gap: 3mm; font-size: 8.8pt; }
        .doc2__meta div { display: grid; gap: .8mm; }
        .doc2__meta dt { font-size: 7.2pt; letter-spacing: .12em; text-transform: uppercase; color: var(--muted); }
        .doc2__meta dd { margin: 0; font-weight: 600; color: var(--ink); word-break: break-word; }

        .doc2__skill { margin-bottom: 5mm; }
        .doc2__skill h5 { font-size: 8pt; letter-spacing: .1em; text-transform: uppercase; color: var(--accent); margin-bottom: 2.5mm; }
        .doc2__skill ul { list-style: none; display: grid; gap: 2.2mm; }
        .doc2__skill li { display: grid; gap: 1.2mm; font-size: 9pt; text-align: left; }
        .doc2__skill span { display: block; height: 1.2mm; background: var(--line); border-radius: 1mm; overflow: hidden; }
        .doc2__skill i { display: block; height: 100%; background: var(--ink); }

        .doc2__tags { display: flex; flex-wrap: wrap; gap: 1.8mm; }
        .doc2__tags li { list-style: none; font-size: 8.2pt; padding: 1mm 2.4mm; border-radius: 1mm;
                         background: var(--soft); color: var(--ink); text-align: left; }

        /* Riwayat pengalaman — lebih lega dari versi lama */
        .doc2__exp { list-style: none; display: grid; gap: 7mm; }
        .doc2__exp li { position: relative; padding-left: 7mm; }
        .doc2__exp li::before { content: ''; position: absolute; left: 0; top: 1.6mm; width: 2.6mm; height: 2.6mm;
                                border-radius: 50%; background: #fff; border: .7mm solid var(--accent); }
        .doc2__exp li::after { content: ''; position: absolute; left: 1.1mm; top: 5.5mm; bottom: -7mm; width: .3mm; background: var(--line); }
        .doc2__exp li:last-child::after { display: none; }
        .doc2__when { font-size: 7.6pt; letter-spacing: .12em; text-transform: uppercase; color: var(--muted); }
        .doc2__what { font-size: 11.5pt; font-weight: 700; color: var(--ink); margin-top: 1mm; line-height: 1.3; }
        .doc2__where { font-size: 9.2pt; font-weight: 600; color: var(--accent); margin-top: .8mm; }
        .doc2__exp p { margin-top: 2.5mm; font-size: 9.2pt; line-height: 1.75; color: var(--text); }

        /* Daftar karya */
        .doc2__works { list-style: none; display: grid; gap: 5mm; }
        .doc2__works li { display: grid; grid-template-columns: 1fr auto; gap: 2mm 5mm; padding-bottom: 4mm; border-bottom: .2mm solid var(--line); }
        .doc2__works li:last-child { border-bottom: 0; padding-bottom: 0; }
        .doc2__works b { font-size: 10.5pt; color: var(--ink); }
        .doc2__works .doc2__when { grid-column: 2; grid-row: 1; text-align: right; }
        .doc2__works p { grid-column: 1 / -1; font-size: 9pt; line-height: 1.7; color: var(--muted); }
        .doc2__works small { grid-column: 1 / -1; font-size: 8.2pt; color: var(--muted); }

        .doc2__quote { list-style: none; display: grid; gap: 4mm; }
        .doc2__quote li { padding: 4mm 5mm; background: var(--soft); border-left: .8mm solid var(--accent); }
        .doc2__quote p { font-style: italic; font-size: 9.2pt; line-height: 1.7; }
        .doc2__quote span { display: block; margin-top: 2mm; font-size: 8.4pt; color: var(--muted); text-align: left; }

        .doc2__note { margin: 8mm 16mm 0; padding-top: 4mm; border-top: .2mm solid var(--line);
                      font-size: 8.4pt; color: var(--muted); text-align: left; }
    </style>
