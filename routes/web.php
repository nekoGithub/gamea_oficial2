<?php

use App\Http\Controllers\AuditoriaController;
use App\Http\Controllers\BaseDatoController;
use App\Http\Controllers\BusquedaController;
use App\Http\Controllers\CredencialController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentoController;
use App\Http\Controllers\EmailCodeVerificationController;
use App\Http\Controllers\NotificacionController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\ResponsableController;
use App\Http\Controllers\Rolecontroller;
use App\Http\Controllers\ServidorController;
use App\Http\Controllers\SistemaController;
use App\Http\Controllers\SistemaOperativoController;
use App\Http\Controllers\SistemaVersionController;
use App\Http\Controllers\SslController;
use App\Http\Controllers\TecnologiaController;
use App\Http\Controllers\UnidadController;
use App\Http\Controllers\UploadsController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\EnsureEmailVerifiedCode;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    EnsureEmailVerifiedCode::class
])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

route::get('/admin/users', [UserController::class, 'index'])->name('admin.users.index');

route::post('/admin/users', [UserController::class, 'store'])->name('admin.users.store');

Route::get('/admin/users/{user}', [UserController::class, 'show'])->name('admin.users.show');

Route::get('/admin/users/{user}/edit', [UserController::class, 'edit'])->name('admin.users.edit');

Route::put('/admin/users/{user}', [UserController::class, 'update'])->name('admin.users.update');

Route::delete('/admin/users/{user}', [UserController::class, 'destroy'])->name('admin.users.destroy');

Route::post('/admin/users/{user}/restore', [UserController::class, 'restore'])->name('admin.users.restore');

Route::post('/admin/users/check-email', [UserController::class, 'checkEmail'])
    ->name('admin.users.checkEmail');

Route::get('/verify-code', [EmailCodeVerificationController::class, 'showForm'])->name('verify.code.form');
Route::post('/verify-code', [EmailCodeVerificationController::class, 'verify'])->name('verify.code.check');
Route::post('/verify/resend', [EmailCodeVerificationController::class, 'resend'])->name('verify.code.resend');



Route::middleware('auth')->group(function () {
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'show'])->name('profile.show');
    Route::post('/profile/info', [App\Http\Controllers\ProfileController::class, 'updateInfo'])->name('profile.update.info');
    Route::post('/profile/password', [App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.update.password');
});
// Roles

route::get('/admin/roles', [Rolecontroller::class, 'index'])->name('admin.roles.index');

route::post('/admin/roles', [Rolecontroller::class, 'store'])->name('admin.roles.store');

Route::get('/admin/roles/{rol}/edit', [Rolecontroller::class, 'edit'])->name('admin.roles.edit');

Route::put('/admin/roles/{rol}', [Rolecontroller::class, 'update'])->name('admin.roles.update');

// Unidades

route::get('/admin/unidades', [UnidadController::class, 'index'])->name('admin.unidades.index');

route::post('/admin/unidades', [UnidadController::class, 'store'])->name('admin.unidades.store');

Route::get('/admin/unidades/{unidad}/edit', [UnidadController::class, 'edit'])->name('admin.unidades.edit');

Route::put('/admin/unidades/{unidad}', [UnidadController::class, 'update'])->name('admin.unidades.update');

Route::delete('/admin/unidades/{unidad}', [UnidadController::class, 'destroy'])->name('admin.unidades.destroy');

Route::put('/admin/unidades/{id}/restore', [UnidadController::class, 'restore'])->name('admin.unidades.restore');

// Responsables

route::get('/admin/responsables', [ResponsableController::class, 'index'])->name('admin.responsables.index');

route::post('/admin/responsables', [ResponsableController::class, 'store'])->name('admin.responsables.store');

Route::get('/admin/responsables/{responsable}/edit', [ResponsableController::class, 'edit'])->name('admin.responsables.edit');

Route::put('/admin/responsables/{responsable}', [ResponsableController::class, 'update'])->name('admin.responsables.update');

