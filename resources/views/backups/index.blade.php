@extends('layouts.app')

@section('title', 'Respaldo de Base de Datos')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
        <h1 class="h3">
            <i class="bi bi-database-fill-gear text-primary"></i>
            Respaldo de Base de Datos
        </h1>
        <div>
            <a href="{{ route('backups.create') }}" class="btn btn-success me-2">
                <i class="bi bi-plus-circle"></i> Crear Respaldo
            </a>
            <a href="{{ route('backups.import.form') }}" class="btn btn-primary">
                <i class="bi bi-upload"></i> Importar Respaldo
            </a>
        </div>
    </div>

    <!-- Tarjetas informativas -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Total Respaldos</h6>
                            <h2 class="mb-0 mt-2">{{ $backups->total() }}</h2>
                        </div>
                        <i class="bi bi-archive fs-1"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Espacio Total</h6>
                            <h2 class="mb-0 mt-2">{{ $totalSize }}</h2>
                        </div>
                        <i class="bi bi-hdd-stack fs-1"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Último Respaldo</h6>
                            <h6 class="mb-0 mt-2">{{ $backups->first()?->created_at->format('d/m/Y H:i') ?? 'Ninguno' }}</h6>
                        </div>
                        <i class="bi bi-clock-history fs-1"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Carpeta Predeterminada</h6>
                            <h6 class="mb-0 mt-2">storage/app/backups</h6>
                        </div>
                        <i class="bi bi-folder fs-1"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white">
            <i class="bi bi-funnel-fill me-1"></i> Filtros
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('backups.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Buscar</label>
                    <input type="text" name="search" class="form-control" placeholder="Nombre o archivo..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Fecha desde</label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Fecha hasta</label>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-2 d-flex gap-2 align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Filtrar
                    </button>
                    <a href="{{ route('backups.index') }}" class="btn btn-secondary w-100">
                        <i class="bi bi-eraser"></i> Limpiar
                    </a>
                </div>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    <!-- Tabla de respaldos -->
    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <i class="bi bi-table me-1"></i> Lista de Respaldos
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Nombre</th>
                            <th>Archivo</th>
                            <th>Fecha</th>
                            <th>Tamaño</th>
                            <th>Descripción</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($backups as $backup)
                        <tr>
                            <td>{{ ($backups->currentPage() - 1) * $backups->perPage() + $loop->iteration }}</td>
                            <td class="fw-semibold">{{ $backup->name }}</td>
                            <td>{{ $backup->filename }}</td>
                            <td>{{ $backup->created_at->format('d/m/Y H:i:s') }}</td>
                            <td><span class="badge bg-info">{{ $backup->size }}</span></td>
                            <td>{{ $backup->description ?? '-' }}</td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#restoreModal" 
                                        data-id="{{ $backup->id }}" data-name="{{ $backup->name }}">
                                        <i class="bi bi-arrow-repeat"></i>
                                    </button>
                                    <a href="{{ route('backups.download', $backup->id) }}" class="btn btn-sm btn-outline-success" title="Descargar">
                                        <i class="bi bi-download"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal"
                                        data-id="{{ $backup->id }}" data-name="{{ $backup->name }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                            <tr><td colspan="7" class="text-center py-4">No hay respaldos registrados. @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">{{ $backups->links('pagination::bootstrap-5') }}</div>
        </div>
    </div>
</div>

<!-- Modal Restaurar -->
<div class="modal fade" id="restoreModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title">⚠ Confirmar restauración</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>¿Restaurar el respaldo <strong id="restoreName"></strong>?</p>
                <p class="text-danger small">Esta acción reemplazará TODOS los datos actuales del sistema.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="restoreForm" method="POST" action="">
                    @csrf
                    <button type="submit" class="btn btn-warning">Restaurar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Eliminar -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Confirmar eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                ¿Eliminar permanentemente el respaldo <strong id="deleteName"></strong>?
                <br><small class="text-muted">El archivo será borrado físicamente del servidor.</small>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="deleteForm" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    const restoreModal = document.getElementById('restoreModal');
    restoreModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const id = button.dataset.id;
        const name = button.dataset.name;
        document.getElementById('restoreName').innerText = name;
        document.getElementById('restoreForm').action = `/backups/${id}/restore`;
    });

    const deleteModal = document.getElementById('deleteModal');
    deleteModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const id = button.dataset.id;
        const name = button.dataset.name;
        document.getElementById('deleteName').innerText = name;
        document.getElementById('deleteForm').action = `/backups/${id}`;
    });
</script>
@endsection
