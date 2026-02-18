<?php

namespace App\Livewire\Contrapartes;

use App\Models\ConfigNotificacion;
use Livewire\Component;
use App\Models\Tercero;
use App\Models\TerceroDocumento;
use App\Models\TerceroFirma;
use App\Models\TerceroFormulario;
use App\Models\User;
use App\Notifications\EstadoFormularioCambiado;
use App\Notifications\NuevoFormulario;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\On;
use Livewire\WithFileUploads;

class Formulario extends Component
{
    use WithFileUploads;

    public $tercero;
    public $datos = [];
    public $paises = ['Colombia'];
    public $departamentos = [];
    public $ciudades = [];
    public $contactos = [];
    public $accionistas = [];
    public $actividades = [];
    public $operaciones = [];
    public $operaciones_extranjeras = [];
    public $paisesVerificacion = [];
    public $departamentosVerificacion = [];
    public $ciudadesVerificacion = [];
    public $archivos = [];
    public $nuevoDocumentoNombre;
    public $nuevoDocumentoArchivo;
    public $tipoFirma = '';
    public $firmaImagen;
    public $firmaDibujo;
    public $firmaDigitalActual;
    public $firmaEscaneadaActual;
    public $terceroId;
    public $modo = 'normal';


    // Campos que pertenecen a la tabla terceros
    public $camposTerceros = [
        'identificacion',
        'tipo_identificacion',
        'tipo',
    ];

    public function mount($terceroId = null, $modo = 'normal')
    {
        //-----------------Lo nuevo------------------
        $this->modo = $modo;

        if ($terceroId) {
            $this->tercero = Tercero::findOrFail($terceroId);
            session(['tercero_id' => $this->tercero->id]);
        } else {
            $this->tercero = Tercero::findOrFail(session('tercero_id'));
        }

        //------------------------------------
        $this->tercero = Tercero::find(session('tercero_id'));

        if (!$this->tercero) {
            return redirect()->route('contrapartes.login');
        }

        // Cargar datos propios de la tabla terceros
        $this->datos['identificacion'] = $this->tercero->identificacion;
        $this->datos['tipo_identificacion'] = $this->tercero->tipo_identificacion;
        $this->datos['tipo'] = $this->tercero->tipo;

        // Cargar datos del formulario dinámico
        $registros = TerceroFormulario::where('tercero_id', $this->tercero->id)->get();

        foreach ($registros as $r) {

            // Excluir actividad_economica del tratamiento booleano
            if ($r->campo == 'actividad_economica') {
                $this->datos[$r->campo] = $r->valor;
                continue;
            }

            if ($r->tipo_campo == 'checkbox') {
                $this->datos[$r->campo] = $r->valor == '1' ? true : false;
            } else {
                $this->datos[$r->campo] = $r->valor;
            }
        }

        if (!empty($this->datos['actividad_economica'])) {
            $this->actividades = explode(',', $this->datos['actividad_economica']);
        } else {
            $this->actividades = [];
        }

        if (!empty($this->datos['operaciones_extranjeras'])) {
            $this->operaciones_extranjeras = explode(',', $this->datos['operaciones_extranjeras']);
        } else {
            $this->operaciones_extranjeras = [];
        }

        // Cargar departamentos desde API estable
        $this->departamentos = $this->obtenerDepartamentosDesdeApi();

        // Si ya hay departamento guardado, cargar ciudades
        if (!empty($this->datos['departamento'])) {
            $this->ciudades = $this->obtenerCiudadesDesdeApi($this->datos['departamento']);
        }

        $this->paisesVerificacion = $this->obtenerListaPaises();

        if (($this->datos['pais_verificacion'] ?? '') == 'Colombia') {
            $this->departamentosVerificacion = $this->obtenerDepartamentosDesdeApi();

            if (!empty($this->datos['departamento_verificacion'])) {
                $this->ciudadesVerificacion = $this->obtenerCiudadesDesdeApi($this->datos['departamento_verificacion']);
            }
        }

        $this->cargarContactos();
        $this->cargarAccionistas();
        $this->cargarOperaciones();
        $this->inicializarDocumentos();
        $this->cargarFirmasActuales();
        $this->puedeFirmar();
    }

