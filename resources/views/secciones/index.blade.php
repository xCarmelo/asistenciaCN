@extends('layouts.app')

@section('title', 'Secciones')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3">Lista de Secciones</h1>
    <div>
        <button type="button" class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#importModal">
            <i class="bi bi-file-earmark-excel"></i> Importar Excel
        </button>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">
            <i class="bi bi-plus-circle"></i> Agregar Sección
        </button>
    </div>
</div>

@if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
@if(session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif
@if(session('warning'))
    <div class="alert alert-warning">
        {{ session('warning') }}
        @if(session('reactivar_id'))
            <a href="{{ route('secciones.reactivar', session('reactivar_id')) }}" class="btn btn-sm btn-warning ms-2">Reactivar</a>
        @endif
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<!-- Filtros con botón -->
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('secciones.index') }}" class="row g-3">
            <div class="col-md-4">
                <label>Buscar por nombre</label>
                <input type="text" name="nombre" class="form-control" placeholder="Nombre de la sección" value="{{ request('nombre') }}">
            </div>
            <div class="col-md-3">
                <label>Estado</label>
                <select name="estado" class="form-select">
                    <option value="">Todos</option>
                    <option value="1" {{ request('estado') == '1' ? 'selected' : '' }}>Activo</option>
                    <option value="0" {{ request('estado') == '0' ? 'selected' : '' }}>Inactivo</option>
                </select>
            </div>
            <div class="col-md-5 d-flex gap-2 align-items-end">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search"></i> Filtrar
                </button>
                <a href="{{ route('secciones.index') }}" class="btn btn-secondary">
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
                    <tr><th>#</th><th>Nombre</th><th>Maestro Guía (Tutelado)</th><th>Estado</th><th>Acciones</th></tr>
                </thead>
                <tbody>
                    @forelse($secciones as $seccion)
                    <tr>
                        <td>{{ ($secciones->currentPage()-1)*$secciones->perPage()+$loop->iteration }}</td>
                        <td>{{ $seccion->nombre }}</td>
                        <td>{{ $seccion->maestroGuia->name ?? 'Sin asignar' }}</td>
                        <td><span class="badge bg-{{ $seccion->estado==1?'success':'secondary' }}">{{ $seccion->estado==1?'Activo':'Inactivo' }}</span></td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary edit-btn" data-bs-toggle="modal" data-bs-target="#editModal"
                                data-id="{{ $seccion->id }}"
                                data-nombre="{{ $seccion->nombre }}"
                                data-maestro="{{ $seccion->id_maestro_guia }}"
                                data-estado="{{ $seccion->estado }}">
                                <i class="bi bi-pencil"></i>
                            </button>
                            @if($seccion->estado==1)
                                <button class="btn btn-sm btn-outline-danger delete-btn" data-bs-toggle="modal" data-bs-target="#deleteModal"
                                    data-id="{{ $seccion->id }}" data-nombre="{{ $seccion->nombre }}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            @else
                                <button class="btn btn-sm btn-outline-success reactivar-btn" data-bs-toggle="modal" data-bs-target="#reactivarModal"
                                    data-id="{{ $seccion->id }}" data-nombre="{{ $seccion->nombre }}">
                                    <i class="bi bi-arrow-repeat"></i> Reactivar
                                </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <td><td colspan="5" class="text-center">No hay secciones registradas.@endforelse
                </tbody>
            </table>
        </div>
        {{ $secciones->links('pagination::bootstrap-5') }}
    </div>
</div>

<!-- MODAL CREAR (solo maestros libres) -->
<div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('secciones.store', request()->query()) }}" method="POST">
                @csrf
                <div class="modal-header"><h5>Agregar Sección</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3"><label>Nombre *</label><input type="text" name="nombre" class="form-control" required></div>
                    <div class="mb-3">
                        <label>Maestro Guía</label>
                        <select name="id_maestro_guia" class="form-select">
                            <option value="">Sin asignar</option>
                            @foreach($maestrosLibres as $maestro)
                                <option value="{{ $maestro->id }}">{{ $maestro->name }}</option>
                            @endforeach
                        </select>
                        <div class="form-text">Solo se muestran maestros sin sección activa asignada.</div>
                    </div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button><button type="submit" class="btn btn-primary">Guardar</button></div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL EDITAR (solo maestros libres + actual) -->
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editForm" method="POST" action="">
                @csrf @method('PUT')
                <div class="modal-header"><h5>Editar Sección</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3"><label>Nombre *</label><input type="text" name="nombre" id="edit_nombre" class="form-control" required></div>
                    <div class="mb-3">
                        <label>Maestro Guía</label>
                        <select name="id_maestro_guia" id="edit_maestro" class="form-select">
                            <option value="">Sin asignar</option>
                        </select>
                        <div class="form-text">Solo se muestran maestros disponibles y el actual.</div>
                    </div>
                    <div class="mb-3">
                        <label>Estado</label>
                        <select name="estado" id="edit_estado" class="form-select">
                            <option value="1">Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button><button type="submit" class="btn btn-primary">Actualizar</button></div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL IMPORTAR -->
