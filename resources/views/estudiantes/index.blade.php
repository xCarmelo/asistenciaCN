{{-- resources/views/estudiantes/index.blade.php --}}
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

    @if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

 {{-- Filtros --}}
<form id="filtrosForm" method="GET" action="{{ route('estudiantes.index') }}" class="row g-2 mb-4">
    <div class="col-md-3"> 
        <label class="form-label fw-semibold">Nombre</label>
        <input type="text" name="nombre" class="form-control" placeholder="Buscar estudiante..." value="{{ request('nombre') }}">
    </div>
    <div class="col-md-3">
        <label class="form-label fw-semibold">Sección</label>
        <select name="seccion_id" class="form-select">
            <option value="">Todas</option>
            @foreach($secciones as $seccion)
                <option value="{{ $seccion->id }}" {{ request('seccion_id') == $seccion->id ? 'selected' : '' }}>
                    {{ $seccion->nombre }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-2">
        <label class="form-label fw-semibold">Estado</label>
        <select name="estado" class="form-select">
            <option value="">Todos</option>
            @foreach($estados as $estado)
                @if($estado->nombre != 'Inactivo')
                    <option value="{{ $estado->id }}" {{ request('estado') == $estado->id ? 'selected' : '' }}>
                        {{ $estado->nombre }}
                    </option>
                @endif
            @endforeach
        </select>
    </div>
    <div class="col-md-2">
        <label class="form-label fw-semibold">Mostrar</label>
        <select name="per_page" class="form-select" onchange="this.form.submit()">
            <option value="15" {{ request('per_page') == 15 ? 'selected' : '' }}>15</option>
            <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
        </select>
    </div>
    <div class="col-md-2 d-flex gap-2 align-items-end">
        <button type="submit" class="btn btn-primary w-100">
            <i class="bi bi-search"></i> Filtrar
        </button>
        <a href="{{ route('estudiantes.index') }}" class="btn btn-secondary w-100">
            <i class="bi bi-eraser"></i> Limpiar
        </a>
    </div>
</form>

@php
    $estadoFiltro = null;
    if (request()->filled('estado')) {
        $estadoFiltro = $estados->firstWhere('id', request('estado'));
    }
    $mostrarCambioSeccion = !request()->filled('estado') || ($estadoFiltro && $estadoFiltro->nombre == 'Activo');
@endphp

<div class="d-flex justify-content-between align-items-center my-3">
    <div>
        <span id="selectedCountBadge" class="badge bg-primary">0 seleccionados</span>
    </div>
    <div class="btn-group">
        <button type="button" class="btn btn-outline-secondary" id="selectAllPageBtn">Seleccionar página</button>
        <button type="button" class="btn btn-outline-secondary" id="deselectAllBtn">Deseleccionar todos</button>
        {{-- <button type="button" class="btn btn-outline-secondary" id="viewSelectedBtn">Ver seleccionados</button> --}}
        @if($mostrarCambioSeccion)
                <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#bulkSeccionModal">Cambiar sección</button>
            @endif
        <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#bulkEstadoModal">Cambiar estado</button>
    </div>
</div>

    {{-- TABLA --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th><input type="checkbox" id="selectAllCheckbox"></th>
                            <th>#</th>
                            <th>Nombre completo</th>
                            <th>N° Lista</th>
                            <th>Sección</th>
                            <th>Género</th>
                            <th>Estado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($historiales as $historial)
                            @php
                                $estudiante = $historial->estudiante;
                                $seccion = $historial->seccion;
                                $estado = $historial->estado;
                                $activo = ($estado && $estado->permite_asistencia && $historial->fecha_fin === null);
                            @endphp
                            <tr>
                                <td>
                                    <input type="checkbox" class="student-checkbox" value="{{ $estudiante->id }}">
                                </td>
                                </td>
                                <td>{{ ($historiales->currentPage() - 1) * $historiales->perPage() + $loop->iteration }}</td>
                                <td class="fw-semibold">{{ $estudiante->name ?? 'N/A' }}</td>
                                <td>{{ $historial->numero_lista ?? '-' }}</td>
                                <td>{{ $seccion->nombre ?? 'Sin asignar' }}</td>
                                <td>
                                    @if(isset($estudiante->genero))
                                        {{ $estudiante->genero == 'M' ? 'Masculino' : ($estudiante->genero == 'F' ? 'Femenino' : '-') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if($estado)
                                        <span class="badge bg-{{ $estado->nombre == 'Activo' ? 'success' : 'secondary' }}">
                                            {{ $estado->nombre }}
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">Sin estado</span>
                                    @endif
                                </td>
                               <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        {{-- Botón Editar: solo si el estado permite edición (ej. Activo) --}}
                                        @if($estado && $estado->nombre == 'Activo')
                                            <button class="btn btn-sm btn-outline-primary edit-btn"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#editModal"
                                                    data-id="{{ $estudiante->id ?? '' }}"
                                                    data-name="{{ $estudiante->name ?? '' }}"
                                                    data-numero_lista="{{ $historial->numero_lista ?? '' }}"
                                                    data-seccion="{{ $historial->seccion_id ?? '' }}"
                                                    data-genero="{{ $estudiante->genero ?? '' }}">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                        @endif

                                        {{-- Botón Eliminar (desactivar): solo si el estado es Activo --}}
                                        @if($estado && $estado->nombre == 'Activo')
                                            <button class="btn btn-sm btn-outline-danger delete-btn"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#deleteModal"
                                                    data-id="{{ $estudiante->id ?? '' }}"
                                                    data-nombre="{{ $estudiante->name ?? '' }}"
                                                    data-tipo="eliminar">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        @else
                                            {{-- Botón Reactivar (cambiar estado): solo si NO es Activo --}}
                                            <button class="btn btn-sm btn-outline-success reactivar-btn"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#reactivarModal"
                                                    data-id="{{ $estudiante->id ?? '' }}"
                                                    data-nombre="{{ $estudiante->name ?? '' }}"
                                                    data-tipo="reactivar">
                                                <i class="bi bi-arrow-repeat"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="bi bi-database-x fs-1 d-block mb-3"></i>
                                        No se encontraron registros.
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $historiales->withQueryString()->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

{{-- MODAL CREAR --}}
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
                    <div class="mb-3">
                        <label>Nombre completo *</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Número de lista *</label>
                        <input type="number" name="numero_lista" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Sección *</label>
                        <select name="id_seccion" class="form-select" required>
                            <option value="">Seleccionar</option>
                            @foreach($secciones as $seccion)
                                <option value="{{ $seccion->id }}">{{ $seccion->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Género *</label>
                        <select name="genero" class="form-select" required>
                            <option value="">Seleccionar</option>
                            <option value="M">Masculino</option>
                            <option value="F">Femenino</option>
                        </select>
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
{{-- MODAL EDITAR --}}
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editForm" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Editar Estudiante</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Nombre completo *</label>
                        <input type="text" name="name" id="edit_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Número de lista *</label>
                        <input type="number" name="numero_lista" id="edit_numero_lista" class="form-control" required min="1">
                    </div>
                    <div class="mb-3">
                        <label>Sección *</label>
                        <select name="id_seccion" id="edit_id_seccion" class="form-select" required>
                            @foreach($secciones as $seccion)
                                <option value="{{ $seccion->id }}">{{ $seccion->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Género *</label>
                        <select name="genero" id="edit_genero" class="form-select" required>
                            <option value="M">Masculino</option>
                            <option value="F">Femenino</option>
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


{{-- MODAL IMPORTAR --}}
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
                    <div class="mb-3">
                        <label>Archivo Excel (xlsx, xls, csv)</label>
                        <input type="file" name="archivo" class="form-control" required>
                    </div>
                    <div class="form-text">
                        Columnas requeridas: <strong>nombre, seccion, numero_lista, genero</strong> (M o F).
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Importar</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL CAMBIO DE SECCIÓN --}}
<div class="modal fade" id="bulkSeccionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="bulkSeccionForm" method="POST" action="{{ route('estudiantes.bulkUpdate') }}">
                @csrf
                <input type="hidden" name="action" value="seccion">
                <input type="hidden" name="estudiantes" id="bulkSeccionEstudiantes">
                <div class="modal-header">
                    <h5 class="modal-title">Cambio masivo de sección</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Se cambiarán <strong id="bulkSeccionCount">0</strong> estudiante(s).</p>
                    <div class="mb-3">
                        <label>Nueva sección *</label>
                        <select name="seccion_id" class="form-select" required>
                            <option value="">-- Seleccionar --</option>
                            @foreach($secciones as $seccion)
                                <option value="{{ $seccion->id }}">{{ $seccion->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning">Aplicar cambio</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL CAMBIO DE ESTADO --}}
<div class="modal fade" id="bulkEstadoModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="bulkEstadoForm" method="POST" action="{{ route('estudiantes.bulkUpdate') }}">
                @csrf
                <input type="hidden" name="action" value="estado">
                <input type="hidden" name="estudiantes" id="bulkEstadoEstudiantes">
                <div class="modal-header">
                    <h5 class="modal-title">Cambio masivo de estado</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Se cambiarán <strong id="bulkEstadoCount">0</strong> estudiante(s).</p>
                    <div class="mb-3">
                        <label>Nuevo estado *</label>
                        <select name="estado_id" class="form-select" required>
                            <option value="">-- Seleccionar --</option>
                            @foreach($estados as $estado)
                                @if($estado->nombre != 'Inactivo')
                                    <option value="{{ $estado->id }}">{{ $estado->nombre }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-info">Aplicar cambio</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL CONFIRMAR ELIMINACIÓN (con opción de estado) --}}
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="deleteForm" method="POST">
                @csrf
                <input type="hidden" name="_method" id="deleteMethod" value="DELETE">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalTitle">Desactivar estudiante</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p id="deleteModalBody"></p>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Motivo de desactivación</label>
                        <select name="estado_id" id="deleteEstadoId" class="form-select" required>
                            <option value="">-- Seleccionar --</option>
                            @foreach($estados as $estado)
                                @if(!$estado->permite_asistencia) {{-- Muestra Inactivo, Retirado, Graduado --}}
                                    <option value="{{ $estado->id }}">{{ $estado->nombre }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger" id="deleteModalBtn">Desactivar</button>
                </div>
            </form>
        </div>
    </div>
</div>


{{-- MODAL REACTIVAR (solo estado) --}}
<div class="modal fade" id="reactivarModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="reactivarForm" method="POST" action="">
                @csrf
                @method('PATCH')
                <div class="modal-header">
                    <h5 class="modal-title">Cambiar estado del estudiante</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p id="reactivarModalBody"></p>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nuevo estado</label>
                        <select name="estado_id" id="reactivarEstadoId" class="form-select" required>
                            <option value="">-- Seleccionar --</option>
                            @foreach($estados as $estado)
                                @if($estado->nombre != 'Inactivo')
                                    <option value="{{ $estado->id }}">{{ $estado->nombre }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Cambiar estado</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Bootstrap
    const bs = (typeof window.bootstrap !== 'undefined') ? window.bootstrap : (typeof bootstrap !== 'undefined' ? bootstrap : null);
    if (bs) {
        document.querySelectorAll('.modal').forEach(modalEl => { try { new bs.Modal(modalEl); } catch(e) {} });
    }

    // ---------- Gestión de selección ----------
    let selectedStudents = JSON.parse(localStorage.getItem('selectedStudents') || '[]');

    function updateUI() {
        const count = selectedStudents.length;
        const badge = document.getElementById('selectedCountBadge');
        if (badge) badge.innerText = `${count} seleccionado${count !== 1 ? 's' : ''}`;
        // Marcar checkboxes
        document.querySelectorAll('.student-checkbox').forEach(cb => {
            const id = parseInt(cb.value);
            cb.checked = selectedStudents.includes(id);
        });
        // Checkbox "seleccionar todos"
        const allCheckboxes = document.querySelectorAll('.student-checkbox');
        const selectAll = document.getElementById('selectAllCheckbox');
        if (selectAll) {
            selectAll.checked = allCheckboxes.length > 0 && Array.from(allCheckboxes).every(cb => cb.checked);
        }
    }

    function saveSelection() {
        localStorage.setItem('selectedStudents', JSON.stringify(selectedStudents));
        updateUI();
    }

    function addStudent(id) {
        if (!selectedStudents.includes(id)) {
            selectedStudents.push(id);
            saveSelection();
        }
    }

    function removeStudent(id) {
        selectedStudents = selectedStudents.filter(s => s !== id);
        saveSelection();
    }

    function clearSelection() {
        selectedStudents = [];
        saveSelection();
    }

    // Evento para checkboxes individuales (delegación)
    document.addEventListener('change', function(e) {
        if (e.target.classList && e.target.classList.contains('student-checkbox')) {
            const id = parseInt(e.target.value);
            if (e.target.checked) addStudent(id);
            else removeStudent(id);
        }
        if (e.target.id === 'selectAllCheckbox') {
            const checkboxes = document.querySelectorAll('.student-checkbox');
            if (e.target.checked) {
                checkboxes.forEach(cb => {
                    const id = parseInt(cb.value);
                    if (!selectedStudents.includes(id)) selectedStudents.push(id);
                });
            } else {
                const idsOnPage = Array.from(checkboxes).map(cb => parseInt(cb.value));
                selectedStudents = selectedStudents.filter(id => !idsOnPage.includes(id));
            }
            saveSelection();
        }
    });

    // Botones
    const selectAllPageBtn = document.getElementById('selectAllPageBtn');
    if (selectAllPageBtn) {
        selectAllPageBtn.addEventListener('click', function() {
            document.querySelectorAll('.student-checkbox').forEach(cb => {
                const id = parseInt(cb.value);
                if (!selectedStudents.includes(id)) selectedStudents.push(id);
            });
            saveSelection();
        });
    }

    const deselectAllBtn = document.getElementById('deselectAllBtn');
    if (deselectAllBtn) deselectAllBtn.addEventListener('click', clearSelection);

    // Mostrar selección al cargar
    updateUI();

    // ---------- Modales individuales ----------
    // Editar
    document.querySelectorAll('.edit-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const form = document.getElementById('editForm');
            if (form) {
                form.action = `/estudiantes/${this.dataset.id}`;
                document.getElementById('edit_name').value = this.dataset.name || '';
                document.getElementById('edit_numero_lista').value = this.dataset.numero_lista || '';
                document.getElementById('edit_id_seccion').value = this.dataset.seccion || '';
                document.getElementById('edit_genero').value = this.dataset.genero || '';
            }
        });
    });

    // Eliminar
    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const nombre = this.dataset.nombre;
            document.getElementById('deleteModalTitle').innerText = 'Desactivar estudiante';
            document.getElementById('deleteModalBody').innerHTML = `¿Desea desactivar al estudiante "<strong>${nombre}</strong>"? Seleccione el motivo:`;
            const estadoSelect = document.getElementById('deleteEstadoId');
            if (estadoSelect) {
                estadoSelect.value = '';
                estadoSelect.disabled = false;
            }
            const deleteForm = document.getElementById('deleteForm');
            if (deleteForm) {
                deleteForm.action = `/estudiantes/${id}`;
                document.getElementById('deleteMethod').value = 'DELETE';
            }
        });
    });

    // Reactivar individual
    document.querySelectorAll('.reactivar-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const nombre = this.dataset.nombre;
            document.getElementById('reactivarModalBody').innerHTML = `Seleccione el nuevo estado para el estudiante "<strong>${nombre}</strong>". Los datos de sección y número de lista se mantendrán igual que antes de su desactivación.`;
            const reactivarForm = document.getElementById('reactivarForm');
            if (reactivarForm) reactivarForm.action = `/estudiantes/${id}/reactivar`;
            const estadoSelect = document.getElementById('reactivarEstadoId');
            if (estadoSelect) estadoSelect.value = '';
        });
    });

    // ---------- Formularios masivos ----------
    const bulkSeccionForm = document.getElementById('bulkSeccionForm');
    if (bulkSeccionForm) {
        bulkSeccionForm.addEventListener('submit', function(e) {
            if (selectedStudents.length === 0) {
                e.preventDefault();
                alert('No hay estudiantes seleccionados.');
                return;
            }
            document.getElementById('bulkSeccionEstudiantes').value = JSON.stringify(selectedStudents);
            document.getElementById('bulkSeccionCount').innerText = selectedStudents.length;
        });
    }

    const bulkEstadoForm = document.getElementById('bulkEstadoForm');
    if (bulkEstadoForm) {
        bulkEstadoForm.addEventListener('submit', function(e) {
            if (selectedStudents.length === 0) {
                e.preventDefault();
                alert('No hay estudiantes seleccionados.');
                return;
            }
            // Eliminar campo antiguo si existe
            let oldInput = document.getElementById('bulkEstadoEstudiantes');
            if (oldInput) oldInput.remove();
            // Crear inputs dinámicos
            selectedStudents.forEach(id => {
                let input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'estudiantes[]';
                input.value = id;
                bulkEstadoForm.appendChild(input);
            });
            document.getElementById('bulkEstadoCount').innerText = selectedStudents.length;
        });
    }

    // Actualizar contadores al abrir modales
    const seccionModal = document.getElementById('bulkSeccionModal');
    if (seccionModal) {
        seccionModal.addEventListener('show.bs.modal', () => {
            document.getElementById('bulkSeccionCount').innerText = selectedStudents.length;
        });
    }
    const estadoModal = document.getElementById('bulkEstadoModal');
    if (estadoModal) {
        estadoModal.addEventListener('show.bs.modal', () => {
            document.getElementById('bulkEstadoCount').innerText = selectedStudents.length;
        });
    }

    // Limpiar selección si viene de operación masiva
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('clear_selection')) {
        localStorage.removeItem('selectedStudents');
        selectedStudents = [];
        updateUI();
        const newUrl = window.location.pathname + window.location.search.replace(/[?&]clear_selection=1/, '').replace(/^&/, '?');
        window.history.replaceState({}, '', newUrl);
    }

    // Al cargar, restaurar filtros desde localStorage
const filtrosForm = document.getElementById('filtrosForm');
if (localStorage.getItem('filtrosEstudiantes')) {
    const filtros = JSON.parse(localStorage.getItem('filtrosEstudiantes'));
    filtrosForm.querySelector('input[name="nombre"]').value = filtros.nombre || '';
    filtrosForm.querySelector('select[name="seccion_id"]').value = filtros.seccion_id || '';
    filtrosForm.querySelector('select[name="estado"]').value = filtros.estado || '';
    // No auto-enviar para evitar doble carga, el usuario puede enviar manualmente
}
filtrosForm.addEventListener('submit', function() {
    const filtros = {
        nombre: this.querySelector('input[name="nombre"]').value,
        seccion_id: this.querySelector('select[name="seccion_id"]').value,
        estado: this.querySelector('select[name="estado"]').value,
        per_page: this.querySelector('select[name="per_page"]').value
    };
    localStorage.setItem('filtrosEstudiantes', JSON.stringify(filtros));
});
document.getElementById('limpiarFiltros')?.addEventListener('click', function() {
    localStorage.removeItem('filtrosEstudiantes');
});
});
</script>
@endsection