<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Nombre</th>
                        <th>Género</th>
                        <th>Tutelado (Sección a cargo)</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($maestros as $maestro)
                    @php
                        $tuteladoNombre = $maestro->seccionesGuiadas->first()?->nombre ?? 'Sin tutelado';
                    @endphp
                    <tr>
                        <td>{{ ($maestros->currentPage() - 1) * $maestros->perPage() + $loop->iteration }}</td>
                        <td>{{ $maestro->name }}</td>
                        <td>{{ $maestro->genero == 'M' ? 'Masculino' : 'Femenino' }}</td>
                        <td>{{ $tuteladoNombre }}</td>
                        <td><span class="badge bg-{{ $maestro->estado == 1 ? 'success' : 'secondary' }}">{{ $maestro->estado == 1 ? 'Activo' : 'Inactivo' }}</span></td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary edit-btn"
                                data-id="{{ $maestro->id }}"
                                data-name="{{ $maestro->name }}"
                                data-genero="{{ $maestro->genero }}"
                                data-tutelado="{{ $maestro->seccionesGuiadas->first()?->id ?? '' }}"
                                data-estado="{{ $maestro->estado == 1 ? 'Activo' : 'Inactivo' }}">
                                <i class="bi bi-pencil"></i>
                            </button>
                            @if($maestro->estado == 1)
                                <button class="btn btn-sm btn-outline-danger delete-btn"
                                    data-id="{{ $maestro->id }}"
                                    data-nombre="{{ $maestro->name }}"
                                    data-tipo="eliminar">
                                    <i class="bi bi-trash"></i>
                                </button>
                            @else
                                <button class="btn btn-sm btn-outline-success reactivar-btn"
                                    data-id="{{ $maestro->id }}"
                                    data-nombre="{{ $maestro->name }}"
                                    data-tipo="reactivar">
                                    <i class="bi bi-arrow-repeat"></i> Reactivar
                                </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                        <tr><td colspan="6" class="text-center">No hay maestros registrados. </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $maestros->links('pagination::bootstrap-5') }}
    </div>
</div>