Route::delete('/admin/responsables/{responsable}', [ResponsableController::class, 'destroy'])->name('admin.responsables.destroy');

Route::put('/admin/responsables/{id}/restore', [ResponsableController::class, 'restore'])->name('admin.responsables.restore');

// Ssls

route::get('/admin/ssls', [SslController::class, 'index'])->name('admin.ssls.index');

route::post('/admin/ssls', [SslController::class, 'store'])->name('admin.ssls.store');

Route::get('/admin/ssls/{ssl}/edit', [SslController::class, 'edit'])->name('admin.ssls.edit');

Route::put('/admin/ssls/{ssl}', [SslController::class, 'update'])->name('admin.ssls.update');

Route::delete('/admin/ssls/{ssl}', [SslController::class, 'destroy'])->name('admin.ssls.destroy');

Route::put('/admin/ssls/{id}/restore', [SslController::class, 'restore'])->name('admin.ssls.restore');

// Sistemas Operativos

Route::get('/admin/sistemas-operativos', [SistemaOperativoController::class, 'index'])->name('admin.sistemas-operativos.index');

Route::post('/admin/sistemas-operativos', [SistemaOperativoController::class, 'store'])->name('admin.sistemas-operativos.store');

Route::get('/admin/sistemas-operativos/{sistemasOperativo}/edit', [SistemaOperativoController::class, 'edit'])->name('admin.sistemas-operativos.edit');

Route::put('/admin/sistemas-operativos/{sistemasOperativo}', [SistemaOperativoController::class, 'update'])->name('admin.sistemas-operativos.update');

Route::delete('/admin/sistemas-operativos/{sistemasOperativo}', [SistemaOperativoController::class, 'destroy'])->name('admin.sistemas-operativos.destroy');

Route::put('/admin/sistemas-operativos/{id}/restore', [SistemaOperativoController::class, 'restore'])->name('admin.sistemas-operativos.restore');

// Servidores

Route::get('/admin/servidores', [ServidorController::class, 'index'])->name('admin.servidores.index');

Route::post('/admin/servidores', [ServidorController::class, 'store'])->name('admin.servidores.store');

Route::get('/admin/servidores/{servidore}/edit', [ServidorController::class, 'edit'])->name('admin.servidores.edit');

Route::put('/admin/servidores/{servidore}', [ServidorController::class, 'update'])->name('admin.servidores.update');

Route::delete('/admin/servidores/{servidore}', [ServidorController::class, 'destroy'])->name('admin.servidores.destroy');

Route::put('/admin/servidores/{id}/restore', [ServidorController::class, 'restore'])->name('admin.servidores.restore');

// Bases de datos

Route::get('/admin/bases-datos', [BaseDatoController::class, 'index'])->name('admin.bases-datos.index');

Route::post('/admin/bases-datos', [BaseDatoController::class, 'store'])->name('admin.bases-datos.store');

Route::get('/admin/bases-datos/{basesDato}/edit', [BaseDatoController::class, 'edit'])->name('admin.bases-datos.edit');

Route::put('/admin/bases-datos/{basesDato}', [BaseDatoController::class, 'update'])->name('admin.bases-datos.update');

Route::delete('/admin/bases-datos/{basesDato}', [BaseDatoController::class, 'destroy'])->name('admin.bases-datos.destroy');

Route::put('/admin/bases-datos/{id}/restore', [BaseDatoController::class, 'restore'])->name('admin.bases-datos.restore');

// Tecnologias

Route::get('/admin/tecnologias', [TecnologiaController::class, 'index'])->name('admin.tecnologias.index');

Route::post('/admin/tecnologias', [TecnologiaController::class, 'store'])->name('admin.tecnologias.store');

Route::get('/admin/tecnologias/{tecnologia}/edit', [TecnologiaController::class, 'edit'])->name('admin.tecnologias.edit');

Route::put('/admin/tecnologias/{tecnologia}', [TecnologiaController::class, 'update'])->name('admin.tecnologias.update');

