<?php

namespace App\Livewire\Dashboard;

use App\Models\Jornada;
use App\Models\Marcacion;
use App\Models\Sede;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class AsistenciaDashboard extends Component
{
    public $totalTrabajando = 0;
    public $totalSedes = 0;
    public $fueraHoy = 0;
    public $marcacionesHoy = 0;

    public function mount()
    {
        $this->cargarKPIs();
    }

    /* ============================
        KPIs
    ============================ */
    public function cargarKPIs()
    {
        $hoy = Carbon::today();

        // sedes con operarios trabajando
        $this->totalSedes = Jornada::whereNull('fin')
            ->whereHas('sede', fn($q) => $q->where('activo', true))
            ->distinct('sede_id')
            ->count('sede_id');

        $this->totalTrabajando = Jornada::whereNull('fin')->count();

        $this->fueraHoy = Jornada::whereDate('inicio', $hoy)
            ->where('fuera_sede', true)
            ->count();

        $this->marcacionesHoy = Marcacion::whereDate('fecha_hora', $hoy)->count();
    }

    public function refreshData()
    {
        $this->cargarKPIs();
    }

    /* ============================
        TABLA MARCACIONES (FILTROS)
    ============================ */
    public function getMarcaciones($filtros)
    {
        // dd((int) $filtros['user_id']);
        $this->skipRender();

        $q = Marcacion::with(['user', 'sede']);

        // rango fechas
        if (!empty($filtros['desde']) && !empty($filtros['hasta'])) {
            $q->whereBetween('fecha_hora', [
                Carbon::parse($filtros['desde'])->startOfDay(),
                Carbon::parse($filtros['hasta'])->endOfDay(),
            ]);
        }

        // empleado
        if (isset($filtros['user_id']) && $filtros['user_id'] !== '') {
            $q->where('user_id', (int) $filtros['user_id']);
        }

        // sede
        if (isset($filtros['sede_id']) && $filtros['sede_id'] !== '') {
            $q->where('sede_id', (int) $filtros['sede_id']);
        }

        // estado
        if ($filtros['estado'] !== null && $filtros['estado'] !== '') {
            $q->where('en_sitio', (int) $filtros['estado']);
        }

        return $q->orderByDesc('fecha_hora')
            ->limit(1000)
            ->get()
            ->map(fn($m) => [
                'user' => $m->user->name . ' ' . $m->user->last_name,
                'sede' => $m->sede->nombre ?? 'N/A',
                'tipo' => ucfirst($m->tipo),
                'fecha' => $m->fecha_hora->format('d/m/Y H:i:s'),
                'distancia' => $m->distancia_metros,
                'estado' => $m->en_sitio ? 'En sede' : 'Fuera',
                'foto' => $m->foto
                    ? asset('storage/' . $m->foto)
                    : null,
            ])
            ->toArray();
    }

    /* ============================
    NÓMINA CON FILTROS
============================ */

    public function getNomina($filtros)
    {
        $this->skipRender();

        $q = Jornada::with(['user', 'sede', 'sedeSalida'])
            ->whereNotNull('fin');

        if (!empty($filtros['desde']) && !empty($filtros['hasta'])) {
            $q->whereBetween('inicio', [
                Carbon::parse($filtros['desde'])->startOfDay(),
                Carbon::parse($filtros['hasta'])->endOfDay(),
            ]);
        }

        if (!empty($filtros['user_id'])) {
            $q->where('user_id', (int) $filtros['user_id']);
        }

        if (!empty($filtros['sede_id'])) {
            $q->where(function ($qq) use ($filtros) {
                $qq->where('sede_id', (int) $filtros['sede_id'])
                    ->orWhere('sede_salida_id', (int) $filtros['sede_id']);
            });
        }

        $registros = $q->select(
            'user_id',
            'sede_id',
            'sede_salida_id',
            DB::raw('DATE(inicio) as fecha'),
            DB::raw('SUM(minutos_trabajados) as minutos')
        )
            ->groupBy(
                'user_id',
                'sede_id',
                'sede_salida_id',
                DB::raw('DATE(inicio)')
            )
            ->orderBy('user_id')
            ->orderBy('fecha', 'desc')
            ->get();

        return $registros->map(function ($r) {

            $horas = round($r->minutos / 60, 2);

            return [
                'empleado' => $r->user->name . ' ' . $r->user->last_name,
                'fecha'    => Carbon::parse($r->fecha)->format('d/m/Y'),
                'sede'     => $r->sede->nombre ?? 'N/A',
                'horas'    => $horas,
            ];
        })->toArray();
    }


    public function render()
    {
        /* ===== JORNADAS ACTIVAS ===== */
        $jornadasActivas = Jornada::with(['user', 'sede'])
            ->whereNull('fin')
            ->get();

        $porSede = $jornadasActivas->groupBy('sede_id');

        $alertasLargas = $jornadasActivas->filter(
            fn($j) => $j->inicio->diffInHours(now()) >= 10
        );

        $fueraSede = $jornadasActivas->where('fuera_sede', true);

        /* ===== CAMBIO DE SEDE ===== */
        $cambioSede = Jornada::with(['user', 'sede', 'sedeSalida'])
            ->whereNotNull('fin')
            ->whereColumn('sede_id', '!=', 'sede_salida_id')
            ->orderByDesc('fin')
            ->limit(10)
            ->get();

        /* ===== FILTROS MARCACIONES ===== */
        $empleados = User::orderBy('name')->get(['id', 'name', 'last_name']);
        $sedes = Sede::where('activo', true)->orderBy('nombre')->get(['id', 'nombre']);

        return view('livewire.dashboard.asistencia-dashboard', [
            'porSede' => $porSede,
            'alertasLargas' => $alertasLargas,
            'fueraSede' => $fueraSede,
            'cambioSede' => $cambioSede,
            'empleados' => $empleados,
            'sedes' => $sedes,
        ])->title('Dashboard Asistencias');
    }
}
