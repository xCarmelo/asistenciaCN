<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\EstudianteController;
use App\Http\Controllers\MaestroController;
use App\Http\Controllers\SeccionController;
use App\Http\Controllers\AsistenciaController;
use Illuminate\Support\Facades\Route;

// Home
Route::get('/', [HomeController::class, 'index'])->name('home');

// Estudiantes
Route::resource('estudiantes', EstudianteController::class)->parameters(['estudiantes' => 'estudiante']);
Route::get('estudiantes/importar', [EstudianteController::class, 'importForm'])->name('estudiantes.import.form');
Route::post('estudiantes/import', [EstudianteController::class, 'import'])->name('estudiantes.import');
Route::patch('/estudiantes/{estudiante}/reactivar', [EstudianteController::class, 'reactivar'])->name('estudiantes.reactivar');

// Maestros
Route::resource('maestros', MaestroController::class);
Route::patch('/maestros/{maestro}/reactivar', [MaestroController::class, 'reactivar'])->name('maestros.reactivar');
Route::post('/maestros/import', [MaestroController::class, 'import'])->name('maestros.import');

// Secciones
Route::resource('secciones', SeccionController::class);
Route::post('/secciones/import', [SeccionController::class, 'import'])->name('secciones.import');
Route::patch('/secciones/{id}/reactivar', [SeccionController::class, 'reactivar'])->name('secciones.reactivar');

// Asistencia principal
Route::get('/asistencia', [AsistenciaController::class, 'index'])->name('asistencia.index');

// Crear asistencias
Route::get('/asistencia/estudiantes/registrar', [AsistenciaController::class, 'createEstudiantes'])->name('asistencia.estudiantes.create');
Route::post('/asistencia/estudiantes/guardar', [AsistenciaController::class, 'storeEstudiantes'])->name('asistencia.estudiantes.store');
Route::get('/asistencia/maestros/registrar', [AsistenciaController::class, 'createMaestros'])->name('asistencia.maestros.create');
Route::post('/asistencia/maestros/guardar', [AsistenciaController::class, 'storeMaestros'])->name('asistencia.maestros.store');

// Eliminar asistencia por fecha
Route::delete('/asistencia/eliminar/{fecha}', [AsistenciaController::class, 'destroyByDate'])->name('asistencia.destroy');

// Reporte y datos AJAX
Route::get('/asistencia/reporte/{fecha}', [AsistenciaController::class, 'reporte'])->name('asistencia.reporte');
Route::get('/asistencia/reporte-data/{fecha}', [AsistenciaController::class, 'getReporteData'])->name('asistencia.reporte.data');
Route::put('/asistencia/estudiante/{id}', [AsistenciaController::class, 'updateEstudianteAsistencia'])->name('asistencia.estudiante.update');
Route::put('/asistencia/maestro/{id}', [AsistenciaController::class, 'updateMaestroAsistencia'])->name('asistencia.maestro.update');

// Edición completa de asistencia por sección
Route::get('/asistencia/estudiantes/editar/{seccion_id}/{fecha}', [AsistenciaController::class, 'editEstudiantesSeccion'])->name('asistencia.estudiantes.editar');
Route::put('/asistencia/estudiantes/actualizar', [AsistenciaController::class, 'updateEstudiantesSeccion'])->name('asistencia.estudiantes.actualizar');

// Edición completa de asistencia de maestros
Route::get('/asistencia/maestros/editar/{fecha}', [AsistenciaController::class, 'editMaestros'])->name('asistencia.maestros.editar');
Route::put('/asistencia/maestros/actualizar', [AsistenciaController::class, 'updateMaestros'])->name('asistencia.maestros.actualizar');

// (Opcional: tus rutas API para ausentes, si las usas)
Route::get('/asistencia/api/ausentes-seccion', [AsistenciaController::class, 'apiAusentesSeccion']);
Route::post('/asistencia/api/actualizar-estado', [AsistenciaController::class, 'apiActualizarEstado']);
Route::get('/asistencia/api/ausentes-docentes', [AsistenciaController::class, 'apiAusentesDocentes']);
Route::post('/asistencia/api/actualizar-estado-docente', [AsistenciaController::class, 'apiActualizarEstadoDocente']);
// Reporte de Ausencias
Route::get('/reporte-ausencias', [App\Http\Controllers\ReporteAusenciasController::class, 'index'])->name('reporte-ausencias');
Route::get('/reporte-ausencias/pdf', [App\Http\Controllers\ReporteAusenciasController::class, 'generarPDF'])->name('reporte-ausencias.pdf');
Route::get('/reporte-ausencias/excel', [App\Http\Controllers\ReporteAusenciasController::class, 'exportarExcel'])->name('reporte-ausencias.excel');
Route::get('/reporte-ausencias/preview', [App\Http\Controllers\ReporteAusenciasController::class, 'vistaPrevia'])->name('reporte-ausencias.preview');

Route::get('/reporte-ausencias/word', [App\Http\Controllers\ReporteAusenciasController::class, 'exportarWord'])->name('reporte-ausencias.word');

Route::resource('backups', App\Http\Controllers\BackupController::class);
Route::post('/backups/{id}/restore', [App\Http\Controllers\BackupController::class, 'restore'])->name('backups.restore');
Route::get('/backups/{id}/download', [App\Http\Controllers\BackupController::class, 'download'])->name('backups.download');
Route::get('/backups/import/form', [App\Http\Controllers\BackupController::class, 'importForm'])->name('backups.import.form');
Route::post('/backups/import', [App\Http\Controllers\BackupController::class, 'import'])->name('backups.import');

