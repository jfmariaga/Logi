<?php

namespace App\Livewire\Colaboradores;

use App\Models\FormularioConocimientoColaborador;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use App\Notifications\EstadoFormularioColaborador;
use App\Notifications\NuevoFormularioColaborador;
use Livewire\Attributes\On;
use Livewire\Component;

class FormularioConocimiento extends Component
{
    public array $datos = [];
    public string $firma = '';
    public string $firmaBase64 = '';
    public array $departamentos = [];
    public array $municipios = [];
    public bool $autoriza_datos = false;
    public bool $autoriza_financieros = false;
    public bool $autoriza_judiciales = false;
    public ?FormularioConocimientoColaborador $formulario = null;
    public ?int $formularioId = null;
    public string $modo = 'colaborador';
    public string $motivoRechazo = '';
    public bool $edicionHabilitada = false;

    public function mount(?int $id = null): void
    {
        $this->formularioId = $id;
        $this->modo = $id ? 'auditoria' : 'colaborador';
        $this->edicionHabilitada = false;
        $this->formulario = $id
            ? FormularioConocimientoColaborador::with('colaborador')->findOrFail($id)
            : FormularioConocimientoColaborador::where('user_id', Auth::id())->first();
        $this->datos = $this->formulario?->datos ?? [];
        $this->firma = $this->formulario?->firma ?? '';
        $this->autoriza_datos = (bool) ($this->datos['autoriza_datos'] ?? false);
        $this->autoriza_financieros = (bool) ($this->datos['autoriza_financieros'] ?? false);
        $this->autoriza_judiciales = (bool) ($this->datos['autoriza_judiciales'] ?? false);

        $user = $this->formulario?->colaborador ?? Auth::user();
        $this->datos = array_merge([
            'nombres_completos' => trim($user->name . ' ' . $user->last_name),
            'tipo_documento' => 'Cédula de ciudadanía',
            'numero_documento' => $user->document,
            'correo' => $user->email,
            'telefono' => $user->phone,
            'cargo' => $user->cargo?->nombre,
            'departamento' => $user->area?->nombre,
            'operaciones_extranjeras' => [],
        ], $this->datos);

        if (!is_array($this->datos['operaciones_extranjeras'])) {
            $this->datos['operaciones_extranjeras'] = [];
        }

        $this->departamentos = $this->obtenerDepartamentosDesdeApi();
        if (!empty($this->datos['departamento'])) {
            $this->municipios = $this->obtenerMunicipiosDesdeApi($this->datos['departamento']);
        }
    }

    public function cambioDepartamento(string $departamento): void
    {
        $this->datos['municipio'] = '';
        $this->municipios = $this->obtenerMunicipiosDesdeApi($departamento);
    }

    private function obtenerDepartamentosDesdeApi(): array
    {
        try {
            return collect(Http::timeout(8)->get('https://raw.githubusercontent.com/marcovega/colombia-json/master/colombia.min.json')->json())
                ->pluck('departamento')->sort()->values()->all();
        } catch (\Throwable) {
            return [];
        }
    }

    private function obtenerMunicipiosDesdeApi(string $departamento): array
    {
        try {
            $registro = collect(Http::timeout(8)->get('https://raw.githubusercontent.com/marcovega/colombia-json/master/colombia.min.json')->json())
                ->firstWhere('departamento', $departamento);

            return $registro['ciudades'] ?? [];
        } catch (\Throwable) {
            return [];
        }
    }

    public function enviarFormulario(): void
    {
        if ($this->modo === 'auditoria') {
            return;
        }

        if ($this->formulario?->estado === 'enviado') {
            $this->dispatch('toast-error', msg: 'El formulario está pendiente de revisión o ya fue aprobado.');
            return;
        }

        $this->enviar();
    }

