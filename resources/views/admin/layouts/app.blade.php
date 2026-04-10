<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Live By Life</title>
  <link rel="icon" type="image/x-icon" href="#">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&family=Playfair+Display:wght@700;800&display=swap" rel="stylesheet">
  @include('admin.particle.css')

  <style>
    /* ═══════════════════════════════════════════════
       THEME : Light Teal  |  LEAD CRM
       Primary   #008d8d   (teal)
       Accent    #00b5b5   (light teal)
       Dark      #006666   (dark teal)
       Support   #e0f7f7   (teal tint bg)
       Sidebar   #f4fafa → #eaf4f4
       Navbar    #008d8d → #006d6d
    ═══════════════════════════════════════════════ */
    :root {
      --pri       : #008d8d;
      --pri-lt    : #00b5b5;
      --pri-dk    : #006666;
      --pri-tint  : #e0f7f7;
      --pri-muted : #b2dfdf;
      --sb-from   : #f4fafa;
      --sb-to     : #e8f4f4;
      --nb-from   : #008d8d;
      --nb-to     : #006d6d;
      --bg        : #f0f6f6;
      --sb-w      : 225px;
      --sb-mini   : 4.6rem;
      --nb-h      : 57px;
      --radius    : 10px;
      --shadow    : 0 4px 24px rgba(0,141,141,.10);
      --text-dark : #0d2e2e;
      --text-mid  : #2a5050;
      --text-soft : #5a8080;
      --border    : #c8e6e6;
    }

    *, *::before, *::after { box-sizing: border-box; }

    body {
      font-family: "DM Sans", -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
      background: var(--bg);
      color: var(--text-dark);
    }

    /* Scrollbar */
    ::-webkit-scrollbar            { width:5px; height:5px; }
    ::-webkit-scrollbar-track      { background:#e0f0f0; }
    ::-webkit-scrollbar-thumb      { background:#a0cece; border-radius:10px; }
    ::-webkit-scrollbar-thumb:hover{ background:var(--pri); }

    /* ════════════════ NAVBAR ════════════════════ */
    .main-header.navbar {
      background: linear-gradient(135deg, var(--nb-from) 0%, var(--nb-to) 100%) !important;
      border-bottom: 2px solid rgba(0,181,181,.4) !important;
      box-shadow: 0 3px 20px rgba(0,141,141,.3);
      min-height: var(--nb-h);
      padding: 0 1rem;
      z-index: 1040;
    }

    .main-header .nav-link,
    .navbar-light .navbar-nav .nav-link,
    .navbar-dark  .navbar-nav .nav-link {
      color: rgba(255,255,255,.85) !important;
      border-radius: 7px;
      margin: 0 2px;
      transition: background .2s, color .2s;
    }
    .main-header .nav-link:hover {
      background: rgba(255,255,255,.15) !important;
      color: #fff !important;
    }

    /* Navbar dropdown */
    .main-header .dropdown-menu {
      background: #fff;
      border: 1px solid var(--border);
      border-radius: var(--radius);
      box-shadow: 0 10px 35px rgba(0,141,141,.15);
      padding: .4rem;
      min-width: 200px;
    }
    .main-header .dropdown-item {
      border-radius: 6px;
      padding: .45rem .9rem;
      font-size: .85rem;
      color: var(--text-mid);
      transition: background .15s;
    }
    .main-header .dropdown-item:hover {
      background: var(--pri-tint);
      color: var(--pri-dk);
    }
    .main-header .dropdown-divider   { border-color: var(--border); margin: .25rem 0; }
    .main-header .dropdown-header    { color: var(--text-soft) !important; font-size:.7rem; letter-spacing:1px; text-transform:uppercase; }

    /* User pill */
    .navbar-user-pill {
      background: rgba(255,255,255,.15);
      border: 1px solid rgba(255,255,255,.3);
      border-radius: 8px;
      padding: .3rem .75rem !important;
      display: flex; align-items: center; gap:.4rem;
    }
    .navbar-user-pill .user-avatar {
      background: rgba(255,255,255,.25);
      border-radius: 50%;
      width:28px; height:28px;
      display:inline-flex; align-items:center; justify-content:center;
    }

    /* ════════════════ SIDEBAR ═══════════════════ */
    .main-sidebar {
      background: linear-gradient(180deg, var(--sb-from) 0%, var(--sb-to) 100%) !important;
      width: var(--sb-w) !important;
      box-shadow: 4px 0 24px rgba(0,141,141,.12);
      border-right: 1px solid var(--border);
      overflow-x: hidden;
      overflow-y: auto;
      transition: width .3s ease, margin-left .3s ease !important;
    }

    /* Brand */
    [class*=sidebar-dark] .brand-link,
    .brand-link {
      background: rgba(0,141,141,.08) !important;
      border-bottom: 1px solid var(--border) !important;
      color: var(--text-dark) !important;
      padding: .78rem .95rem;
      display: flex;
      align-items: center;
      gap: .9rem;
      min-height: 68px;
      width: 100%;
      white-space: nowrap;
      overflow: hidden;
    }
    .brand-link .brand-image {
      width: 38px;
      height: 38px;
      padding: 4px;
      border-radius: 10px;
      border: 1px solid rgba(0,141,141,.22);
      background: linear-gradient(180deg, #ffffff 0%, #eef9f9 100%);
      box-shadow: 0 3px 10px rgba(0,141,141,.12);
      flex-shrink: 0;
      object-fit: contain;
    }
    .brand-link .brand-text {
      font-family: 'Playfair Display', serif;
      font-size: 1rem;
      font-weight: 700;
      color: var(--pri-dk);
      white-space: nowrap;
    }

    .sidebar {
      padding: .5rem .35rem;
      overflow-x: hidden;
    }

    /* ── Sidebar section label ── */
    .nav-sidebar .nav-header {
      font-size: .62rem;
      font-weight: 700;
      letter-spacing: 1.6px;
      text-transform: uppercase;
      color: var(--pri-muted);
      padding: .9rem .85rem .3rem;
    }

    .nav-sidebar .nav-item {
      width: 100%;
      margin-bottom: 2px;
    }
    .nav-sidebar > .nav-item > .nav-link,
    .nav-sidebar .nav-link {
      color: var(--text-mid) !important;
      border-radius: 8px;
      padding: .52rem .75rem !important;
      display: flex;
      align-items: center;
      gap: .55rem;
      font-size: .85rem;
      font-weight: 500;
      transition: background .2s, color .2s;
      white-space: nowrap;
      overflow: hidden;
    }
    .nav-sidebar .nav-link:hover {
      background: var(--pri-tint) !important;
      color: var(--pri-dk) !important;
    }
    .nav-sidebar > .nav-item > .nav-link.active {
      background: linear-gradient(135deg, var(--pri-dk), var(--pri)) !important;
      color: #fff !important;
      box-shadow: 0 4px 16px rgba(0,141,141,.25);
    }

    .nav-sidebar .nav-icon {
      font-size: 14px !important;
      width: 20px;
      min-width: 20px;
      text-align: center;
      color: var(--pri);
      flex-shrink: 0;
    }
    .nav-sidebar > .nav-item > .nav-link.active .nav-icon { color: #fff; }

    .nav-sidebar p {
      margin: 0; flex: 1;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      transition: opacity .25s;
    }

    /* Treeview */
    .nav-treeview {
      margin-top: .25rem;
      margin-left: .35rem;
      padding: .3rem 0 .35rem .85rem;
      background: rgba(0,141,141,.05);
      border-left: 2px solid rgba(0,141,141,.14);
      border-radius: 0 0 10px 10px;
    }
    .nav-treeview .nav-link {
      min-height: 40px;
      font-size: .8rem !important;
      color: var(--text-soft) !important;
      padding: .48rem .7rem !important;
      border-radius: 8px;
      gap: .5rem;
    }
    .nav-treeview .nav-link:hover {
      background: var(--pri-tint) !important;
      color: var(--pri-dk) !important;
    }
    .nav-treeview .nav-link.active {
      background: rgba(0,141,141,.12) !important;
      color: var(--pri-dk) !important;
    }

    /* ════════════════ CONTENT WRAPPER ══════════ */
    .content-wrapper {
      background: var(--bg) !important;
      min-height: calc(100vh - var(--nb-h));
    }

    /* ════════════════ CARDS ════════════════════ */
    .card {
      border: none !important;
      border-radius: var(--radius) !important;
      box-shadow: var(--shadow) !important;
    }
    .card-header {
      background: #fff !important;
      border-bottom: 2px solid var(--border) !important;
      border-radius: var(--radius) var(--radius) 0 0 !important;
      font-weight: 700; font-size: .9rem; color: var(--text-dark);
      padding: .9rem 1.25rem;
    }

    /* ════════════════ BUTTONS ══════════════════ */
    .btn {
      border-radius: 7px !important;
      font-weight: 600; font-size: .82rem;
      padding: .42rem .95rem;
      border: none;
      transition: transform .18s, box-shadow .18s;
    }
    .btn:hover  { transform: translateY(-2px); box-shadow: 0 5px 16px rgba(0,0,0,.15); }
    .btn:active { transform: translateY(0); }

    .btn-primary { background: linear-gradient(135deg, var(--pri-dk), var(--pri-lt)) !important; color:#fff !important; }
    .btn-success { background: linear-gradient(135deg,#1e8449,#27ae60) !important;  color:#fff !important; }
    .btn-danger  { background: linear-gradient(135deg,#922b21,#e74c3c) !important;  color:#fff !important; }
    .btn-warning { background: linear-gradient(135deg,#b7770d,#f39c12) !important;  color:#fff !important; }
    .btn-info    { background: linear-gradient(135deg,#006080,#0097b5) !important;  color:#fff !important; }
    .btn-default { background:#f0fafa !important; color:var(--pri-dk) !important; border:1px solid var(--border) !important; }
    .btn-default:hover { background:var(--pri-tint) !important; }
    .btn-xs { padding:.2rem .5rem;  font-size:.72rem; border-radius:5px !important; }
    .btn-sm { padding:.3rem .72rem; font-size:.78rem; }

    /* ════════════════ TABLES ═══════════════════ */
    .table thead th {
      background: var(--pri);
      color: #fff;
      font-weight: 700; font-size: .78rem;
      text-transform: uppercase; letter-spacing: .5px;
      border-bottom: 2px solid var(--pri-dk) !important;
      border-color: var(--pri-dk) !important;
      padding: .68rem .9rem;
    }
    .table td {
      vertical-align: middle; font-size: .85rem;
      color: var(--text-dark); padding: .6rem .9rem;
      border-color: #e0eeee;
    }
    .table-hover tbody tr:hover   { background: var(--pri-tint); }
    .table-striped tbody tr:nth-of-type(odd) { background: #f7fdfd; }

    /* Pagination */
    .dataTables_wrapper .dataTables_paginate .paginate_button.current,
    .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
      background: var(--pri) !important; color:#fff !important;
      border-radius:6px; border:none !important;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
      background: var(--pri-tint) !important; color:var(--pri-dk) !important;
      border:none !important; border-radius:6px;
    }

    /* ════════════════ FORMS ════════════════════ */
    .form-control {
      border-radius: 7px !important;
      border: 1.5px solid var(--border);
      font-size: .85rem; color: var(--text-dark);
      background: #fff;
      transition: border-color .2s, box-shadow .2s;
    }
    .form-control:focus {
      border-color: var(--pri) !important;
      box-shadow: 0 0 0 3px rgba(0,141,141,.12) !important;
    }
    label { font-size:.8rem; font-weight:600; color:var(--text-mid); margin-bottom:.3rem; }

    /* Select2 */
    .select2-container--default .select2-selection--single {
      border: 1.5px solid var(--border) !important;
      border-radius: 7px !important; height:36px !important;
      display:flex; align-items:center;
    }
    .select2-container--default.select2-container--focus .select2-selection--single,
    .select2-container--default.select2-container--open  .select2-selection--single {
      border-color:var(--pri) !important;
      box-shadow:0 0 0 3px rgba(0,141,141,.12) !important;
    }
    .select2-results__option--highlighted { background:var(--pri) !important; color:#fff !important; }

    /* ════════════════ BADGES ═══════════════════ */
    .badge { border-radius:5px; font-size:.72rem; font-weight:600; padding:.3em .65em; }
    .badge-primary   { background:var(--pri);  color:#fff; }
    .badge-success   { background:#27ae60;     color:#fff; }
    .badge-warning   { background:#f39c12;     color:#fff; }
    .badge-danger    { background:#e74c3c;     color:#fff; }
    .badge-info      { background:#0097b5;     color:#fff; }
    .badge-secondary { background:#555;        color:#fff; }

    /* ════════════════ ALERTS ═══════════════════ */
    .alert { border:none; border-radius:var(--radius); font-size:.85rem; border-left:4px solid; }
    .alert-success { background:#e8f8f0; border-color:#27ae60; color:#1e8449; }
    .alert-danger  { background:#fdf3f3; border-color:#e74c3c; color:#922b21; }
    .alert-warning { background:#fdf8e8; border-color:#f39c12; color:#7d5c00; }
    .alert-info    { background:var(--pri-tint); border-color:var(--pri); color:var(--pri-dk); }

    /* ════════════════ FOOTER ═══════════════════ */
    .main-footer {
      background: #fff !important;
      border-top: 2px solid var(--border) !important;
      color: var(--text-soft); font-size: .8rem;
      padding: .75rem 1.25rem;
    }
    .main-footer a { color: var(--pri); font-weight: 600; }
    .main-footer a:hover { color: var(--pri-dk); }

    /* ════════════════ LAYOUT MARGINS ═══════════ */
    @media (min-width: 768px) {
      body:not(.sidebar-mini-md):not(.sidebar-mini-xs):not(.layout-top-nav) .main-header,
      body:not(.sidebar-mini-md):not(.sidebar-mini-xs):not(.layout-top-nav) .content-wrapper,
      body:not(.sidebar-mini-md):not(.sidebar-mini-xs):not(.layout-top-nav) .main-footer {
        margin-left: var(--sb-w);
        transition: margin-left .3s ease;
      }
    }
        body.sidebar-collapse .main-sidebar {
          width: var(--sb-mini) !important;
        }
        body.sidebar-collapse .main-sidebar .brand-link .brand-text,
        body.sidebar-collapse .main-sidebar .sidebar .nav-link p,
        body.sidebar-collapse .main-sidebar .sidebar .nav-header {
          opacity: 0;
          transition: opacity .2s ease;
          pointer-events: none;
        }
        body.sidebar-collapse .main-sidebar:hover .brand-link .brand-text,
        body.sidebar-collapse .main-sidebar:hover .sidebar .nav-link p,
        body.sidebar-collapse .main-sidebar:hover .sidebar .nav-header {
          opacity: 1;
          pointer-events: auto;
        }
      }
      @media (min-width: 768px) {
        body:not(.sidebar-collapse):not(.sidebar-mini-md):not(.sidebar-mini-xs):not(.layout-top-nav) .main-header,
        body:not(.sidebar-collapse):not(.sidebar-mini-md):not(.sidebar-mini-xs):not(.layout-top-nav) .content-wrapper,
        body:not(.sidebar-collapse):not(.sidebar-mini-md):not(.sidebar-mini-xs):not(.layout-top-nav) .main-footer {
          margin-left: var(--sb-w);
          transition: margin-left .3s ease;
        }

        body.sidebar-collapse:not(.sidebar-mini-md):not(.sidebar-mini-xs):not(.layout-top-nav) .main-header,
        body.sidebar-collapse:not(.sidebar-mini-md):not(.sidebar-mini-xs):not(.layout-top-nav) .content-wrapper,
        body.sidebar-collapse:not(.sidebar-mini-md):not(.sidebar-mini-xs):not(.layout-top-nav) .main-footer {
          margin-left: 0;
          transition: margin-left .3s ease;
        }
      }
      @media (max-width: 991.98px) {
        body:not(.sidebar-mini-md):not(.sidebar-mini-xs):not(.layout-top-nav) .main-header,
        body:not(.sidebar-mini-md):not(.sidebar-mini-xs):not(.layout-top-nav) .content-wrapper,
        body:not(.sidebar-mini-md):not(.sidebar-mini-xs):not(.layout-top-nav) .main-footer {
          margin-left: 0;
        }
      }

    /* ════════════════ STAT CARDS (small-box) ═══ */
    .small-box {
      border-radius: 14px !important;
      overflow: hidden;
      box-shadow: 0 4px 20px rgba(0,141,141,.12) !important;
      border: 1px solid rgba(0,141,141,.08);
      transition: transform .22s, box-shadow .22s;
      background: #fff;
    }
    .small-box:hover {
      transform: translateY(-4px);
      box-shadow: 0 10px 32px rgba(0,141,141,.2) !important;
    }
    .small-box > .inner {
      padding: 1.1rem 1.25rem .85rem;
    }
    .small-box > .inner h3 {
      font-size: 2rem; font-weight: 800;
      color: var(--text-dark); margin: 0 0 .15rem;
      font-family: 'DM Sans', sans-serif;
    }
    .small-box > .inner p {
      font-size: .78rem; font-weight: 600;
      color: var(--text-soft); margin: 0;
      text-transform: uppercase; letter-spacing: .6px;
    }
    .small-box > .small-box-footer {
      background: rgba(0,141,141,.07) !important;
      color: var(--pri) !important;
      font-size: .78rem; font-weight: 600;
      padding: .45rem 1rem;
      display: flex; align-items: center; gap: .35rem;
      border-top: 1px solid rgba(0,141,141,.08);
      text-decoration: none;
      transition: background .18s, color .18s;
    }
    .small-box > .small-box-footer:hover {
      background: rgba(0,141,141,.14) !important;
      color: var(--pri-dk) !important;
    }
    .small-box .icon { opacity: .12; }
    .small-box .icon i { font-size: 4rem !important; color: var(--pri); }

    /* Teal stat box variant */
    .small-box.bg-teal,
    .small-box.bg-primary {
      background: linear-gradient(135deg, var(--pri-dk), var(--pri-lt)) !important;
      border-color: transparent;
    }
    .small-box.bg-teal > .inner h3,
    .small-box.bg-teal > .inner p,
    .small-box.bg-primary > .inner h3,
    .small-box.bg-primary > .inner p { color: #fff !important; }
    .small-box.bg-teal .icon i,
    .small-box.bg-primary .icon i    { color: rgba(255,255,255,.3); opacity:1; }
    .small-box.bg-teal > .small-box-footer,
    .small-box.bg-primary > .small-box-footer {
      background: rgba(0,0,0,.12) !important;
      color: rgba(255,255,255,.9) !important;
      border-top: 1px solid rgba(255,255,255,.1);
    }

    /* ════════════════ INFO-BOX ══════════════════ */
    .info-box {
      border-radius: 14px !important;
      box-shadow: 0 4px 20px rgba(0,141,141,.10) !important;
      border: 1px solid rgba(0,141,141,.08);
      background: #fff;
      overflow: hidden;
      transition: transform .22s, box-shadow .22s;
    }
    .info-box:hover {
      transform: translateY(-3px);
      box-shadow: 0 8px 28px rgba(0,141,141,.16) !important;
    }
    .info-box-icon {
      border-radius: 12px 0 0 12px !important;
      background: linear-gradient(135deg, var(--pri-dk), var(--pri)) !important;
      width: 72px;
      display: flex; align-items: center; justify-content: center;
    }
    .info-box-icon i { color: #fff; font-size: 1.5rem !important; }
    .info-box-content { padding: .85rem 1rem; }
    .info-box-text {
      font-size: .72rem; font-weight: 700;
      text-transform: uppercase; letter-spacing: .7px;
      color: var(--text-soft);
    }
    .info-box-number {
      font-size: 1.5rem; font-weight: 800;
      color: var(--text-dark);
    }

    /* ════════════════ FORM CARDS ════════════════ */
    /* Pull-up hero section used on create/edit pages */
    .page-hero {
      background: linear-gradient(135deg, #006666 0%, #008d8d 55%, #00a8a8 100%);
      padding: 1.6rem 2rem 4.2rem;
      position: relative; overflow: hidden;
    }
    .page-hero::before {
      content:''; position:absolute; inset:0; pointer-events:none;
      background-image: radial-gradient(rgba(255,255,255,.07) 1px, transparent 1px);
      background-size: 26px 26px;
    }
    .page-hero .orb {
      position:absolute; border-radius:50%; pointer-events:none;
      width:200px; height:200px;
      background:radial-gradient(circle,rgba(255,255,255,.12) 0%,transparent 65%);
      top:-60px; right:40px;
    }
    .page-hero h1 {
      font-family:'Playfair Display',serif;
      font-size:1.45rem; font-weight:800; color:#fff; margin:0 0 .25rem;
      position:relative; z-index:2;
    }
    .page-hero p { color:rgba(255,255,255,.6); font-size:.82rem; margin:0; position:relative; z-index:2; }

    /* Pull-card wrapper */
    .pull-card {
      margin-top: -2.4rem;
      position: relative; z-index: 10;
      padding: 0 1.5rem;
    }

    /* Form card shell */
    .form-card, .main-card {
      background: #fff;
      border-radius: 16px;
      box-shadow: 0 6px 32px rgba(0,141,141,.10);
      border: 1px solid #d0eded;
      overflow: hidden;
    }
    .form-card-head, .main-card-head {
      padding: 1.1rem 1.5rem;
      border-bottom: 1px solid #e4f0f0;
      display: flex; align-items: center; justify-content: space-between;
      flex-wrap: wrap; gap: .75rem;
      background: #f9fdfd;
    }
    .form-card-title, .main-card-title {
      font-size: .92rem; font-weight: 800;
      color: var(--text-dark);
      display: flex; align-items: center; gap: .5rem;
    }
    .form-card-title i, .main-card-title i { color: var(--pri); }
    .form-card-body, .main-card-body { padding: 1.75rem 1.5rem; }

    /* Form field rows */
    .frow { display:grid; grid-template-columns:1fr 1fr; gap:1.25rem; margin-bottom:1.25rem; }
    @media(max-width:640px){ .frow { grid-template-columns:1fr; } }
    .frow-3 { grid-template-columns:1fr 1fr 1fr; }
    @media(max-width:768px){ .frow-3 { grid-template-columns:1fr 1fr; } }
    @media(max-width:480px){ .frow-3 { grid-template-columns:1fr; } }
    .frow-full { grid-template-columns:1fr; }
    .fgroup { display:flex; flex-direction:column; }
    .flabel {
      font-size:.76rem; font-weight:700; color:var(--text-mid);
      margin-bottom:.42rem; letter-spacing:.02em;
      display:flex; align-items:center; gap:.3rem;
    }
    .flabel .req { color:#dc2626; }
    .finput, .fselect, .ftextarea {
      width:100%; border:1.5px solid var(--border); border-radius:8px;
      padding:.52rem .85rem; font-size:.86rem; color:var(--text-dark);
      font-family:'DM Sans',sans-serif; background:#fff;
      transition:border-color .2s, box-shadow .2s; outline:none;
    }
    .finput:focus, .fselect:focus, .ftextarea:focus {
      border-color:var(--pri);
      box-shadow:0 0 0 3px rgba(0,141,141,.12);
    }
    .finput.is-invalid, .fselect.is-invalid, .ftextarea.is-invalid {
      border-color:#dc2626;
      box-shadow:0 0 0 3px rgba(220,38,38,.08);
    }
    .fselect { cursor:pointer; }
    .ftextarea { resize:vertical; min-height:90px; }
    .ferr { font-size:.74rem; color:#dc2626; margin-top:.3rem; }

    /* Section divider inside forms */
    .fsection {
      font-size:.68rem; font-weight:800; text-transform:uppercase;
      letter-spacing:1.6px; color:var(--text-soft);
      display:flex; align-items:center; gap:.6rem;
      margin:1.75rem 0 1.1rem;
    }
    .fsection::after { content:''; flex:1; height:1px; background:#dceaea; }
    .fsection .fsi {
      width:20px; height:20px;
      background:linear-gradient(135deg,var(--pri-dk),var(--pri));
      border-radius:5px; display:inline-flex; align-items:center; justify-content:center;
      font-size:.58rem; color:#fff;
    }

    /* Form action buttons */
    .btn-submit {
      background:linear-gradient(135deg,var(--pri-dk),var(--pri-lt));
      color:#fff; border:none; border-radius:8px;
      padding:.55rem 1.4rem; font-size:.86rem; font-weight:700;
      font-family:'DM Sans',sans-serif; cursor:pointer;
      display:inline-flex; align-items:center; gap:.4rem;
      box-shadow:0 3px 12px rgba(0,141,141,.25);
      transition:transform .18s, box-shadow .18s;
    }
    .btn-submit:hover { transform:translateY(-2px); box-shadow:0 6px 20px rgba(0,141,141,.35); }
    .btn-cancel {
      background:#f0fafa; color:var(--text-mid);
      border:1.5px solid var(--border); border-radius:8px;
      padding:.52rem 1.2rem; font-size:.86rem; font-weight:600;
      text-decoration:none; display:inline-flex; align-items:center; gap:.4rem;
      transition:background .18s;
    }
    .btn-cancel:hover { background:var(--pri-tint); color:var(--pri-dk); }
    .btn-create {
      background:linear-gradient(135deg,var(--pri-dk),var(--pri-lt));
      color:#fff !important; border-radius:8px; padding:.45rem 1.1rem;
      font-size:.82rem; font-weight:700; text-decoration:none;
      display:inline-flex; align-items:center; gap:.4rem; border:none;
      transition:transform .18s, box-shadow .18s;
      box-shadow:0 3px 12px rgba(0,141,141,.25);
    }
    .btn-create:hover { transform:translateY(-2px); box-shadow:0 6px 20px rgba(0,141,141,.35); color:#fff !important; }

    /* ════════════════ TABLE CARDS ═══════════════ */
    table.dataTable thead th {
      background: var(--pri) !important;
      color: #fff !important;
      font-size: .7rem !important; font-weight: 700 !important;
      text-transform: uppercase; letter-spacing: .7px;
      border-bottom: 2px solid var(--pri-dk) !important;
      padding: .72rem 1rem !important; white-space: nowrap;
    }
    table.dataTable tbody tr { border-bottom: 1px solid #e4f0f0 !important; transition: background .15s; }
    table.dataTable tbody tr:hover { background: var(--pri-tint) !important; }
    table.dataTable tbody td { padding: .7rem 1rem !important; font-size: .83rem; color: var(--text-dark); vertical-align: middle !important; }
    table.dataTable tbody td a[style*="color:#2563eb"] { color: var(--pri) !important; }
    table.dataTable tbody td a[style*="color:#C9960C"] { color: var(--pri) !important; }

    /* Count / role badge */
    .count-badge, .role-chip {
      background: var(--pri-tint); color: var(--pri-dk);
      border: 1px solid var(--pri-muted);
      border-radius: 6px; padding: .12rem .6rem;
      font-size: .72rem; font-weight: 700;
    }
    /* Status badges */
    .sb-active, .sb-completed {
      background:#e0f7f7; color:#006666;
      border:1px solid #a0d8d8;
      border-radius:20px; padding:.2rem .75rem;
      font-size:.72rem; font-weight:700;
      display:inline-block;
    }
    .sb-inactive, .sb-cancelled {
      background:#fff1f2; color:#be123c;
      border:1px solid #fecdd3;
      border-radius:20px; padding:.2rem .75rem;
      font-size:.72rem; font-weight:700;
      display:inline-block;
    }

    /* DataTables search + pagination */
    .dataTables_wrapper .dataTables_filter input {
      border:1.5px solid var(--border); border-radius:7px;
      padding:.35rem .75rem; font-size:.83rem;
      transition:border-color .2s;
    }
    .dataTables_wrapper .dataTables_filter input:focus {
      border-color:var(--pri); outline:none;
      box-shadow:0 0 0 3px rgba(0,141,141,.12);
    }
    .dataTables_wrapper .dataTables_info { font-size:.78rem; color:var(--text-soft); }

    /* Alert success inside cards */
    .alert-success-custom {
      background:#e0f7f7; border-left:4px solid var(--pri);
      border-radius:8px; padding:.75rem 1rem;
      color:var(--pri-dk); font-size:.84rem; font-weight:600;
      display:flex; align-items:center; gap:.5rem;
      margin-bottom:1rem;
    }

    /* ════════════════ SIDEBAR SPACING ═══════════ */
    .main-sidebar { padding-bottom: 1rem; }

    /* Sidebar nav item spacing */
    .nav-sidebar .nav-item { margin-bottom: 3px; }

    /* Sidebar section header spacing */
    .nav-sidebar .nav-header {
      margin-top: .5rem;
      padding: .75rem .85rem .25rem;
      font-size: .6rem; letter-spacing: 1.8px;
      color: var(--pri-muted);
      border-top: 1px solid rgba(0,141,141,.08);
    }
    .nav-sidebar .nav-header:first-child { border-top: none; }

    /* Treeview indent line */
    .nav-treeview { border-radius: 0; }
    .nav-treeview .nav-item { margin-bottom: 1px; }

    /* Active treeview item indicator dot */
    .nav-treeview .nav-link.active::before {
      content: '';
      width: 6px; height: 6px;
      border-radius: 50%;
      background: var(--pri);
      display: inline-block;
      margin-right: .1rem;
      flex-shrink: 0;
    }

    .nav-sidebar .menu-open > .nav-link {
      background: rgba(0,141,141,.1) !important;
      color: var(--pri-dk) !important;
      font-weight: 700;
    }

    .nav-sidebar .menu-open > .nav-link .nav-icon,
    .nav-sidebar .menu-open > .nav-link .right {
      color: var(--pri-dk) !important;
    }

    .nav-sidebar .right {
      margin-left: auto;
      font-size: .78rem;
      flex-shrink: 0;
    }

    .nav-sidebar .nav-link {
      min-height: 42px;
    }

    /* Sidebar user info block at bottom (optional) */
    .sidebar-user-panel {
      padding: .85rem 1rem;
      border-top: 1px solid var(--border);
      margin-top: auto;
      display: flex; align-items: center; gap: .65rem;
    }
    .sidebar-user-panel .avatar {
      width: 34px; height: 34px; border-radius: 50%;
      background: linear-gradient(135deg,var(--pri-dk),var(--pri));
      display: flex; align-items: center; justify-content: center;
      color: #fff; font-size: .75rem; font-weight: 800; flex-shrink: 0;
    }
    .sidebar-user-panel .uname { font-size: .8rem; font-weight: 700; color: var(--text-dark); }
    .sidebar-user-panel .urole { font-size: .7rem; color: var(--text-soft); }

    .suggestions-list {
      list-style:none; padding:0; margin:0;
      position:absolute; width:100%; max-height:200px; overflow-y:auto;
      background:#fff; border-radius:8px;
      box-shadow:0 8px 28px rgba(0,141,141,.12);
      z-index:1000;
    }
    .suggestions-list li { padding:10px 14px; cursor:pointer; font-size:.85rem; }
    .suggestions-list li:hover { background:var(--pri-tint); color:var(--pri-dk); }

    @media (max-width:767.98px) {
      .main-sidebar {
        width: min(84vw, 280px) !important;
      }

      .nav-sidebar .nav-link,
      .nav-treeview .nav-link {
        white-space: normal;
        line-height: 1.25;
      }

      .form-group label { display:block; width:100%; }
      .row.input-row .col { flex:0 0 100%; max-width:100%; margin-bottom:10px; }
    }
    @media (min-width:768px) and (max-width:1199.98px) {
      .row.input-row .col { flex:0 0 50%; max-width:50%; margin-bottom:10px; }
    }
    @media (min-width:1200px) {
      .row.input-row .col { flex:0 0 14.285%; max-width:14.285%; }
    }
  </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed text-sm">
<div class="wrapper">

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-dark">
    @include('admin.particle.navbar')
  </nav>

  <!-- Main Sidebar -->
  <aside class="main-sidebar sidebar-light-primary elevation-4">
    @include('admin.particle.sidebar')
  </aside>

  <!-- Content Wrapper -->
  <div class="content-wrapper">
    @yield('content')
  </div>

  <footer class="main-footer">
    @include('admin.particle.footer')
  </footer>

  <aside class="control-sidebar control-sidebar-light"></aside>
</div>

@include('admin.particle.script')
@yield('style')
@yield('pageScript')
</body>
</html>
