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
        .sheet { position: relative; width: 210mm; height: 297mm; margin: 24px auto; background: #fff; box-shadow: 0 10px 40px rgba(15, 23, 42, .15); overflow: hidden; display: flex; flex-direction: column; }
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

        .cols { display: grid; grid-template-columns: 1fr 1.15fr; gap: 10mm; padding: 9mm 16mm 0; flex: 1; min-height: 0; overflow: hidden; }
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
        .project__desc { margin-top: 4mm; font-size: 9.2pt; line-height: 1.7; white-space: pre-line; color: var(--text); overflow: hidden; }
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
            .sheet { margin: 0; box-shadow: none; page-break-after: always; break-after: page; }
            .sheet:last-child { page-break-after: auto; break-after: auto; }
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
    </style>
