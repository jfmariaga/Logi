<?php

use Illuminate\Support\Facades\Route;
// middleware
use App\Http\Middleware\Auth as AuthGuard;
use App\Livewire\Admin\CursoAsignaciones;
use App\Livewire\Admin\CursoMateriales;
use App\Livewire\Admin\CursoPreguntas;
use App\Livewire\Admin\CursoResultados;
use App\Livewire\Admin\Cursos;
use App\Livewire\Admin\PageEditor;
// end middleware

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Artisan;

use App\Livewire\Pruebas\PruebaVelocidad;
use App\Livewire\PaginaWeb\Pagina;
use App\Livewire\Auth\Login;
use App\Livewire\Categoria\Categorias;
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
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/usuarios', Usuarios::class)->name('usuarios');
    Route::get('/categorias', Categorias::class)->name('categorias')->middleware('permission:ver categorias'); // se queda por si despues se necesita
    Route::get('/proveedores', Proveedores::class)->name('proveedores')->middleware('permission:ver proveedores'); // se queda por si depsues se necesita
    Route::get('/repositorio', Repositorio::class)->name('repositorio'); // en pausa, queda para la ultima fase, la que es con edición de documentos
    Route::get('/gestion-documental', GestionDocumental::class)->name('gestion-documental'); // en pausa, queda para la ultima fase, la que es con edición de documentos
    Route::get('/admin/pages/{slug}', PageEditor::class)->name('admin.pages.edit');
    Route::get('/sedes', Sedes::class)->name('sedes');
    Route::get('/roles', Roles::class)->name('roles');
    Route::get('/dashboard-asistencia', AsistenciaDashboard::class)->name('asistencia');
    Route::get('/programacion', Programacion::class)->name('programacion');
    Route::get('/admin/cursos', Cursos::class)->name('cursos');
    Route::get('/admin/cursos/{curso_id}/materiales',CursoMateriales::class)->name('admin.cursos.materiales');
    Route::get('/admin/cursos/{curso_id}/preguntas', CursoPreguntas::class)->name('admin.cursos.preguntas');
    Route::get('/admin/cursos/{curso}/asignaciones', CursoAsignaciones::class)->name('admin.cursos.asignaciones');
    Route::get('/mis-cursos', MisCursos::class)->name('mis-cursos');
    Route::get('/mis-cursos/{curso}', CursoPlayer::class)->name('mis-cursos.player');
    Route::get('/evaluacion/{intento}', CursoEvaluacion::class)->name('mis-cursos.evaluacion');
    Route::get('/admin/cursos/{curso}/resultados',CursoResultados::class)->name('admin.cursos.resultados');
    Route::get('/informacion-de-interes',InformacionDeInteres::class)->name('informacion_de_interes');
}); 

Route::post('logout', function () {
    Auth::logout();
    Session::flush();
    Artisan::call('cache:clear');

    return redirect('/login');
})->name('cerrar-sesion');
