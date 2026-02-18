<?php

use Illuminate\Support\Facades\Route;
// middleware
use App\Http\Middleware\Auth as AuthGuard;
use App\Livewire\Admin\CursoAsignaciones;
use App\Livewire\Admin\CursoMateriales;
use App\Livewire\Admin\CursoPreguntas;
use App\Livewire\Admin\CursoResultados;
use App\Livewire\Admin\Cursos;
use App\Livewire\Admin\Inventarios;
use App\Livewire\Admin\PageEditor;
use App\Livewire\Admin\Productos;
use App\Livewire\Admin\Terceros;
use App\Livewire\Admin\TercerosDetalle;
// end middleware

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Artisan;

use App\Livewire\Pruebas\PruebaVelocidad;
use App\Livewire\PaginaWeb\Pagina;
use App\Livewire\Auth\Login;
use App\Livewire\Categoria\Categorias;
use App\Livewire\Contrapartes\Documentos;
use App\Livewire\Contrapartes\Firma;
use App\Livewire\Contrapartes\Formulario;
use App\Livewire\Contrapartes\Login as ContrapartesLogin;
use App\Livewire\Dashboard\AsistenciaDashboard;
use App\Livewire\Proveedores\Proveedores;
use App\Livewire\Dashboard\Dashboard;
use App\Livewire\Empleado\CursoEvaluacion;
use App\Livewire\Empleado\CursoPlayer;
use App\Livewire\Empleado\MisCursos;
use App\Livewire\Usuarios\Usuarios;
use App\Livewire\Roles\Roles;
use App\Livewire\GestionDocumental\GestionDocumental;
use App\Livewire\Marcador\Marcador;
use App\Livewire\Repositorio\Repositorio;
use App\Livewire\Sedes\Sedes;
use App\Livewire\Programacion\Programacion;
use App\Livewire\Notificaciones\InformacionDeInteres;
// por si se quiere probar cositas
Route::get('/pruebas', PruebaVelocidad::class);

Route::get('/', Pagina::class)->name('home');
Route::get('/login', Login::class)->name('login');
Route::get('/marcacion', Marcador::class)->name('marcacion');

Route::middleware([AuthGuard::class])->group(function () {
    // Route::get('/dashboard', Dashboard::class)->name('dashboard')->middleware('permission:ver dashboard');
    Route::get('/dashboard', Dashboard::class)->name('dashboard')->middleware();
    Route::get('/usuarios', Usuarios::class)->name('usuarios')->middleware('permission:ver usuarios');
    Route::get('/categorias', Categorias::class)->name('categorias')->middleware('permission:ver categorias'); // se queda por si despues se necesita
    Route::get('/proveedores', Proveedores::class)->name('proveedores')->middleware('permission:ver proveedores'); // se queda por si depsues se necesita
    Route::get('/repositorio', Repositorio::class)->name('repositorio'); // en pausa, queda para la ultima fase, la que es con edición de documentos
    Route::get('/gestion-documental', GestionDocumental::class)->name('gestion-documental')->middleware('permission:ver gestión documental'); // en pausa, queda para la ultima fase, la que es con edición de documentos
    Route::get('/admin/pages/{slug}', PageEditor::class)->name('admin.pages.edit')->middleware('permission:ver Sección página web');
    Route::get('/sedes', Sedes::class)->name('sedes')->middleware('permission:ver sedes');
    Route::get('/roles', Roles::class)->name('roles')->middleware('permission:ver roles');
    Route::get('/dashboard-asistencia', AsistenciaDashboard::class)->name('asistencia')->middleware('permission:ver marcaciones');
    Route::get('/programacion', Programacion::class)->name('programacion')->middleware('permission:ver programación');
    Route::get('/admin/cursos', Cursos::class)->name('cursos')->middleware('permission:ver capacitaciones');
    Route::get('/admin/cursos/{curso_id}/materiales', CursoMateriales::class)->name('admin.cursos.materiales')->middleware('permission:ver materiales');
    Route::get('/admin/cursos/{curso_id}/preguntas', CursoPreguntas::class)->name('admin.cursos.preguntas')->middleware('permission:ver preguntas');
    Route::get('/admin/cursos/{curso}/asignaciones', CursoAsignaciones::class)->name('admin.cursos.asignaciones')->middleware('permission:ver asignaciones');
    Route::get('/admin/cursos/{curso}/resultados', CursoResultados::class)->name('admin.cursos.resultados')->middleware('permission:ver resultados');
    Route::get('/mis-cursos', MisCursos::class)->name('mis-cursos')->middleware('permission:ver mis cursos');
    Route::get('/mis-cursos/{curso}', CursoPlayer::class)->name('mis-cursos.player');
    Route::get('/evaluacion/{intento}', CursoEvaluacion::class)->name('mis-cursos.evaluacion');
    Route::get('/informacion-de-interes', InformacionDeInteres::class)->name('informacion_de_interes')->middleware('permission:ver información de interes');
    Route::get('/admin/terceros', Terceros::class)->name('terceros')->middleware('permission:ver listado');
    Route::get('/admin/terceros/{id}/auditar', function ($id) {
        return view('admin.auditar-tercero', compact('id'));
    })->name('admin.terceros.auditar')->middleware('permission:ver formularios');
    // Route::get('/admin/productos', Productos::class)->name('productos')->middleware('permission:ver gestión documental');
    // Route::get('/admin/inventario', Inventarios::class)->name('inventario')->middleware('permission:ver gestión documental');
});

Route::prefix('contrapartes')->group(function () {
    Route::get('/', ContrapartesLogin::class)->name('contrapartes.login');
    Route::middleware(['auth.tercero'])->group(function () {
        Route::get('/formulario', Formulario::class)->name('contrapartes.formulario');
        Route::get('/documentos', Documentos::class)->name('contrapartes.documentos');
        Route::get('/firma', Firma::class)->name('contrapartes.firma');
    });
});
Route::post('logout', function () {
    Auth::logout();
    Session::flush();
    Artisan::call('cache:clear');

    return redirect('/login');
})->name('cerrar-sesion');