Route::delete('/admin/tecnologias/{tecnologia}', [TecnologiaController::class, 'destroy'])->name('admin.tecnologias.destroy');

Route::put('/admin/tecnologias/{id}/restore', [TecnologiaController::class, 'restore'])->name('admin.tecnologias.restore');

// Scraping asistido
Route::get('/admin/tecnologias/{id}/scrape', [TecnologiaController::class, 'scrapeInfo'])->name('admin.tecnologias.scrape');

// Credenciales

Route::get('/admin/credenciales/buscar-sistema', [CredencialController::class, 'buscarSistema'])
    ->name('admin.credenciales.buscarSistema');

Route::get('/admin/credenciales/', [CredencialController::class, 'index'])->name('admin.credenciales.index');

Route::post('/admin/credenciales/', [CredencialController::class, 'store'])->name('admin.credenciales.store');

Route::get('/admin/credenciales/{credenciale}/edit', [CredencialController::class, 'edit'])->name('admin.credenciales.edit');

Route::put('/admin/credenciales/{credenciale}', [CredencialController::class, 'update'])->name('admin.credenciales.update');

Route::delete('/admin/credenciales/{credenciale}', [CredencialController::class, 'destroy'])->name('admin.credenciales.destroy');

Route::put('/admin/credenciales/{id}/restore', [CredencialController::class, 'restore'])->name('admin.credenciales.restore');

Route::post('/admin/credenciales/{credenciale}/ver-password', [CredencialController::class, 'verPassword'])->name('admin.credenciales.ver-password');

// Ssitemas

Route::get('/admin/sistemas/', [SistemaController::class, 'index'])->name('admin.sistemas.index');

Route::post('/admin/sistemas/', [SistemaController::class, 'store'])->name('admin.sistemas.store');

Route::get('/admin/sistemas/{sistema}/edit', [SistemaController::class, 'edit'])->name('admin.sistemas.edit');

Route::put('/admin/sistemas/{sistema}', [SistemaController::class, 'update'])->name('admin.sistemas.update');

Route::delete('/admin/sistemas/{sistema}', [SistemaController::class, 'destroy'])->name('admin.sistemas.destroy');

Route::put('/admin/sistemas/{id}/restore', [SistemaController::class, 'restore'])->name('admin.sistemas.restore');

// Endpoint para obtener detalles de Unidad
Route::get('/admin/unidades/{id}/detalle', [UnidadController::class, 'detalle'])->name('admin.unidades.detalle');

// Endpoint para obtener detalles de SSL
Route::get('/admin/ssls/{id}/detalle', [SslController::class, 'detalle'])->name('admin.ssls.detalle');

// Flujo staging con background processing
Route::post('/admin/sistemas/{sistema}/versiones/iniciar-upload', [SistemaVersionController::class, 'iniciarUpload'])
    ->name('admin.sistemas.versiones.iniciar-upload');

Route::post('/admin/sistemas/{sistema}/versiones/upload-chunk', [SistemaVersionController::class, 'uploadChunk'])
    ->name('admin.sistemas.versiones.upload-chunk');

Route::post('/admin/sistemas/{sistema}/versiones/completar-upload', [SistemaVersionController::class, 'completarUpload'])
    ->name('admin.sistemas.versiones.completar-upload');

Route::get('/admin/sistemas/{sistema}/versiones/listar-uploads', [SistemaVersionController::class, 'listarUploads'])
    ->name('admin.sistemas.versiones.listar-uploads');

Route::get('/admin/sistemas/{sistema}/versiones/upload/{upload}/status', [SistemaVersionController::class, 'getUploadStatus'])
    ->name('admin.sistemas.versiones.upload.status');

Route::delete('/admin/sistemas/{sistema}/versiones/upload/{upload}/cancelar', [SistemaVersionController::class, 'cancelarUpload'])
    ->name('admin.sistemas.versiones.upload.cancelar');

// sistemas versiones