<div class="modal fade" id="importModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('secciones.import', request()->query()) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header"><h5>Importar secciones</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3"><label>Archivo Excel</label><input type="file" name="archivo" class="form-control" required></div>
                    <div class="form-text"><strong>Columnas:</strong> nombre, maestro</div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button><button type="submit" class="btn btn-primary">Importar</button></div>
            </form>
        </div>
    </div>
</div>

<!-- MODALES DE CONFIRMACIÓN -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5>Confirmar desactivación</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body" id="deleteModalBody"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="deleteForm" method="POST" style="display:inline;">@csrf @method('DELETE')<button type="submit" class="btn btn-danger">Desactivar</button></form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="reactivarModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5>Confirmar reactivación</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body" id="reactivarModalBody"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="reactivarForm" method="POST" style="display:inline;">@csrf @method('PATCH')<button type="submit" class="btn btn-success">Reactivar</button></form>
            </div>
        </div>
    </div>
</div>

<script>
    // Datos de maestros desde el controlador
    const todosMaestros = @json($todosMaestros);
    const occupiedIds = @json($occupiedMaestroIds);

    // Llenar modal de edición con maestros disponibles + actual
    const editModal = document.getElementById('editModal');
    editModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const id = button.dataset.id;
        const nombre = button.dataset.nombre;
        const maestroActual = button.dataset.maestro; // puede ser null o ''
        const estado = button.dataset.estado;

        const form = document.getElementById('editForm');
        form.action = '/secciones/' + id + '?' + new URLSearchParams(window.location.search).toString();
        document.getElementById('edit_nombre').value = nombre;
        document.getElementById('edit_estado').value = estado;

        const select = document.getElementById('edit_maestro');
        select.innerHTML = '<option value="">Sin asignar</option>';

        // Filtrar: maestros no ocupados O el maestro actual
        const disponibles = todosMaestros.filter(m => {
            const isOccupied = occupiedIds.includes(m.id);
            // Si es el maestro actual (aunque esté ocupado por esta sección) lo incluimos
            if (parseInt(m.id) === parseInt(maestroActual)) return true;
            // Si no está ocupado, lo incluimos
            return !isOccupied;
        });

        disponibles.forEach(m => {
            const option = document.createElement('option');
            option.value = m.id;
            option.textContent = m.name;
            if (parseInt(m.id) === parseInt(maestroActual)) option.selected = true;
            select.appendChild(option);
        });

        // Si el maestro actual no está en la lista (caso raro), lo añadimos
        if (maestroActual && !disponibles.some(m => parseInt(m.id) === parseInt(maestroActual))) {
            const option = document.createElement('option');
            option.value = maestroActual;
            option.textContent = 'Maestro actual (desconocido)';
            option.selected = true;
            select.appendChild(option);
        }
    });

    // Eliminar
    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const nombre = this.dataset.nombre;
            const params = new URLSearchParams(window.location.search);
            document.getElementById('deleteModalBody').innerText = '¿Desactivar sección "' + nombre + '"? Se liberará el maestro asignado.';
            document.getElementById('deleteForm').action = '/secciones/' + id + '?' + params.toString();
        });
    });

    // Reactivar
    document.querySelectorAll('.reactivar-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const nombre = this.dataset.nombre;
            const params = new URLSearchParams(window.location.search);
            document.getElementById('reactivarModalBody').innerText = '¿Reactivar sección "' + nombre + '"?';
            document.getElementById('reactivarForm').action = '/secciones/' + id + '/reactivar?' + params.toString();
        });
    });
</script>
@endsection
