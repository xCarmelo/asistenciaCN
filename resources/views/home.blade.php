@extends('layouts.app')

@section('title', 'Inicio')

@section('content')
<!-- Sección de bienvenida con fondo oscuro y logo -->
<div class=" p-2 text-center mb-5 py-5 px-3 rounded-3 shadow-lg" style="background: linear-gradient(135deg, #1a1f2e 0%, #2d3748 100%); color: white; border-radius: 1.5rem;">
    
    <img 
        src="{{ asset('images/logo-colegio.jpg') }}" 
        alt="Logo CNSR" 
        width="150" 
        height="200"
        class="mb-3 rounded-circle shadow"
        style="object-fit: cover; border: 4px solid rgba(255,255,255,0.2);"
        onerror="this.src='https://placehold.co/200x200?text=Logo'"
    >

    <h1 class="display-6 fw-bold mb-3">
        Gestión escolar moderna, intuitiva y segura.
    </h1>

    <p class="lead mb-0" style="color: #cbd5e1;">
        Administra estudiantes, maestros, secciones y asistencia desde un solo lugar.
    </p>

</div>

<!-- Tarjetas de estadísticas cuadradas y centradas -->
<div class="row g-4 mb-5 justify-content-center">
    <div class="col-12 col-sm-6 col-md-3">
        <div class="card card-stats shadow-sm border-0 h-100 text-center p-3">
            <div class="stats-icon mx-auto bg-primary bg-opacity-10 text-primary mb-3 d-flex align-items-center justify-content-center" style="width: 64px; height: 64px; border-radius: 1rem;">
                <i class="bi bi-people fs-1"></i>
            </div>
            <div class="stats-number">{{ $totalEstudiantes }}</div>
            <div class="text-muted text-uppercase small fw-semibold mt-2">Estudiantes</div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-md-3">
        <div class="card card-stats shadow-sm border-0 h-100 text-center p-3">
            <div class="stats-icon mx-auto bg-info bg-opacity-10 text-info mb-3 d-flex align-items-center justify-content-center" style="width: 64px; height: 64px; border-radius: 1rem;">
                <i class="bi bi-person-badge fs-1"></i>
            </div>
            <div class="stats-number">{{ $totalMaestros }}</div>
            <div class="text-muted text-uppercase small fw-semibold mt-2">Maestros</div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-md-3">
        <div class="card card-stats shadow-sm border-0 h-100 text-center p-3">
            <div class="stats-icon mx-auto bg-warning bg-opacity-10 text-warning mb-3 d-flex align-items-center justify-content-center" style="width: 64px; height: 64px; border-radius: 1rem;">
                <i class="bi bi-grid-3x3-gap-fill fs-1"></i>
            </div>
            <div class="stats-number">{{ $totalSecciones }}</div>
            <div class="text-muted text-uppercase small fw-semibold mt-2">Secciones</div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-md-3">
        <div class="card card-stats shadow-sm border-0 h-100 text-center p-3">
            <div class="stats-icon mx-auto bg-danger bg-opacity-10 text-danger mb-3 d-flex align-items-center justify-content-center" style="width: 64px; height: 64px; border-radius: 1rem;">
                <i class="bi bi-calendar-week fs-1"></i>
            </div>
            <div class="stats-number">{{ $seccionesConAsistenciaHoy }}</div>
            <div class="text-muted text-uppercase small fw-semibold mt-2">Secciones con asistencia hoy</div>
        </div>
    </div>
</div>

<!-- Tabla: Maestros ausentes hoy -->
<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3">
        <h5 class="card-title mb-0 fw-semibold">
            <i class="bi bi-person-x me-2 text-danger"></i> Maestros ausentes hoy
        </h5>
        <small class="text-muted">Fecha: {{ now()->toDateString() }}</small>
    </div>
    <div class="card-body">
        @if($maestrosAusentes->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Maestro</th>
                            <th>Sección(es) a cargo (Tutelado)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($maestrosAusentes as $ausente)
                        @php
                            $secciones = $ausente->maestro->seccionesGuiadas ?? collect();
                            $nombresSecciones = $secciones->pluck('nombre')->implode(', ');
                        @endphp
                        <tr>
                            <td class="fw-medium">{{ $ausente->maestro->name ?? 'N/A' }}</td>
                            <td>{{ $nombresSecciones ?: 'Sin sección asignada' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-4">
                <i class="bi bi-emoji-smile fs-1 text-success"></i>
                <p class="mt-2 text-muted mb-0">¡No hay maestros ausentes hoy!</p>
            </div>
        @endif
    </div>
</div>
@endsection
