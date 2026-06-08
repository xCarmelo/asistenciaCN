<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\EstudianteController;
use App\Http\Controllers\MaestroController;
use App\Http\Controllers\SeccionController;
use App\Http\Controllers\AsistenciaController;
use App\Http\Controllers\ReporteAusenciasController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\MaestroAsistenciaController;
use Illuminate\Support\Facades\Route;

// Home
Route::get('/', [HomeController::class, 'index'])->name('home'); 

// ======================
// RUTAS ESPECÍFICAS (ANTES DE LAS RESOURCE)
// ======================

// --- Estudiantes (importar, reactivar)
Route::get('estudiantes/importar', [EstudianteController::class, 'importForm'])->name('estudiantes.import.form');
Route::post('estudiantes/import', [EstudianteController::class, 'import'])->name('estudiantes.import');
Route::patch('/estudiantes/{estudiante}/reactivar', [EstudianteController::class, 'reactivar'])->name('estudiantes.reactivar');

// --- Maestros (importar, reactivar)
Route::patch('/maestros/{maestro}/reactivar', [MaestroController::class, 'reactivar'])->name('maestros.reactivar');
Route::post('/maestros/import', [MaestroController::class, 'import'])->name('maestros.import');

// --- Secciones (importar, reactivar)
Route::post('/secciones/import', [SeccionController::class, 'import'])->name('secciones.import');
Route::patch('/secciones/{id}/reactivar', [SeccionController::class, 'reactivar'])->name('secciones.reactivar');

// --- Asistencia (rutas específicas ANTES que resource)
Route::get('/asistencia', [AsistenciaController::class, 'index'])->name('asistencia.index');
Route::get('/asistencia/estudiantes/registrar', [AsistenciaController::class, 'createEstudiantes'])->name('asistencia.estudiantes.create');
Route::post('/asistencia/estudiantes/guardar', [AsistenciaController::class, 'storeEstudiantes'])->name('asistencia.estudiantes.store');
Route::get('/asistencia/maestros/registrar', [AsistenciaController::class, 'createMaestros'])->name('asistencia.maestros.create');
Route::post('/asistencia/maestros/guardar', [AsistenciaController::class, 'storeMaestros'])->name('asistencia.maestros.store');
Route::delete('/asistencia/eliminar/{fecha}', [AsistenciaController::class, 'destroyByDate'])->name('asistencia.destroy');
Route::get('/asistencia/reporte/{fecha}', [AsistenciaController::class, 'reporte'])->name('asistencia.reporte');
Route::get('/asistencia/reporte-data/{fecha}', [AsistenciaController::class, 'getReporteData'])->name('asistencia.reporte.data');
Route::put('/asistencia/estudiante/{id}', [AsistenciaController::class, 'updateEstudianteAsistencia'])->name('asistencia.estudiante.update');
Route::put('/asistencia/maestro/{id}', [AsistenciaController::class, 'updateMaestroAsistencia'])->name('asistencia.maestro.update');
Route::get('/asistencia/estudiantes/editar/{seccion_id}/{fecha}', [AsistenciaController::class, 'editEstudiantesSeccion'])->name('asistencia.estudiantes.editar');
Route::put('/asistencia/estudiantes/actualizar', [AsistenciaController::class, 'updateEstudiantesSeccion'])->name('asistencia.estudiantes.actualizar');
Route::get('/asistencia/maestros/editar/{fecha}', [AsistenciaController::class, 'editMaestros'])->name('asistencia.maestros.editar');
Route::put('/asistencia/maestros/actualizar', [AsistenciaController::class, 'updateMaestros'])->name('asistencia.maestros.actualizar');

// Rutas API (ausentes)
Route::get('/asistencia/api/ausentes-seccion', [AsistenciaController::class, 'apiAusentesSeccion']);
Route::post('/asistencia/api/actualizar-estado', [AsistenciaController::class, 'apiActualizarEstado']);
Route::get('/asistencia/api/ausentes-docentes', [AsistenciaController::class, 'apiAusentesDocentes']);
Route::post('/asistencia/api/actualizar-estado-docente', [AsistenciaController::class, 'apiActualizarEstadoDocente']);

// --- Maestros Asistencias (CRUD)
Route::prefix('maestros/asistencias')->name('maestros.asistencias.')->group(function () {
    Route::get('/', [MaestroAsistenciaController::class, 'index'])->name('index');
    Route::get('/crear', [MaestroAsistenciaController::class, 'create'])->name('create');
    Route::post('/', [MaestroAsistenciaController::class, 'store'])->name('store');
    Route::get('/{id}/editar', [MaestroAsistenciaController::class, 'edit'])->name('edit');
    Route::put('/{id}', [MaestroAsistenciaController::class, 'update'])->name('update');
    Route::delete('/{id}', [MaestroAsistenciaController::class, 'destroy'])->name('destroy');
});

// --- Reporte de Ausencias
Route::get('/reporte-ausencias', [ReporteAusenciasController::class, 'index'])->name('reporte-ausencias');
Route::get('/reporte-ausencias/pdf', [ReporteAusenciasController::class, 'generarPDF'])->name('reporte-ausencias.pdf');
Route::get('/reporte-ausencias/excel', [ReporteAusenciasController::class, 'exportarExcel'])->name('reporte-ausencias.excel');
Route::get('/reporte-ausencias/preview', [ReporteAusenciasController::class, 'vistaPrevia'])->name('reporte-ausencias.preview');
Route::get('/reporte-ausencias/word', [ReporteAusenciasController::class, 'exportarWord'])->name('reporte-ausencias.word');

// --- Respaldos
Route::resource('backups', BackupController::class);
Route::post('/backups/{id}/restore', [BackupController::class, 'restore'])->name('backups.restore');
Route::get('/backups/{id}/download', [BackupController::class, 'download'])->name('backups.download');
Route::get('/backups/import/form', [BackupController::class, 'importForm'])->name('backups.import.form');
Route::post('/backups/import', [BackupController::class, 'import'])->name('backups.import');

// ======================
// RUTAS RESOURCE (AL FINAL)
// ======================
Route::resource('estudiantes', EstudianteController::class)->parameters(['estudiantes' => 'estudiante']);
Route::resource('maestros', MaestroController::class);
Route::resource('secciones', SeccionController::class);

Route::delete('/maestros/asistencias/eliminar/{fecha}', [App\Http\Controllers\MaestroAsistenciaController::class, 'destroyByDate'])->name('maestros.asistencias.destroyByDate');

Route::post('/estudiantes/bulk-update', [EstudianteController::class, 'bulkUpdate'])->name('estudiantes.bulkUpdate');