    // ====================== firma ===============================

    #[On('setFirmaDibujo')]
    public function setFirmaDibujo($firma)
    {
        $this->firmaDibujo = $firma;

        $this->guardarFirmaDibujo();
    }

    public function guardarFirmaDibujo()
    {
        if (!$this->puedeFirmar()) {
            $this->dispatch('toast-error', msg: 'No cumple los requisitos para firmar');
            return;
        }

        if (!$this->firmaDibujo) {
            $this->dispatch('toast-error', msg: 'Debe realizar un dibujo para firmar');
            return;
        }

        $imagen = str_replace('data:image/png;base64,', '', $this->firmaDibujo);
        $imagen = base64_decode($imagen);

        $nombre = "firma_digital_{$this->tercero->identificacion}.png";
        $ruta = "documentos_contrapartes/{$this->tercero->identificacion}/$nombre";

        Storage::disk('public')->put($ruta, $imagen);

        TerceroFirma::create([
            'tercero_id' => $this->tercero->id,
            'tipo' => 'digital',
            'archivo' => $ruta
        ]);

        $this->cargarFirmasActuales();
        $this->dispatch('toast-ok', msg: 'Firma digital guardada correctamente.');
    }

    public function guardarFirmaImagen()
    {
        $this->validate([
            'firmaImagen' => 'required|image|max:2048'
        ]);

        $ruta = $this->firmaImagen
            ->store("documentos_contrapartes/{$this->tercero->identificacion}", 'public');

        TerceroFirma::create([
            'tercero_id' => $this->tercero->id,
            'tipo' => 'escaneada',
            'archivo' => $ruta
        ]);

        $this->firmaImagen = null;
        $this->cargarFirmasActuales();

        $this->dispatch('toast-ok', msg: 'Firma escaneada guardada correctamente.');
    }

    public function eliminarFirma($tipo)
    {
        $firma = TerceroFirma::where('tercero_id', $this->tercero->id)
            ->where('tipo', $tipo)
            ->first();

        if ($firma) {

            Storage::disk('public')->delete($firma->archivo);

            $firma->delete();

            $this->dispatch('toast-ok', msg: 'Firma eliminada correctamente');
        }
        $this->cargarFirmasActuales();
    }

    public function firmaActual($tipo)
    {
        return TerceroFirma::where('tercero_id', $this->tercero->id)
            ->where('tipo', $tipo)
            ->first();
    }

    public function cargarFirmasActuales()
    {
        $this->firmaDigitalActual = $this->firmaActual('digital');
        $this->firmaEscaneadaActual = $this->firmaActual('escaneada');
    }

    public function puedeFirmar()
    {
        // 1. Verificar progreso
        if ($this->tercero->progreso < 100) {
            return false;
        }

        // 2. Verificar documentos obligatorios
        $pendientes = TerceroDocumento::where('tercero_id', $this->tercero->id)
            ->where('obligatorio', true)
            ->where('cargado', false)
            ->count();

        if ($pendientes > 0) {
            return false;
        }

        // 3. Verificar si ya existe firma
        $existeFirma = TerceroFirma::where('tercero_id', $this->tercero->id)->exists();

        if ($existeFirma) {
            return false;
        }

        return true;
    }

    public function yaFirmado()
    {
        return TerceroFirma::where('tercero_id', $this->tercero->id)->exists();
    }

    //=================== Cargar documentos ========================

    public function documentosRequeridos()
    {
        if ($this->tercero->tipo == 'juridica') {
            return [
                'Cámara de Comercio',
                'RUT',
                'Estados Financieros',
                'Certificación Bancaria',
                'Referencias Comerciales',
                'Fotocopia Cédula Representante Legal'
            ];
        }

        return [
            'Cámara de Comercio',
            'RUT',
            'Estados Financieros',
            'Certificación Bancaria',
            'Referencias Comerciales',
            'Fotocopia Cédula Representante Legal',
            'Declaración de renta del último año'
        ];
    }

