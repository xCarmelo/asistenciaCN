<nav class="bg-gray-800 text-white w-64 min-h-screen p-4">
    <div class="mb-8">
        <h1 class="text-2xl font-bold">Logo</h1>
    </div>
    <ul class="space-y-2">
        <li><a href="{{ route('home') }}" class="block py-2 px-4 rounded hover:bg-gray-700 {{ request()->routeIs('home') ? 'bg-gray-700' : '' }}">Inicio</a></li>
        <li><a href="{{ route('estudiantes.index') }}" class="block py-2 px-4 rounded hover:bg-gray-700 {{ request()->routeIs('estudiantes.*') ? 'bg-gray-700' : '' }}">Estudiantes</a></li>
        <li><a href="{{ route('maestros.index') }}" class="block py-2 px-4 rounded hover:bg-gray-700 {{ request()->routeIs('maestros.*') ? 'bg-gray-700' : '' }}">Maestros</a></li>
        <li><a href="{{ route('secciones.index') }}" class="block py-2 px-4 rounded hover:bg-gray-700 {{ request()->routeIs('secciones.*') ? 'bg-gray-700' : '' }}">Secciones</a></li>
        <li><a href="{{ route('asistencia.index') }}" class="block py-2 px-4 rounded hover:bg-gray-700 {{ request()->routeIs('asistencia.*') ? 'bg-gray-700' : '' }}">Asistencia</a></li>
    </ul>
</nav>
