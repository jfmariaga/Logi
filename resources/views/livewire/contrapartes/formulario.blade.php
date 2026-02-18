<div>
    <style>
        /* CONTENEDOR */
        .audit-card {
            background: #ffffff;
            border-radius: 16px;
            padding: 35px;
            margin-top: 25px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, .08);
            text-align: center;
            max-width: 720px;
            margin-inline: auto;
        }

        /* TITULOS */
        .audit-title {
            font-size: 24px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 10px;
        }

        .audit-subtitle {
            color: #6b7280;
            font-size: 15px;
            margin-bottom: 25px;
        }

        /* ACCIONES */
        .audit-actions {
            display: flex;
            justify-content: center;
            gap: 20px;
            flex-wrap: wrap;
        }

        /* BOTONES BASE */
        .audit-actions button {
            border: none;
            border-radius: 14px;
            padding: 16px 26px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all .25s ease;
            box-shadow: 0 6px 18px rgba(0, 0, 0, .08);
        }

        /* APROBAR */
        .btn-approve {
            background: linear-gradient(135deg, #22c55e, #16a34a);
            color: white;
        }

        .btn-approve:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 25px rgba(34, 197, 94, .35);
        }

        /* RECHAZAR */
        .btn-reject {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
        }

        .btn-reject:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 25px rgba(239, 68, 68, .35);
        }

        /* ICONOS */
        .audit-actions i {
            font-size: 20px;
        }

        /* ESTADO */
        .status-box {
            background: #f1f5f9;
            padding: 20px;
            border-radius: 14px;
            color: #374151;
        }

        .status-box h4 {
            margin-bottom: 6px;
            font-weight: 700;
        }

        /* VOLVER */
        .back-container {
            margin-top: 30px;
        }

        .btn-back {
            text-decoration: none;
            padding: 12px 22px;
            border-radius: 12px;
            background: #f3f4f6;
            color: #374151;
            font-weight: 600;
            transition: .25s;
        }

        .btn-back:hover {
            background: #e5e7eb;
        }
    </style>
    <fieldset @if ($this->yaEnviado() || $modo == 'auditoria') disabled @endif>
        <x-layouts.contrapartes-externo>
            <div class="form-container formulario-contrapartes">
                <div class="form-card">
                    <!-- HEADER -->
                    <div class="text-center mb-3">
                        <img src="{{ asset('img-logisticarga/logo.png') }}" style="max-width:260px">
                        <h5 class="mt-3">FORMULARIO DE REGISTRO DE CONTRAPARTES</h5>
                        <p class="info-text">
                            Complete la información solicitada para continuar con el proceso.
                        </p>
                    </div>
                    <!-- PROGRESO -->
                    <div class="progress-container mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="fw-bold">Progreso del formulario</span>
                            <span class="fw-bold">{{ $tercero->progreso }}%</span>
                        </div>

                        <div class="progress" style="height: 12px;">
                            <div class="progress-bar progress-bar-logi" style="width: {{ $tercero->progreso }}%">
                            </div>
                        </div>
                    </div>
                    <div class="text-end mt-2 info-text">
                        Los campos se guardan automáticamente al ser modificados
                    </div>
                    <br>

                    @if ($this->yaEnviado())
                        <div class="alert alert-primary text-center">
                            🔒 Este formulario fue enviado y se encuentra bloqueado para edición.
                        </div>
                    @endif

                    <!-- SECCIÓN GENERAL -->
                    <table class="tabla-corporativa">

                        <tr class="titulo-seccion">
                            <td colspan="4">
                                INFORMACIÓN GENERAL PROVEEDOR - PRODUCTOR
                            </td>
                        </tr>

                        <tr>
                            @if ($tercero->tipo == 'juridica')
                                <td class="label-td">Razón Social</td>
                                <td colspan="3">
                                    <input class="input-corporativo" wire:model.defer="datos.razon_social"
                                        wire:change="guardar('razon_social','text')">
                                </td>
                            @else
                                <td class="label-td">Nombre Completo</td>
                                <td colspan="3">
                                    <input class="input-corporativo" wire:model.defer="datos.nombre_completo"
                                        wire:change="guardar('nombre_completo','text')">
                                </td>
                            @endif
                        </tr>

                        <tr>
                            <td class="label-td">¿Tiene Casa Matriz?</td>
                            <td>
                                <label class="me-2">
                                    <input type="radio" name="casa_matriz" wire:model.defer="datos.casa_matriz"
                                        wire:change="guardar('casa_matriz','radio')" value="Si"> Sí
                                </label>

                                <label>
                                    <input type="radio" name="casa_matriz" wire:model.defer="datos.casa_matriz"
                                        wire:change="guardar('casa_matriz','radio')" value="No"> No
                                </label>
                            </td>

                            <td class="label-td">Indique cuál</td>
                            <td>
                                <input class="input-corporativo" wire:model.defer="datos.cual_casa_matriz"
                                    wire:change="guardar('cual_casa_matriz','text')"
                                    @if (($datos['casa_matriz'] ?? '') !== 'Si') disabled @endif>
                            </td>
                        </tr>

                        <tr>
                            <td class="label-td">Tipo de identificación</td>
                            <td>
                                <select class="input-corporativo" wire:model.defer="datos.tipo_identificacion"
                                    wire:change="guardar('tipo_identificacion','select')">
                                    <option value="">Seleccionar</option>
                                    <option value="CC">CC</option>
                                    <option value="NIT">NIT</option>
                                    <option value="CE">CE</option>
                                    <option value="TI">TI</option>
                                    <option value="PASAPORTE">PASAPORTE</option>
                                    <option value="NUIP">NUIP</option>
                                    <option value="PPT">PPT</option>
                                </select>
                            </td>

                            <td class="label-td">Número de identificación</td>
                            <td>
                                <input class="input-corporativo" wire:model.defer="datos.identificacion"
                                    wire:change="guardar('identificacion','text')">
                            </td>
                        </tr>

                        <tr>
                            <td class="label-td">Email Corporativo</td>
                            <td>
                                <input class="input-corporativo" wire:model.defer="datos.email"
                                    wire:change="guardar('email','email')">
                            </td>

                            <td class="label-td">Dirección</td>
                            <td>
                                <input class="input-corporativo" wire:model.defer="datos.direccion"
                                    wire:change="guardar('direccion','text')">
                            </td>
                        </tr>

                        <tr>
                            <td class="label-td">Fecha de Matrícula</td>
                            <td>
                                <input type="date" class="input-corporativo" wire:model.defer="datos.fecha_matricula"
                                    wire:change="guardar('fecha_matricula','date')">
                            </td>

                            <td class="label-td">Tipo de Sociedad</td>
                            <td>
                                <select class="input-corporativo" wire:model.defer="datos.tipo_sociedad"
                                    wire:change="guardar('tipo_sociedad','select')">

                                    <option value="">Seleccionar</option>
                                    <option value="EU">EU - Empresa Unipersonal</option>
                                    <option value="ONG">ONG - Organización No Gubernamental</option>
                                    <option value="SA">SA - Sociedad Anónima</option>
                                    <option value="SC">SC - Sociedad Colectiva</option>
                                    <option value="SCA">SCA - Sociedad Comandita por Acciones</option>
                                    <option value="SEN C">S en C - Sociedad Comandita Simple</option>
                                    <option value="LTDA">Ltda - Sociedad de Responsabilidad Limitada</option>
                                    <option value="SAS">SAS - Sociedad por Acciones Simplificada</option>
                                    <option value="SCOOP">S. Coop - Sociedad Cooperativa</option>
                                    <option value="SAM">SAM - Sociedades de economía mixta</option>
                                    <option value="NA">NA - NO APLICA</option>
                                </select>
                            </td>
                        </tr>
                    </table>
                    <br>
                    <!-- ==================== DATOS DE UBICACIÓN ==================== -->
                    <table class="tabla-corporativa mt-3">

                        <tr class="titulo-seccion">
                            <td colspan="4">
                                DATOS DE UBICACIÓN
                            </td>
                        </tr>

                        <tr>
                            <td class="label-td">País</td>
                            <td>
                                <select class="input-corporativo" wire:model="datos.pais"
                                    wire:change="guardar('pais','select')">
                                    <option value="">Seleccionar</option>
                                    @foreach ($paises as $pais)
                                        <option value="{{ $pais }}">{{ $pais }}</option>
                                    @endforeach
                                </select>
                            </td>

                            <td class="label-td">Departamento</td>
                            <td>
                                <select class="input-corporativo" wire:model="datos.departamento"
                                    wire:change="cambioDepartamento($event.target.value)">
                                    <option value="">Seleccionar</option>
                                    @foreach ($departamentos as $dep)
                                        <option value="{{ $dep }}">{{ $dep }}</option>
                                    @endforeach
                                </select>
                            </td>
                        </tr>

                        <tr>

                            <td class="label-td">Ciudad</td>
                            <td>
                                <div wire:loading wire:target="cambioDepartamento" class="text-red small mb-1">
                                    Cargando ciudades...
                                </div>
                                <select class="input-corporativo" wire:model.defer="datos.ciudad"
                                    wire:change="guardar('ciudad','select')">
                                    <option value="">Seleccionar</option>
                                    @foreach ($ciudades as $ciu)
                                        <option value="{{ $ciu }}">{{ $ciu }}</option>
                                    @endforeach
                                </select>
                            </td>

                            <td class="label-td">Dirección Corporativa</td>
                            <td>
                                <input class="input-corporativo" wire:model.defer="datos.direccion_corporativa"
                                    wire:change="guardar('direccion_corporativa','text')">
                            </td>
                        </tr>

                        <tr>
                            <td class="label-td">Teléfono Corporativo</td>
                            <td>
                                <input class="input-corporativo" wire:model.defer="datos.telefono"
                                    wire:change="guardar('telefono','text')">
                            </td>

                            <td class="label-td">Email de Contacto</td>
                            <td>
                                <input class="input-corporativo" wire:model.defer="datos.email_contacto"
                                    wire:change="guardar('email_contacto','email')">
                            </td>
                        </tr>

                    </table>
                    <br>
                    <!-- ==================== INFORMACIÓN DE CONTACTOS ==================== -->
                    <table class="tabla-corporativa mt-3">

                        <tr class="titulo-seccion">
                            <td colspan="5">
                                INFORMACIÓN DE CONTACTOS
                            </td>
                        </tr>

                        <tr class="subtitulo-tabla">
                            <th>Nombre Completo</th>
                            <th>E-Mail</th>
                            <th>Teléfono</th>
                            <th>Cargo</th>
                            <th>Área</th>
                        </tr>

                        @foreach ($contactos as $index => $contacto)
                            <tr>
                                <td>
                                    <input class="input-corporativo"
                                        wire:model.defer="contactos.{{ $index }}.nombre"
                                        wire:change="guardarContacto({{ $index }}, 'nombre')">
                                </td>

                                <td>
                                    <input class="input-corporativo"
                                        wire:model.defer="contactos.{{ $index }}.email"
                                        wire:change="guardarContacto({{ $index }}, 'email')">
                                </td>

                                <td>
                                    <input class="input-corporativo"
                                        wire:model.defer="contactos.{{ $index }}.telefono"
                                        wire:change="guardarContacto({{ $index }}, 'telefono')">
                                </td>

                                <td>
                                    <input class="input-corporativo"
                                        wire:model.defer="contactos.{{ $index }}.cargo"
                                        wire:change="guardarContacto({{ $index }}, 'cargo')">
                                </td>

                                <td>
                                    <input class="input-corporativo"
                                        wire:model.defer="contactos.{{ $index }}.area"
                                        wire:change="guardarContacto({{ $index }}, 'area')">
                                </td>
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="5">
                                <div class="contenedor-botones-contacto">
                                    <button class="btn btn-contacto-agregar" wire:click="agregarContacto">
                                        + Agregar Contacto
                                    </button>
                                    <button class="btn btn-contacto-eliminar" wire:click="eliminarUltimoContacto"
                                        @if (count($contactos) <= 1) disabled @endif>
                                        - Eliminar Último
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </table>
                    <br>
                    <!-- ==================== REPRESENTANTE LEGAL ==================== -->
                    <table class="tabla-corporativa mt-3">

                        <tr class="titulo-seccion">
                            <td colspan="4">
                                REPRESENTANTE LEGAL
                            </td>
                        </tr>

                        <tr>
                            <td class="label-td">Primer Nombre</td>
                            <td>
                                <input class="input-corporativo" wire:model.defer="datos.rep_primer_nombre"
                                    wire:change="guardar('rep_primer_nombre','text')">
                            </td>

                            <td class="label-td">Segundo Nombre</td>
                            <td>
                                <input class="input-corporativo" wire:model.defer="datos.rep_segundo_nombre"
                                    wire:change="guardar('rep_segundo_nombre','text')">
                            </td>
                        </tr>

                        <tr>
                            <td class="label-td">Primer Apellido</td>
                            <td>
                                <input class="input-corporativo" wire:model.defer="datos.rep_primer_apellido"
                                    wire:change="guardar('rep_primer_apellido','text')">
                            </td>

                            <td class="label-td">Segundo Apellido</td>
                            <td>
                                <input class="input-corporativo" wire:model.defer="datos.rep_segundo_apellido"
                                    wire:change="guardar('rep_segundo_apellido','text')">
                            </td>
                        </tr>

                        <tr>
                            <td class="label-td">Tipo de Identificación</td>
                            <td>
                                <select class="input-corporativo" wire:model.defer="datos.rep_tipo_identificacion"
                                    wire:change="guardar('rep_tipo_identificacion','select')">

                                    <option value="">Seleccionar</option>
                                    <option value="CC">CC</option>
                                    <option value="CE">CE</option>
                                    <option value="TI">TI</option>
                                    <option value="Pasaporte">Pasaporte</option>
                                    <option value="NIT">NIT</option>
                                    <option value="PPT">PPT</option>

                                </select>
                            </td>

                            <td class="label-td">Número de Documento</td>
                            <td>
                                <input class="input-corporativo" wire:model.defer="datos.rep_numero_documento"
                                    wire:change="guardar('rep_numero_documento','text')">
                            </td>
                        </tr>
                    </table>
                    <!-- ==================== REPRESENTANTE LEGAL SUPLENTE ==================== -->
                    <table class="tabla-corporativa mt-3">
                        <tr class="titulo-seccion">
                            <td colspan="4">
                                REPRESENTANTE LEGAL SUPLENTE
                            </td>
                        </tr>
                        <tr>
                            <td class="label-td">Primer Nombre</td>
                            <td>
                                <input class="input-corporativo" wire:model.defer="datos.sup_primer_nombre"
                                    wire:change="guardar('sup_primer_nombre','text')">
                            </td>

                            <td class="label-td">Segundo Nombre</td>
                            <td>
                                <input class="input-corporativo" wire:model.defer="datos.sup_segundo_nombre"
                                    wire:change="guardar('sup_segundo_nombre','text')">
                            </td>
                        </tr>
                        <tr>
                            <td class="label-td">Primer Apellido</td>
                            <td>
                                <input class="input-corporativo" wire:model.defer="datos.sup_primer_apellido"
                                    wire:change="guardar('sup_primer_apellido','text')">
                            </td>

                            <td class="label-td">Segundo Apellido</td>
                            <td>
                                <input class="input-corporativo" wire:model.defer="datos.sup_segundo_apellido"
                                    wire:change="guardar('sup_segundo_apellido','text')">
                            </td>
                        </tr>

                        <tr>
                            <td class="label-td">Tipo de Identificación</td>
                            <td>
                                <select class="input-corporativo" wire:model.defer="datos.sup_tipo_identificacion"
                                    wire:change="guardar('sup_tipo_identificacion','select')">

                                    <option value="">Seleccionar</option>
                                    <option value="CC">CC</option>
                                    <option value="CE">CE</option>
                                    <option value="TI">TI</option>
                                    <option value="Pasaporte">Pasaporte</option>
                                    <option value="NIT">NIT</option>
                                    <option value="PPT">PPT</option>

                                </select>
                            </td>
                            <td class="label-td">Número de Documento</td>
                            <td>
                                <input class="input-corporativo" wire:model.defer="datos.sup_numero_documento"
                                    wire:change="guardar('sup_numero_documento','text')">
                            </td>
                        </tr>
                    </table>
                    <br>
                    <!-- ==================== COMPOSICIÓN ACCIONARIA ==================== -->
                    <table class="tabla-corporativa mt-3">
                        <tr class="titulo-seccion">
                            <td colspan="8">
                                COMPOSICIÓN ACCIONARIA
                            </td>
                        </tr>
                        <tr class="subtitulo-tabla">
                            <th>Tipo ID</th>
                            <th>Documento</th>
                            <th>Nombre</th>
                            <th>Nacionalidad</th>
                            <th>Cotiza en Bolsa</th>
                            <th>Porcentaje %</th>
                            <th>Es PEP</th>
                            <th>Tributación Extranjera</th>
                        </tr>
                        @foreach ($accionistas as $index => $acc)
                            <tr>
                                <td>
                                    <select class="input-corporativo"
                                        wire:model.defer="accionistas.{{ $index }}.tipo_id"
                                        wire:change="guardarAccionista({{ $index }}, 'tipo_id')">

                                        <option value="">Seleccionar</option>
                                        <option value="CC">CC</option>
                                        <option value="CE">CE</option>
                                        <option value="TI">TI</option>
                                        <option value="Pasaporte">Pasaporte</option>
                                        <option value="NIT">NIT</option>
                                        <option value="PPT">PPT</option>
                                    </select>
                                </td>

                                <td>
                                    <input class="input-corporativo"
                                        wire:model.defer="accionistas.{{ $index }}.documento"
                                        wire:change="guardarAccionista({{ $index }}, 'documento')">
                                </td>

                                <td>
                                    <input class="input-corporativo"
                                        wire:model.defer="accionistas.{{ $index }}.nombre"
                                        wire:change="guardarAccionista({{ $index }}, 'nombre')">
                                </td>

                                <td>
                                    <input class="input-corporativo"
                                        wire:model.defer="accionistas.{{ $index }}.nacionalidad"
                                        wire:change="guardarAccionista({{ $index }}, 'nacionalidad')">
                                </td>

                                <td>
                                    <select class="input-corporativo"
                                        wire:model.defer="accionistas.{{ $index }}.cotiza"
                                        wire:change="guardarAccionista({{ $index }}, 'cotiza')">

                                        <option value="">Seleccionar</option>
                                        <option value="Si">Sí</option>
                                        <option value="No">No</option>
                                    </select>
                                </td>

                                <td>
                                    <input class="input-corporativo" type="number" min="0" max="100"
                                        step="0.01" wire:model.defer="accionistas.{{ $index }}.porcentaje"
                                        wire:change="guardarAccionista({{ $index }}, 'porcentaje')">
                                </td>

                                <td>
                                    <select class="input-corporativo"
                                        wire:model.defer="accionistas.{{ $index }}.pep"
                                        wire:change="guardarAccionista({{ $index }}, 'pep')">

                                        <option value="">Seleccionar</option>
                                        <option value="Si">Sí</option>
                                        <option value="No">No</option>
                                    </select>
                                </td>

                                <td>
                                    <select class="input-corporativo"
                                        wire:model.defer="accionistas.{{ $index }}.tributacion"
                                        wire:change="guardarAccionista({{ $index }}, 'tributacion')">

                                        <option value="">Seleccionar</option>
                                        <option value="Si">Sí</option>
                                        <option value="No">No</option>
                                    </select>
                                </td>
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="8">
                                <div class="contenedor-botones-contacto">
                                    <button class="btn btn-contacto-agregar" wire:click="agregarAccionista">
                                        + Agregar
                                    </button>

                                    <button class="btn btn-contacto-eliminar" wire:click="eliminarUltimoAccionista"
                                        @if (count($accionistas) <= 1) disabled @endif>
                                        - Eliminar
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </table>
                    <br>
                    <!-- ==================== INFORMACIÓN BANCARIA ==================== -->
                    <table class="tabla-corporativa mt-3">

                        <tr class="titulo-seccion">
                            <td colspan="4">
                                INFORMACIÓN BANCARIA
                            </td>
                        </tr>

                        <tr>
                            <td class="label-td">Entidad Bancaria</td>
                            <td>
                                <select class="input-corporativo" wire:model.defer="datos.entidad_bancaria"
                                    wire:change="guardar('entidad_bancaria','select')">

                                    <option value="">Seleccionar</option>
                                    <option>Banco de Bogotá</option>
                                    <option>Banco Popular</option>
                                    <option>Banco Itaú Corpbanca Colombia</option>
                                    <option>Bancolombia</option>
                                    <option>Banco Citibank Colombia</option>
                                    <option>Banco GNB Sudameris</option>
                                    <option>BBVA Colombia</option>
                                    <option>Itaú Helm Bank</option>
                                    <option>Red Multibanca Colpatria</option>
                                    <option>Banco de Occidente</option>
                                    <option>Banco Caja Social</option>
                                    <option>Banco Agrario de Colombia</option>
                                    <option>Banco Davivienda</option>
                                    <option>Banco Av. Villas</option>
                                    <option>Banco Procredit Colombia</option>
                                    <option>Banco Pichincha</option>
                                    <option>Bancoomeva</option>
                                    <option>Falabella</option>
                                    <option>Banco Finandina</option>
                                    <option>Banco Multibank S.A</option>
                                    <option>Banco Santander de Negocios Colombia</option>
                                    <option>Banco Cooperativo Coopcentral</option>
                                    <option>Banco Compartir S.A</option>
                                    <option>Financiera Juriscoop</option>
                                    <option>Cooperativa Financiera de Antioquia</option>
                                    <option>Cooperativa Financiera Cotrafa</option>
                                    <option>Confiar</option>
                                    <option>Serfinansa</option>
                                    <option>Coltefinanciera</option>
                                    <option>Nequi</option>
                                    <option>Daviplata</option>
                                    <option>Banco Scotiabank Colpatria S.A.</option>
                                    <option>Otro</option>
                                </select>
                            </td>

                            <td class="label-td">Tipo de Cuenta</td>
                            <td>
                                <select class="input-corporativo" wire:model.defer="datos.tipo_cuenta"
                                    wire:change="guardar('tipo_cuenta','select')">

                                    <option value="">Seleccionar</option>
                                    <option value="Ahorros">Ahorros</option>
                                    <option value="Corriente">Corriente</option>
                                </select>
                            </td>
                        </tr>

                        <tr>
                            <td class="label-td">Número de Cuenta</td>
                            <td colspan="3">
                                <input class="input-corporativo" wire:model.defer="datos.numero_cuenta"
                                    wire:change="guardar('numero_cuenta','text')">
                            </td>
                        </tr>

                    </table>
                    <br>
                    <!-- ==================== INFORMACIÓN FINANCIERA ==================== -->
                    <table class="tabla-corporativa mt-3">

                        <tr class="titulo-seccion">
                            <td colspan="4">
                                INFORMACIÓN FINANCIERA
                            </td>
                        </tr>

                        <tr>
                            <td class="label-td">Ingresos Operacionales</td>
                            <td>
                                <x-input-pesos campo="ingresos_operacionales" />
                            </td>

                            <td class="label-td">Utilidad Operacional</td>
                            <td>
                                <x-input-pesos campo="utilidad_operacional" />
                            </td>
                        </tr>

                        <tr>
                            <td class="label-td">Utilidad Neta</td>
                            <td>
                                <x-input-pesos campo="utilidad_neta" />
                            </td>

                            <td class="label-td">Utilidades Acumuladas</td>
                            <td>
                                <x-input-pesos campo="utilidades_acumuladas" />
                            </td>
                        </tr>

                        <tr>
                            <td class="label-td">Depreciaciones</td>
                            <td>
                                <x-input-pesos campo="depreciaciones" />
                            </td>

                            <td class="label-td">Activo Corriente</td>
                            <td>
                                <x-input-pesos campo="activo_corriente" />
                            </td>
                        </tr>

                        <tr>
                            <td class="label-td">Activo No Corriente</td>
                            <td>
                                <x-input-pesos campo="activo_no_corriente" />
                            </td>

                            <td class="label-td">Pasivo Corto Plazo</td>
                            <td>
                                <x-input-pesos campo="pasivo_corto_plazo" />
                            </td>
                        </tr>

                        <tr>
                            <td class="label-td">Pasivo Largo Plazo</td>
                            <td>
                                <x-input-pesos campo="pasivo_largo_plazo" />
                            </td>

                            <td class="label-td">Patrimonio</td>
                            <td>
                                <x-input-pesos campo="patrimonio" />
                            </td>
                        </tr>

                        <tr>
                            <td class="label-td">Total Obligaciones Financieras</td>
                            <td>
                                <x-input-pesos campo="total_obligaciones" />
                            </td>

                            <td class="label-td">Capital Social</td>
                            <td>
                                <x-input-pesos campo="capital_social" />
                            </td>
                        </tr>
                    </table>
                    <br>
                    <!-- ==================== DECLARACIONES ==================== -->
                    <table class="tabla-corporativa mt-3">
                        <tr class="titulo-seccion">
                            <td colspan="4">
                                DECLARACIÓN DE ORIGEN DE FONDOS Y AUTORIZACIÓN CONSULTA CENTRALES DE RIESGO
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4" class="p-3 text-justify">
                                Declaro expresamente que: <br>
                                1. Los recursos que poseo proviene de las siguientes fuentes (detalle, ocupación,
                                oficio, actividad o negocio): <br>
                                2. Tanto mi actividad, profesión u oficio es lícita y la ejerzo dentro del marco legal y
                                los recursos que poseo no provienen de actividades ilícitas de las contempladas en el
                                Código penal Colombiano <br>
                                3. La información que he suministrado en la solicitud y en este documento es veraz y
                                verificable y me obligo a actualizarla anualmente. <br>
                                4. Los recursos que se deriven del desarrollo de operaciones no se destinarán a la
                                financiación del terrorismo, grupos terroristas, actividades terroristas, corrupción y/o
                                soborno. <br>
                                5. Autorizo expresamente para fines de información financiera consultar las centrales de
                                riesgo
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4" class="p-2">

                                <strong>Actividad económica:</strong>

                                <label class="ms-3">
                                    <input type="checkbox" wire:model="actividades" wire:change="guardarActividades"
                                        value="Honorarios">
                                    Honorarios
                                </label>

                                <label class="ms-3">
                                    <input type="checkbox" wire:model="actividades" wire:change="guardarActividades"
                                        value="Comisiones">
                                    Comisiones
                                </label>

                                <label class="ms-3">
                                    <input type="checkbox" wire:model="actividades" wire:change="guardarActividades"
                                        value="Inversiones">
                                    Inversiones
                                </label>
                                <label class="ms-3">
                                    <input type="checkbox" wire:model="actividades" wire:change="guardarActividades"
                                        value="Rendimientos financieros">
                                    Rendimientos financieros
                                </label>
                                <label class="ms-3">
                                    <input type="checkbox" wire:model="actividades" wire:change="guardarActividades"
                                        value="Pension">
                                    Pensión
                                </label>
                                <label class="ms-3">
                                    <input type="checkbox" wire:model="actividades" wire:change="guardarActividades"
                                        value="Salario">
                                    Salario
                                </label>
                                <label class="ms-3">
                                    <input type="checkbox" wire:model="actividades" wire:change="guardarActividades"
                                        value="Rentas">
                                    Rentas
                                </label>
                                <label class="ms-3">
                                    <input type="checkbox" wire:model="actividades" wire:change="guardarActividades"
                                        value="Otros ingresos">
                                    Otros ingresos
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4" class="p-2 text-center">
                                <label>
                                    <input type="checkbox" wire:model.defer="datos.decl_origen_ingresos"
                                        wire:change="guardar('decl_origen_ingresos','checkbox')">
                                    Acepto y certifico esta declaración
                                </label>
                            </td>
                        </tr>

                    </table>
                    <!-- ==================== AUTORIZACIÓN SAGRILAFT ==================== -->
                    <table class="tabla-corporativa mt-3">
                        <tr class="titulo-seccion">
                            <td colspan="4">
                                AUTORIZACIÓN SAGRILAFT
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4" class="p-3 text-justify">
                                Declaro expresamente obrando en nombre propio y/o de la sociedad jurídica que represento
                                cumplimos con las normas sobre prevención, control de lavado de activos y la
                                financiación
                                del terrorismo, que ni yo ni la sociedad que represento, ni sus accionistas directa o
                                indirectamente , ni sus representantes legales, ni sus miembros de junta directiva, se
                                encuentran en las listas restrictivas vinculantes para Colombia, de conformidad con el
                                derecho internacional (listas de las Naciones Unidas) o en las listas de la OFAC y que
                                en el
                                evento de tener conocimiento de que alguna de las personas anteriormente relacionadas
                                presentamos reporte en dichas listas, lo informaremos de manera oportuna a LA EMPRESA.
                                Reconocemos y autorizamos el derecho que tiene LA EMPRESA para realizar las
                                verificaciones
                                que considere pertinentes en las listas restrictivas y sobre el origen de los activos y
                                los
                                recursos que manejamos y para efectuar los reportes antes los entes de control UIAF y a
                                dar
                                por terminada la relación vinculante entre las partes si en su verificación encuentra
                                mérito
                                para tal fin.
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4" class="p-2 text-center">
                                <label>
                                    <input type="checkbox" wire:model.defer="datos.decl_sagrilaft"
                                        wire:change="guardar('decl_sagrilaft','checkbox')">
                                    Acepto autorización SAGRILAFT
                                </label>
                            </td>
                        </tr>
                    </table>
                    <!-- ==================== AUTORIZACIÓN DATOS PERSONALES ==================== -->
                    <table class="tabla-corporativa mt-3">
                        <tr class="titulo-seccion">
                            <td colspan="4">
                                AUTORIZACIÓN PARA TRATAMIENTO DE DATOS CONOCIMIENTOS PARA JURDICOS NATURALES Y
                                EXTRANEJEROS
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4" class="p-3 text-justify">
                                Con el propósito de dar un adecuado tratamiento a sus datos personales de acuerdo al
                                Régimen General de Protección de Datos Personales reglamentado por la Constitución
                                Política Nacional en sus artículos 15 y 20, la Ley 1581 de 2012, el Decreto 1377 de 2013
                                y demás preceptos normativos, y siendo primordial para nuestra empresa contar con su
                                consentimiento para mantener una comunicación constante con usted, le compartimos que
                                LOGISTICARGA JM S.A.S, ha creado una Política de Tratamiento de Información, por medio
                                de la cual se establecen los parámetros para manejar la información contenida en los
                                Bancos y Bases de Datos de dicha entidad, que usted podrá consultar en cualquier momento
                                y por los canales de comunicación establecidos por la empresa.
                                HABEAS DATA : De esta manera, es nuestra responsabilidad informarle que en dicha
                                política se establecen las finalidades con las cuales son tratados sus datos personales
                                por la empresa; entre las cuales se encuentran las siguientes:
                                • Mantener una comunicación constante con los titulares de los datos personales,
                                relativa al desarrollo de las actividades propias de la empresa de acuerdo con los
                                perfiles de cada tipo de base de datos que posea la empresa.
                                • Cumplir con las obligaciones legales y contractuales en las que se requiera recaudar
                                información personal mediante la elaboración de Bases de Datos para efectos de control,
                                supervisión y proyectos llevados a cabo por la entidad.
                                • Almacenar y actualizar los datos de los clientes y contratistas de la empresa.
                                • Envío de circulares informativas de inventarios y similares.
                                • Realizar labores y gestiones de mercadeo para efectos de mejorar los servicios
                                prestados por la entidad y mejorar el conocimiento del cliente o destinatario.
                                La Entidad obtiene sus datos personales porque usted mismo los ha suministrado, porque
                                los ha obtenido de un tercero autorizado por usted o por la ley para suministrarlos, o
                                porque son datos públicos, es decir, datos para cuyo tratamiento no se requiere de su
                                autorización previa. Usted tiene el derecho de conocer, actualizar, rectificar su
                                información, y/o revocar la autorización para su tratamiento. En particular, son
                                derechos de los titulares según se establece en el artículo 8 de la Ley 1581 de 2012: a)
                                Conocer, actualizar y rectificar sus datos personales b) Solicitar prueba de la
                                autorización otorgada c) Ser informado, previa solicitud, respecto del uso que le ha
                                dado a sus datos personales; d) Presentar ante la Superintendencia de Industria y
                                Comercio quejas por infracciones a lo dispuesto en la ley e) Revocar la autorización y/o
                                solicitar la supresión del dato f) Acceder en forma gratuita a sus datos personales que
                                hayan sido objeto de Tratamiento. Si su deseo es realizar cualquiera de estas acciones,
                                lo invitamos a consultar la Política de Tratamiento de Datos Personales de la empresa,
                                para conocer el procedimiento que debe realizar para enviar su solicitud al correo
                                electrónico Servicios@logisticargajm.com; o comuníquese con nosotros al teléfono:
                                3105462363.
                                TRATAMIENTO DE DATOS FINANCIEROS: Autorizo a LOGISTICARGA JM S.A.S. para que con fines
                                estadísticos de verificación del riesgo creditico o de reporte histórico de
                                comportamiento comercial, solicite, procese, conserve, verifique, consulte, suministre,
                                reporte o actualice cualquier información relacionada con el comportamiento financiero,
                                crediticio o comercial a los operadores de bancos de datos o centrales de información
                                autorizados por la legislación, incluidos, DATACREDITO y CIFIN, en los términos y
                                durante el tiempo que la ley establezca.
                                TRATAMIENTO DE DATOS JUDUCIALES :Teniendo en cuenta lo anterior, autorizo LOGISTICARGA
                                JM S.A.S. de manera voluntaria, previa, explícita, informace sobre mis aspectos
                                judiciales nacionales como internacionales de acuerdo Consejo de Seguridad de las
                                Naciones Unidas, relacionadas con el Financiamiento del Terrorismo, en consonancia con
                                el artículo 20 de la Ley 1121 de 2006 y las Recomendaciones GAFI No. 6 y 7, los
                                servicios obligados deben consultar permanentemente las Listas Vinculantes.
                                Como empleado de LOGISTICARGA JM S.A.S. doy mi consentimiento para que me sean
                                practicadas las pruebas de alcohol y drogas, siempre que la organización lo considere
                                necesario y sin necesidad de aviso previo.
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4" class="p-2 text-center">
                                <label>
                                    <input type="checkbox" wire:model.defer="datos.decl_datos_personales"
                                        wire:change="guardar('decl_datos_personales','checkbox')">
                                    Acepto autorización de datos personales
                                </label>
                            </td>
                        </tr>
                    </table>
                    <br>
                    <!-- ==================== INFORMACIÓN TRIBUTARIA ==================== -->
                    <table class="tabla-corporativa mt-3">

                        <tr class="titulo-seccion">
                            <td colspan="6">
                                INFORMACIÓN TRIBUTARIA
                            </td>
                        </tr>

                        <!-- ============== RENTA ============== -->

                        <tr class="subtitulo-tabla">
                            <td colspan="6" style="text-align:center !important; font-weight:bold;">
                                RENTA
                            </td>
                        </tr>

                        <tr>
                            <td class="label-td">Contribuyente del impuesto de Renta</td>
                            <td colspan="5">
                                <label class="me-3">
                                    <input type="radio" name="contribuyente_renta"
                                        wire:model="datos.contribuyente_renta"
                                        wire:change="guardar('contribuyente_renta','radio')" value="Si"> Sí
                                </label>

                                <label>
                                    <input type="radio" name="contribuyente_renta"
                                        wire:model="datos.contribuyente_renta"
                                        wire:change="guardar('contribuyente_renta','radio')" value="No"> No
                                </label>
                            </td>
                        </tr>

                        <!-- GRAN CONTRIBUYENTE -->

                        <tr>
                            <td class="label-td">Gran contribuyente</td>
                            <td>
                                <label class="me-3">
                                    <input type="radio" name="gran_contribuyente"
                                        wire:model="datos.gran_contribuyente"
                                        wire:change="guardar('gran_contribuyente','radio')" value="Si"> Sí
                                </label>

                                <label>
                                    <input type="radio" name="gran_contribuyente"
                                        wire:model="datos.gran_contribuyente"
                                        wire:change="guardar('gran_contribuyente','radio')" value="No"> No
                                </label>
                            </td>

                            <td class="label-td">No. Resolución</td>
                            <td colspan="3">
                                <input class="input-corporativo"
                                    wire:model.defer="datos.resolucion_gran_contribuyente"
                                    wire:change="guardar('resolucion_gran_contribuyente','text')"
                                    @disabled(($datos['gran_contribuyente'] ?? '') === 'No')>
                            </td>
                        </tr>

                        <!-- AUTORRETENEDOR -->

                        <tr>
                            <td class="label-td">Autorretenedor</td>
                            <td>
                                <label class="me-3">
                                    <input type="radio" name="autorretenedor" wire:model="datos.autorretenedor"
                                        wire:change="guardar('autorretenedor','radio')" value="Si"> Sí
                                </label>

                                <label>
                                    <input type="radio" name="autorretenedor" wire:model="datos.autorretenedor"
                                        wire:change="guardar('autorretenedor','radio')" value="No"> No
                                </label>
                            </td>

                            <td class="label-td">No. Resolución</td>
                            <td colspan="3">
                                <input class="input-corporativo" wire:model.defer="datos.resolucion_autorretenedor"
                                    wire:change="guardar('resolucion_autorretenedor','text')"
                                    @disabled(($datos['autorretenedor'] ?? '') === 'No')>
                            </td>
                        </tr>
                        <tr>
                            <td class="label-td">Régimen simple de tributación</td>
                            <td>
                                <label class="me-3">
                                    <input type="radio" name="regimen_simple" wire:model="datos.regimen_simple"
                                        wire:change="guardar('regimen_simple','radio')" value="Si"> Sí
                                </label>

                                <label>
                                    <input type="radio" name="regimen_simple" wire:model="datos.regimen_simple"
                                        wire:change="guardar('regimen_simple','radio')" value="No"> No
                                </label>
                            </td>

                            <td class="label-td">Facturador Electrónico</td>
                            <td colspan="3">
                                <label class="me-3">
                                    <input type="radio" name="facturador_electronico"
                                        wire:model="datos.facturador_electronico"
                                        wire:change="guardar('facturador_electronico','radio')" value="Si"> Sí
                                </label>

                                <label>
                                    <input type="radio" name="facturador_electronico"
                                        wire:model="datos.facturador_electronico"
                                        wire:change="guardar('facturador_electronico','radio')" value="No"> No
                                </label>
                            </td>
                        </tr>


                        <!-- IVA -->

                        <tr class="subtitulo-tabla">
                            <td colspan="6" style="text-align:center !important; font-weight:bold;">
                                IVA
                            </td>
                        </tr>

                        <tr>
                            <td class="label-td">Régimen de IVA</td>
                            <td>
                                <select class="input-corporativo" wire:model.defer="datos.regimen_iva"
                                    wire:change="guardar('regimen_iva','select')">

                                    <option value="">Seleccionar</option>
                                    <option value="Comun">Común</option>
                                    <option value="Simplificado">Simplificado</option>

                                </select>
                            </td>

                            <td class="label-td">Agente Retenedor IVA</td>
                            <td colspan="3">
                                <label class="me-3">
                                    <input type="radio" name="agente_retenedor_iva"
                                        wire:model="datos.agente_retenedor_iva"
                                        wire:change="guardar('agente_retenedor_iva','radio')" value="Si"> Sí
                                </label>

                                <label>
                                    <input type="radio" name="agente_retenedor_iva"
                                        wire:model="datos.agente_retenedor_iva"
                                        wire:change="guardar('agente_retenedor_iva','radio')" value="No"> No
                                </label>
                            </td>
                        </tr>

                        <!-- ICA -->

                        <tr class="subtitulo-tabla">
                            <td colspan="6" style="text-align:center !important; font-weight:bold;">
                                ICA
                            </td>
                        </tr>

                        <tr>
                            <td class="label-td">Responsable de Industria y Comercio</td>
                            <td colspan="5">
                                <label class="me-3">
                                    <input type="radio" name="responsable_ica" wire:model="datos.responsable_ica"
                                        wire:change="guardar('responsable_ica','radio')" value="Si"> Sí
                                </label>

                                <label>
                                    <input type="radio" name="responsable_ica" wire:model="datos.responsable_ica"
                                        wire:change="guardar('responsable_ica','radio')" value="No"> No
                                </label>
                            </td>
                        </tr>

                        <tr>
                            <td class="label-td">CIIU Actividad ICA</td>
                            <td>
                                <input class="input-corporativo" wire:model.defer="datos.ciiu_ica"
                                    wire:change="guardar('ciiu_ica','text')" @disabled(($datos['responsable_ica'] ?? '') === 'No')>
                            </td>

                            <td class="label-td">% Retención</td>
                            <td>
                                <input class="input-corporativo" wire:model.defer="datos.retencion_ica"
                                    wire:change="guardar('retencion_ica','text')" @disabled(($datos['responsable_ica'] ?? '') === 'No')>
                            </td>

                            <td class="label-td">Ciudad</td>
                            <td>
                                <input class="input-corporativo" wire:model.defer="datos.ciudad_ica"
                                    wire:change="guardar('ciudad_ica','text')" @disabled(($datos['responsable_ica'] ?? '') === 'No')>
                            </td>
                        </tr>

                    </table>
                    <br>
                    <!--===================== OPERACIONES EXTRANGERAS ====================-->
                    <table class="tabla-corporativa mt-3">

                        <tr class="titulo-seccion">
                            <td colspan="7">
                                OPERACIONES INTERNACIONALES
                            </td>
                        </tr>

                        <tr>
                            <td colspan="3">¿Realiza transacciones en moneda extranjera?</td>
                            <td colspan="4">
                                <label class="me-3">
                                    <input type="radio" name="transacciones_extranjera"
                                        wire:model="datos.transacciones_extranjera"
                                        wire:change="guardar('transacciones_extranjera','radio')" value="Si"> Sí
                                </label>

                                <label>
                                    <input type="radio" name="transacciones_extranjera"
                                        wire:model="datos.transacciones_extranjera"
                                        wire:change="guardar('transacciones_extranjera','radio')" value="No"> No
                                </label>
                            </td>
                        </tr>

                        <tr>
                            <td colspan="3">¿Posee productos financieros en el exterior?</td>
                            <td colspan="4">
                                <label class="me-3">
                                    <input type="radio" name="productos_exterior"
                                        wire:model="datos.productos_exterior"
                                        wire:change="guardar('productos_exterior','radio')" value="Si"> Sí
                                </label>

                                <label>
                                    <input type="radio" name="productos_exterior"
                                        wire:model="datos.productos_exterior"
                                        wire:change="guardar('productos_exterior','radio')" value="No"> No
                                </label>
                            </td>
                        </tr>

                        <tr>
                            <td colspan="3">¿Posee cuentas en moneda extranjera?</td>
                            <td colspan="4">
                                <label class="me-3">
                                    <input type="radio" name="cuentas_extranjera"
                                        wire:model="datos.cuentas_extranjera"
                                        wire:change="guardar('cuentas_extranjera','radio')" value="Si"> Sí
                                </label>

                                <label>
                                    <input type="radio" name="cuentas_extranjera"
                                        wire:model="datos.cuentas_extranjera"
                                        wire:change="guardar('cuentas_extranjera','radio')" value="No"> No
                                </label>
                            </td>
                        </tr>

                        <tr>
                            <td colspan="7" class="p-2 text-center">
                                <strong>Indique operaciones extranjeras:</strong>

                                <label class="ms-3">
                                    <input type="checkbox" wire:model="operaciones_extranjeras"
                                        wire:change="guardarOperaciones" value="Importaciones">
                                    Importaciones
                                </label>

                                <label class="ms-3">
                                    <input type="checkbox" wire:model="operaciones_extranjeras"
                                        wire:change="guardarOperaciones" value="Exportaciones">
                                    Exportaciones
                                </label>

                                <label class="ms-3">
                                    <input type="checkbox" wire:model="operaciones_extranjeras"
                                        wire:change="guardarOperaciones" value="Inversiones">
                                    Inversiones
                                </label>

                                <label class="ms-3">
                                    <input type="checkbox" wire:model="operaciones_extranjeras"
                                        wire:change="guardarOperaciones" value="Prestamos">
                                    Préstamos
                                </label>

                                <label class="ms-3">
                                    <input type="checkbox" wire:model="operaciones_extranjeras"
                                        wire:change="guardarOperaciones" value="Giros">
                                    Giros
                                </label>

                                <label class="ms-3">
                                    <input type="checkbox" wire:model="operaciones_extranjeras"
                                        wire:change="guardarOperaciones" value="Pagos de servicios">
                                    Pagos de servicios
                                </label>
                            </td>
                        </tr>
                        <tr class="subtitulo-tabla">
                            <th>Tipo de Producto</th>
                            <th>Número de Producto</th>
                            <th>Entidad</th>
                            <th>Monto</th>
                            <th>Moneda</th>
                            <th>País</th>
                            <th>Ciudad</th>
                        </tr>

                        @foreach ($operaciones as $index => $op)
                            <tr>
                                <td>
                                    <select class="input-corporativo"
                                        wire:model.defer="operaciones.{{ $index }}.tipo_producto"
                                        wire:change="guardarOperacion({{ $index }}, 'tipo_producto')">

                                        <option value="">Seleccionar</option>
                                        <option value="Cuenta corriente">Cuenta corriente</option>
                                        <option value="Cuenta de ahorro">Cuenta de ahorro</option>
                                        <option value="Cuenta nómina">Cuenta nómina</option>
                                        <option value="Cuenta de valores">Cuenta de valores</option>
                                        <option value="No Aplica">No Aplica</option>
                                    </select>
                                </td>

                                <td>
                                    <input class="input-corporativo"
                                        wire:model.defer="operaciones.{{ $index }}.numero_producto"
                                        wire:change="guardarOperacion({{ $index }}, 'numero_producto')">
                                </td>

                                <td>
                                    <input class="input-corporativo"
                                        wire:model.defer="operaciones.{{ $index }}.entidad"
                                        wire:change="guardarOperacion({{ $index }}, 'entidad')">
                                </td>

                                <td>
                                    <!-- AQUÍ VA TU COMPONENTE -->
                                    {{-- <x-input-pesos campo="operaciones.{{ $index }}.monto" /> --}}
                                    <x-input-pesos-operacion index="{{ $index }}" campo="monto" />
                                </td>

                                <td>
                                    <input class="input-corporativo"
                                        wire:model.defer="operaciones.{{ $index }}.moneda"
                                        wire:change="guardarOperacion({{ $index }}, 'moneda')">
                                </td>

                                <td>
                                    <input class="input-corporativo"
                                        wire:model.defer="operaciones.{{ $index }}.pais"
                                        wire:change="guardarOperacion({{ $index }}, 'pais')">
                                </td>

                                <td>
                                    <input class="input-corporativo"
                                        wire:model.defer="operaciones.{{ $index }}.ciudad"
                                        wire:change="guardarOperacion({{ $index }}, 'ciudad')">
                                </td>
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="7" class="text-center">
                                <div class="contenedor-botones-contacto">
                                    <button class="btn btn-contacto-agregar" wire:click="agregarOperacion">
                                        + Agregar
                                    </button>

                                    <button class="btn btn-contacto-eliminar" wire:click="eliminarUltimaOperacion"
                                        @if (count($operaciones) <= 1) disabled @endif>
                                        - Eliminar
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </table>
                    <br>
                    <!--===================== DECLARACIÓN PEP  ====================-->
                    <table class="tabla-corporativa mt-3">

                        <tr class="titulo-seccion">
                            <td colspan="4">
                                DECLARACIÓN PEP (Persona Expuesta Políticamente)
                            </td>
                        </tr>

                        <tr>
                            <td colspan="3">
                                Por su cargo o actividad administrativa o tiene a su cargo el manejo de recursos
                                públicos.
                            </td>
                            <td>
                                <label class="me-3">
                                    <input type="radio" name="pep_recursos_publicos"
                                        wire:model="datos.pep_recursos_publicos"
                                        wire:change="guardar('pep_recursos_publicos','radio')" value="Si"> Sí
                                </label>

                                <label>
                                    <input type="radio" name="pep_recursos_publicos"
                                        wire:model="datos.pep_recursos_publicos"
                                        wire:change="guardar('pep_recursos_publicos','radio')" value="No"> No
                                </label>
                            </td>
                        </tr>

                        <tr>
                            <td colspan="3">
                                Por su cargo o actividad ejerce algún grado o tipo de poder público.
                            </td>
                            <td>
                                <label class="me-3">
                                    <input type="radio" name="pep_poder_publico"
                                        wire:model="datos.pep_poder_publico"
                                        wire:change="guardar('pep_poder_publico','radio')" value="Si"> Sí
                                </label>

                                <label>
                                    <input type="radio" name="pep_poder_publico"
                                        wire:model="datos.pep_poder_publico"
                                        wire:change="guardar('pep_poder_publico','radio')" value="No"> No
                                </label>
                            </td>
                        </tr>

                        <tr>
                            <td colspan="3">
                                Por su actividad, ocupación u oficio, goza de reconocimiento público general.
                            </td>
                            <td>
                                <label class="me-3">
                                    <input type="radio" name="pep_reconocimiento"
                                        wire:model="datos.pep_reconocimiento"
                                        wire:change="guardar('pep_reconocimiento','radio')" value="Si"> Sí
                                </label>

                                <label>
                                    <input type="radio" name="pep_reconocimiento"
                                        wire:model="datos.pep_reconocimiento"
                                        wire:change="guardar('pep_reconocimiento','radio')" value="No"> No
                                </label>
                            </td>
                        </tr>

                        <tr>
                            <td colspan="3">
                                Es usted sujeto de obligaciones tributarias en otro país diferente a Colombia.
                            </td>
                            <td>
                                <label class="me-3">
                                    <input type="radio" name="pep_obligaciones_exterior"
                                        wire:model="datos.pep_obligaciones_exterior"
                                        wire:change="guardar('pep_obligaciones_exterior','radio')" value="Si"> Sí
                                </label>

                                <label>
                                    <input type="radio" name="pep_obligaciones_exterior"
                                        wire:model="datos.pep_obligaciones_exterior"
                                        wire:change="guardar('pep_obligaciones_exterior','radio')" value="No"> No
                                </label>
                            </td>
                        </tr>
                    </table>
                    <br>
                    <!--===================== VERIFICACIÓN DE INFORMACIÓN  ====================-->
                    <table class="tabla-corporativa mt-3">

                        <tr class="titulo-seccion">
                            <td colspan="4">
                                VERIFICACIÓN DE LA INFORMACIÓN
                            </td>
                        </tr>

                        <tr>
                            <td class="label-td" style="width:25%">
                                Relacione la Actividad económica - CIIU de la contraparte
                            </td>

                            <td style="width:35%">
                                <strong>Principal:</strong>
                                <input class="input-corporativo mt-1" placeholder="Actividad principal"
                                    wire:model.defer="datos.ciiu_principal"
                                    wire:change="guardar('ciiu_principal','text')">
                            </td>

                            <td class="label-td" style="width:15%">
                                Descripción Principal
                            </td>

                            <td style="width:25%">
                                <textarea class="input-corporativo" rows="3" placeholder="Descripción de la actividad principal"
                                    wire:model.defer="datos.descripcion_principal" wire:change="guardar('descripcion_principal','text')"></textarea>
                            </td>
                        </tr>

                        <tr>
                            <td class="label-td">
                                Actividad secundaria
                            </td>

                            <td>
                                <strong>Secundario:</strong>
                                <input class="input-corporativo mt-1" placeholder="Actividad secundaria"
                                    wire:model.defer="datos.ciiu_secundario"
                                    wire:change="guardar('ciiu_secundario','text')">
                            </td>

                            <td class="label-td">
                                Descripción Secundaria
                            </td>

                            <td>
                                <textarea class="input-corporativo" rows="3" placeholder="Descripción de la actividad secundaria"
                                    wire:model.defer="datos.descripcion_secundaria" wire:change="guardar('descripcion_secundaria','text')"></textarea>
                            </td>
                        </tr>

                        <tr>
                            <td class="label-td">
                                ¿Cuál es el medio de pago o recaudo de la operación?
                            </td>

                            <td>
                                <select class="input-corporativo" wire:model.defer="datos.medio_pago"
                                    wire:change="guardar('medio_pago','select')">

                                    <option value="">Seleccione</option>
                                    <option value="Efectivo">Efectivo</option>
                                    <option value="Transferencia">Transferencia</option>
                                    <option value="Cheque">Cheque</option>
                                    <option value="Activos Virtuales">Activos Virtuales</option>
                                </select>
                            </td>

                            <td class="label-td">
                                ¿Cuenta con un relacionado PEP?
                            </td>

                            <td>
                                <select class="input-corporativo" wire:model.defer="datos.relacionado_pep"
                                    wire:change="guardar('relacionado_pep','select')">

                                    <option value="">Seleccione</option>
                                    <option value="Si">Sí</option>
                                    <option value="No">No</option>
                                </select>
                            </td>
                        </tr>
                    </table>
                    <!--=============    DATOS DE UBICACIÓN – VERIFICACIÓN ===============-->
                    <table class="tabla-corporativa mt-3">

                        <tr class="subtitulo-tabla">
                            <td colspan="6" style="text-align:center !important; font-weight:bold;">
                                DATOS DE UBICACIÓN – VERIFICACIÓN
                            </td>
                        </tr>

                        <tr>
                            <td class="label-td">País:</td>

                            <td>
                                <select class="input-corporativo" wire:model="datos.pais_verificacion"
                                    wire:change="guardar('pais_verificacion','select')">

                                    <option value="">Seleccione</option>

                                    @foreach ($paisesVerificacion as $pais)
                                        <option value="{{ $pais }}">{{ strtoupper($pais) }}</option>
                                    @endforeach

                                </select>
                            </td>

                            <td class="label-td">Departamento:</td>

                            <td>
                                <select class="input-corporativo" wire:model="datos.departamento_verificacion"
                                    wire:change="cambioDepartamentoVerificacion($event.target.value)"
                                    @if (($datos['pais_verificacion'] ?? '') != 'Colombia') disabled @endif>

                                    @if (($datos['pais_verificacion'] ?? '') == 'Colombia')

                                        <option value="">Seleccione</option>

                                        @foreach ($departamentosVerificacion as $dep)
                                            <option value="{{ $dep }}">{{ $dep }}</option>
                                        @endforeach
                                    @else
                                        <option value="NO APLICA">NO APLICA</option>
                                    @endif
                                </select>
                            </td>
                            <td class="label-td">Municipio:</td>
                            <td>
                                <div wire:loading wire:target="cambioDepartamentoVerificacion"
                                    class="text-red small mb-1">
                                    Cargando municipios...
                                </div>

                                <select class="input-corporativo" wire:model.defer="datos.ciudad_verificacion"
                                    wire:change="guardar('ciudad_verificacion','select')"
                                    @if (($datos['pais_verificacion'] ?? '') != 'Colombia') disabled @endif>

                                    @if (($datos['pais_verificacion'] ?? '') == 'Colombia')

                                        <option value="">Seleccione</option>

                                        @foreach ($ciudadesVerificacion as $ciu)
                                            <option value="{{ $ciu }}">{{ $ciu }}</option>
                                        @endforeach
                                    @else
                                        <option value="NO APLICA">NO APLICA</option>
                                    @endif
                                </select>
                            </td>
                        </tr>
                    </table>
                    <!--=============  PAÍSES DE ALTO RIESGO ===============-->
                    <table class="tabla-corporativa mt-3">

                        <tr class="subtitulo-tabla">
                            <td colspan="4" style="text-align:center !important; font-weight:bold;">
                                PAÍSES DE ALTO RIESGO O TRANSACCIONES CON ACTIVOS VIRTUALES
                            </td>
                        </tr>

                        <tr>
                            <td class="label-td" style="width:25%">
                                ¿Está usted ubicado en alguno de estos países?
                            </td>

                            <td style="width:35%">
                                <select class="input-corporativo" wire:model.defer="datos.pais_alto_riesgo"
                                    wire:change="guardar('pais_alto_riesgo','select')">

                                    <option value="">Seleccione</option>
                                    <option value="NO APLICA">NO APLICA</option>

                                    <option>BULGARIA</option>
                                    <option>BURKINA FASO</option>
                                    <option>CAMERÚN</option>
                                    <option>CROACIA</option>
                                    <option>HAITÍ</option>
                                    <option>JAMAICA</option>
                                    <option>KENIA</option>
                                    <option>MALI</option>
                                    <option>MOZAMBIQUE</option>
                                    <option>NAMIBIA</option>
                                    <option>NIGERIA</option>
                                    <option>FILIPINAS</option>
                                    <option>REPÚBLICA DEMOCRÁTICA DEL CONGO</option>
                                    <option>SENEGAL</option>
                                    <option>SUDÁN DEL SUR</option>
                                    <option>SIRIA</option>
                                    <option>TANZANIA</option>
                                    <option>TURQUÍA</option>
                                    <option>VIETNAM</option>
                                    <option>YEMEN</option>
                                    <option>COREA DEL NORTE</option>
                                    <option>IRÁN</option>
                                    <option>BIRMANIA</option>
                                    <option>ÁFRICA DEL SUR</option>
                                    <option>MÓNACO</option>
                                    <option>VENEZUELA</option>

                                </select>
                            </td>

                            <td class="label-td" style="width:20%">
                                ¿Desarrolla actividades con Activos Virtuales?
                            </td>

                            <td style="width:20%">
                                <select class="input-corporativo" wire:model.defer="datos.activos_virtuales"
                                    wire:change="guardar('activos_virtuales','select')">

                                    <option value="">Seleccione</option>
                                    <option value="Si">Sí</option>
                                    <option value="No">No</option>

                                </select>
                            </td>
                        </tr>
                    </table>
                    <br>
                    <!--============= DOCUMENTOS REQUERIDOS ===============-->
                    <table class="tabla-corporativa mt-3">

                        <tr class="titulo-seccion">
                            <td colspan="3">
                                DOCUMENTOS REQUERIDOS
                            </td>
                        </tr>

                        <tr class="subtitulo-tabla">
                            <th>Documento</th>
                            <th>Persona Jurídica</th>
                            <th>Persona Natural o Extranjera</th>
                        </tr>

                        <tr>
                            <td>Certificaciones como BASC / C-TPAT / OEA</td>
                            <td class="text-center">SI APLICA</td>
                            <td class="text-center">SI APLICA</td>
                        </tr>

                        <tr>
                            <td>Cámara de Comercio menor a 30 días</td>
                            <td class="text-center">X</td>
                            <td class="text-center">-</td>
                        </tr>

                        <tr>
                            <td>RUT (Todas las páginas), actualizado último año</td>
                            <td class="text-center">X</td>
                            <td class="text-center">X</td>
                        </tr>

                        <tr>
                            <td>Estados financieros comparativos de los dos últimos años</td>
                            <td class="text-center">X</td>
                            <td class="text-center">X</td>
                        </tr>

                        <tr>
                            <td>Certificación bancaria (incluyendo realización pago)</td>
                            <td class="text-center">X</td>
                            <td class="text-center">X</td>
                        </tr>

                        <tr>
                            <td>Referencias comerciales emitidas por proveedores, actualizadas</td>
                            <td class="text-center">X</td>
                            <td class="text-center">X</td>
                        </tr>

                        <tr>
                            <td>Declaración de renta del último año</td>
                            <td class="text-center">-</td>
                            <td class="text-center">X</td>
                        </tr>

                        <tr>
                            <td>Fotocopia de cédula</td>
                            <td class="text-center">X</td>
                            <td class="text-center">X</td>
                        </tr>
                        <tr>
                            <td>Procuraduría</td>
                            <td class="text-center">SI APLICA</td>
                            <td class="text-center">SI APLICA</td>
                        </tr>

                        <tr>
                            <td>OFAC</td>
                            <td class="text-center">SI APLICA</td>
                            <td class="text-center">SI APLICA</td>
                        </tr>

                        <tr>
                            <td>Contraloría</td>
                            <td class="text-center">SI APLICA</td>
                            <td class="text-center">SI APLICA</td>
                        </tr>

                        <tr>
                            <td>Certificación moneda extranjera o activos virtuales</td>
                            <td class="text-center">SI APLICA</td>
                            <td class="text-center">SI APLICA</td>
                        </tr>

                        <tr>
                            <td>
                                Origen de los recursos empleados en las operaciones realizadas a través de la
                                transportadora
                            </td>
                            <td class="text-center">SI APLICA</td>
                            <td class="text-center">SI APLICA</td>
                        </tr>
                    </table>
                    <br>
                    <!--============= REQUISITOS ADICIONALES ===============-->
                    <table class="tabla-corporativa mt-3">

                        <tr class="titulo-seccion">
                            <td colspan="2">
                                REQUISITOS ADICIONALES POR TIPO DE PROVEEDOR - PRODUCTOR (No aplica para Clientes)
                            </td>
                        </tr>

                        <tr class="subtitulo-tabla">
                            <th style="width:40%">Tipo de Proveedor</th>
                            <th style="width:60%">Requisitos Adicionales</th>
                        </tr>

                        <tr>
                            <td>
                                <strong>Prestación de Servicios:</strong>
                                Empresas cuya labor sea de prestar servicios a la compañía.
                            </td>

                            <td>
                                Deberán tener el pago de la seguridad social de los empleados al día y cumplir con la
                                respectiva documentación bajo la función del trabajo a realizar solicitados por el área
                                de
                                Seguridad y salud en el trabajo de la compañía.
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <strong>Productos Químicos:</strong>
                                Suministran productos químicos de línea industrial usados en limpieza, desinfección,
                                tratamiento de aguas.
                            </td>

                            <td>
                                Certificaciones (NTC - INVIMA - Fichas Técnicas y Hoja de seguridad en idioma español
                                por
                                cada producto). Apto para uso en alimentos.
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <strong>Equipo Médico Científico:</strong>
                                Empresas que su fuerte o core es la venta de equipos especializados de Salud.
                            </td>

                            <td>
                                Certificaciones (INVIMA - Fichas Técnicas).
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <strong>Tecnología:</strong>
                                Empresas que su fuerte o core es la venta de aplicativos, equipos de computación y
                                comunicación, sonido, elementos periféricos, consumibles entre otros.
                            </td>

                            <td>
                                Certificación de ser distribuidor autorizado y/o fabricante.
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <strong>Material de Empaque de Primer Contacto:</strong>
                                Empresas que abastecen a las plantas de empaque con insumos como:
                                Canastilla plástica, cajas de cartón y alveolos.
                            </td>

                            <td>
                                Certificado de metales Pesados y microbiológicos, Certificado migración de tintas, Carta
                                garantía de inocuidad, Certificado de inocuidad, Certificado de no Alérgenos.
                                Trazabilidad.
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <strong>Material de Empaque de No Contacto:</strong>
                                Empresas que abastecen a las plantas de empaque con insumos como:
                                Estibas, Esquineros, Zuncho, Grapa.
                            </td>

                            <td>
                                Estibas: Certificado Norma NIMF 15, Certificado ICA, Ficha técnica Esquinero: Ficha
                                Técnica,
                                Certificado de Análisis Metales Pesados y Microbiológicos Zuncho y Grapa: Certificado de
                                Calidad, Ficha Técnica.
                            </td>
                        </tr>

                    </table>
                    <!--============= CARGAR DOCUMENTOS REQUERIDOS ===============-->
                    <table class="tabla-corporativa mt-4">

                        <tr class="titulo-seccion">
                            <td colspan="3">CARGAR DOCUMENTOS REQUERIDOS</td>
                        </tr>

                        @foreach ($this->documentosRequeridos() as $index => $doc)
                            @php
                                $registro = \App\Models\TerceroDocumento::where('tercero_id', $tercero->id)
                                    ->where('tipo_documento', $doc)
                                    ->first();
                            @endphp

                            <tr>
                                <td style="width:40%">
                                    <strong>{{ $doc }}</strong>
                                </td>
                                <td style="width:40%">
                                    @if ($registro && $registro->cargado)
                                        <div class="d-flex gap-2 align-items-center">
                                            <button class="btn btn-sm btn-danger"
                                                wire:click="eliminarDocumento({{ $registro->id }})"
                                                wire:loading.attr="disabled"
                                                wire:target="eliminarDocumento({{ $registro->id }})">

                                                <span wire:loading.remove
                                                    wire:target="eliminarDocumento({{ $registro->id }})">
                                                    Eliminar
                                                </span>

                                                <span wire:loading
                                                    wire:target="eliminarDocumento({{ $registro->id }})">
                                                    Eliminando...
                                                </span>

                                            </button>
                                        </div>
                                    @else
                                        <input type="file" wire:model="archivos.{{ $index }}"
                                            class="input-corporativo">
                                    @endif
                                    <div wire:loading wire:target="archivos.{{ $index }}"
                                        class="text-red mb-1">
                                        Subiendo archivo...
                                    </div>
                                </td>

                                <td style="width:20%" class="text-center">
                                    @if ($registro && $registro->cargado)
                                        <span class="badge bg-success">Cargado</span>
                                    @else
                                        <span class="badge bg-secondary">Pendiente</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="3" class="text-center p-3">
                                <button class="btn btn-contacto-agregar" wire:click="subirArchivos"
                                    wire:loading.attr="disabled">
                                    <span wire:loading.remove wire:target="subirArchivos">
                                        Enviar Archivos
                                    </span>
                                    <span wire:loading wire:target="subirArchivos">
                                        Procesando...
                                    </span>
                                </button>
                            </td>
                        </tr>
                    </table>
                    <!--============= CARGAR DOCUMENTOS ADICIONALES ===============-->
                    <table class="tabla-corporativa mt-4">
                        <tr class="titulo-seccion">
                            <td colspan="3">AGREGAR DOCUMENTOS ADICIONALES</td>
                        </tr>

                        <tr>
                            <td>
                                <input class="input-corporativo" placeholder="Nombre del documento"
                                    wire:model="nuevoDocumentoNombre">
                            </td>

                            <td>
                                <input type="file" class="input-corporativo" wire:model="nuevoDocumentoArchivo">
                                <div wire:loading wire:target="nuevoDocumentoArchivo" class="text-red mb-1">
                                    Subiendo archivo...
                                </div>
                            </td>

                            <td class="text-center">
                                <button class="btn btn-contacto-agregar" wire:click="agregarDocumentoAdicional"
                                    wire:loading.attr="disabled">

                                    <span wire:loading.remove wire:target="agregarDocumentoAdicional">
                                        + Subir
                                    </span>

                                    <span wire:loading wire:target="agregarDocumentoAdicional">
                                        Subiendo...
                                    </span>
                                </button>
                            </td>
                        </tr>
                    </table>
                    <!--============= DOCUMENTOS CARGADOS ===============-->
                    <table class="tabla-corporativa mt-4">

                        <tr class="titulo-seccion">
                            <td colspan="3">DOCUMENTOS CARGADOS</td>
                        </tr>

                        @foreach (\App\Models\TerceroDocumento::where('tercero_id', $tercero->id)->get() as $doc)
                            <tr>
                                <td style="width:50%">
                                    {{ $doc->tipo_documento }}
                                </td>

                                <td style="width:30%" class="text-center">
                                    @if ($doc->archivo)
                                        <a href="{{ Storage::url($doc->archivo) }}" target="_blank">
                                            Ver archivo
                                        </a>
                                    @endif
                                </td>

                                <td style="width:20%" class="text-center">
                                    @if (!$doc->obligatorio)
                                        <button class="btn btn-sm btn-danger"
                                            wire:click="eliminarDocumentoAdicional({{ $doc->id }})"
                                            wire:loading.attr="disabled"
                                            wire:target="eliminarDocumentoAdicional({{ $doc->id }})">

                                            <span wire:loading.remove
                                                wire:target="eliminarDocumentoAdicional({{ $doc->id }})">
                                                Eliminar
                                            </span>

                                            <span wire:loading
                                                wire:target="eliminarDocumentoAdicional({{ $doc->id }})">
                                                Eliminando...
                                            </span>

                                        </button>

                                        <div wire:loading
                                            wire:target="eliminarDocumentoAdicional({{ $doc->id }})"
                                            class="text-danger small mt-1">
                                            Eliminando archivo...
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </table>
                    <br>
                    <!-- ==================== SECCIÓN FIRMA ==================== -->
                    <table class="tabla-corporativa mt-4">

                        <tr class="titulo-seccion">
                            <td colspan="4">
                                FIRMA DEL FORMULARIO
                            </td>
                        </tr>

                        <tr>
                            <td class="label-td">Documento de Identidad</td>
                            <td>
                                <input class="input-corporativo" value="{{ $tercero->identificacion }}" disabled>
                            </td>

                            <td class="label-td">Nombre Completo</td>
                            <td>
                                <input class="input-corporativo"
                                    value="{{ $tercero->tipo == 'juridica' ? $datos['razon_social'] ?? '' : $datos['nombre_completo'] ?? '' }}"
                                    disabled>
                            </td>
                        </tr>

                        <!-- ===================== ESTADO ACTUAL DE FIRMA ===================== -->

                        {{-- @if ($this->yaFirmado())

                            <tr>
                                <td colspan="4" class="text-center p-4">

                                    <div class="alert alert-success">
                                        ✔ Este formulario ya se encuentra firmado correctamente.
                                    </div>

                                    <strong>Tipo de firma registrada:</strong>
                                    {{ $firmaDigitalActual ? 'Firma Dibujada' : 'Firma Escaneada' }}

                                    <div class="mt-3">

                                        @if ($firmaDigitalActual)
                                            <strong>Firma digital registrada:</strong><br>
                                            <img src="{{ Storage::url($firmaDigitalActual->archivo) }}"
                                                style="max-width:400px; border:1px solid #ccc; padding:5px">
                                            <br>
                                            <button class="btn btn-danger" wire:click="eliminarFirma('digital')"
                                                wire:loading.attr="disabled">
                                                <span wire:loading.remove wire:target="eliminarFirma">
                                                    Eliminar firma digital
                                                </span>
                                                <span wire:loading wire:target="eliminarFirma">
                                                    Eliminando...
                                                </span>
                                            </button>
                                        @endif

                                        @if ($firmaEscaneadaActual)
                                            <strong>Firma escaneada registrada:</strong><br>
                                            <img src="{{ Storage::url($firmaEscaneadaActual->archivo) }}"
                                                style="max-width:400px; border:1px solid #ccc; padding:5px">
                                            <br>
                                            <button class="btn btn-danger" wire:click="eliminarFirma('escaneada')"
                                                wire:loading.attr="disabled">
                                                <span wire:loading.remove wire:target="eliminarFirma">
                                                    Eliminar firma escaneada
                                                </span>
                                                <span wire:loading wire:target="eliminarFirma">
                                                    Eliminando...
                                                </span>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @else --}}

                        @if ($this->yaFirmado())

                            <tr>
                                <td colspan="4" class="text-center p-4">

                                    <div class="alert alert-success">
                                        ✔ Este formulario ya se encuentra firmado correctamente.
                                    </div>

                                    <strong>Tipo de firma registrada:</strong>
                                    {{ $firmaDigitalActual ? 'Firma Dibujada' : 'Firma Escaneada' }}

                                    <div class="mt-3">

                                        @if ($firmaDigitalActual)
                                            <strong>Firma digital registrada:</strong><br>
                                            <img src="{{ Storage::url($firmaDigitalActual->archivo) }}"
                                                style="max-width:400px; border:1px solid #ccc; padding:5px">
                                            <br>

                                            @if (!$this->yaEnviado())
                                                <button class="btn btn-danger" wire:click="eliminarFirma('digital')"
                                                    wire:loading.attr="disabled">
                                                    <span wire:loading.remove wire:target="eliminarFirma">
                                                        Eliminar firma digital
                                                    </span>
                                                    <span wire:loading wire:target="eliminarFirma">
                                                        Eliminando...
                                                    </span>
                                                </button>
                                            @endif

                                        @endif

                                        @if ($firmaEscaneadaActual)
                                            <strong>Firma escaneada registrada:</strong><br>
                                            <img src="{{ Storage::url($firmaEscaneadaActual->archivo) }}"
                                                style="max-width:400px; border:1px solid #ccc; padding:5px">
                                            <br>

                                            @if (!$this->yaEnviado())
                                                <button class="btn btn-danger" wire:click="eliminarFirma('escaneada')"
                                                    wire:loading.attr="disabled">
                                                    <span wire:loading.remove wire:target="eliminarFirma">
                                                        Eliminar firma escaneada
                                                    </span>
                                                    <span wire:loading wire:target="eliminarFirma">
                                                        Eliminando...
                                                    </span>
                                                </button>
                                            @endif

                                        @endif
                                    </div>
                                    {{-- ================= NUEVA SECCIÓN DE ENVÍO ================= --}}
                                    <br>
                                    @if (!$this->yaEnviado())
                                        <div class="alert alert-info mt-4">
                                            El formulario está firmado pero aún no ha sido enviado oficialmente.
                                        </div>

                                        <button class="btn btn-lg btn-success mt-2" wire:click="enviarFormulario"
                                            wire:loading.attr="disabled">

                                            <span wire:loading.remove wire:target="enviarFormulario">
                                                📤 Enviar Documento
                                            </span>

                                            <span wire:loading wire:target="enviarFormulario">
                                                Enviando...
                                            </span>

                                        </button>
                                    @else
                                        <div class="alert alert-primary mt-4">
                                            📌 Este formulario ya fue ENVIADO oficialmente y se encuentra bloqueado para
                                            modificaciones.
                                        </div>
                                    @endif

                                </td>
                            </tr>
                        @else
                            <!-- ===================== VALIDACIONES PARA PODER FIRMAR ===================== -->

                            @if (!$this->puedeFirmar())

                                <tr>
                                    <td colspan="4" class="text-center p-4">

                                        <div class="alert alert-warning">

                                            Para poder firmar debe cumplir los siguientes requisitos:

                                            <ul class="mt-2 text-start">
                                                <li>El progreso del formulario debe estar al 100%</li>
                                                <li>Todos los documentos obligatorios deben estar cargados</li>
                                            </ul>

                                        </div>

                                    </td>
                                </tr>
                            @else
                                <!-- ===================== SELECCIÓN TIPO FIRMA ===================== -->

                                <tr>
                                    <td colspan="4" class="text-center p-3">
                                        <select class="input-corporativo" wire:model.live="tipoFirma"
                                            style="max-width:300px">
                                            <option value="">Elija el tipo de firma</option>
                                            <option value="digital">FIRMA DIBUJO</option>
                                            <option value="escaneada">FIRMA IMAGEN</option>
                                        </select>
                                    </td>
                                </tr>

                                <!-- ===================== FIRMA DIBUJADA ===================== -->

                                @if ($tipoFirma == 'digital')
                                    <tr>
                                        <td colspan="4" class="text-center p-3">

                                            <div wire:ignore>
                                                <canvas id="canvasFirma"
                                                    style="border:2px solid #000; width:500px; height:200px;">
                                                </canvas>
                                            </div>

                                            <input type="hidden" wire:model="firmaDibujo" id="firmaDibujo">

                                            <div class="mt-2">
                                                <button type="button" class="btn btn-danger"
                                                    onclick="limpiarFirma()">
                                                    Limpiar Firma
                                                </button>

                                                <button type="button" class="btn btn-contacto-agregar"
                                                    onclick="guardarFirmaCanvas()">
                                                    Firmar
                                                </button>
                                            </div>
                                            <small class="d-block mt-2 text-muted">
                                                Dibuje su firma dentro del recuadro y luego presione "Firmar"
                                            </small>
                                        </td>
                                    </tr>
                                @endif

                                <!-- ===================== FIRMA ESCANEADA ===================== -->

                                @if ($tipoFirma == 'escaneada')
                                    <tr>
                                        <td colspan="4" class="text-center p-3">
                                            <input type="file" wire:model="firmaImagen" class="input-corporativo"
                                                accept=".jpg,.jpeg,.png">
                                            <small class="d-block mt-2 text-muted">
                                                Archivos permitidos: jpg, jpeg, png
                                            </small>
                                            <br>
                                            <div wire:loading wire:target="firmaImagen" class="text-red mt-2">
                                                Cargando imagen...
                                            </div>
                                            <br>
                                            <div class="mt-2">
                                                <button class="btn btn-contacto-agregar"
                                                    wire:click="guardarFirmaImagen" wire:loading.attr="disabled">
                                                    <span wire:loading.remove wire:target="guardarFirmaImagen">
                                                        Guardar Firma
                                                    </span>
                                                    <span wire:loading wire:target="guardarFirmaImagen">
                                                        Guardando...
                                                    </span>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            @endif
                        @endif
                    </table>
                </div>
                @push('scripts')
                    <script>
                        function iniciarCanvasFirma() {

                            let canvas = document.getElementById("canvasFirma");

                            if (!canvas) return;

                            let ctx = canvas.getContext("2d");

                            canvas.width = 500;
                            canvas.height = 200;

                            ctx.lineWidth = 2;
                            ctx.lineCap = "round";
                            ctx.strokeStyle = "#000";

                            let drawing = false;

                            canvas.onmousedown = function(e) {
                                drawing = true;
                                ctx.beginPath();
                                ctx.moveTo(e.offsetX, e.offsetY);
                            };

                            canvas.onmousemove = function(e) {
                                if (drawing) {
                                    ctx.lineTo(e.offsetX, e.offsetY);
                                    ctx.stroke();
                                }
                            };

                            canvas.onmouseup = function() {
                                drawing = false;
                            };

                            canvas.onmouseleave = function() {
                                drawing = false;
                            };
                        }

                        function limpiarFirma() {

                            let canvas = document.getElementById("canvasFirma");

                            if (!canvas) return;

                            let ctx = canvas.getContext("2d");

                            ctx.clearRect(0, 0, canvas.width, canvas.height);

                            document.getElementById("firmaDibujo").value = "";
                        }

                        function guardarFirmaCanvas() {

                            let canvas = document.getElementById("canvasFirma");

                            if (!canvas) return;

                            let ctx = canvas.getContext("2d");

                            // Verificar si el canvas está en blanco
                            let blank = document.createElement('canvas');
                            blank.width = canvas.width;
                            blank.height = canvas.height;

                            if (canvas.toDataURL() === blank.toDataURL()) {
                                Swal.fire({
                                    icon: 'warning',
                                    text: 'No se identifico la firma',
                                    timer: 2500,
                                    showConfirmButton: false
                                });
                                return;
                            }

                            let dataURL = canvas.toDataURL("image/png");

                            document.getElementById("firmaDibujo").value = dataURL;

                            // Enviar directamente el string a Livewire
                            Livewire.dispatch('setFirmaDibujo', [dataURL]);
                        }

                        // ===== EVENTOS CORRECTOS PARA LIVEWIRE 3 =====

                        document.addEventListener('livewire:initialized', () => {
                            iniciarCanvasFirma();
                        });

                        Livewire.hook('morph.updated', () => {
                            iniciarCanvasFirma();
                        });

                        window.addEventListener('toast-ok', e => {
                            Swal.fire({
                                icon: 'success',
                                text: e.detail.msg,
                                timer: 2500,
                                showConfirmButton: false
                            });
                        });

                        window.addEventListener('toast-error', e => {
                            Swal.fire({
                                icon: 'error',
                                text: e.detail.msg,
                                timer: 2500,
                                showConfirmButton: false
                            });
                        });

                        function motivoRechazo() {
                            Swal.fire({
                                title: 'Motivo del rechazo',
                                input: 'textarea',
                                showCancelButton: true,
                                confirmButtonText: 'Rechazar'
                            }).then(res => {
                                if (res.isConfirmed) {
                                    Livewire.find(@this.__instance.id)
                                        .rechazar(res.value)
                                }
                            })
                        }
                    </script>
                @endpush
            </div>
        </x-layouts.contrapartes-externo>
    </fieldset>
    @if ($modo == 'auditoria')
        {{-- <div class="card mt-4 shadow-sm">
            <div class="card-body text-center">

                @if ($tercero->estado == 'enviado')
                    <h5 class="mb-3">Acciones de Auditoría</h5>

                    <p class="text-muted">
                        Este formulario se encuentra pendiente de revisión.
                        Selecciona una acción para continuar.
                    </p>

                    @can('aprobar formularios')
                        <div class="d-flex justify-content-center gap-3 mt-3">

                            <button class="btn btn-success btn-lg px-4" wire:click="aprobar">
                                <i class="la la-check-circle"></i>
                                Aprobar formulario
                            </button>

                            <button class="btn btn-danger btn-lg px-4" onclick="motivoRechazo()">
                                <i class="la la-times-circle"></i>
                                Rechazar formulario
                            </button>

                        </div>
                    @endcan
                @else
                    <div class="alert alert-info text-center mb-3">

                        <h5 class="mb-1">Estado del formulario</h5>

                        <p class="mb-0">
                            Este formulario no se encuentra pendiente de auditoría.
                            Estado actual:
                            <strong>{{ ucfirst(str_replace('_', ' ', $tercero->estado)) }}</strong>
                        </p>

                    </div>
                @endif
                <div class="mt-4">
                    <a href="{{ url('/admin/terceros') }}" class="btn btn-outline-secondary">
                        ← Volver al listado
                    </a>
                </div>
            </div>
        </div> --}}

        <div class="audit-card">

            @if ($tercero->estado == 'enviado')
                <h3 class="audit-title">Acciones de Auditoría</h3>

                <p class="audit-subtitle">
                    Este formulario se encuentra pendiente de revisión.
                    Selecciona una acción para continuar.
                </p>

                @can('aprobar formularios')
                    <div class="audit-actions">

                        <button class="btn-approve" wire:click="aprobar">
                            <i class="la la-check-circle"></i>
                            <span>Aprobar formulario</span>
                        </button>

                        <button class="btn-reject" onclick="motivoRechazo()">
                            <i class="la la-times-circle"></i>
                            <span>Rechazar formulario</span>
                        </button>

                    </div>
                @endcan
            @else
                <div class="status-box">
                    <h4>Estado del formulario</h4>
                    <p>
                        Este formulario no se encuentra pendiente de auditoría.<br>
                        Estado actual:
                        <strong>{{ ucfirst(str_replace('_', ' ', $tercero->estado)) }}</strong>
                    </p>
                </div>
            @endif

            <div class="back-container">
                <a href="{{ url('/admin/terceros') }}" class="btn-back">
                    ← Volver al listado
                </a>
            </div>

        </div>

    @endif
</div>