    public function enviar(): void
    {
        if ($this->firmaBase64 !== '') {
            $this->guardarFirmaManuscrita();
        }

        foreach (['salario_mensual', 'egresos_mensuales', 'otros_ingresos', 'total_activos', 'total_pasivos', 'patrimonio_neto'] as $campo) {
            if (array_key_exists($campo, $this->datos)) {
                $this->datos[$campo] = preg_replace('/[^0-9]/', '', (string) $this->datos[$campo]);
            }
        }

        $this->validate([
            'datos.fecha_diligenciamiento' => 'required|date',
            'datos.tipo_diligenciamiento' => 'required|in:vinculacion,actualizacion',
            'datos.nombres_completos' => 'required|string|max:255',
            'datos.tipo_documento' => 'required|string|max:80',
            'datos.numero_documento' => 'required|string|max:80',
            'datos.expedicion_documento' => 'required|string|max:255',
            'datos.fecha_nacimiento' => 'required|date',
            'datos.lugar_nacimiento' => 'required|string|max:255',
            'datos.nacionalidad' => 'required|string|max:100',
            'datos.direccion' => 'required|string|max:255',
            'datos.departamento' => 'required|string|max:150',
            'datos.municipio' => 'required|string|max:150',
            'datos.telefono' => 'required|string|max:50',
            'datos.cargo' => 'required|string|max:150',
            'datos.salario_mensual' => 'required|numeric|min:0',
            'datos.egresos_mensuales' => 'required|numeric|min:0',
            'datos.otros_ingresos' => 'required|numeric|min:0',
            'datos.total_activos' => 'required|numeric|min:0',
            'datos.total_pasivos' => 'required|numeric|min:0',
            'datos.patrimonio_neto' => 'required|numeric|min:0',
            'datos.productos_exterior' => 'required|in:si,no',
            'datos.transacciones_extranjera' => 'required|in:si,no',
            'datos.fuente_recursos' => 'required|string|max:1000',
            'datos.pep_cargo_publico' => 'required|in:si,no',
            'datos.familiares_publicos' => 'required|in:si,no',
            'datos.reconocimiento_publico' => 'required|in:si,no',
            'datos.conflicto_interes' => 'required|in:si,no',
            'firma' => 'required|string|max:255',
        ], [
            'required' => 'El campo :attribute es obligatorio.',
            'in' => 'Seleccione una opción válida.',
            'numeric' => 'Ingrese un valor numérico válido.',
        ], [
            'datos.fecha_diligenciamiento' => 'fecha de diligenciamiento',
            'datos.tipo_diligenciamiento' => 'tipo de diligenciamiento',
            'datos.nombres_completos' => 'nombres y apellidos completos',
            'datos.tipo_documento' => 'tipo de documento',
            'datos.numero_documento' => 'número de documento',
            'datos.expedicion_documento' => 'lugar y fecha de expedición del documento',
            'datos.fecha_nacimiento' => 'fecha de nacimiento',
            'datos.lugar_nacimiento' => 'lugar de nacimiento',
            'datos.nacionalidad' => 'nacionalidad',
            'datos.direccion' => 'dirección de residencia',
            'datos.telefono' => 'teléfono celular',
            'datos.cargo' => 'nombre del cargo',
            'datos.salario_mensual' => 'salario mensual',
            'datos.egresos_mensuales' => 'egresos mensuales',
            'datos.otros_ingresos' => 'otros ingresos mensuales',
            'datos.total_activos' => 'total de activos',
            'datos.total_pasivos' => 'total de pasivos',
            'datos.patrimonio_neto' => 'patrimonio neto',
            'datos.productos_exterior' => 'productos en el exterior',
            'datos.transacciones_extranjera' => 'transacciones en moneda extranjera',
            'datos.fuente_recursos' => 'fuentes de los recursos',
            'datos.pep_cargo_publico' => 'cargo público',
            'datos.familiares_publicos' => 'familiares en cargos públicos',
            'datos.reconocimiento_publico' => 'reconocimiento público',
            'datos.conflicto_interes' => 'conflicto de interés',
            'datos.municipio' => 'municipio',
            'datos.departamento' => 'departamento',
            'firma' => 'firma manuscrita',
        ]);

        $this->datos['autoriza_datos'] = $this->autoriza_datos;
        $this->datos['autoriza_financieros'] = $this->autoriza_financieros;
        $this->datos['autoriza_judiciales'] = $this->autoriza_judiciales;

        $this->validate([
            'autoriza_datos' => 'accepted',
            'autoriza_financieros' => 'accepted',
            'autoriza_judiciales' => 'accepted',
        ], [
            '*.accepted' => 'Debe aceptar todas las autorizaciones para enviar el formulario.',
        ], [
            'autoriza_datos' => 'autorización para tratamiento de datos personales',
            'autoriza_financieros' => 'autorización de datos financieros',
            'autoriza_judiciales' => 'autorización de datos judiciales',
        ]);

        $esActualizacion = FormularioConocimientoColaborador::where('user_id', Auth::id())->exists();

        $this->formulario = FormularioConocimientoColaborador::updateOrCreate(
            ['user_id' => Auth::id()],
            [
                'datos' => $this->datos,
                'firma' => $this->firma,
                'estado' => 'enviado',
                'observaciones' => null,
                'enviado_at' => now(),
                'revisado_por' => null,
                'revisado_at' => null,
            ]
        );

        $this->notificarRevisores($esActualizacion);

        $this->dispatch('toast-ok', msg: 'Formulario enviado para revisión.');
        $this->mount();
    }

