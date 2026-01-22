<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;

use App\Models\Sede;
use App\Models\User;
use App\Models\Programacion\Programacion;
use App\Models\Programacion\ProgramacionPorUsuario;

class Calendario extends Component
{

    public $diasEnEspañol = [];
    public $mesesEnEspañol = [];
    public $mesActual;
    public $anoActual;
    public $diasDelMes;
    public $fecha;
    public $user_id;
    public $sedes = [], $usuarios = [];

    // filtros
    public $filtro_tipo = 0, $filtro_sede = 0, $filtro_usuario = 0; 

    public $programaciones = [];

    protected $listeners = ['getProgramaciones'];

    public function mount()
    {

        if (!$this->mesActual && !$this->anoActual) {
            $this->mesActual = date('n');
            $this->anoActual = date('Y');
        }

        $this->user_id = auth()->user()->id;

        // asi solo lo llenamos una vez
        $this->mesesEnEspañol = [
            'January'   => 'Enero',
            'February'  => 'Febrero',
            'March'     => 'Marzo',
            'April'     => 'Abril',
            'May'       => 'Mayo',
            'June'      => 'Junio',
            'July'      => 'Julio',
            'August'    => 'Agosto',
            'September' => 'Septiembre',
            'October'   => 'Octubre',
            'November'  => 'Noviembre',
            'December'  => 'Diciembre',
        ];
         
        $this->diasEnEspañol = [
            'Mon' => 'Lun', 
            'Tue' => 'Mar', 
            'Wed' => 'Mié', 
            'Thu' => 'Jue', 
            'Fri' => 'Vie', 
            'Sat' => 'Sáb', 
            'Sun' => 'Dom'
        ];

        $this->sedes = Sede::where('activo', 1)->orderBy('nombre')->get();
        $this->usuarios = User::where('status', 1)->orderBy('name')->get();

        $this->actualizarDiasDelMes();
    }

    public function getProgramaciones(){

        $fecha_min = date('Y-m-01', strtotime($this->anoActual . '-' . $this->mesActual . '-01'));
        $fecha_max = date('Y-m-31', strtotime($this->anoActual . '-' . $this->mesActual . '-01'));

        $programaciones = Programacion::whereBetween('desde' , [ $fecha_min , $fecha_max ] )->orWhereBetween( 'hasta' , [ $fecha_min , $fecha_max ]  )->with(['personal' => function($query) {
                                                                $query->select('users.id', 'users.name', 'users.last_name', 'users.phone', 'users.picture');
                                                    }, 'sede'])->get()->toArray();

        // agrupamos por fechas
        $this->programaciones = [];
        foreach( $programaciones as $p ){

            // aplicamos filtro de sede
            if( $this->filtro_tipo == 0 && $this->filtro_sede && $this->filtro_sede != $p['sede_id'] ){
                continue;
            }
     
            // aplicamos filtro de usuario
            if( $this->filtro_tipo && $this->filtro_usuario ){
                $usuario_asignado = ProgramacionPorUsuario::where('programacion_id', $p['id'])->where('user_id', $this->filtro_usuario)->first();
                if( !isset( $usuario_asignado->id ) ){ // si esta programación no tiene al usuario se omite
                    continue;
                }
            }

            $desde_tmp = $p['desde'];
            // rellenamos los rangos a los que aplica esta programación

            while( $desde_tmp <= $p['hasta'] ){

                // este hace referencia a los operadores que trabajan este día
                // los sacamos a parte, por si esta en varias sedes ese mismo dia solo mostrarlo una vez
                if( isset( $p['personal'] ) && $p['personal'] ){
                    foreach( $p['personal'] as $operador ){
                        $this->programaciones[ $desde_tmp ][ 'personal' ][ $operador['id'] ] = $operador;
                    }
                }

                $this->programaciones[ $desde_tmp ]['programaciones'][] = $p;
                // aumentamos el dia, para seguir validando
                $desde_tmp = date('Y-m-d',  strtotime( $desde_tmp . ' + 1 day' ) );
            }
        }

        krsort($this->programaciones);

        // dd( $this->programaciones );
    }

    // actualiza los dias del mes
    private function actualizarDiasDelMes()
    {
        // Obtener el número de días en el mes actual
        $diasEnElMes = cal_days_in_month(CAL_GREGORIAN, $this->mesActual, $this->anoActual);

        // Obtener el día de la semana del primer día del mes
        $primerDiaDeLaSemana = date('w', strtotime($this->anoActual . '-' . $this->mesActual . '-01'));

        // Ajustar el día de la semana para que el lunes sea 1, martes sea 2, etc.
        $primerDiaDeLaSemana = ($primerDiaDeLaSemana == 0) ? 7 : $primerDiaDeLaSemana;
        $primerDiaDeLaSemana--; // Restar 1 para que el lunes sea 1, martes sea 2, etc.

        // Inicializar el array de días del mes
        $this->diasDelMes = [];

        // Construir el array de días del mes organizados por semana
        $semana = [];
        $dia = 1;
        for ($i = 0; $i < 6; $i++) { // 6 semanas es el máximo posible en un mes
            for ($j = 0; $j < 7; $j++) {
                if ($i == 0 && $j < $primerDiaDeLaSemana) {
                    $semana[] = null; // Día fuera del mes
                } elseif ($dia <= $diasEnElMes) {
                    $semana[] = $dia;
                    $dia++;
                } else {
                    $semana[] = null; // Día fuera del mes
                }
            }
            $this->diasDelMes[] = $semana;
            $semana = [];
            if ($dia > $diasEnElMes) {
                break;
            }
        }

        $this->getProgramaciones();
    }

    public function mesAnterior()
    {
        $this->mesActual = ($this->mesActual == 1) ? 12 : $this->mesActual - 1;
        $this->anoActual = ($this->mesActual == 12) ? $this->anoActual - 1 : $this->anoActual;
        $this->actualizarDiasDelMes();
    }

    public function mesSiguiente()
    {
        $this->mesActual = ($this->mesActual == 12) ? 1 : $this->mesActual + 1;
        $this->anoActual = ($this->mesActual == 1) ? $this->anoActual + 1 : $this->anoActual;
        $this->actualizarDiasDelMes();
    }


    public function render()
    {
        return view('livewire.dashboard.calendario');
    }
}
