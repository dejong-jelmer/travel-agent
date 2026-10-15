{{-- The design, for the PDF and the page alike. Sizes in px: dompdf counts 96 to the inch, as browsers do. Tables
    stand in for flex and grid, which dompdf does not lay out. The weights are classes, as the PDF maps them to fonts.
    Text that has to sit in the middle of a circle or a tile has a line-height of 1.2 times its size and a padding on
    top of half the height less 0.6 times the size: dompdf puts the baseline at 80% of the line, not in its middle, and
    there both agree. The height of such a tile is that of its content, as dompdf counts it, where the page counts
    the padding in. --}}
html, body { margin: 0; padding: 0; background: #faf7f2; }
body { color: #1f3640; font-size: 14px; line-height: 1.5; }
p, h1, h2, h3 { margin: 0; }
a { color: #2f5560; }
img { border: 0; }
table { border-collapse: collapse; }
td { padding: 0; vertical-align: middle; }
.w6 { font-weight: 600; }
.w7 { font-weight: 700; }
.w8 { font-weight: 800; }
.muted { color: #5f6b70; }
.ink { color: #1f3640; }
.full { width: 100%; }
.right { text-align: right; }
.center { text-align: center; }
.nowrap { white-space: nowrap; }
.top-align td { vertical-align: top; }
.kicker { font-size: 11px; letter-spacing: 0.12em; text-transform: uppercase; color: #5f6b70; }
.sun { color: #f2a516; }
{{-- An icon has a cell of its own, which centers it on the row: in a line of text, dompdf sets it on the baseline,
    above the middle. dompdf keeps an image inline whatever its display, so text-align centers it, as margin: auto
    does on the page. --}}
.icon { display: block; width: 16px; height: 16px; }
.icon-20 { display: block; width: 20px; height: 20px; }
.icon-22 { display: block; width: 22px; height: 22px; }
.stop { display: block; width: 18px; height: 18px; margin: 0 auto; }
.day, .line-dot, .line-tile, .number-circle, .icon-tile div { box-sizing: content-box; }

{{-- The header: logo, period, title and the tabs of the three parts. --}}
.top { background: #ffffff; padding: 24px 20px 20px; border-bottom: 1px solid #e6dfd4; }
{{-- The logo has a margin of its own, of about 10px at this size: the negative margin lines it up with the title. --}}
.logo { display: block; width: 150px; height: 79px; margin: -10px 0 -10px -10px; }
.period { margin-top: 20px; font-size: 13px; color: #5f6b70; }
h1 { margin-top: 6px; font-size: 30px; line-height: 1.12; letter-spacing: -0.01em; }
.travelers { margin-top: 6px; font-size: 13px; color: #5f6b70; }
.tabs { margin-top: 20px; padding: 4px; border-radius: 12px; background: #f2ece3; }
.tabs td { width: 33.33%; padding: 0 2px; }
.tab { display: block; padding: 12px 0; border-radius: 9px; font-size: 13px; color: #1f3640; text-align: center; text-decoration: none; }
.tab-active { background: #1f3640; color: #ffffff; }

.main { padding: 28px 20px 36px; }
.block { margin-top: 28px; }
.block-first { margin-top: 0; }

{{-- A journey: its day, then a card per ride, with a row or a hop for each change in between. --}}
{{-- The day is a tile of 56 by 62px. --}}
.day { width: 56px; height: 57px; padding-top: 5px; border-radius: 12px; background: #1f3640; color: #ffffff; text-align: center; }
.day-weekday { font-size: 11px; line-height: 13px; letter-spacing: 0.1em; color: #f2a516; }
.day-number { font-size: 22px; line-height: 26px; }
.day-month { font-size: 11px; line-height: 13px; }
{{-- A heading stays on the page of what it heads. --}}
.journey-head, .city, .heading, .lead { page-break-inside: avoid; page-break-after: avoid; }
.journey-head { margin-bottom: 6px; }
.journey-title { padding-left: 14px; }
.journey-title h2 { font-size: 20px; line-height: 1.25; }
.journey-summary { font-size: 13px; color: #5f6b70; }
.card { margin-top: 10px; padding: 14px 16px; border: 1px solid #e6dfd4; border-radius: 14px; background: #ffffff; page-break-inside: avoid; }
.card-icon { width: 24px; }
.card-line { font-size: 13px; }
.card-duration { font-size: 12px; color: #5f6b70; }
.times { margin-top: 12px; }
.times .time { width: 48px; font-size: 17px; }
.times .dot { width: 18px; padding: 0 9px; }
.times .station { font-size: 15px; }
.connector { width: 2px; height: 18px; margin: 2px auto; background: #c9d3d6; }
.badges { margin-top: 12px; }
{{-- A link to a page elsewhere, after the external-link icon. On the page the icon sits on the middle of the
    capitals 3px under the baseline; the PDF sets its own. --}}
.info-link { margin-top: 8px; font-size: 13px; }
.card .info-link, .route .info-link { margin-top: 12px; }
.info-link img { width: 16px; height: 16px; margin-right: 6px; vertical-align: -3px; }
.badge { display: inline-block; margin-right: 6px; padding: 6px 10px; border-radius: 8px; background: #fdf0d8; color: #6b4100; font-size: 12px; }
.change { margin-top: 10px; padding: 2px 16px; font-size: 13px; color: #5f6b70; page-break-inside: avoid; }
.change .text { padding-left: 10px; }
{{-- Labels with a background are inline blocks: dompdf draws the background of an inline element above its text. --}}
.short { display: inline-block; padding: 3px 8px; border-radius: 6px; background: #fdf0d8; color: #7a4a00; line-height: 16px; }
.hop { margin-top: 10px; padding: 12px 16px; border-radius: 14px; background: #f2ece3; page-break-inside: avoid; }
.hop .text { padding-left: 12px; }
.hop-title { font-size: 14px; }
.hop-sub { font-size: 13px; color: #5f6b70; }
.hop-duration { font-size: 14px; }
.hop-label { font-size: 11px; color: #5f6b70; }
.line-dot { width: 32px; height: 25px; padding-top: 7px; border-radius: 16px; color: #ffffff; font-size: 15px; line-height: 18px; text-align: center; }
.line-tile { width: 26px; height: 21px; padding-top: 5px; border-radius: 7px; color: #ffffff; font-size: 13px; line-height: 16px; text-align: center; }
.line-or { padding: 0 6px; font-size: 13px; color: #5f6b70; }
.line-direction { padding-left: 6px; font-size: 13px; }

{{-- Tips, and the cards of a stay. --}}
.tip { padding: 14px 16px; border-radius: 14px; background: #fdf0d8; color: #4a3410; font-size: 13px; line-height: 1.55; page-break-inside: avoid; }
.tip .text { padding-left: 12px; }
.tip a { color: #6b4100; }
.stay { padding: 18px; border-radius: 16px; background: #1f3640; color: #ffffff; page-break-inside: avoid; }
.stay a { color: #ffffff; }
.stay-name { margin-top: 12px; font-size: 18px; }
.stay-address { margin-top: 12px; font-size: 13px; color: #d5e0e3; }
.tiles { margin-top: 12px; }
.tiles td { width: 50%; }
.tiles .first { padding-right: 4px; }
.tiles .second { padding-left: 4px; }
.tile { padding: 10px 12px; border-radius: 10px; background: #f2ece3; }
.stay .tile { background: #2b4651; }
.tile-label { font-size: 11px; color: #5f6b70; }
.stay .tile-label { color: #c9d6da; }
.tile-value { font-size: 15px; }
.stay-links { margin-top: 12px; font-size: 13px; }
.stay-links .route-to { text-decoration: none; }

{{-- Panels with numbered steps: what to do on a delay, and the way to the door. --}}
.panel { padding: 20px 18px; border: 1px solid #e6dfd4; border-radius: 16px; background: #ffffff; }
.panel h2 { font-size: 20px; }
.panel-intro { margin-top: 4px; font-size: 14px; color: #5f6b70; }
.steps { margin-top: 14px; }
.steps td { padding-bottom: 12px; }
.steps .number { width: 28px; }
.steps .step { padding-left: 12px; font-size: 14px; }
.number-circle { width: 28px; height: 22px; padding-top: 6px; border-radius: 14px; background: #e7eef0; font-size: 13px; line-height: 16px; text-align: center; color: #1f3640; }
.number-sun { background: #f2a516; }
.call { margin-top: 4px; padding: 14px; border-radius: 12px; background: #f2ece3; font-size: 14px; }
.button { display: block; margin-top: 12px; padding: 13px 0; border-radius: 10px; background: #1f3640; color: #ffffff; font-size: 15px; text-align: center; text-decoration: none; }
.button img { width: 18px; height: 18px; vertical-align: middle; margin-right: 6px; }

{{-- The Verblijf part: per stay a card with the address, and one with the way from the station. --}}
.city h2 { font-size: 24px; }
.place { margin-top: 14px; padding: 16px; border: 1px solid #e6dfd4; border-radius: 16px; background: #ffffff; page-break-inside: avoid; }
.place-name { font-size: 18px; }
.place-contact { margin-top: 12px; font-size: 13px; }
.route { margin-top: 14px; padding: 18px 16px; border: 1px solid #e6dfd4; border-radius: 16px; background: #ffffff; }
.route-title { font-size: 15px; }
.route-total { font-size: 12px; color: #5f6b70; }
.line-route { margin-top: 16px; padding: 16px 14px; border-radius: 12px; background: #f7f3ec; page-break-inside: avoid; }
.line-route .rail { width: 9px; }
.line-route .rail-line { width: 11px; border-left: 3px solid #c9d3d6; }
.line-route .label { padding: 0 0 0 12px; }
.line-route .stop-row td { padding-top: 2px; padding-bottom: 2px; }
.line-route .segment td { padding-top: 6px; padding-bottom: 12px; }
.line-route .stop-name { font-size: 14px; }
.line-route .stop-sub, .line-route .segment-duration { font-size: 12px; color: #5f6b70; }
.line-route .segment-lines { font-size: 13px; }

{{-- The Praktisch part. --}}
.checklist { padding: 20px 18px; border-radius: 16px; background: #1f3640; color: #ffffff; }
.checklist h2 { margin-top: 4px; font-size: 20px; }
.checklist .item td { padding: 12px 0; border-bottom: 1px solid #335260; font-size: 14px; }
.checklist .item-last td { border-bottom: 0; }
.checklist .box { width: 32px; }
.checkbox { width: 18px; height: 18px; border-radius: 4px; background: #ffffff; }
.heading .icon-tile { width: 40px; }
.icon-tile div { width: 40px; height: 30px; padding-top: 10px; border-radius: 11px; background: #e7eef0; text-align: center; }
.icon-tile img { display: block; width: 20px; height: 20px; margin: 0 auto; }
.heading h2 { padding-left: 12px; font-size: 20px; }
.box-card { margin-top: 12px; padding: 16px; border: 1px solid #e6dfd4; border-radius: 14px; background: #ffffff; page-break-inside: avoid; }
.box-title { font-size: 15px; }
.box-text { margin-top: 8px; font-size: 14px; line-height: 1.55; }
.lead { margin-top: 12px; font-size: 14px; line-height: 1.55; color: #5f6b70; }
.pair { margin-top: 12px; }
.pair td { width: 50%; vertical-align: top; }
.pair .first { padding-right: 5px; }
.pair .second { padding-left: 5px; }
.pair .box-card { margin-top: 0; padding: 14px; }
.pair .box-title { font-size: 14px; }
.pair .box-text { margin-top: 6px; font-size: 13px; line-height: 1.5; color: #3d4f56; }
.ehic { display: inline-block; padding: 4px 8px; border-radius: 6px; background: #1f3640; color: #ffffff; font-size: 11px; line-height: 13px; letter-spacing: 0.06em; }
.ehic-title .box-title { padding-left: 8px; }
.water { margin-top: 12px; }
.water td { width: 50%; }
.water .first { padding-right: 4px; }
.water .second { padding-left: 4px; }
.water-yes { padding: 10px 12px; border-radius: 10px; background: #e3eee6; }
.water-no { padding: 10px 12px; border-radius: 10px; background: #f8e2dc; }
.water-yes .w8 { font-size: 13px; color: #1d5a3a; }
.water-no .w8 { font-size: 13px; color: #8a2a14; }
.water .meaning { font-size: 12px; }
.pharmacies { margin-top: 12px; border: 1px solid #e6dfd4; border-radius: 14px; background: #ffffff; page-break-inside: avoid; }
.pharmacy { padding: 14px 16px; border-bottom: 1px solid #eee8de; }
.pharmacy-last { border-bottom: 0; }
.pharmacy-name { margin-top: 4px; font-size: 15px; }
.pharmacy a { font-size: 13px; }

{{-- The footer: who to call in an emergency. --}}
.foot { padding: 22px 20px 28px; background: #1f3640; color: #ffffff; }
{{-- The title goes to the next page with the first number, and the top of the footer with it. --}}
.foot-title { page-break-before: avoid; page-break-after: avoid; }
.foot-row { margin-top: 10px; padding: 15px 16px; border-radius: 12px; background: #2b4651; color: #ffffff; page-break-inside: avoid; }
.foot-row a { color: inherit; text-decoration: none; }
.foot-row-agency { background: #f2a516; color: #1f3640; }
.foot-label { font-size: 14px; }
.foot-number { font-size: 18px; }
.foot-row-agency .foot-number { font-size: 16px; }