    private function notificarRevisores(bool $esActualizacion): void
    {
        $revisores = User::role(['SuperAdmin', 'Administrativo'])
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->get();

        if ($revisores->isEmpty()) {
            return;
        }

        try {
            Notification::send($revisores, new NuevoFormularioColaborador($this->formulario, $esActualizacion));
        } catch (\Throwable $exception) {
            Log::error('No se pudo notificar a los revisores sobre el formulario de colaborador', [
                'formulario_id' => $this->formulario->id,
                'error' => $exception->getMessage(),
            ]);
        }
    }

    public function aprobar(): void
    {
        if ($this->modo !== 'auditoria') {
            return;
        }

        if (!$this->formulario || $this->formulario->estado !== 'enviado') {
            return;
        }

        $this->formulario->update([
            'estado' => 'aprobado',
            'revisado_por' => Auth::id(),
            'revisado_at' => now(),
        ]);

        if ($this->formulario->colaborador?->email) {
            try {
                $this->formulario->colaborador->notify(new EstadoFormularioColaborador('aprobado'));
            } catch (\Throwable $exception) {
                Log::error('No se pudo notificar aprobación del formulario de colaborador', [
                    'formulario_id' => $this->formulario->id,
                    'error' => $exception->getMessage(),
                ]);
            }
        }
        $this->formulario->refresh();
        $this->dispatch('toast-ok', msg: 'Formulario aprobado correctamente.');
    }

    public function rechazar(?string $motivo = null): void
    {
        if ($this->modo !== 'auditoria') {
            return;
        }

        if (!$this->formulario || $this->formulario->estado !== 'enviado') {
            return;
        }

        $this->formulario->update([
            'estado' => 'rechazado',
            'observaciones' => ($motivo ?? $this->motivoRechazo) ?: 'Debe corregir la información del formulario.',
            'revisado_por' => Auth::id(),
            'revisado_at' => now(),
        ]);

        if ($this->formulario->colaborador?->email) {
            try {
                $this->formulario->colaborador->notify(new EstadoFormularioColaborador('rechazado', $this->formulario->observaciones));
            } catch (\Throwable $exception) {
                Log::error('No se pudo notificar rechazo del formulario de colaborador', [
                    'formulario_id' => $this->formulario->id,
                    'error' => $exception->getMessage(),
                ]);
            }
        }
        $this->formulario->refresh();
        $this->motivoRechazo = '';
        $this->dispatch('toast-ok', msg: 'Formulario rechazado y enviado al colaborador para corrección.');
    }

    #[On('rechazar-formulario-colaborador')]
    public function rechazarDesdeAuditoria(string $motivo = ''): void
    {
        $this->rechazar($motivo);
    }

    public function habilitarEdicion(): void
    {
        if ($this->modo !== 'colaborador' || $this->formulario?->estado !== 'aprobado') {
            return;
        }

        $this->edicionHabilitada = true;
    }

    public function getSoloLecturaProperty(): bool
    {
        if ($this->modo === 'auditoria' || $this->formulario?->estado === 'enviado') {
            return true;
        }

        if ($this->formulario?->estado === 'aprobado') {
            return !$this->edicionHabilitada;
        }

        return false;
    }

    public function firmaDataUri(): ?string
    {
        if (!$this->firma || !Storage::disk('public')->exists($this->firma)) {
            return null;
        }

        $mime = Storage::disk('public')->mimeType($this->firma) ?: 'image/png';
        return 'data:' . $mime . ';base64,' . base64_encode(Storage::disk('public')->get($this->firma));
    }

    private function guardarFirmaManuscrita(): void
    {
        $contenido = preg_replace('#^data:image/\w+;base64,#i', '', $this->firmaBase64);
        $imagen = base64_decode($contenido, true);

        if ($imagen === false) {
            $this->addError('firma', 'La firma manuscrita no es válida.');
            return;
        }

        if ($this->firma && Storage::disk('public')->exists($this->firma)) {
            Storage::disk('public')->delete($this->firma);
        }

        $ruta = 'formularios_colaboradores/' . Auth::id() . '/firma_' . now()->format('YmdHis') . '.png';
        Storage::disk('public')->put($ruta, $imagen);
        $this->firma = $ruta;
        $this->firmaBase64 = '';
    }

    public function render()
    {
        return view('livewire.colaboradores.formulario-conocimiento')
            ->title('Formulario de conocimiento de colaboradores');
    }
}
