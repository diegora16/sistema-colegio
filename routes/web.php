<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

// Redirigir raíz al login
Route::get('/', fn () => redirect()->route('login'));

// ── Rutas públicas (solo invitados) ─────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

// ── Rutas protegidas (requieren autenticación) ───────────────────────────────
Route::middleware('auth')->group(function () {

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ── Configuración ────────────────────────────────────────────────────────
    Route::prefix('configuracion')->name('configuracion.')->group(function () {
        Route::resource('anios', 'App\Http\Controllers\Configuracion\AnioAcademicoController')
            ->only(['index', 'destroy']);
        Route::post('anios/{anio}/activar', 'App\Http\Controllers\Configuracion\AnioAcademicoController@activar')->name('anios.activar');
        Route::resource('niveles',   'App\Http\Controllers\Configuracion\NivelEducativoController')
            ->parameters(['niveles' => 'nivel']);
        Route::resource('grados',    'App\Http\Controllers\Configuracion\GradoController');
        Route::resource('secciones', 'App\Http\Controllers\Configuracion\SeccionController')
            ->parameters(['secciones' => 'seccion']);
    });

    // ── Alumnos ──────────────────────────────────────────────────────────────
    Route::get( '/alumnos/promover', 'App\Http\Controllers\AlumnoController@promoverForm')->name('alumnos.promover');
    Route::post('/alumnos/promover', 'App\Http\Controllers\AlumnoController@promoverEjecutar')->name('alumnos.promover.ejecutar');
    Route::resource('alumnos',    'App\Http\Controllers\AlumnoController');
    Route::get('/apoderados/buscar', 'App\Http\Controllers\ApoderadoController@buscarPorDni')
        ->name('apoderados.buscar');
    Route::resource('apoderados', 'App\Http\Controllers\ApoderadoController')
        ->only(['index', 'edit', 'update']);

    // ── Pagos ────────────────────────────────────────────────────────────────
    Route::prefix('pagos')->name('pagos.')->group(function () {
        Route::get('/',                              'App\Http\Controllers\PagoController@index')->name('index');
        Route::get('/matricular',                    'App\Http\Controllers\MatriculaController@index')->name('matricular');
        Route::post('/matricular',                   'App\Http\Controllers\MatriculaController@store')->name('matricular.store');
        Route::get('/{inscripcion}',                 'App\Http\Controllers\PagoController@show')->name('show');
        Route::delete('/{inscripcion}',              'App\Http\Controllers\PagoController@eliminarInscripcion')->name('destroy');
        Route::post('/{inscripcion}/pagar',          'App\Http\Controllers\PagoController@pagar')->name('pagar');
        Route::delete('/{inscripcion}/pagos/{pago}', 'App\Http\Controllers\PagoController@eliminarPago')->name('eliminar_pago');
    });

    // ── Reportes ─────────────────────────────────────────────────────────────
    Route::prefix('reportes')->name('reportes.')->group(function () {
        Route::get('/',                 'App\Http\Controllers\ReporteController@index')             ->name('index');
        Route::get('/alumnos',          'App\Http\Controllers\ReporteController@alumnos')           ->name('alumnos');
        Route::get('/alumnos/pdf',      'App\Http\Controllers\ReporteController@alumnosPdf')        ->name('alumnos.pdf');
        Route::get('/morosidad',        'App\Http\Controllers\ReporteController@morosidad')         ->name('morosidad');
        Route::get('/morosidad/pdf',    'App\Http\Controllers\ReporteController@morosidadPdf')      ->name('morosidad.pdf');
        Route::get('/pagos-alumno',     'App\Http\Controllers\ReporteController@pagosPorAlumno')    ->name('pagos_alumno');
        Route::get('/pagos-alumno/pdf', 'App\Http\Controllers\ReporteController@pagosPorAlumnoPdf')->name('pagos_alumno.pdf');
    });

});