    public function inicializarDocumentos()
    {
        foreach ($this->documentosRequeridos() as $doc) {

            TerceroDocumento::firstOrCreate(
                [
                    'tercero_id' => $this->tercero->id,
                    'tipo_documento' => $doc
                ],
                [
                    'obligatorio' => true,
                    'cargado' => false
                ]
            );
        }
    }

    public function subirArchivos()
    {
        // $this->validate();

        foreach ($this->documentosRequeridos() as $index => $doc) {

            if (!isset($this->archivos[$index])) continue;

            $archivo = $this->archivos[$index];

            $registro = TerceroDocumento::where('tercero_id', $this->tercero->id)
                ->where('tipo_documento', $doc)
                ->first();

            // Si ya existe archivo previo, eliminarlo
            if ($registro && $registro->archivo && Storage::disk('public')->exists($registro->archivo)) {
                Storage::disk('public')->delete($registro->archivo);
            }

            $ruta = $archivo->store(
                "documentos_contrapartes/{$this->tercero->identificacion}",
                'public'
            );

            TerceroDocumento::updateOrCreate(
                [
                    'tercero_id' => $this->tercero->id,
                    'tipo_documento' => $doc
                ],
                [
                    'archivo' => $ruta,
                    'cargado' => true,
                    'obligatorio' => true
                ]
            );
        }
    }

    public function agregarDocumentoAdicional()
    {
        if (!$this->nuevoDocumentoArchivo || !$this->nuevoDocumentoNombre) return;

        $ruta = $this->nuevoDocumentoArchivo
            ->store("documentos_contrapartes/{$this->tercero->identificacion}", 'public');


        TerceroDocumento::create([
            'tercero_id' => $this->tercero->id,
            'tipo_documento' => $this->nuevoDocumentoNombre,
            'archivo' => $ruta,
            'obligatorio' => false,
            'cargado' => true
        ]);

        $this->nuevoDocumentoNombre = '';
        $this->nuevoDocumentoArchivo = '';
    }

    public function eliminarDocumento($id)
    {
        $doc = TerceroDocumento::find($id);

        if (!$doc) return;

        if ($doc->archivo && Storage::disk('public')->exists($doc->archivo)) {
            Storage::disk('public')->delete($doc->archivo);
        }

        $doc->update([
            'archivo' => null,
            'cargado' => false
        ]);

        session()->flash('mensaje', 'Documento eliminado correctamente');
    }

    public function eliminarDocumentoAdicional($id)
    {
        $documento = TerceroDocumento::find($id);

        if (!$documento) return;

        // Eliminar archivo físico si existe
        if ($documento->archivo && Storage::disk('public')->exists($documento->archivo)) {
            Storage::disk('public')->delete($documento->archivo);
        }

        // Eliminar registro
        $documento->delete();
    }

    public function documentosCompletos()
    {
        return TerceroDocumento::where('tercero_id', $this->tercero->id)
            ->where('obligatorio', true)
            ->where('cargado', false)
            ->count() == 0;
    }

    //=================== Campos obligatorios ========================

    public function getCamposObligatorios()
    {
        $base = $this->tercero->tipo == 'juridica'
            ? ['razon_social']
            : ['nombre_completo'];

        $base = array_merge($base, [
            'email',
            'direccion',
            'pais',
            'departamento',
            'ciudad',
            'telefono',
            'email_contacto',

            'nombre_0',
            'email_0',
            'telefono_0',
            'cargo_0',
            'area_0',

            'rep_primer_nombre',
            'rep_primer_apellido',
            'rep_segundo_apellido',
            'rep_tipo_identificacion',
            'rep_numero_documento',

            'entidad_bancaria',
            'tipo_cuenta',
            'numero_cuenta',

            // 'ingresos_operacionales',
            // 'utilidad_neta',
            // 'depreciaciones',
            // 'activo_no_corriente',
            // 'pasivo_largo_plazo',
            // 'total_obligaciones',

            // 'utilidad_operacional',
            // 'utilidades_acumuladas',
            // 'activo_corriente',
            // 'pasivo_corto_plazo',
            // 'patrimonio',
            // 'capital_social',

            'decl_origen_ingresos',
            'decl_sagrilaft',
            'decl_datos_personales',
            'actividad_economica',

            'contribuyente_renta',
            'regimen_iva',
            'agente_retenedor_iva',
            'responsable_ica',

            'pep_recursos_publicos',
            'pep_poder_publico',
            'pep_reconocimiento',
            'pep_obligaciones_exterior',

            'ciiu_principal',
            'descripcion_principal',
            'ciiu_secundario',
            'descripcion_secundaria',
            'medio_pago',
            'relacionado_pep',

            'pais_verificacion',
            'departamento_verificacion',
            'ciudad_verificacion',

            'pais_alto_riesgo',
            'activos_virtuales',
        ]);

        if ($this->tercero->tipo == 'juridica') {
            $base = array_merge($base, [
                // AHORA agregamos los del suplente
                'sup_primer_nombre',
                'sup_primer_apellido',
                'sup_segundo_apellido',
                'sup_tipo_identificacion',
                'sup_numero_documento'
            ]);
        }

        return $base;
    }

