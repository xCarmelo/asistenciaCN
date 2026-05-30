@extends('layouts.app')

@section('title', 'Importar Respaldo')

@section('content')
<div class="container">
    <div class="card shadow-sm mx-auto" style="max-width: 600px;">
        <div class="card-header bg-white">
            <h4 class="mb-0"><i class="bi bi-upload me-2"></i> Importar Respaldo</h4>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('backups.import') }}" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Archivo de respaldo</label>
                    <input type="file" name="backup_file" class="form-control" accept=".sql,.zip,.json,.csv,.txt" required>
                    <div class="form-text">Formatos permitidos: .sql, .zip, .json, .csv, .txt</div>
                </div>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle-fill"></i> Los archivos SQL se restaurarán automáticamente. Otros formatos se guardarán como archivos para restaurar manualmente.
                </div>
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('backups.index') }}" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Importar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
