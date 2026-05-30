@extends('layouts.app')

@section('title', 'Reporte de Asistencia - ' . \Carbon\Carbon::parse($fecha)->format('d/m/Y'))

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
        <h1 class="h3">Reporte de Asistencia</h1>
        <div>
            <a href="{{ route('asistencia.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Volver a Asistencia
            </a>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <strong>Fecha:</strong> {{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered mb-0" id="reporteTabla" style="min-width: 1300px;">
                    <thead class="table-light">
                        <tr>
                            <th rowspan="2" class="align-middle" style="background-color: #f8f9fa;">GRADO / SECCIÓN</th>
                            <th colspan="3" style="background-color: #e7f0ff;">ASISTENCIA ESPERADA</th>
                            <th colspan="3" style="background-color: #e0f7e8;">ASISTENCIA REAL</th>
                            <th colspan="3" style="background-color: #fff3df;">INASISTENCIA JUSTIFICADA</th>
                            <th colspan="3" style="background-color: #ffe6e5;">INASISTENCIA INJUSTIFICADA</th>
                            <th rowspan="2" style="background-color: #f2f2f2;">AUSENTES</th>
                            <th rowspan="2" style="background-color: #f8f9fa;">ACCIONES</th>
                        </tr>
                        <tr>
                            <th style="background-color: #e7f0ff;">F</th>
                            <th style="background-color: #e7f0ff;">V</th>
                            <th style="background-color: #e7f0ff;">T</th>
                            <th style="background-color: #e0f7e8;">F</th>
                            <th style="background-color: #e0f7e8;">V</th>
                            <th style="background-color: #e0f7e8;">T</th>
                            <th style="background-color: #fff3df;">F</th>
                            <th style="background-color: #fff3df;">V</th>
                            <th style="background-color: #fff3df;">T</th>
                            <th style="background-color: #ffe6e5;">F</th>
                            <th style="background-color: #ffe6e5;">V</th>
                            <th style="background-color: #ffe6e5;">T</th>
                        </tr>
                    </thead>
                    <tbody id="reporteBody">
                        <!-- Los datos se cargarán dinámicamente -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modales (igual que antes) -->
<div class="modal fade" id="modalEditEstudiante" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar asistencia de estudiante</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p><strong>Nombre:</strong> <span id="estudianteNombre"></span></p>
                <p><strong>Número de lista:</strong> <span id="estudianteNumero"></span></p>
                <div class="mb-3">
                    <label class="form-label">Estado de asistencia</label>
                    <select id="estadoAsistencia" class="form-select">
                        <option value="P">Presente</option>
                        <option value="A">Ausente</option>
                        <option value="J">Justificado</option>
                        <option value="T">Llegada tarde</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="guardarEstudiante">Guardar cambios</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditMaestro" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar asistencia de docente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p><strong>Nombre:</strong> <span id="maestroNombre"></span></p>
                <div class="mb-3">
                    <label class="form-label">Estado de asistencia</label>
                    <select id="estadoMaestro" class="form-select">
                        <option value="P">Presente</option>
                        <option value="A">Ausente</option>
                        <option value="J">Justificado</option>
                        <option value="T">Llegada tarde</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="guardarMaestro">Guardar cambios</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const fecha = '{{ $fecha }}';
        const corteId = 1;
        let currentEstudianteId = null;
        let currentMaestroId = null;
        let currentSeccionId = null;

        function cargarReporte() {
            fetch(`{{ route('asistencia.reporte.data', $fecha) }}?corte_id=${corteId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        renderizarReporte(data);
                    } else {
                        console.error('Error al cargar reporte');
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        function renderizarReporte(data) {
            const tbody = document.getElementById('reporteBody');
            tbody.innerHTML = '';

            let currentGrado = null;
            let resumenGrado = null;
            let totalEsperadoGeneral = { F: 0, V: 0, T: 0 };
            let totalRealGeneral = { F: 0, V: 0, T: 0 };

            data.secciones.forEach(item => {
                const grado = item.grado;
                // Acumular totales generales
                totalEsperadoGeneral.F += item.cef;
                totalEsperadoGeneral.V += item.cem;
                totalRealGeneral.F += item.crf;
                totalRealGeneral.V += item.crm;

                if (currentGrado !== null && grado !== currentGrado) {
                    // Mostrar resumen del grado anterior
                    tbody.appendChild(crearFilaResumen(currentGrado, resumenGrado));
                    resumenGrado = null;
                }

                if (resumenGrado === null) {
                    resumenGrado = {
                        grado: grado,
                        cef: 0, cem: 0,
                        crf: 0, crm: 0,
                        justificadosF: 0, justificadosM: 0,
                        injustificadosF: 0, injustificadosM: 0
                    };
                }

                resumenGrado.cef += item.cef;
                resumenGrado.cem += item.cem;
                resumenGrado.crf += item.crf;
                resumenGrado.crm += item.crm;
                resumenGrado.justificadosF += item.justificadosF;
                resumenGrado.justificadosM += item.justificadosM;
                resumenGrado.injustificadosF += item.injustificadosF;
                resumenGrado.injustificadosM += item.injustificadosM;

                tbody.appendChild(crearFilaSeccion(item));
                currentGrado = grado;
            });

            if (resumenGrado !== null) {
                tbody.appendChild(crearFilaResumen(currentGrado, resumenGrado));
            }

            // Calcular T totales
            totalEsperadoGeneral.T = totalEsperadoGeneral.F + totalEsperadoGeneral.V;
            totalRealGeneral.T = totalRealGeneral.F + totalRealGeneral.V;

            tbody.appendChild(crearFilaAdministrativos(totalEsperadoGeneral, totalRealGeneral));
            tbody.appendChild(crearFilaDocentes(data.docentes));
        }

        function crearFilaSeccion(item) {
            const tr = document.createElement('tr');
            tr.dataset.seccionId = item.seccion_id;
            const esperadoT = item.cef + item.cem;
            const realT = item.crf + item.crm;
            const justificadosT = item.justificadosF + item.justificadosM;
            const injustificadosT = item.injustificadosF + item.injustificadosM;
            const editUrl = `/asistencia/estudiantes/editar/${item.seccion_id}/${fecha}`;
            tr.innerHTML = `
                <td>${item.seccion_nombre}</td>
                <td style="background-color: #e7f0ff;">${item.cef}</td>
                <td style="background-color: #e7f0ff;">${item.cem}</td>
                <td style="background-color: #e7f0ff;">${esperadoT}</td>
                <td style="background-color: #e0f7e8;">${item.crf}</td>
                <td style="background-color: #e0f7e8;">${item.crm}</td>
                <td style="background-color: #e0f7e8;">${realT}</td>
                <td style="background-color: #fff3df;">${item.justificadosF}</td>
                <td style="background-color: #fff3df;">${item.justificadosM}</td>
                <td style="background-color: #fff3df;">${justificadosT}</td>
                <td style="background-color: #ffe6e5;">${item.injustificadosF}</td>
                <td style="background-color: #ffe6e5;">${item.injustificadosM}</td>
                <td style="background-color: #ffe6e5;">${injustificadosT}</td>
                <td style="background-color: #f2f2f2;">
                    <div style="max-height: 120px; overflow-y: auto;">
                        <table class="table table-sm table-bordered mb-0" style="font-size: 0.8rem;">
                            <thead><tr><th>#</th><th>Nombre</th><th>Estado</th><th>Acción</th></tr></thead>
                            <tbody>
                                ${item.ausentes.map(a => `
                                    <tr data-estudiante-id="${a.id}">
                                        <td>${a.numero_lista || '-'}</td>
                                        <td>${a.name}</td>
                                        <td>${a.asistencia === 'A' ? 'Ausente' : (a.asistencia === 'J' ? 'Justificado' : (a.asistencia === 'T' ? 'Llegada tarde' : 'Presente'))}</td>
                                        <td><button class="btn btn-sm btn-primary edit-estudiante" data-id="${a.id}" data-name="${a.name}" data-numero="${a.numero_lista || '-'}" data-estado="${a.asistencia}"><i class="bi bi-pencil"></i></button></td>
                                    </tr>
                                `).join('')}
                            </tbody>
                        </table>
                    </div>
                </td>
                <td style="background-color: #f8f9fa;">
                    <a href="${editUrl}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil-square"></i> Editar</a>
                </td>
            `;
            return tr;
        }

        function crearFilaResumen(grado, resumen) {
            const textoGrado = grado + getOrdinalSuffix(grado);
            const esperadoT = resumen.cef + resumen.cem;
            const realT = resumen.crf + resumen.crm;
            const justificadosT = resumen.justificadosF + resumen.justificadosM;
            const injustificadosT = resumen.injustificadosF + resumen.injustificadosM;
            const tr = document.createElement('tr');
            tr.className = 'table-primary fw-bold';
            tr.innerHTML = `
                <td>A.E (${textoGrado})</td>
                <td style="background-color: #e7f0ff;">${resumen.cef}</td>
                <td style="background-color: #e7f0ff;">${resumen.cem}</td>
                <td style="background-color: #e7f0ff;">${esperadoT}</td>
                <td style="background-color: #e0f7e8;">${resumen.crf}</td>
                <td style="background-color: #e0f7e8;">${resumen.crm}</td>
                <td style="background-color: #e0f7e8;">${realT}</td>
                <td style="background-color: #fff3df;">${resumen.justificadosF}</td>
                <td style="background-color: #fff3df;">${resumen.justificadosM}</td>
                <td style="background-color: #fff3df;">${justificadosT}</td>
                <td style="background-color: #ffe6e5;">${resumen.injustificadosF}</td>
                <td style="background-color: #ffe6e5;">${resumen.injustificadosM}</td>
                <td style="background-color: #ffe6e5;">${injustificadosT}</td>
                <td colspan="2" style="background-color: #f2f2f2;"></td>
            `;
            return tr;
        }

        function crearFilaAdministrativos(esperados, reales) {
            const tr = document.createElement('tr');
            tr.className = 'table-info fw-bold';
            tr.innerHTML = `
                <td>ADMINISTRATIVOS</td>
                <td style="background-color: #e7f0ff;">${esperados.F}</td>
                <td style="background-color: #e7f0ff;">${esperados.V}</td>
                <td style="background-color: #e7f0ff;">${esperados.T}</td>
                <td style="background-color: #e0f7e8;">${reales.F}</td>
                <td style="background-color: #e0f7e8;">${reales.V}</td>
                <td style="background-color: #e0f7e8;">${reales.T}</td>
                <td colspan="6" style="background-color: #f2f2f2;"></td>
                <td style="background-color: #f2f2f2;">-</td>
                <td style="background-color: #f8f9fa;">-</td>
            `;
            return tr;
        }

        function crearFilaDocentes(docentes) {
            const tr = document.createElement('tr');
            tr.className = 'table-warning';
            const esperadosT = docentes.esperadosF + docentes.esperadosV;
            const realesT = docentes.realesF + docentes.realesV;
            const editUrl = `/asistencia/maestros/editar/${fecha}`;
            tr.innerHTML = `
                <td>DOCENTES</td>
                <td style="background-color: #e7f0ff;">${docentes.esperadosF}</td>
                <td style="background-color: #e7f0ff;">${docentes.esperadosV}</td>
                <td style="background-color: #e7f0ff;">${esperadosT}</td>
                <td style="background-color: #e0f7e8;">${docentes.realesF}</td>
                <td style="background-color: #e0f7e8;">${docentes.realesV}</td>
                <td style="background-color: #e0f7e8;">${realesT}</td>
                <td colspan="6" style="background-color: #f2f2f2;"></td>
                <td style="background-color: #f2f2f2;">
                    <div style="max-height: 120px; overflow-y: auto;">
                        <table class="table table-sm table-bordered mb-0" style="font-size: 0.8rem;">
                            <thead><tr><th>Nombre</th><th>Estado</th><th>Acción</th></tr></thead>
                            <tbody>
                                ${docentes.ausentes.map(m => `
                                    <tr data-maestro-id="${m.id}">
                                        <td>${m.name}</td>
                                        <td>${m.asistencia === 'A' ? 'Ausente' : (m.asistencia === 'J' ? 'Justificado' : (m.asistencia === 'T' ? 'Llegada tarde' : 'Presente'))}</td>
                                        <td><button class="btn btn-sm btn-primary edit-maestro" data-id="${m.id}" data-name="${m.name}" data-estado="${m.asistencia}"><i class="bi bi-pencil"></i></button></td>
                                    </tr>
                                `).join('')}
                            </tbody>
                        </table>
                    </div>
                </td>
                <td style="background-color: #f8f9fa;">
                    <a href="${editUrl}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil-square"></i> Editar</a>
                </td>
            `;
            return tr;
        }

        function getOrdinalSuffix(grado) {
            const num = parseInt(grado);
            if (num === 7) return 'MO';
            if (num === 8) return 'VO';
            if (num === 9) return 'NO';
            if (num === 10) return 'MO';
            if (num === 11) return 'VO';
            return `${num}°`;
        }

        // Eventos de edición individual (modales) y guardado
        // (código igual que antes, solo se mantiene la lógica)
        document.getElementById('reporteBody').addEventListener('click', function(e) {
            if (e.target.closest('.edit-estudiante')) {
                const btn = e.target.closest('.edit-estudiante');
                currentEstudianteId = btn.dataset.id;
                document.getElementById('estudianteNombre').innerText = btn.dataset.name;
                document.getElementById('estudianteNumero').innerText = btn.dataset.numero;
                document.getElementById('estadoAsistencia').value = btn.dataset.estado;
                new bootstrap.Modal(document.getElementById('modalEditEstudiante')).show();
            }
            if (e.target.closest('.edit-maestro')) {
                const btn = e.target.closest('.edit-maestro');
                currentMaestroId = btn.dataset.id;
                document.getElementById('maestroNombre').innerText = btn.dataset.name;
                document.getElementById('estadoMaestro').value = btn.dataset.estado;
                new bootstrap.Modal(document.getElementById('modalEditMaestro')).show();
            }
        });

        document.getElementById('guardarEstudiante').addEventListener('click', function() {
            const nuevoEstado = document.getElementById('estadoAsistencia').value;
            fetch(`/asistencia/estudiante/${currentEstudianteId}`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ asis: nuevoEstado, fecha: fecha, id_corte: corteId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    bootstrap.Modal.getInstance(document.getElementById('modalEditEstudiante')).hide();
                    cargarReporte();
                } else alert('Error: ' + data.message);
            })
            .catch(error => console.error(error));
        });

        document.getElementById('guardarMaestro').addEventListener('click', function() {
            const nuevoEstado = document.getElementById('estadoMaestro').value;
            fetch(`/asistencia/maestro/${currentMaestroId}`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ asis: nuevoEstado, fecha: fecha, id_corte: corteId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    bootstrap.Modal.getInstance(document.getElementById('modalEditMaestro')).hide();
                    cargarReporte();
                } else alert('Error: ' + data.message);
            });
        });

        cargarReporte();
    });
</script>
@endsection
