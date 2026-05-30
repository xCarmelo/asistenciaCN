<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') | CNSR</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,400;14..32,500;14..32,600;14..32,700&display=swap" rel="stylesheet">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f0f2f5;
            overflow-x: hidden;
        }
        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 280px;
            height: 100vh;
            background: linear-gradient(180deg, #1e2a3a 0%, #0f1724 100%);
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
            z-index: 1000;
            overflow-y: auto;
        }
        .sidebar.collapsed {
            transform: translateX(-280px);
        }
        /* Contenido principal */
        .main-content {
            margin-left: 280px;
            transition: margin-left 0.3s ease;
            min-height: 100vh;
        }
        .main-content.expanded {
            margin-left: 0;
        }
        /* Navbar superior - sin usuario, solo botón colapsar */
        .navbar-top {
            background: white;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            padding: 0.75rem 1.5rem;
            position: sticky;
            top: 0;
            z-index: 999;
        }
        /* Sidebar interno */
        .sidebar-header {
            padding: 1.5rem 1rem;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .sidebar-header img {
            max-height: 50px;
            margin-bottom: 0.5rem;
        }
        .sidebar-header h5 {
            color: white;
            font-weight: 600;
            margin-bottom: 0;
        }
        .sidebar-header small {
            color: rgba(255,255,255,0.6);
        }
        .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 0.75rem 1.5rem;
            transition: all 0.2s;
            border-radius: 0.5rem;
            margin: 0.25rem 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-weight: 500;
        }
        .nav-link i {
            font-size: 1.25rem;
            width: 1.5rem;
        }
        .nav-link:hover {
            background-color: rgba(255,255,255,0.1);
            color: white;
            transform: translateX(5px);
        }
        .nav-link.active {
            background-color: #2c6e9e;
            color: white;
        }
        /* Tarjetas */
        .card-stats {
            border: none;
            border-radius: 1rem;
            transition: transform 0.2s, box-shadow 0.2s;
            height: 100%;
        }
        .card-stats:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.05);
        }
        .stats-icon {
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 1rem;
            font-size: 1.5rem;
        }
        .stats-number {
            font-size: 2rem;
            font-weight: 700;
            line-height: 1.2;
        }
        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-280px);
            }
            .sidebar.mobile-show {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0;
            }
        }
        /* Scrollbar */
        .sidebar::-webkit-scrollbar {
            width: 6px;
        }
        .sidebar::-webkit-scrollbar-track {
            background: #1e2a3a;
        }
        .sidebar::-webkit-scrollbar-thumb {
            background: #4a5b6e;
            border-radius: 3px;
        }
        /* Ajustes para paginación con Bootstrap */ 
        .pagination .page-link {
            font-size: 0.875rem;
        }
        .pagination .page-link i {
            font-size: 1rem;
        }
    </style>
<style>.total-cell { background-color: #ffffff !important; }</style></head>
<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <img src="{{ asset('images/logo-colegio.jpg') }}" alt="Logo CNSR" onerror="this.src='https://via.placeholder.com/50'">
            <h5>Colegio CNSR</h5>
            <small>Control de Asistencia</small>
        </div>
        <nav class="mt-3">
            <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                <i class="bi bi-speedometer2"></i> Inicio
            </a>
            <a class="nav-link {{ request()->routeIs('estudiantes.*') ? 'active' : '' }}" href="{{ route('estudiantes.index') }}">
                <i class="bi bi-people"></i> Estudiantes
            </a>
            <a class="nav-link {{ request()->routeIs('maestros.*') ? 'active' : '' }}" href="{{ route('maestros.index') }}">
                <i class="bi bi-person-badge"></i> Maestros
            </a>
            <a class="nav-link {{ request()->routeIs('secciones.*') ? 'active' : '' }}" href="{{ route('secciones.index') }}">
                <i class="bi bi-grid-3x3-gap-fill"></i> Sección/Grado
            </a>
            <a class="nav-link {{ request()->routeIs('asistencia.*') ? 'active' : '' }}" href="{{ route('asistencia.index') }}">
                <i class="bi bi-calendar-check"></i> Asistencia
            </a>
            <a class="nav-link" href="{{ route('reporte-ausencias') }}">
                <i class="bi bi-file-text"></i> Reporte
            </a>
            <a class="nav-link" href="{{ route('backups.index') }}">
                <i class="bi bi-database-fill-gear"></i> Respaldos
            </a>
        </nav>
    </div>

    <!-- Contenido principal -->
    <div class="main-content" id="mainContent">
        <nav class="navbar-top d-flex justify-content-between align-items-center">
            <button class="btn btn-sm btn-outline-secondary" id="toggleSidebar" type="button">
                <i class="bi bi-list"></i> <span class="d-none d-md-inline ms-2">Menú</span>
            </button>
            <!-- Sin avatar ni usuario -->
        </nav>
        <main class="px-3 px-md-4 py-4">
            @yield('content')
        </main>
    </div>

    <script>
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent');
        const toggleBtn = document.getElementById('toggleSidebar');

        toggleBtn.addEventListener('click', () => {
            if (window.innerWidth <= 768) {
                sidebar.classList.toggle('mobile-show');
            } else {
                sidebar.classList.toggle('collapsed');
                mainContent.classList.toggle('expanded');
            }
        });

        window.addEventListener('resize', () => {
            if (window.innerWidth > 768) {
                sidebar.classList.remove('mobile-show');
                if (sidebar.classList.contains('collapsed')) {
                    mainContent.classList.add('expanded');
                } else {
                    mainContent.classList.remove('expanded');
                }
            } else {
                mainContent.classList.remove('expanded');
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
<style>
    .card-stats {
        transition: transform 0.2s, box-shadow 0.2s;
        border-radius: 1.25rem;
    }
    .card-stats:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.1);
    }
    .stats-icon {
        border-radius: 1rem;
    }
        /* Ajustes para paginación con Bootstrap */ 
        .pagination .page-link {
            font-size: 0.875rem;
        }
        .pagination .page-link i {
            font-size: 1rem;
        }
</style>
