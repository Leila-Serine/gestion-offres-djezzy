<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Espace Djezzy') · Djezzy</title>
    {{-- Le même logo officiel sert aussi d’icône pour l’onglet du navigateur. --}}
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/djezzy-logo.svg') }}">
    <link rel="alternate icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    {{-- Styles communs intégrés ici pour que les vues fonctionnent sans fichier CSS séparé. --}}
    <style>
/* Design commun aux vues Blade. Bootstrap 5 fournit la grille et les composants. */
:root{--djezzy-red:#e30613;--djezzy-red-dark:#b80813;--djezzy-red-light:#fff0f1;--app-bg:#f5f6f8;--sidebar-bg:#1c2027;--text-main:#20242b;--text-muted:#737b87;--border-color:#e7e9ee}
body{margin:0;background:var(--app-bg);color:var(--text-main);font-family:Inter,system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif}
a{color:var(--djezzy-red)}a:hover{color:var(--djezzy-red-dark)}
.app-sidebar{position:fixed;inset:0 auto 0 0;z-index:1030;display:flex;flex-direction:column;width:258px;background:var(--sidebar-bg);color:#fff}
.sidebar-brand{display:flex;align-items:center;gap:12px;min-height:88px;padding:20px 22px;border-bottom:1px solid #ffffff17}
/* Logo vectoriel local, conservé dans ses proportions officielles. */
.brand-logo{display:block;flex:0 0 auto;width:56px;height:63px;object-fit:contain;filter:drop-shadow(0 8px 16px #e306132b)}.guest-panel .brand-logo,.welcome-nav .brand-logo{width:66px;height:74px}.guest-mini-brand .brand-logo{width:47px;height:53px}.mobile-brand-logo{display:block;width:39px;height:44px;object-fit:contain}.mobile-sidebar .brand-logo{width:48px;height:54px}
.brand-name{color:#fff;font-size:18px;font-weight:800;letter-spacing:.025em;line-height:1.1}.brand-subtitle{margin-top:4px;color:#aeb5c0;font-size:11px}
.sidebar-content{flex:1;padding:28px 14px}.sidebar-label{padding:0 14px 12px;color:#818b99;font-size:10px;font-weight:800;letter-spacing:.14em;text-transform:uppercase}
.sidebar-link{position:relative;display:flex;align-items:center;gap:13px;min-height:46px;margin-bottom:6px;padding:11px 14px;border-radius:10px;color:#c3cad3;font-size:14px;font-weight:500;text-decoration:none;transition:background .18s,color .18s}
.sidebar-link i{width:20px;font-size:18px;text-align:center}.sidebar-link:hover{background:#ffffff12;color:#fff}.sidebar-link.active{background:#e306132b;color:#fff;font-weight:700}.sidebar-link.active:before{position:absolute;top:10px;bottom:10px;left:0;width:3px;border-radius:0 3px 3px 0;background:var(--djezzy-red);content:""}.sidebar-link.active i{color:#ff5862}
.sidebar-footer{padding:18px 22px 24px;border-top:1px solid #ffffff17;color:#8993a0;font-size:11px}
.app-main{min-height:100vh;margin-left:258px}.app-topbar{position:sticky;top:0;z-index:1020;display:flex;align-items:center;justify-content:space-between;height:72px;padding:0 36px;border-bottom:1px solid var(--border-color);background:#fffffff5;backdrop-filter:blur(12px)}
.topbar-location{display:flex;align-items:center;gap:10px;color:var(--text-muted);font-size:13px}.topbar-location i{color:var(--djezzy-red);font-size:10px}.user-avatar{display:grid;flex:0 0 auto;place-items:center;width:38px;height:38px;border:1px solid #ffd8dc;border-radius:11px;background:var(--djezzy-red-light);color:var(--djezzy-red);font-size:15px;font-weight:800}.user-name{font-size:13px;font-weight:700}.user-role{color:var(--text-muted);font-size:11px}
.profile-trigger{display:flex;align-items:center;gap:10px;padding:5px 8px 5px 5px;border:1px solid transparent;border-radius:12px;background:transparent;color:var(--text-main)}.profile-trigger:hover,.profile-trigger:focus-visible{border-color:var(--border-color);background:#f8f9fb}.profile-trigger .bi-chevron-down{color:var(--text-muted);font-size:12px}.profile-menu{min-width:230px;padding:8px;border:1px solid var(--border-color);border-radius:12px}.profile-menu .dropdown-item{border-radius:7px}
.page-container{width:100%;max-width:1600px;margin:0 auto;padding:32px 36px 56px}.page-title{margin:0 0 6px;font-size:clamp(26px,3vw,34px);font-weight:800;letter-spacing:-.04em}.page-subtitle{margin:0;color:var(--text-muted);font-size:14px;line-height:1.55}.page-heading{display:flex;align-items:center;justify-content:space-between;gap:20px;margin-bottom:25px}.page-heading-actions{display:flex;gap:10px;flex-wrap:wrap}.page-eyebrow{margin-bottom:10px;color:var(--djezzy-red);font-size:11px;font-weight:800;letter-spacing:.12em;text-transform:uppercase}
.app-card{border:1px solid var(--border-color);border-radius:16px;background:#fff;box-shadow:0 8px 30px #141b2609}.section-header{display:flex;align-items:center;justify-content:space-between;gap:16px;padding:23px 26px;border-bottom:1px solid var(--border-color)}.section-title{margin:0;font-size:18px;font-weight:800;letter-spacing:-.025em}.section-subtitle{margin:5px 0 0;color:var(--text-muted);font-size:12px}.section-body{padding:26px}.content-narrow{max-width:800px}.content-medium{max-width:1000px}
.btn-djezzy{border-color:var(--djezzy-red);background:var(--djezzy-red);color:#fff;font-weight:700}.btn-djezzy:hover,.btn-djezzy:focus-visible{border-color:var(--djezzy-red-dark);background:var(--djezzy-red-dark);color:#fff}.btn-outline-djezzy{border-color:#efb9be;color:var(--djezzy-red);font-weight:600}.btn-outline-djezzy:hover,.btn-outline-djezzy:focus-visible{border-color:var(--djezzy-red);background:var(--djezzy-red);color:#fff}.btn-soft{border:1px solid var(--border-color);background:#fff;color:var(--text-main);font-weight:600}.btn-soft:hover{border-color:#cfd3db;background:#f9fafb;color:var(--text-main)}.btn{border-radius:9px}.form-label{font-size:13px;font-weight:700}.form-control,.form-select{min-height:44px;border-color:#dfe3e9;border-radius:9px}.form-control:focus,.form-select:focus,.form-check-input:focus{border-color:#ed8990;box-shadow:0 0 0 .2rem #e3061317}.form-check-input:checked{border-color:var(--djezzy-red);background-color:var(--djezzy-red)}.form-text{font-size:12px;line-height:1.5}.form-actions{display:flex;gap:10px;flex-wrap:wrap;padding-top:20px;border-top:1px solid var(--border-color)}
.app-table thead th{padding:14px 20px;border-bottom:1px solid var(--border-color);background:#f8f9fb;color:#7d8794;font-size:11px;font-weight:800;letter-spacing:.065em;text-transform:uppercase}.app-table tbody td{padding:18px 20px;border-color:#edf0f2;vertical-align:middle}.app-table tbody tr:hover td{background:#fcfcfd}.app-table tbody tr:last-child td{border-bottom:0}.category-badge{display:inline-flex;align-items:center;padding:6px 11px;border:1px solid #eceef1;border-radius:7px;background:#f6f7f9;color:#515a66;font-size:12px;font-weight:700}.text-value{color:var(--text-main);font-weight:700}.muted-value{color:var(--text-muted);font-size:12px}.item-icon{display:grid;flex:0 0 auto;place-items:center;width:43px;height:43px;border:1px solid #ffdade;border-radius:11px;background:var(--djezzy-red-light);color:var(--djezzy-red);font-size:18px}.item-name{color:var(--text-main);font-size:14px;font-weight:750;line-height:1.35}.item-caption{margin-top:3px;color:var(--text-muted);font-size:11px}.item-identity{display:flex;align-items:center;gap:13px;min-width:180px}.detail-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:0 28px}.detail-field{padding:19px 0;border-bottom:1px solid var(--border-color)}.detail-field dt{margin-bottom:6px;color:var(--text-muted);font-size:12px;font-weight:600}.detail-field dd{margin:0;font-size:14px;font-weight:700;overflow-wrap:anywhere}.empty-state{padding:52px 20px;text-align:center}.empty-icon{display:grid;place-items:center;width:58px;height:58px;margin:0 auto 14px;border-radius:15px;background:var(--djezzy-red-light);color:var(--djezzy-red);font-size:23px}.empty-state h3{font-size:16px;font-weight:800}.empty-state p{margin:0;color:var(--text-muted);font-size:13px}
.listing-hero{display:flex;align-items:center;justify-content:space-between;gap:24px;margin-bottom:24px;padding:27px 30px;border-radius:18px;background:linear-gradient(115deg,#252a32,#1b2027 75%);color:#fff}.listing-hero .page-eyebrow{color:#ff7881}.listing-hero .page-title{color:#fff}.listing-hero .page-subtitle{color:#c3c9d1}.listing-count{min-width:112px;padding:15px 19px;border:1px solid #ffffff29;border-radius:13px;background:#ffffff14}.listing-count strong{display:block;font-size:29px;line-height:1}.listing-count span{display:block;margin-top:7px;color:#c3c9d1;font-size:11px}.listing-hero .btn-djezzy{white-space:nowrap}.listing-actions{display:flex;align-items:center;gap:12px;flex-wrap:wrap}.mobile-topbar{display:none}.mobile-sidebar{width:min(310px,88vw);background:var(--sidebar-bg);color:#fff}.mobile-sidebar .offcanvas-header{border-bottom:1px solid #ffffff17}.mobile-sidebar .btn-close{filter:invert(1) grayscale(1)}.mobile-sidebar .offcanvas-body{display:flex;flex-direction:column}.mobile-sidebar .sidebar-content{padding:22px 6px}
/* Pages accessibles avant la connexion. */
.guest-shell{min-height:100vh;display:grid;grid-template-columns:minmax(290px,42%) 1fr;background:#fff}.guest-panel{position:relative;display:flex;flex-direction:column;justify-content:space-between;overflow:hidden;padding:38px clamp(26px,4vw,72px);background:linear-gradient(145deg,#262b33,#171b21);color:#fff}.guest-panel:after{position:absolute;right:-180px;bottom:-220px;width:550px;height:550px;border:1px solid #ffffff26;border-radius:50%;box-shadow:0 0 0 90px #ffffff06,0 0 0 180px #e306130d;content:"";pointer-events:none}.guest-panel .brand-name{font-size:21px}.guest-panel-copy{position:relative;z-index:1;max-width:440px}.guest-panel-copy h2{font-size:clamp(30px,3.5vw,48px);font-weight:800;letter-spacing:-.045em;line-height:1.12}.guest-panel-copy p{margin-top:17px;color:#bfc5cf;font-size:15px;line-height:1.7}.guest-panel-note{position:relative;z-index:1;color:#929ba8;font-size:11px}.guest-content{display:flex;align-items:center;justify-content:center;padding:50px clamp(20px,5vw,90px)}.guest-form{width:100%;max-width:480px}.guest-form h1{font-size:clamp(28px,3vw,36px);font-weight:800;letter-spacing:-.04em}.guest-form .lead-copy{color:var(--text-muted);font-size:14px;line-height:1.6}.guest-form .form-control{min-height:48px}.guest-form .form-label{margin-bottom:8px}.guest-form .btn{min-height:46px}.guest-mini-brand{display:none}
/* Champs de mot de passe des pages publiques : l'œil reste accessible au clavier. */
.password-field{position:relative}
.password-field .form-control{padding-right:3.25rem}
.password-field .form-control.is-invalid{background-image:none;padding-right:3.25rem}
.password-toggle{position:absolute;top:50%;right:7px;z-index:2;display:grid;place-items:center;width:36px;height:36px;transform:translateY(-50%);border:0;border-radius:8px;background:transparent;color:#68717e;font-size:18px;cursor:pointer}
.password-toggle:hover{background:var(--djezzy-red-light);color:var(--djezzy-red)}
.password-toggle:focus-visible{outline:2px solid var(--djezzy-red);outline-offset:2px}
@media(max-width:991.98px){.app-sidebar,.desktop-topbar{display:none}.app-main{margin-left:0}.mobile-topbar{display:flex;height:65px;padding:0 20px}.page-container{padding:24px 20px 42px}.guest-shell{grid-template-columns:1fr}.guest-panel{display:none}.guest-content{min-height:100vh}.guest-mini-brand{display:flex;align-items:center;gap:10px;margin-bottom:36px}.guest-mini-brand .brand-name{color:var(--text-main)}}
@media(max-width:767.98px){.page-heading,.section-header,.listing-hero{align-items:stretch;flex-direction:column}.section-body{padding:20px}.section-header{padding:20px}.detail-grid{grid-template-columns:1fr}.responsive-table thead{display:none}.responsive-table,.responsive-table tbody,.responsive-table tr,.responsive-table td{display:block;width:100%}.responsive-table tbody{padding:12px}.responsive-table tbody tr.data-row{display:flex;flex-direction:column;margin-bottom:12px;padding:14px 16px;border:1px solid var(--border-color);border-radius:12px}.responsive-table tbody tr.data-row:last-child{margin-bottom:0}.responsive-table tbody tr.data-row td{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:7px 0;border:0}.responsive-table tbody tr.data-row td:before{flex:0 0 78px;color:#89929e;font-size:11px;font-weight:700;text-transform:uppercase;content:attr(data-label)}.responsive-table tbody tr.data-row td.primary-cell{order:-1;justify-content:flex-start;padding-bottom:14px;margin-bottom:5px;border-bottom:1px solid var(--border-color)}.responsive-table tbody tr.data-row td.primary-cell:before,.responsive-table tbody tr.data-row td.actions-cell:before{display:none}.responsive-table tbody tr.data-row td.actions-cell{padding-top:14px;margin-top:6px;border-top:1px solid var(--border-color)}.responsive-table tbody tr.data-row td.actions-cell>div{width:100%;flex-wrap:wrap}.responsive-table tbody tr.empty-row td{border:0}}
@media(max-width:575.98px){.page-container{padding:20px 15px 36px}.listing-hero{padding:24px 22px}.guest-content{padding:28px 20px}.page-heading-actions,.form-actions{width:100%}.page-heading-actions .btn,.form-actions .btn{flex:1}}
@media(prefers-reduced-motion:reduce){.sidebar-link{transition:none}}

    </style>
</head>
<body>
    {{-- Pages accessibles avant l'espace client : aucun auth()->user() requis ici. --}}
    <div class="guest-shell">
        <aside class="guest-panel">
            <div class="d-flex align-items-center gap-3"><img class="brand-logo" src="{{ asset('images/djezzy-logo.svg') }}" alt="Djezzy" width="66" height="74"><div class="brand-subtitle">Espace professionnel</div></div>
            <div class="guest-panel-copy"><div class="page-eyebrow text-white-50">Espace professionnel</div><h2>Votre activité, dans un espace dédié.</h2><p>Retrouvez les offres, vos boutiques et vos commandes dans une interface claire et pratique.</p></div>
            <div class="guest-panel-note">DJEZZY · ESPACE CLIENT / COMMERCIAL</div>
        </aside>
        <main class="guest-content">
            <div class="guest-form">
                <div class="guest-mini-brand"><img class="brand-logo" src="{{ asset('images/djezzy-logo.svg') }}" alt="Djezzy" width="47" height="53"><span class="fw-semibold">Espace professionnel</span></div>
                @yield('content')
            </div>
        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Un seul comportement pour les champs de connexion et d'inscription.
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('[data-password-toggle]').forEach(function (button) {
                button.addEventListener('click', function () {
                    const input = document.getElementById(button.dataset.passwordToggle);
                    if (!input) return;

                    const visible = input.type === 'password';
                    input.type = visible ? 'text' : 'password';
                    button.setAttribute('aria-label', visible ? 'Masquer le mot de passe' : 'Afficher le mot de passe');
                    button.setAttribute('aria-pressed', String(visible));

                    const icon = button.querySelector('i');
                    if (icon) {
                        icon.classList.toggle('bi-eye', !visible);
                        icon.classList.toggle('bi-eye-slash', visible);
                    }
                });
            });
        });
    </script>
</body>
</html>