    public function cargarOperaciones()
    {
        $this->operaciones = [];

        $registros = TerceroFormulario::where('tercero_id', $this->tercero->id)
            ->where('seccion', 'operaciones')
            ->get();

        foreach ($registros as $r) {

            if ($r->campo == 'operaciones_extranjeras') {
                $this->operaciones_extranjeras = explode(',', $r->valor);
                continue;
            }

            $partes = explode('_', $r->campo);
            $index = array_pop($partes);
            $campo = implode('_', $partes);

            $this->operaciones[$index][$campo] = $r->valor;
        }

        if (empty($this->operaciones)) {
            $this->operaciones[] = [
                'tipo_producto' => '',
                'numero_producto' => '',
                'entidad' => '',
                'monto' => '',
                'moneda' => '',
                'pais' => '',
                'ciudad' => ''
            ];
        }

        ksort($this->operaciones);
        $this->operaciones = array_values($this->operaciones);
    }

    public function guardarOperaciones()
    {
        $valor = !empty($this->operaciones_extranjeras)
            ? implode(',', $this->operaciones_extranjeras)
            : '';

        TerceroFormulario::updateOrCreate(
            [
                'tercero_id' => $this->tercero->id,
                'campo' => 'operaciones_extranjeras'
            ],
            [
                'valor' => $valor,
                'seccion' => 'operaciones',
                'tipo_campo' => 'checkbox',
                'obligatorio' => false
            ]
        );
    }

    public function agregarOperacion()
    {
        $this->operaciones[] = [
            'tipo_producto' => '',
            'numero_producto' => '',
            'entidad' => '',
            'monto' => '',
            'moneda' => '',
            'pais' => '',
            'ciudad' => ''
        ];
    }

    public function eliminarUltimaOperacion()
    {
        $total = count($this->operaciones);

        if ($total <= 1) return;

        $indice = $total - 1;

        TerceroFormulario::where('tercero_id', $this->tercero->id)
            ->where('seccion', 'operaciones')
            ->where('campo', 'like', "%_{$indice}")
            ->delete();

        unset($this->operaciones[$indice]);

        $this->operaciones = array_values($this->operaciones);
    }

    public function guardarOperacion($index, $campo)
    {
        $valor = $this->operaciones[$index][$campo] ?? '';

        // Limpieza especial para monto
        if ($campo == 'monto') {
            $valor = preg_replace('/[^0-9]/', '', $valor);
        }

        TerceroFormulario::updateOrCreate(
            [
                'tercero_id' => $this->tercero->id,
                'campo' => "{$campo}_{$index}"
            ],
            [
                'valor' => $valor,
                'seccion' => 'operaciones',
                'tipo_campo' => $campo == 'monto' ? 'money' : 'text',
                'obligatorio' => false
            ]
        );
    }

    public function guardarActividades()
    {
        $valor = !empty($this->actividades)
            ? implode(',', $this->actividades)
            : '';

        TerceroFormulario::updateOrCreate(
            [
                'tercero_id' => $this->tercero->id,
                'campo' => 'actividad_economica'
            ],
            [
                'valor' => $valor,
                'seccion' => 'declaraciones',
                'tipo_campo' => 'checkbox',
                'obligatorio' => true
            ]
        );

        $this->calcularProgreso();
    }

