<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Gestion Stock') }}</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <style>
        :root {
            --sidebar-width: 260px;
            --sidebar-collapsed-width: 80px;
            --sidebar-bg: #1e1e2d;
            --sidebar-hover: #2b2b40;
            --accent-color: #3b82f6;
            --transition-speed: 0.3s;
        }

        body {
            background-color: #f4f7fe;
            overflow-x: hidden;
        }

        /* Sidebar Structure */
        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            background-color: var(--sidebar-bg);
            color: #a2a3b7;
            transition: all var(--transition-speed);
            z-index: 1050;
            box-shadow: 4px 0 10px rgba(0, 0, 0, 0.1);
            overflow-x: hidden;
        }

        /* État Fermé (Collapsed) */
        body.sidebar-collapsed .sidebar {
            width: var(--sidebar-collapsed-width);
        }

        body.sidebar-collapsed .sidebar .sidebar-brand span,
        body.sidebar-collapsed .sidebar .nav-link span,
        body.sidebar-collapsed .sidebar .bi-chevron-down,
        body.sidebar-collapsed .sidebar .submenu {
            display: none !important;
        }

        body.sidebar-collapsed .sidebar .nav-link {
            justify-content: center;
            padding: 0.8rem;
        }

        body.sidebar-collapsed .sidebar .nav-link i {
            margin: 0;
            font-size: 1.4rem;
        }

        /* Content Area Adjustments */
        .main-wrapper {
            margin-left: var(--sidebar-width);
            transition: all var(--transition-speed);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        body.sidebar-collapsed .main-wrapper {
            margin-left: var(--sidebar-collapsed-width);
        }

        /* Sidebar Elements */
        .sidebar-header {
            padding: 1.5rem 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            display: flex;
            align-items: center;
            justify-content: center;
            /* Centrage horizontal par défaut */
            min-height: 80px;
        }

        .sidebar-brand {
            color: #ffffff;
            font-weight: 700;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            /* Centre le contenu du lien */
            gap: 10px;
            width: 100%;
            transition: all var(--transition-speed);
        }

        .logo-img {
            height: 45px;
            /* Taille légèrement ajustée */
            width: auto;
            transition: all var(--transition-speed);
            display: block;
        }

        /* --- CORRECTION POUR LE CENTRAGE DU LOGO --- */
        body.sidebar-collapsed .sidebar-header {
            padding: 1rem 0;
            /* Réduit l'espace autour */
        }

        body.sidebar-collapsed .sidebar-brand {
            gap: 0;
            /* Supprime l'espace entre l'image (invisible) et le texte */
            padding: 0;
        }

        body.sidebar-collapsed .logo-img {
            margin: 0 auto;
            /* Centre l'image dans l'espace réduit */
            height: 35px;
            /* Optionnel: réduit un peu la taille pour l'esthétique */
        }

        /* ------------------------------------------ */

        .nav-list {
            list-style: none;
            padding: 1rem 0.8rem;
            margin: 0;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 0.8rem 1rem;
            color: #a2a3b7 !important;
            text-decoration: none;
            border-radius: 10px;
            margin-bottom: 5px;
            transition: all 0.2s;
        }

        .nav-link:hover {
            background-color: var(--sidebar-hover);
            color: #ffffff !important;
        }

        .nav-link.active {
            background-color: var(--accent-color) !important;
            color: #ffffff !important;
        }

        .nav-link i {
            min-width: 30px;
            font-size: 1.2rem;
        }

        .submenu {
            padding-left: 2.8rem;
            list-style: none;
        }

        .submenu-link {
            color: #7e8299;
            text-decoration: none;
            display: block;
            padding: 0.5rem 0;
            font-size: 0.9rem;
        }

        .submenu-link:hover,
        .submenu-link.active {
            color: #ffffff;
        }

        /* Top Navbar */
        .top-navbar {
            background: #ffffff;
            padding: 0.75rem 1.5rem;
            border-bottom: 1px solid #eef0f7;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        #sidebarToggle {
            background: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 5px 10px;
            cursor: pointer;
            color: var(--sidebar-bg);
        }

        .rotate-icon {
            transition: transform 0.3s;
        }

        .collapsed .rotate-icon {
            transform: rotate(-90deg);
        }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>

<body>

    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <a href="#" class="sidebar-brand">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="logo-img">
                <span>SKY STOCK</span>
            </a>
        </div>

        <ul class="nav-list">
            <li class="nav-item">
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="bi bi-grid-1x2"></i> <span>Dashboard</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('categories.index') }}" class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                    <i class="bi bi-tags"></i> <span>Catégories</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('produits.index') }}" class="nav-link {{ request()->routeIs('produits.*') ? 'active' : '' }}">
                    <i class="bi bi-box"></i> <span>Produits</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('clients.index') }}" class="nav-link {{ request()->routeIs('clients.*') ? 'active' : '' }}">
                    <i class="bi bi-people"></i> <span>Clients</span>
                </a>
            </li>

            <li class="nav-item">
                <button type="button" id="btnMouvement" class="nav-link w-100 border-0 bg-transparent {{ request()->routeIs('mouvements.*') ? 'active' : '' }}">
                    <i class="bi bi-arrow-left-right"></i>
                    <span>Mouvements</span>
                    <i class="bi bi-chevron-down ms-auto rotate-icon"></i>
                </button>
                <ul id="menuMouvement" class="submenu {{ request()->routeIs('mouvements.*') ? '' : 'd-none' }}">
                    <li><a href="{{ route('mouvements.entree') }}" class="submenu-link">Entrées</a></li>
                    <li><a href="{{ route('mouvements.sortie') }}" class="submenu-link">Sorties</a></li>
                    <li><a href="{{ route('mouvements.ajustement') }}" class="submenu-link">Ajustements</a></li>
                    <li><a href="{{ route('mouvements.index') }}" class="submenu-link">Historique</a></li>
                </ul>
            </li>

            <li class="nav-item">
                <a href="{{ route('factures.index') }}" class="nav-link {{ request()->routeIs('factures.*') ? 'active' : '' }}">
                    <i class="bi bi-receipt"></i> <span>Factures</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('profile.edit') }}"
                    class="nav-link {{ request()->routeIs('profile.edit') ? 'active' : '' }}">
                    <i class="bi bi-person-gear"></i>
                    <span>Mon Profil</span>
                </a>
            </li>
        </ul>
    </div>

    <div class="main-wrapper">
        <nav class="top-navbar shadow-sm">
            <div class="d-flex align-items-center gap-3">
                <button id="sidebarToggle">
                    <i class="bi bi-list fs-4"></i>
                </button>
                <span class="fw-bold">Bienvenue, {{ Auth::user()->name }}</span>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="btn btn-outline-danger btn-sm rounded-pill px-3">
                    <i class="bi bi-box-arrow-right"></i> Déconnexion
                </button>
            </form>
        </nav>

        <div class="p-4">
            @yield('content')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebarToggle');
            const body = document.body;

            // Toggle Sidebar (Ouvert / Fermé)
            sidebarToggle.addEventListener('click', () => {
                body.classList.toggle('sidebar-collapsed');
            });

            // Gestion Sous-menu Mouvement
            const btnMouvement = document.getElementById('btnMouvement');
            const menuMouvement = document.getElementById('menuMouvement');
            if (btnMouvement && menuMouvement) {
                btnMouvement.addEventListener('click', (e) => {
                    // Si la sidebar est fermée, on l'ouvre d'abord
                    if (body.classList.contains('sidebar-collapsed')) {
                        body.classList.remove('sidebar-collapsed');
                    }
                    menuMouvement.classList.toggle('d-none');
                    btnMouvement.classList.toggle('collapsed');
                });
            }
        });
    </script>

    @stack('scripts')
</body>

</html>