Route::get('/admin/sistemas/{sistema}/versiones/{version}/descargar/{tipo}', [SistemaVersionController::class, 'descargar'])
    ->name('admin.sistemas.versiones.descargar');

Route::get('/admin/sistemas/{sistema}/versiones/check-duplicate', [SistemaVersionController::class, 'checkDuplicate'])
    ->name('admin.sistemas.versiones.check-duplicate');

Route::get('/admin/sistemas/{sistema}/versiones/crear', [SistemaVersionController::class, 'create'])->name('admin.sistemas.versiones.create');

Route::get('/admin/sistemas/{sistema}/versiones/{version}/editar', [SistemaVersionController::class, 'edit'])
    ->where('version', '[0-9]+')
    ->name('admin.sistemas.versiones.edit');

Route::put('/admin/sistemas/{sistema}/versiones/{version}/restaurar', [SistemaVersionController::class, 'restore'])
    ->where('version', '[0-9]+')
    ->name('admin.sistemas.versiones.restore');

Route::put('/admin/sistemas/{sistema}/versiones/{version}/marcar-actual', [SistemaVersionController::class, 'marcarActual'])
    ->where('version', '[0-9]+')
    ->name('admin.sistemas.versiones.marcar-actual');

Route::get('/admin/sistemas/{sistema}/versiones', [SistemaVersionController::class, 'index'])->name('admin.sistemas.versiones.index');

Route::post('/admin/sistemas/{sistema}/versiones', [SistemaVersionController::class, 'store'])->name('admin.sistemas.versiones.store');

Route::put('/admin/sistemas/{sistema}/versiones/{version}', [SistemaVersionController::class, 'update'])
    ->where('version', '[0-9]+')
    ->name('admin.sistemas.versiones.update');

Route::delete('/admin/sistemas/{sistema}/versiones/{version}', [SistemaVersionController::class, 'destroy'])
    ->where('version', '[0-9]+')
    ->name('admin.sistemas.versiones.destroy');


// ========== MÓDULO DE UPLOADS ==========

// ========== MÓDULO DE UPLOADS ==========
Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {

    Route::prefix('uploads')->name('uploads.')->group(function () {
        // Ver lista de uploads del usuario
        Route::get('/', [UploadsController::class, 'index'])->name('index');

        // Ver detalles de un upload
        Route::get('/{upload}', [UploadsController::class, 'show'])->name('show');

        // ✅ Validar archivo para reanudación
        Route::post('/{upload}/validar-archivo', [UploadsController::class, 'validarArchivo'])->name('validar-archivo');

        // Obtener estado de chunks
        Route::get('/{upload}/chunks-status', [UploadsController::class, 'chunksStatus'])->name('chunks-status');

        // Cancelar upload (POST, no DELETE)
        Route::post('/{upload}/cancelar', [UploadsController::class, 'cancelar'])->name('cancelar');
    });
});


Route::post('/admin/sistemas/{sistema}/versiones/upload-manual-chunk', [SistemaVersionController::class, 'uploadManualChunk'])
    ->name('admin.sistemas.versiones.upload-manual-chunk');

// Documentos

Route::get('/admin/documentos', [DocumentoController::class, 'index'])->name('admin.documentos.index');

Route::post('/admin/documentos', [DocumentoController::class, 'store'])->name('admin.documentos.store');

Route::get('/admin/documentos/{documento}/edit', [DocumentoController::class, 'edit'])->name('admin.documentos.edit');

Route::put('/admin/documentos/{documento}', [DocumentoController::class, 'update'])->name('admin.documentos.update');

Route::delete('/admin/documentos/{documento}', [DocumentoController::class, 'destroy'])->name('admin.documentos.destroy');

Route::put('/admin/documentos/{id}/restore', [DocumentoController::class, 'restore'])->name('admin.documentos.restore');


// Notificaciones module
Route::get('admin/notificaciones/', [NotificacionController::class, 'index'])->name('admin.notificaciones.index');

Route::put('admin/notificaciones/{id}/marcar-enviada', [NotificacionController::class, 'marcarEnviada'])->name('admin.notificaciones.marcar-enviada');