    //==================Metodos para contactos=========================

    public function cargarContactos()
    {
        $this->contactos = [];

        $registros = TerceroFormulario::where('tercero_id', $this->tercero->id)
            ->where('seccion', 'contactos')
            ->get();

        foreach ($registros as $r) {
            [$campo, $index] = explode('_', $r->campo);
            $this->contactos[$index][$campo] = $r->valor;
        }

        if (empty($this->contactos)) {
            $this->contactos[] = [
                'nombre' => '',
                'email' => '',
                'telefono' => '',
                'cargo' => '',
                'area' => ''
            ];
        }
    }

    public function agregarContacto()
    {
        $this->contactos[] = [
            'nombre' => '',
            'email' => '',
            'telefono' => '',
            'cargo' => '',
            'area' => ''
        ];
    }

    public function eliminarUltimoContacto()
    {
        $total = count($this->contactos);

        // Mínimo debe quedar 1
        if ($total <= 1) {
            return;
        }

        $indice = $total - 1;

        // Eliminar de base de datos
        TerceroFormulario::where('tercero_id', $this->tercero->id)
            ->where('seccion', 'contactos')
            ->where(function ($q) use ($indice) {
                $q->where('campo', 'like', "%_$indice");
            })
            ->delete();

        // Eliminar del array
        unset($this->contactos[$indice]);

        $this->contactos = array_values($this->contactos);
    }

    public function guardarContacto($index, $campo)
    {
        $valor = $this->contactos[$index][$campo] ?? '';

        TerceroFormulario::updateOrCreate(
            [
                'tercero_id' => $this->tercero->id,
                'campo' => "{$campo}_{$index}"
            ],
            [
                'valor' => $valor,
                'seccion' => 'contactos',
                'tipo_campo' => 'text',
                'obligatorio' => $index == 0
            ]
        );

        $this->calcularProgreso();
    }

    // ============== COMPOSICIÓN ACCIONARIA ==============
    public function cargarAccionistas()
    {
        $this->accionistas = [];

        $registros = TerceroFormulario::where('tercero_id', $this->tercero->id)
            ->where('seccion', 'accionistas')
            ->get();

        foreach ($registros as $r) {

            $campoCompleto = $r->campo;

            // Quitar prefijo acc_
            $campoSinPrefijo = str_replace('acc_', '', $campoCompleto);

            $partes = explode('_', $campoSinPrefijo);

            $index = array_pop($partes);
            $campo = implode('_', $partes);

            $this->accionistas[$index][$campo] = $r->valor;
        }

        if (empty($this->accionistas)) {
            $this->accionistas[] = [
                'tipo_id' => '',
                'documento' => '',
                'nombre' => '',
                'nacionalidad' => '',
                'cotiza' => '',
                'porcentaje' => '',
                'pep' => '',
                'tributacion' => ''
            ];
        }

        ksort($this->accionistas);
        $this->accionistas = array_values($this->accionistas);
    }

    public function agregarAccionista()
    {
        $this->accionistas[] = [
            'tipo_id' => '',
            'documento' => '',
            'nombre' => '',
            'nacionalidad' => '',
            'cotiza' => '',
            'porcentaje' => '',
            'pep' => '',
            'tributacion' => ''
        ];
    }

    public function eliminarUltimoAccionista()
    {
        $total = count($this->accionistas);

        if ($total <= 1) {
            return;
        }

        $indice = $total - 1;

        TerceroFormulario::where('tercero_id', $this->tercero->id)
            ->where('seccion', 'accionistas')
            ->where(function ($q) use ($indice) {
                $q->where('campo', 'like', "%_{$indice}");
            })
            ->delete();

        unset($this->accionistas[$indice]);

        $this->accionistas = array_values($this->accionistas);
    }

    public function guardarAccionista($index, $campo)
    {
        $valor = $this->accionistas[$index][$campo] ?? '';

        TerceroFormulario::updateOrCreate(
            [
                'tercero_id' => $this->tercero->id,
                'campo' => "acc_{$campo}_{$index}"
            ],
            [
                'valor' => $valor,
                'seccion' => 'accionistas',
                'tipo_campo' => 'text',
                'obligatorio' => false
            ]
        );
    }

