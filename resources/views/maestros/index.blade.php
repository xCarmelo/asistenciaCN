@extends('layouts.app')

@section('title', 'Maestros')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3">Lista de Maestros</h1>
    <div>
        <button type="button" class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#importModal">
            <i class="bi bi-file-earmark-excel"></i> Importar Excel
        </button>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">
            <i class="bi bi-plus-circle"></i> Agregar Maestro
        </button>
    </div>
</div>

@if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
@if(session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif

<!-- Filtros con botón -->
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('maestros.index') }}" class="row g-3">
            <div class="col-md-3">
                <label>Nombre</label>
                <input type="text" name="nombre" class="form-control" placeholder="Nombre del maestro" value="{{ request('nombre') }}">
            </div>
            <div class="col-md-3">
                <label>Género</label>
                <select name="genero" class="form-select">
                    <option value="">Todos</option>
                    <option value="M" {{ request('genero') == 'M' ? 'selected' : '' }}>Masculino</option>
                    <option value="F" {{ request('genero') == 'F' ? 'selected' : '' }}>Femenino</option>
                </select>
            </div>
            <div class="col-md-3">
                <label>Estado</label>
                <select name="estado" class="form-select">
                    <option value="Activo" {{ request('estado') == 'Activo' ? 'selected' : '' }}>Activo</option>
                    <option value="Inactivo" {{ request('estado') == 'Inactivo' ? 'selected' : '' }}>Inactivo</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2 align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search"></i> Filtrar
                </button>
                <a href="{{ route('maestros.index') }}" class="btn btn-secondary w-100">
                    <i class="bi bi-eraser"></i> Limpiar
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Tabla -->
<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Nombre</th>
                        <th>Género</th>
                        <th>Sección a cargo (Tutelado)</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($maestros as $maestro)
                    <tr>
                        <td>{{ ($maestros->currentPage() - 1) * $maestros->perPage() + $loop->iteration }}</td>
                        <td>{{ $maestro->name }}</td>
                        <td>{{ $maestro->genero == 'M' ? 'Masculino' : 'Femenino' }}</td>
                        <td>{{ $maestro->seccionesGuiadas->first()?->nombre ?? 'Sin asignar' }}</td>
                        <td><span class="badge bg-{{ $maestro->estado == 1 ? 'success' : 'secondary' }}">{{ $maestro->estado == 1 ? 'Activo' : 'Inactivo' }}</span></td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary edit-btn" data-bs-toggle="modal" data-bs-target="#editModal"
                                data-id="{{ $maestro->id }}"
                                data-name="{{ $maestro->name }}"
                                data-genero="{{ $maestro->genero }}"
                                data-tutelado="{{ $maestro->seccionesGuiadas->first()?->id ?? '' }}"
                                data-tutelado_nombre="{{ $maestro->seccionesGuiadas->first()?->nombre ?? '' }}"
                                data-estado="{{ $maestro->estado == 1 ? 'Activo' : 'Inactivo' }}">
                                <i class="bi bi-pencil"></i>
                            </button>
                            @if($maestro->estado == 1)
                                <button class="btn btn-sm btn-outline-danger delete-btn" data-bs-toggle="modal" data-bs-target="#deleteModal"
                                    data-id="{{ $maestro->id }}" data-nombre="{{ $maestro->name }}" data-tipo="eliminar">
                                    <i class="bi bi-trash"></i>
                                </button>
                            @else
                                <button class="btn btn-sm btn-outline-success reactivar-btn" data-bs-toggle="modal" data-bs-target="#deleteModal"
                                    data-id="{{ $maestro->id }}" data-nombre="{{ $maestro->name }}" data-tipo="reactivar">
                                    <i class="bi bi-arrow-repeat"></i> Reactivar
                                </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                        <table><td colspan="6" class="text-center">No hay maestros registrados.@endforelse
                </tbody>
            </table>
        </div>
        {{ $maestros->links('pagination::bootstrap-5') }}
    </div>
</div>