Route::post('admin/notificaciones/{id}/reenviar', [NotificacionController::class, 'reenviar'])->name('admin.notificaciones.reenviar');

Route::delete('admin/notificaciones/{id}', [NotificacionController::class, 'destroy'])->name('admin.notificaciones.destroy');

Route::post('admin/notificaciones/limpiar', [NotificacionController::class, 'limpiar'])->name('admin.notificaciones.limpiar');

Route::get('/admin/notificaciones/{notificacion}/detalle', [NotificacionController::class, 'show'])
    ->name('admin.notificaciones.show');

// Auditorías
Route::get('/auditorias/count', [AuditoriaController::class, 'count'])->name('admin.auditorias.count');

Route::get('admin/auditorias', [AuditoriaController::class, 'index'])->name('admin.auditorias.index');

Route::get('admin/auditorias/datatable', [AuditoriaController::class, 'datatable'])->name('admin.auditorias.datatable');

Route::get('admin/auditorias/exportar-pdf', [AuditoriaController::class, 'exportarPDF'])->name('admin.auditorias.exportar-pdf');

Route::get('admin/auditorias/{auditoria}', [AuditoriaController::class, 'show'])->name('admin.auditorias.show');

Route::delete('admin/auditorias/limpiar', [AuditoriaController::class, 'limpiar'])->name('admin.auditorias.limpiar');


// reportes 
// Dashboard de reportes
Route::get('admin/reportes/', [ReporteController::class, 'index'])->name('admin.reportes.index');

// Vistas de reportes
Route::get('admin/reportes/sistemas', [ReporteController::class, 'sistemas'])->name('admin.reportes.sistemas');

Route::get('admin/reportes/ssl', [ReporteController::class, 'ssl'])->name('admin.reportes.ssl');

Route::get('admin/reportes/servidores', [ReporteController::class, 'servidores'])->name('admin.reportes.servidores');

Route::get('admin/reportes/credenciales', [ReporteController::class, 'credenciales'])->name('admin.reportes.credenciales');

// Exportaciones PDF
Route::get('admin/reportes/sistemas/exportar-pdf', [ReporteController::class, 'exportarSistemasPDF'])->name('admin.reportes.sistemas.exportar-pdf');

Route::get('admin/reportes/ssl/exportar-pdf', [ReporteController::class, 'exportarSslPDF'])->name('admin.reportes.ssl.exportar-pdf');

Route::get('admin/reportes/servidores/exportar-pdf', [ReporteController::class, 'exportarServidoresPDF'])->name('admin.reportes.servidores.exportar-pdf');

Route::get('admin/reportes/credenciales/exportar-pdf', [ReporteController::class, 'exportarCredencialesPDF'])->name('admin.reportes.credenciales.exportar-pdf');

Route::get('admin/reportes/sistemas/exportar-excel', [ReporteController::class, 'exportarSistemasExcel'])->name('admin.reportes.sistemas.exportar-excel');


// Errores 
Route::get('/register', function () {
    return response()->view('admin.errors.404', [], 404);
});

// busqueda de sistemas 
Route::get('/admin/buscar', [BusquedaController::class, 'index'])
    ->name('admin.buscar')
    ->middleware('auth');


Route::get('/test-websocket', function () {
    return view('test-websocket');
})->middleware('auth');

Route::get('/admin/monitoreo/servidores', [App\Http\Controllers\MonitoreoController::class, 'index'])
    ->name('admin.monitoreo.index')
    ->middleware('auth');

Route::get('/admin/monitoreo/servidores/{servidor}/ping', [App\Http\Controllers\MonitoreoController::class, 'pingServidor'])
    ->name('admin.monitoreo.ping')
    ->middleware('auth');

Route::get('/admin/monitoreo/sistemas/{sistema}/ping', [App\Http\Controllers\MonitoreoController::class, 'pingSistema'])
    ->name('admin.monitoreo.ping.sistema')
    ->middleware('auth');