    // ================= MÉTODOS PARA API DE COLOMBIA =================

    private function obtenerDepartamentosDesdeApi()
    {
        try {
            $response = Http::get('https://raw.githubusercontent.com/marcovega/colombia-json/master/colombia.min.json');

            return collect($response->json())
                ->pluck('departamento')
                ->sort()
                ->values()
                ->toArray();
        } catch (\Exception $e) {
            return [];
        }
    }

    private function obtenerCiudadesDesdeApi($departamento)
    {
        try {
            $response = Http::get('https://raw.githubusercontent.com/marcovega/colombia-json/master/colombia.min.json');

            $data = collect($response->json());

            $registro = $data->firstWhere('departamento', $departamento);

            return $registro ? $registro['ciudades'] : [];
        } catch (\Exception $e) {
            return [];
        }
    }

    // ================= EVENTOS DEPENDIENTES =================

    public function updatedDatosDepartamento($value)
    {
        $this->ciudades = $this->obtenerCiudadesDesdeApi($value);

        $this->datos['ciudad'] = '';

        $this->guardar('departamento', 'select');
    }

    public function updatedDatosPais()
    {
        $this->departamentos = $this->obtenerDepartamentosDesdeApi();

        $this->datos['departamento'] = '';
        $this->datos['ciudad'] = '';

        $this->guardar('pais', 'select');
    }

    public function cambioDepartamento($valor)
    {
        $this->ciudades = $this->obtenerCiudadesDesdeApi($valor);

        $this->datos['ciudad'] = '';

        $this->guardar('departamento', 'select');
    }

    public function guardar($campo, $tipo = 'text')
    {
        if ($this->yaEnviado()) {
            $this->dispatch('toast-error', msg: 'El formulario ya fue enviado y no puede ser modificado.');
            return;
        }

        // === NUEVO: limpieza de campos monetarios sin afectar nada más ===
        $camposMonetarios = [
            'ingresos_operacionales',
            'utilidad_operacional',
            'utilidad_neta',
            'utilidades_acumuladas',
            'depreciaciones',
            'activo_corriente',
            'activo_no_corriente',
            'pasivo_corto_plazo',
            'pasivo_largo_plazo',
            'total_obligaciones',
            'patrimonio',
            'capital_social'
        ];

        if (in_array($campo, $camposMonetarios)) {

            $valorActual = $this->datos[$campo] ?? '';

            $this->datos[$campo] = preg_replace('/[^0-9]/', '', $valorActual);
        }

        // === FIN NUEVO ===


        // Si cambia casa_matriz a NO, limpiar automáticamente "cual_casa_matriz"
        if ($campo == 'casa_matriz' && $this->datos[$campo] == 'No') {

            $this->datos['cual_casa_matriz'] = '';

            TerceroFormulario::updateOrCreate(
                [
                    'tercero_id' => $this->tercero->id,
                    'campo' => 'cual_casa_matriz'
                ],
                [
                    'valor' => '',
                    'seccion' => 'general',
                    'tipo_campo' => 'text',
                    'obligatorio' => false
                ]
            );
        }

        // Si el campo pertenece a la tabla terceros
        if (in_array($campo, $this->camposTerceros)) {

            $this->tercero->update([
                $campo => $this->datos[$campo]
            ]);

            return;
        }

        // Todo lo demás va a tercero_formularios
        $esObligatorio = in_array($campo, $this->getCamposObligatorios());

        if (in_array($campo, $camposMonetarios)) {
            $seccion = 'financiera';
        } elseif (in_array($campo, ['decl_sagrilaft', 'decl_datos_personales', 'decl_origen_ingresos'])) {
            $seccion = 'declaraciones';
        } else {
            $seccion = 'general';
        }

        // === FIN NUEVO ===


        TerceroFormulario::updateOrCreate(
            [
                'tercero_id' => $this->tercero->id,
                'campo' => $campo
            ],
            [
                'valor' => $this->datos[$campo] ?? '',
                'seccion' => $seccion,          // 👈 aquí solo cambiamos esto
                'tipo_campo' => $tipo,
                'obligatorio' => $esObligatorio
            ]
        );

        $this->calcularProgreso();
    }

