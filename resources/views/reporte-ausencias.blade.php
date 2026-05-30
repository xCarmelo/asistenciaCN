@extends('layouts.app')

@section('title', 'Reporte de Ausencias y Llegadas Tarde')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
        <h1 class="h3">
            <i class="bi bi-file-text-fill text-primary"></i>
            Reporte de Ausencias y Llegadas Tarde
        </h1>
    </div>

    <!-- Filtros -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white">
            <i class="bi bi-funnel-fill me-1"></i> Filtros
        </div>
        <div class="card-body">
            <form id="filtrosForm" method="GET" action="{{ route('reporte-ausencias') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Sección *</label>
                    <select name="seccion_id" id="seccion_id" class="form-select" required>
                        <option value="">-- Seleccionar --</option>
                        @foreach($secciones as $seccion)
                            <option value="{{ $seccion->id }}" {{ $filtros['seccion_id'] == $seccion->id ? 'selected' : '' }}>{{ $seccion->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-3" id="corte_container">
                    <label class="form-label fw-semibold">Corte Evaluativo</label>
                    <select name="corte_id" id="corte_id" class="form-select">
                        <option value="">-- Seleccionar --</option>
                        @foreach($cortes as $corte)
                            <option value="{{ $corte->id }}" {{ $filtros['corte_id'] == $corte->id ? 'selected' : '' }}>{{ $corte->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-2" id="anio_container">
                    <label class="form-label fw-semibold">Año</label>
                    <select name="anio" id="anio" class="form-select">
                        <option value="">-- Seleccionar --</option>
                        @foreach($anios as $anio)
                            <option value="{{ $anio }}" {{ $filtros['anio'] == $anio ? 'selected' : '' }}>{{ $anio }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-4" id="rango_container">
                    <label class="form-label fw-semibold">Rango de fechas</label>
                    <div class="row g-2">
                        <div class="col-6">
                            <input type="date" name="desde" id="desde" class="form-control" value="{{ $filtros['desde'] }}" placeholder="Desde">
                        </div>
                        <div class="col-6">
                            <input type="date" name="hasta" id="hasta" class="form-control" value="{{ $filtros['hasta'] }}" placeholder="Hasta">
                        </div>
                    </div>
                </div>
                
                <div class="col-12">
                    <button type="submit" class="btn btn-primary" id="btnConsultar">
                        <i class="bi bi-search"></i> Consultar
                    </button>
                    <button type="button" class="btn btn-success" id="btnExportarExcel">
                        <i class="bi bi-file-earmark-excel"></i> Exportar Excel
                    </button>
                    <button type="button" class="btn btn-danger" id="btnExportarPDF">
                        <i class="bi bi-file-pdf"></i> Exportar PDF
                    </button>
                    <button type="button" class="btn btn-secondary" id="btnExportarWord">
                        <i class="bi bi-file-word"></i> Exportar Word
                    </button>
                    <a href="{{ route('reporte-ausencias') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-eraser"></i> Limpiar filtros
                    </a>
                </div>
            </form>
        </div>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Vista previa del reporte -->
    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <i class="bi bi-eye me-1"></i> Vista previa del reporte
        </div>
        <div class="card-body p-0">
            <iframe id="pdfPreview" src="" style="width: 100%; height: 600px; border: none;"></iframe>
            <div id="noPreview" class="text-center py-5 text-muted" style="display: none;">
                <i class="bi bi-file-earmark-pdf fs-1"></i>
                <p class="mt-2">Seleccione los filtros y consulte para generar la vista previa</p>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const corteSelect = document.getElementById('corte_id');
        const anioSelect = document.getElementById('anio');
        const desdeInput = document.getElementById('desde');
        const hastaInput = document.getElementById('hasta');

        function actualizarEstadoCampos() {
            const tieneCorte = corteSelect.value !== '';
            const tieneRango = (desdeInput.value !== '' && hastaInput.value !== '');

            if (tieneCorte) {
                desdeInput.disabled = true;
                hastaInput.disabled = true;
                anioSelect.disabled = false;
                desdeInput.value = '';
                hastaInput.value = '';
            } else {
                desdeInput.disabled = false;
                hastaInput.disabled = false;
                anioSelect.disabled = false;
            }

            if (tieneRango) {
                corteSelect.disabled = true;
                anioSelect.disabled = true;
                corteSelect.value = '';
                anioSelect.value = '';
            } else if (!tieneCorte) {
                corteSelect.disabled = false;
                anioSelect.disabled = false;
            }
        }

        corteSelect.addEventListener('change', actualizarEstadoCampos);
        desdeInput.addEventListener('change', actualizarEstadoCampos);
        hastaInput.addEventListener('change', actualizarEstadoCampos);
        actualizarEstadoCampos();

        function cargarVistaPrevia() {
            const formData = new FormData(document.getElementById('filtrosForm'));
            const params = new URLSearchParams();
            for (let [key, value] of formData.entries()) {
                if (value) params.append(key, value);
            }
            fetch('{{ route("reporte-ausencias.preview") }}?' + params.toString())
                .then(response => response.json())
                .then(data => {
                    const iframe = document.getElementById('pdfPreview');
                    const noPreview = document.getElementById('noPreview');
                    if (data.html && data.html.trim() !== '') {
                        const blob = new Blob([data.html], {type: 'text/html'});
                        const url = URL.createObjectURL(blob);
                        iframe.src = url;
                        iframe.style.display = 'block';
                        noPreview.style.display = 'none';
                    } else {
                        iframe.style.display = 'none';
                        noPreview.style.display = 'block';
                        noPreview.innerHTML = '<div class="alert alert-warning">No se pudo generar la vista previa. Verifique los filtros.</div>';
                    }
                })
                .catch(error => {
                    console.error('Error al cargar vista previa:', error);
                    const noPreview = document.getElementById('noPreview');
                    noPreview.innerHTML = '<div class="alert alert-danger">Error al cargar vista previa</div>';
                    noPreview.style.display = 'block';
                });
        }

        const seccion = document.getElementById('seccion_id').value;
        const corte = document.getElementById('corte_id').value;
        const desde = document.getElementById('desde').value;
        const hasta = document.getElementById('hasta').value;
        if (seccion && (corte || (desde && hasta))) {
            cargarVistaPrevia();
        }

        document.getElementById('btnConsultar').addEventListener('click', function(e) {
            e.preventDefault();
            cargarVistaPrevia();
        });

        function exportar(tipo) {
            const form = document.getElementById('filtrosForm');
            const seccionVal = form.querySelector('[name="seccion_id"]').value;
            const corteVal = form.querySelector('[name="corte_id"]').value;
            const desdeVal = form.querySelector('[name="desde"]').value;
            const hastaVal = form.querySelector('[name="hasta"]').value;
            if (!seccionVal || (!corteVal && (!desdeVal || !hastaVal))) {
                alert('Debe seleccionar una sección y un corte o rango de fechas.');
                return false;
            }
            const formData = new FormData(form);
            const params = new URLSearchParams();
            for (let [key, value] of formData.entries()) {
                if (value) params.append(key, value);
            }
            let url = '';
            if (tipo === 'pdf') url = '{{ route("reporte-ausencias.pdf") }}';
            else if (tipo === 'excel') url = '{{ route("reporte-ausencias.excel") }}';
            else if (tipo === 'word') url = '{{ route("reporte-ausencias.word") }}';
            window.open(url + '?' + params.toString(), '_blank');
            return true;
        }

        document.getElementById('btnExportarPDF').addEventListener('click', () => exportar('pdf'));
        document.getElementById('btnExportarExcel').addEventListener('click', () => exportar('excel'));
        document.getElementById('btnExportarWord').addEventListener('click', () => exportar('word'));
    });
</script>
@endsection
