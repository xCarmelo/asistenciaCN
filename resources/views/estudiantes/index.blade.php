@extends('layouts.app')

@section('title', 'Estudiantes')

@section('content')
<div class="container-fluid">

    {{-- HEADER --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1">
                <i class="bi bi-people-fill text-primary"></i>
                Lista de Estudiantes
            </h1>
            <p class="text-muted mb-0">Gestión general de estudiantes registrados.</p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#importModal">
                <i class="bi bi-file-earmark-excel"></i> Importar Excel
            </button>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">
                <i class="bi bi-plus-circle"></i> Nuevo Estudiante
            </button>
        </div>
    </div>

    {{-- ALERTAS --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- FILTROS CON BOTÓN --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('estudiantes.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Nombre</label>
                    <input type="text" name="nombre" class="form-control" placeholder="Buscar estudiante..." value="{{ request('nombre') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Sección</label>
                    <select name="seccion_id" class="form-select">
                        <option value="">Todas</option>
                        @foreach($secciones as $seccion)
                            <option value="{{ $seccion->id }}" {{ request('seccion_id') == $seccion->id ? 'selected' : '' }}>{{ $seccion->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Estado</label>
                    <select name="estado" class="form-select">
                        <option value="">Todos</option>
                        <option value="Activo" {{ request('estado') == 'Activo' ? 'selected' : '' }}>Activo</option>
                        <option value="Inactivo" {{ request('estado') == 'Inactivo' ? 'selected' : '' }}>Inactivo</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2 align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Filtrar
                    </button>
                    <a href="{{ route('estudiantes.index') }}" class="btn btn-secondary w-100">
                        <i class="bi bi-eraser"></i> Limpiar
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- TABLA --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Nombre completo</th>
                            <th>Número lista</th>
                            <th>Sección</th>
                            <th>Género</th>
                            <th>Estado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($estudiantes as $estudiante)
                        <tr>
                            <td>{{ ($estudiantes->currentPage() - 1) * $estudiantes->perPage() + $loop->iteration }}</td>
                            <td class="fw-semibold">{{ $estudiante->name }}</td>
                            <td>{{ $estudiante->numero_lista ?? '-' }}</td>
                            <td>{{ $estudiante->seccion->nombre ?? 'Sin asignar' }}</td>
                            <td>
                                @if($estudiante->genero == 'M') Masculino
                                @elseif($estudiante->genero == 'F') Femenino
                                @else - @endif
                            </td>
                            <td><span class="badge bg-{{ $estudiante->estado == 'Activo' ? 'success' : 'secondary' }}">{{ $estudiante->estado }}</span></td>
                            <td>
                                <div class="d-flex justify-content-center gap-2">
                                    <button class="btn btn-sm btn-outline-primary edit-btn" data-bs-toggle="modal" data-bs-target="#editModal"
                                        data-id="{{ $estudiante->id }}"
                                        data-name="{{ $estudiante->name }}"
                                        data-numero_lista="{{ $estudiante->numero_lista }}"
                                        data-seccion="{{ $estudiante->id_seccion }}"
                                        data-genero="{{ $estudiante->genero }}"
                                        data-anio="{{ $estudiante->año }}"
                                        data-estado="{{ $estudiante->estado }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    @if($estudiante->estado == 'Activo')
                                        <button class="btn btn-sm btn-outline-danger delete-btn" data-bs-toggle="modal" data-bs-target="#deleteModal"
                                            data-id="{{ $estudiante->id }}" data-nombre="{{ $estudiante->name }}" data-tipo="eliminar">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    @else
                                        <button class="btn btn-sm btn-outline-success reactivar-btn" data-bs-toggle="modal" data-bs-target="#deleteModal"
                                            data-id="{{ $estudiante->id }}" data-nombre="{{ $estudiante->name }}" data-tipo="reactivar">
                                            <i class="bi bi-arrow-repeat"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                            <tr><td colspan="7" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-database-x fs-1 d-block mb-3"></i>
                                    No se encontraron registros.
                                </div>
                            </div></td>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">{{ $estudiantes->withQueryString()->links('pagination::bootstrap-5') }}</div>
        </div>
    </div>
</div>

<!-- MODAL CREAR -->
<div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('estudiantes.store', request()->query()) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Agregar Estudiante</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3"><label>Nombre completo *</label><input type="text" name="name" class="form-control" required></div>
                    <div class="mb-3"><label>Número de lista *</label><input type="number" name="numero_lista" class="form-control" required></div>
                    <div class="mb-3"><label>Sección *</label>
                        <select name="id_seccion" class="form-select" required>
                            <option value="">Seleccionar</option>
                            @foreach($secciones as $seccion)
                                <option value="{{ $seccion->id }}">{{ $seccion->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3"><label>Género *</label>
                        <select name="genero" class="form-select" required>
                            <option value="">Seleccionar</option>
                            <option value="M">Masculino</option>
                            <option value="F">Femenino</option>
                        </select>
                    </div>
                    <div class="mb-3"><label>Año</label><input type="number" name="año" class="form-control" value="{{ date('Y') }}"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL EDITAR -->
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editForm" method="POST" action="">
                @csrf @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Editar Estudiante</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3"><label>Nombre completo *</label><input type="text" name="name" id="edit_name" class="form-control" required></div>
                    <div class="mb-3"><label>Número de lista *</label><input type="number" name="numero_lista" id="edit_numero_lista" class="form-control" required></div>
                    <div class="mb-3"><label>Sección *</label>
                        <select name="id_seccion" id="edit_id_seccion" class="form-select" required>
                            @foreach($secciones as $seccion)
                                <option value="{{ $seccion->id }}">{{ $seccion->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3"><label>Género *</label>
                        <select name="genero" id="edit_genero" class="form-select" required>
                            <option value="M">Masculino</option>
                            <option value="F">Femenino</option>
                        </select>
                    </div>
                    <div class="mb-3"><label>Año</label><input type="number" name="año" id="edit_anio" class="form-control"></div>
                    <div class="mb-3"><label>Estado</label>
                        <select name="estado" id="edit_estado" class="form-select">
                            <option value="Activo">Activo</option>
                            <option value="Inactivo">Inactivo</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL IMPORTAR -->
<div class="modal fade" id="importModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('estudiantes.import', request()->query()) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Importar estudiantes desde Excel</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3"><label>Archivo Excel (xlsx, xls, csv)</label><input type="file" name="archivo" class="form-control" required></div>
                    <div class="form-text">Columnas requeridas: <strong>nombre, seccion, numero_lista, genero</strong> (M o F). Opcional: año.</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Importar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL CONFIRMAR ELIMINAR / REACTIVAR -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalTitle">Confirmar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="deleteModalBody"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="deleteForm" method="POST" style="display:inline;">
                    @csrf
                    <input type="hidden" name="_method" id="deleteMethod" value="DELETE">
                    <button type="submit" class="btn btn-danger" id="deleteModalBtn">Aceptar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Esperar a que Bootstrap esté disponible globalmente
        function initBootstrap() {
            if (typeof window.bootstrap !== 'undefined') {
                return window.bootstrap;
            }
            if (typeof bootstrap !== 'undefined') {
                window.bootstrap = bootstrap;
                return bootstrap;
            }
            setTimeout(initBootstrap, 100);
            return null;
        }
        
        const bs = initBootstrap();
        if (!bs) {
            console.error('Bootstrap no se cargó después de varios intentos.');
            return;
        }

        // ======================================
        // MODAL EDITAR
        // ======================================
        document.querySelectorAll('.edit-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const form = document.getElementById('editForm');
                if (!form) return;
                form.action = `/estudiantes/${this.dataset.id}`;
                document.getElementById('edit_name').value = this.dataset.name ?? '';
                document.getElementById('edit_numero_lista').value = this.dataset.numero_lista ?? '';
                document.getElementById('edit_id_seccion').value = this.dataset.seccion ?? '';
                document.getElementById('edit_genero').value = this.dataset.genero ?? '';
                document.getElementById('edit_anio').value = this.dataset.anio ?? '';
                document.getElementById('edit_estado').value = this.dataset.estado ?? '';
            });
        });

        // ======================================
        // MODAL ELIMINAR / REACTIVAR
        // ======================================
        const deleteForm = document.getElementById('deleteForm');
        const deleteMethod = document.getElementById('deleteMethod');
        const modalTitle = document.getElementById('deleteModalTitle');
        const modalBody = document.getElementById('deleteModalBody');
        const modalBtn = document.getElementById('deleteModalBtn');

        document.querySelectorAll('.delete-btn, .reactivar-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                const nombre = this.dataset.nombre;
                const tipo = this.dataset.tipo;
                if (tipo === 'eliminar') {
                    modalTitle.innerText = 'Confirmar eliminación';
                    modalBody.innerText = `¿Desea pasar a inactivo al estudiante "${nombre}"?`;
                    modalBtn.innerText = 'Eliminar';
                    modalBtn.classList.remove('btn-success');
                    modalBtn.classList.add('btn-danger');
                    deleteForm.action = `/estudiantes/${id}`;
                    deleteMethod.value = 'DELETE';
                } else {
                    modalTitle.innerText = 'Confirmar reactivación';
                    modalBody.innerText = `¿Desea reactivar al estudiante "${nombre}"?`;
                    modalBtn.innerText = 'Reactivar';
                    modalBtn.classList.remove('btn-danger');
                    modalBtn.classList.add('btn-success');
                    deleteForm.action = `/estudiantes/${id}/reactivar`;
                    deleteMethod.value = 'PATCH';
                }
            });
        });

        // Inicializar modales solo si existen
        document.querySelectorAll('.modal').forEach(modalEl => {
            try {
                new bs.Modal(modalEl);
            } catch(e) {
                console.warn('Error inicializando modal:', e);
            }
        });
    });
</script>
@endsection
