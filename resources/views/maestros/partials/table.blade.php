<tbody>
    @forelse($maestros as $maestro)
    @php
        $historial = $maestro->historialActivo;
        $tuteladoNombre = $historial?->seccion?->nombre ?? 'Sin asignar';
        $estadoNombre = $historial?->estado?->nombre ?? 'Inactivo';
        $estadoBadge = ($estadoNombre == 'Activo') ? 'success' : 'secondary';
        $tuteladoId = $historial?->seccion_id ?? '';
    @endphp
    <tr>
        <td>{{ ($maestros->currentPage() - 1) * $maestros->perPage() + $loop->iteration }}</td>
        <td class="fw-semibold">{{ $maestro->name }}</td>
        <td>{{ $maestro->genero == 'M' ? 'Masculino' : 'Femenino' }}</td>
        <td>{{ $tuteladoNombre }}</td>
        <td><span class="badge bg-{{ $estadoBadge }}">{{ $estadoNombre }}</span></td>
        <td>
            <button class="btn btn-sm btn-outline-primary edit-btn"
                data-id="{{ $maestro->id }}"
                data-name="{{ $maestro->name }}"
                data-genero="{{ $maestro->genero }}"
                data-tutelado="{{ $tuteladoId }}"
                data-tutelado_nombre="{{ $tuteladoNombre }}"
                data-estado="{{ $estadoNombre }}">
                <i class="bi bi-pencil"></i>
            </button>
            @if($estadoNombre == 'Activo')
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
    <tr>
        <td colspan="6" class="text-center">No hay maestros registrados.</td>
    </tr>
    @endforelse
</tbody>

@if(method_exists($maestros, 'links'))
    <div class="mt-3">
        {{ $maestros->links('pagination::bootstrap-5') }}
    </div>
@endif