    // ================= CÁLCULO DE PROGRESO =================

    public function calcularProgreso()
    {
        $obligatorios = $this->getCamposObligatorios();

        $total = count($obligatorios);

        $diligenciados = TerceroFormulario::where('tercero_id', $this->tercero->id)
            ->whereIn('campo', $obligatorios)
            ->whereNotNull('valor')
            ->where('valor', '!=', '')
            ->count();


        $porcentaje = $total > 0 ? ($diligenciados / $total) * 100 : 0;

        $this->tercero->update([
            'progreso' => round($porcentaje)
        ]);
    }

    // =============== Datos de ubicacion =======================

    private function obtenerListaPaises()
    {
        return [
            "Afganistán",
            "Albania",
            "Alemania",
            "Andorra",
            "Angola",
            "Antigua y Barbuda",
            "Arabia Saudita",
            "Argelia",
            "Argentina",
            "Armenia",
            "Australia",
            "Austria",
            "Azerbaiyán",

            "Bahamas",
            "Bangladés",
            "Barbados",
            "Baréin",
            "Bélgica",
            "Belice",
            "Benín",
            "Bielorrusia",
            "Bolivia",
            "Bosnia y Herzegovina",
            "Botsuana",
            "Brasil",
            "Brunéi",
            "Bulgaria",
            "Burkina Faso",

            "Cabo Verde",
            "Camboya",
            "Camerún",
            "Canadá",
            "Catar",
            "Chad",
            "Chile",
            "China",
            "Chipre",
            "Colombia",
            "Comoras",
            "Corea del Norte",
            "Corea del Sur",
            "Costa de Marfil",
            "Costa Rica",
            "Croacia",
            "Cuba",

            "Dinamarca",
            "Dominica",
            "Ecuador",
            "Egipto",
            "El Salvador",
            "Emiratos Árabes Unidos",
            "Eslovaquia",
            "Eslovenia",
            "España",
            "Estados Unidos",
            "Estonia",
            "Etiopía",

            "Filipinas",
            "Finlandia",
            "Fiyi",
            "Francia",

            "Gabón",
            "Gambia",
            "Georgia",
            "Ghana",
            "Granada",
            "Grecia",
            "Guatemala",
            "Guinea",
            "Guinea Ecuatorial",
            "Guyana",

            "Haití",
            "Honduras",
            "Hungría",

            "India",
            "Indonesia",
            "Irak",
            "Irán",
            "Irlanda",
            "Islandia",
            "Israel",
            "Italia",

            "Jamaica",
            "Japón",
            "Jordania",

            "Kazajistán",
            "Kenia",
            "Kirguistán",
            "Kuwait",

            "Laos",
            "Letonia",
            "Líbano",
            "Libia",
            "Lituania",
            "Luxemburgo",

            "Madagascar",
            "Malasia",
            "Maldivas",
            "Malta",
            "Marruecos",
            "Mauricio",
            "México",
            "Moldavia",
            "Mónaco",
            "Mongolia",
            "Montenegro",
            "Mozambique",

            "Namibia",
            "Nepal",
            "Nicaragua",
            "Níger",
            "Nigeria",
            "Noruega",
            "Nueva Zelanda",

            "Omán",

            "Países Bajos",
            "Pakistán",
            "Panamá",
            "Paraguay",
            "Perú",
            "Polonia",
            "Portugal",

            "Reino Unido",
            "República Checa",
            "República Dominicana",
            "Rumania",
            "Rusia",
            "Ruanda",

            "Senegal",
            "Serbia",
            "Singapur",
            "Siria",
            "Sudáfrica",
            "Suecia",
            "Suiza",

            "Tailandia",
            "Tanzania",
            "Túnez",
            "Turquía",

            "Ucrania",
            "Uganda",
            "Uruguay",
            "Uzbekistán",

            "Vaticano",
            "Venezuela",
            "Vietnam",

            "Yemen",

            "Zambia",
            "Zimbabue"
        ];
    }

