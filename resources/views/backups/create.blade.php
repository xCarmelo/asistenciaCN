@extends('layouts.app')

@section('title', 'Crear Respaldo')

@section('content')
<div class="container">
    <div class="card shadow-sm mx-auto" style="max-width: 600px;">
        <div class="card-header bg-white">
            <h4 class="mb-0"><i class="bi bi-database-add me-2"></i> Crear Respaldo</h4>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('backups.store') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Ruta personalizada (opcional)</label>
                    <input type="text" name="custom_path" class="form-control" placeholder="Ej: D:/backups/mi_respaldo">
                    <div class="form-text">Dejar vacío para usar la carpeta predeterminada: <code>storage/app/backups</code></div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Descripción (opcional)</label>
                    <textarea name="description" class="form-control" rows="3" placeholder="Comentario sobre este respaldo..."></textarea>
                </div>
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('backups.index') }}" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Crear Respaldo</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
