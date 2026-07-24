<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CategoriaHerramientaController;
use App\Http\Controllers\Api\HerramientaController;
use App\Http\Controllers\Api\MantenimientoController;
use App\Http\Controllers\Api\TrabajoMantenimientoController;
use App\Http\Controllers\Api\AsignacionController;
use App\Http\Controllers\Api\TrabajoHerramientaController;
use App\Http\Controllers\Api\TrabajoRepuestoController;

Route::prefix('categorias-herramientas')->group(function () {
    Route::get('/', [CategoriaHerramientaController::class, 'index']);
    Route::post('/', [CategoriaHerramientaController::class, 'store']);
    Route::get('/{id}', [CategoriaHerramientaController::class, 'show']);
    Route::put('/{id}', [CategoriaHerramientaController::class, 'update']);
    Route::delete('/{id}', [CategoriaHerramientaController::class, 'destroy']);
    Route::patch('/{id}/restore', [CategoriaHerramientaController::class, 'restore']);
    Route::delete('/{id}/force-delete', [CategoriaHerramientaController::class, 'forceDelete']);
    Route::get('/all-with-deleted', [CategoriaHerramientaController::class, 'allWithDeleted']);
    Route::get('/trashed', [CategoriaHerramientaController::class, 'trashed']);
});

Route::prefix('herramientas')->group(function () {
    Route::get('/', [HerramientaController::class, 'index']);
    Route::post('/', [HerramientaController::class, 'store']);
    Route::get('/{id}', [HerramientaController::class, 'show']);
    Route::put('/{id}', [HerramientaController::class, 'update']);
    Route::delete('/{id}', [HerramientaController::class, 'destroy']);
    Route::patch('/{id}/restore', [HerramientaController::class, 'restore']);
    Route::delete('/{id}/force-delete', [HerramientaController::class, 'forceDelete']);
    Route::get('/all-with-deleted', [HerramientaController::class, 'allWithDeleted']);
    Route::get('/trashed', [HerramientaController::class, 'trashed']);
    Route::get('/by-categoria/{idCategoria}', [HerramientaController::class, 'byCategoria']);
    Route::get('/by-estado-fisico/{estadoFisico}', [HerramientaController::class, 'byEstadoFisico']);
});

Route::prefix('mantenimientos')->group(function () {
    Route::get('/', [MantenimientoController::class, 'index']);
    Route::post('/', [MantenimientoController::class, 'store']);
    Route::get('/{id}', [MantenimientoController::class, 'show']);
    Route::put('/{id}', [MantenimientoController::class, 'update']);
    Route::delete('/{id}', [MantenimientoController::class, 'destroy']);
    Route::patch('/{id}/restore', [MantenimientoController::class, 'restore']);
    Route::delete('/{id}/force-delete', [MantenimientoController::class, 'forceDelete']);
    Route::get('/all-with-deleted', [MantenimientoController::class, 'allWithDeleted']);
    Route::get('/trashed', [MantenimientoController::class, 'trashed']);
});

Route::prefix('trabajos-mantenimiento')->group(function () {
    Route::get('/', [TrabajoMantenimientoController::class, 'index']);
    Route::post('/', [TrabajoMantenimientoController::class, 'store']);
    Route::get('/{id}', [TrabajoMantenimientoController::class, 'show']);
    Route::put('/{id}', [TrabajoMantenimientoController::class, 'update']);
    Route::delete('/{id}', [TrabajoMantenimientoController::class, 'destroy']);
    Route::get('/by-estado/{estado}', [TrabajoMantenimientoController::class, 'byEstado']);
    Route::get('/by-mantenimiento/{idMantenimiento}', [TrabajoMantenimientoController::class, 'byMantenimiento']);
});

Route::prefix('asignaciones')->group(function () {
    Route::get('/', [AsignacionController::class, 'index']);
    Route::post('/', [AsignacionController::class, 'store']);
    Route::get('/{id}', [AsignacionController::class, 'show']);
    Route::put('/{id}', [AsignacionController::class, 'update']);
    Route::delete('/{id}', [AsignacionController::class, 'destroy']);
    Route::patch('/{id}/restore', [AsignacionController::class, 'restore']);
    Route::delete('/{id}/force-delete', [AsignacionController::class, 'forceDelete']);
    Route::get('/all-with-deleted', [AsignacionController::class, 'allWithDeleted']);
    Route::get('/trashed', [AsignacionController::class, 'trashed']);
    Route::get('/by-trabajo/{idTrabajo}', [AsignacionController::class, 'byTrabajo']);
    Route::get('/by-personal/{idPersonal}', [AsignacionController::class, 'byPersonal']);
});

Route::prefix('trabajo-herramientas')->group(function () {
    Route::get('/', [TrabajoHerramientaController::class, 'index']);
    Route::post('/', [TrabajoHerramientaController::class, 'store']);
    Route::get('/{id}', [TrabajoHerramientaController::class, 'show']);
    Route::put('/{id}', [TrabajoHerramientaController::class, 'update']);
    Route::delete('/{id}', [TrabajoHerramientaController::class, 'destroy']);
    Route::patch('/{id}/restore', [TrabajoHerramientaController::class, 'restore']);
    Route::delete('/{id}/force-delete', [TrabajoHerramientaController::class, 'forceDelete']);
    Route::get('/all-with-deleted', [TrabajoHerramientaController::class, 'allWithDeleted']);
    Route::get('/trashed', [TrabajoHerramientaController::class, 'trashed']);
    Route::get('/by-trabajo/{idTrabajo}', [TrabajoHerramientaController::class, 'byTrabajo']);
    Route::get('/by-herramienta/{idHerramienta}', [TrabajoHerramientaController::class, 'byHerramienta']);
});

Route::prefix('trabajo-repuestos')->group(function () {
    Route::get('/', [TrabajoRepuestoController::class, 'index']);
    Route::post('/', [TrabajoRepuestoController::class, 'store']);
    Route::get('/{id}', [TrabajoRepuestoController::class, 'show']);
    Route::put('/{id}', [TrabajoRepuestoController::class, 'update']);
    Route::delete('/{id}', [TrabajoRepuestoController::class, 'destroy']);
    Route::patch('/{id}/restore', [TrabajoRepuestoController::class, 'restore']);
    Route::delete('/{id}/force-delete', [TrabajoRepuestoController::class, 'forceDelete']);
    Route::get('/all-with-deleted', [TrabajoRepuestoController::class, 'allWithDeleted']);
    Route::get('/trashed', [TrabajoRepuestoController::class, 'trashed']);
    Route::get('/by-trabajo/{idTrabajo}', [TrabajoRepuestoController::class, 'byTrabajo']);
    Route::get('/by-repuesto/{idRepuesto}', [TrabajoRepuestoController::class, 'byRepuesto']);
});