<!-- MODAL CREAR -->
<div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('maestros.store', request()->query()) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Agregar Maestro</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3"><label>Nombre completo *</label><input type="text" name="name" class="form-control" required></div>
                    <div class="mb-3">
                        <label>Género *</label>
                        <select name="genero" class="form-select" required>
                            <option value="">Seleccionar</option>
                            <option value="M">Masculino</option>
                            <option value="F">Femenino</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Sección a cargo (Tutelado)</label>
                        <select name="tutelado" class="form-select">
                            <option value="">Sin asignar</option>
                            @foreach($seccionesDisponibles as $seccion)
                                <option value="{{ $seccion->id }}">{{ $seccion->nombre }}</option>
                            @endforeach
                        </select>
                        <div class="form-text">Solo se muestran secciones sin maestro guía asignado.</div>
                    </div>
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
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Editar Maestro</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3"><label>Nombre completo *</label><input type="text" name="name" id="edit_name" class="form-control" required></div>
                    <div class="mb-3">
                        <label>Género *</label>
                        <select name="genero" id="edit_genero" class="form-select" required>
                            <option value="M">Masculino</option>
                            <option value="F">Femenino</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Sección a cargo (Tutelado)</label>
                        <select name="tutelado" id="edit_tutelado" class="form-select">
                            <!-- Las opciones se llenarán con JavaScript -->
                        </select>
                        <div class="form-text">Solo se muestran secciones disponibles más la actual.</div>
                    </div>
                    <div class="mb-3">
                        <label>Estado</label>
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
            <form action="{{ route('maestros.import', request()->query()) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Importar maestros desde Excel</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3"><label>Archivo Excel (xlsx, xls, csv)</label><input type="file" name="archivo" class="form-control" required></div>
                    <div class="form-text">Columnas requeridas: <strong>nombre, genero, tutelado</strong> (nombre de la sección).</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Importar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL CONFIRMAR -->
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
    // Datos para edición (secciones disponibles + actual)
    const seccionesDisponibles = @json($seccionesDisponibles);

    // Llenar modal de edición
    document.querySelectorAll('.edit-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const name = this.dataset.name;
            const genero = this.dataset.genero;
            const tuteladoId = this.dataset.tutelado;
            const tuteladoNombre = this.dataset.tutelado_nombre;
            const estado = this.dataset.estado;

            const form = document.getElementById('editForm');
            form.action = `/maestros/${id}?${new URLSearchParams(window.location.search).toString()}`;
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_genero').value = genero;
            document.getElementById('edit_estado').value = estado;

            // Construir select de tutelado: opciones = disponibles + actual (si tiene y no está en disponibles)
            const select = document.getElementById('edit_tutelado');
            select.innerHTML = '<option value="">Sin asignar</option>';
            // Agregar secciones disponibles
            seccionesDisponibles.forEach(seccion => {
                const option = document.createElement('option');
                option.value = seccion.id;
                option.textContent = seccion.nombre;
                if (seccion.id == tuteladoId) option.selected = true;
                select.appendChild(option);
            });
            // Si el maestro tiene una sección actual que no está en disponibles (porque está ocupada por él mismo), la agregamos manualmente
            if (tuteladoId && !seccionesDisponibles.some(s => s.id == tuteladoId)) {
                const option = document.createElement('option');
                option.value = tuteladoId;
                option.textContent = tuteladoNombre || 'Sección actual';
                option.selected = true;
                select.appendChild(option);
            }
        });
    });

    // Eliminar / Reactivar
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
                modalTitle.innerText = 'Confirmar desactivación';
                modalBody.innerText = `¿Desactivar al maestro "${nombre}"?`;
                modalBtn.innerText = 'Desactivar';
                modalBtn.classList.remove('btn-success');
                modalBtn.classList.add('btn-danger');
                deleteForm.action = `/maestros/${id}?${new URLSearchParams(window.location.search).toString()}`;
                deleteMethod.value = 'DELETE';
            } else {
                modalTitle.innerText = 'Confirmar reactivación';
                modalBody.innerText = `¿Reactivar al maestro "${nombre}"?`;
                modalBtn.innerText = 'Reactivar';
                modalBtn.classList.remove('btn-danger');
                modalBtn.classList.add('btn-success');
                deleteForm.action = `/maestros/${id}/reactivar?${new URLSearchParams(window.location.search).toString()}`;
                deleteMethod.value = 'PATCH';
            }
        });
    });
</script>
@endsection