    public function updatedDatosPaisVerificacion($value)
    {
        if ($value == 'Colombia') {

            $this->departamentosVerificacion = $this->obtenerDepartamentosDesdeApi();
        } else {

            $this->departamentosVerificacion = ['NO APLICA'];
            $this->ciudadesVerificacion = ['NO APLICA'];

            $this->datos['departamento_verificacion'] = 'NO APLICA';
            $this->datos['ciudad_verificacion'] = 'NO APLICA';
        }

        $this->guardar('pais_verificacion', 'select');
    }

    public function cambioDepartamentoVerificacion($valor)
    {
        $this->ciudadesVerificacion = $this->obtenerCiudadesDesdeApi($valor);

        $this->datos['ciudad_verificacion'] = '';

        $this->guardar('departamento_verificacion', 'select');
    }

    // ===================== ENVIAR FORMULARIO =======================

    public function yaEnviado()
    {
        return $this->tercero->enviado;
    }

    // public function enviarFormulario()
    // {
    //     if (!$this->yaFirmado()) {
    //         $this->dispatch('toast-error', msg: 'Debe firmar el formulario antes de enviarlo.');
    //         return;
    //     }

    //     $this->tercero->update([
    //         'enviado' => true,
    //         'estado' => 'enviado'
    //     ]);

    //     // Buscar SuperAdmins válidos
    //     $superAdmins = User::role('SuperAdmin')
    //         ->whereNotNull('email')
    //         ->where('email', '!=', '')
    //         ->get();

    //     if ($superAdmins->isEmpty()) {
    //         Log::warning('No hay SuperAdmins para notificar formulario ID: ' . $this->tercero->id);
    //         return;
    //     }

    //     try {
    //         Notification::send($superAdmins, new NuevoFormulario($this->tercero));
    //     } catch (\Throwable $e) {
    //         Log::error('Error enviando notificación de formulario', [
    //             'tercero_id' => $this->tercero->id,
    //             'error' => $e->getMessage()
    //         ]);
    //     }

    //     $this->dispatch('toast-ok', msg: 'Formulario enviado correctamente. Ya no podrá ser modificado.');
    // }

    public function enviarFormulario()
    {
        if (!$this->yaFirmado()) {
            $this->dispatch('toast-error', msg: 'Debe firmar el formulario antes de enviarlo.');
            return;
        }

        $this->tercero->update([
            'enviado' => true,
            'estado' => 'enviado'
        ]);

        // 🔎 Buscar configuración
        $config = ConfigNotificacion::where('evento', 'nuevo_formulario')->first();

        if (!$config) {
            Log::warning('No existe configuración de notificación para nuevo_formulario');
            return;
        }

        // 👥 Buscar usuarios del rol configurado
        $usuarios = User::role($config->rol)
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->get();

        if ($usuarios->isEmpty()) {
            Log::warning('No hay usuarios para el rol: ' . $config->rol);
            return;
        }

        try {
            Notification::send($usuarios, new NuevoFormulario($this->tercero));
        } catch (\Throwable $e) {
            Log::error('Error enviando notificación', [
                'tercero_id' => $this->tercero->id,
                'rol' => $config->rol,
                'error' => $e->getMessage()
            ]);
        }

        $this->dispatch('toast-ok', msg: 'Formulario enviado correctamente.');
    }

    //=========================== auditoria ==============================
    public function aprobar()
    {
        if ($this->modo !== 'auditoria') return;

        $this->tercero->update([
            'estado' => 'aprobado'
        ]);

        Notification::route('mail', $this->datos['email_contacto'])
            ->notify(new EstadoFormularioCambiado('aprobado'));

        $this->dispatch('toast-ok', msg: 'Formulario aprobado correctamente');
    }

    public function rechazar($motivo)
    {
        if ($this->modo !== 'auditoria') return;

        $this->tercero->update([
            'estado'  => 'rechazado',
            'enviado' => 0
        ]);

        Notification::route('mail', $this->datos['email_contacto'])
            ->notify(new EstadoFormularioCambiado('rechazado', $motivo));

        $this->dispatch('toast-ok', msg: 'Formulario rechazado');
    }

    public function render()
    {
        return view('livewire.contrapartes.formulario')
            ->layout('components.layouts.contrapartes-externo');
    }
}
