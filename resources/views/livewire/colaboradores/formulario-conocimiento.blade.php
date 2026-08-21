<div>
    <style>
        .conocimiento-form { max-width: 1180px; margin: 24px auto; }
        .conocimiento-form .form-card { background: #fff; padding: 28px; border-radius: 16px; box-shadow: 0 10px 30px rgba(0, 0, 0, .08); }
        .conocimiento-form .form-header { border-bottom: 3px solid #173f5f; padding-bottom: 16px; }
        .conocimiento-form .form-header h4 { color: #173f5f; font-weight: 700; }
        .conocimiento-form .tabla-corporativa { width: 100%; border-collapse: collapse; table-layout: fixed; }
        .conocimiento-form .tabla-corporativa td { border: 1px solid #cbd5df; padding: 10px; vertical-align: middle; word-wrap: break-word; }
        .conocimiento-form .titulo-seccion { background: #173f5f; color: #fff; text-align: center; font-weight: 700; }
        .conocimiento-form .titulo-seccion td { color: #fff !important; }
        .conocimiento-form .label-td { background: #f5f8fa; color: #34495e; font-weight: 600; width: 25%; }
        .conocimiento-form .input-corporativo { width: 100%; border: 1px solid #b9c7d3; border-radius: 4px; padding: 8px; background: #fff; }
        .conocimiento-form .input-corporativo:focus { border-color: #2f9c95; outline: 0; box-shadow: 0 0 0 2px rgba(47, 156, 149, .15); }
        .conocimiento-form .declaracion { text-align: justify; line-height: 1.55; background: #fbfcfd; }
        .conocimiento-form .firma-manuscrita { width: 100%; height: 180px; border: 2px dashed #173f5f; background: #fff; touch-action: none; }
        .conocimiento-form .firma-preview { max-width: 260px; max-height: 100px; border: 1px solid #cbd5df; padding: 8px; }
        .conocimiento-form .money-cell { display: flex; align-items: center; gap: 6px; }
        .conocimiento-form .money-cell span { font-weight: 700; color: #173f5f; }
        .conocimiento-form .check-option { display: inline-flex; align-items: center; gap: 6px; margin: 0 18px 6px 0; font-weight: 400; }
        .conocimiento-form .check-option input { margin: 0; flex: 0 0 auto; }
        .conocimiento-form .check-option { vertical-align: middle; line-height: 1.35; }
        .conocimiento-form .tabla-corporativa td label { display: inline-flex; align-items: center; gap: 6px; margin: 0 18px 4px 0; cursor: pointer; }
        .conocimiento-form .tabla-corporativa td label input[type="radio"],
        .conocimiento-form .tabla-corporativa td label input[type="checkbox"] { margin: 0; flex: 0 0 auto; }
        .conocimiento-form .authorization-options { display: flex; flex-wrap: nowrap; align-items: flex-start; width: 100%; gap: 6px 18px; padding: 10px 18px; }
        .conocimiento-form .authorization-options .check-option { margin: 0; flex: 1 1 0; min-width: 0; white-space: normal; }
        @media (max-width: 768px) {
            .conocimiento-form .authorization-options { flex-wrap: wrap; }
            .conocimiento-form .authorization-options .check-option { flex: 1 1 100%; }
        }
        .conocimiento-form .firma-visible { max-width: 320px; max-height: 140px; border: 1px solid #cbd5df; padding: 8px; background: #fff; }
        .conocimiento-form .audit-card { background: #fff; border-radius: 16px; padding: 28px; margin-top: 25px; box-shadow: 0 10px 30px rgba(0, 0, 0, .08); text-align: center; }
        .conocimiento-form .audit-title { color: #173f5f; font-weight: 700; }
        .conocimiento-form .audit-subtitle { color: #6b7280; }
        .conocimiento-form .audit-actions { display: flex; justify-content: center; gap: 16px; flex-wrap: wrap; }
        .conocimiento-form .audit-actions button { border: 0; border-radius: 8px; padding: 12px 20px; color: #fff; font-weight: 600; }
        .conocimiento-form .btn-approve { background: #198754; }
        .conocimiento-form .btn-reject { background: #dc3545; }
        .conocimiento-form .status-box { background: #f1f5f9; padding: 16px; border-radius: 8px; }
        .conocimiento-form .back-container { margin-top: 22px; }
        .conocimiento-form .btn-back { color: #173f5f; }
        @media (max-width: 768px) {
            .conocimiento-form { margin: 8px auto; }
            .conocimiento-form .form-card { padding: 12px; border-radius: 10px; }
            .conocimiento-form .tabla-corporativa, .conocimiento-form .tabla-corporativa tbody,
            .conocimiento-form .tabla-corporativa tr, .conocimiento-form .tabla-corporativa td {
                display: block; width: 100% !important;
            }
            .conocimiento-form .tabla-corporativa tr { margin-bottom: 2px; }
            .conocimiento-form .tabla-corporativa td { border-left: 0; border-right: 0; }
            .conocimiento-form .tabla-corporativa td + td { border-top: 0; }
            .conocimiento-form .label-td { border-bottom: 0; padding-bottom: 4px; }
            .conocimiento-form .titulo-seccion td { padding: 8px 10px; }
            .conocimiento-form .money-cell { flex-wrap: nowrap; }
            .conocimiento-form .firma-manuscrita { height: 150px; }
            .conocimiento-form .audit-actions { flex-direction: column; }
            .conocimiento-form .audit-actions button { width: 100%; }
        }
        @media print { .conocimiento-form .form-card { box-shadow: none; } .conocimiento-form button, .no-print { display: none !important; } .conocimiento-form .input-corporativo { border: 0; } }
    </style>

    <div class="conocimiento-form" x-data="conocimientoFormulario">
        <div class="form-card">
            <div class="form-header text-center mb-3">
                <img src="{{ asset('img-logisticarga/logo.png') }}" style="max-width:220px" alt="Logisticarga">
                <h4 class="mt-3">FORMULARIO DE CONOCIMIENTO DE COLABORADORES</h4>
                <div class="row small text-muted"><div class="col-md-4">Código: SAG-FTO-01</div><div class="col-md-4">Versión: V - 02</div><div class="col-md-4">Edición: 22/04/2026</div></div>
            </div>

            <p class="declaracion p-3">El diligenciamiento del presente formulario permite realizar el proceso de conocimiento de la contraparte de colaboradores y la debida diligencia, de acuerdo con el SAGRILAFT y la normativa legal vigente en materia de prevención de Lavado de Activos, Financiación del Terrorismo, Financiación de Proliferación de Armas de Destrucción Masiva, Corrupción, Soborno y Fraude.</p>

            @if ($formulario?->estado === 'aprobado')
                <div class="alert alert-success d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <span>Su formulario fue aprobado.@if ($modo === 'colaborador' && !$edicionHabilitada) El formulario está bloqueado para edición.@endif</span>
                    @if ($modo === 'colaborador' && !$edicionHabilitada)
                        <button type="button" class="btn btn-outline-success btn-sm no-print text-dark" x-on:click="await $wire.habilitarEdicion(); await $nextTick(); reinicializarFirma()">Habilitar edición</button>
                    @endif
                </div>
            @elseif ($formulario?->estado === 'enviado')<div class="alert alert-info">Su formulario está pendiente de revisión y se encuentra bloqueado para edición.</div>@elseif ($formulario?->estado === 'rechazado')<div class="alert alert-danger">Su formulario fue rechazado. Corrija la información y envíelo nuevamente.<br><strong>Observaciones:</strong> {{ $formulario->observaciones }}</div>@endif

            <fieldset @disabled($this->soloLectura)>
            <form x-on:submit.prevent="enviarFormulario">
                <table class="tabla-corporativa">
                    <tr><td class="label-td">Fecha de diligenciamiento</td><td><input class="input-corporativo" wire:model="datos.fecha_diligenciamiento" type="date" required></td><td class="label-td">Vinculación / Actualización</td><td><label><input type="radio" wire:model="datos.tipo_diligenciamiento" value="vinculacion"> Vinculación</label><br><label><input type="radio" wire:model="datos.tipo_diligenciamiento" value="actualizacion"> Actualización</label></td></tr>
                    <tr class="titulo-seccion"><td colspan="4">1. DATOS PERSONALES</td></tr>
                    <tr><td class="label-td">Nombres y apellidos completos</td><td colspan="3"><input class="input-corporativo" wire:model="datos.nombres_completos" required></td></tr>
                    <tr><td class="label-td">Tipo de documento</td><td><select class="input-corporativo" wire:model="datos.tipo_documento" required><option value="">Seleccione</option><option>Cédula de ciudadanía</option><option>Cédula de extranjería</option><option>Pasaporte</option></select></td><td class="label-td">Número de documento</td><td><input class="input-corporativo" wire:model="datos.numero_documento" required></td></tr>
                    <tr><td class="label-td">Lugar y fecha de expedición del documento</td><td colspan="3"><input class="input-corporativo" wire:model="datos.expedicion_documento" required></td></tr>
                    <tr><td class="label-td">Fecha de nacimiento</td><td><input class="input-corporativo" wire:model="datos.fecha_nacimiento" type="date" required></td><td class="label-td">Lugar de nacimiento</td><td><input class="input-corporativo" wire:model="datos.lugar_nacimiento" required></td></tr>
                    <tr><td class="label-td">Nacionalidad</td><td colspan="3"><input class="input-corporativo" wire:model="datos.nacionalidad" required></td></tr>

                    <tr class="titulo-seccion"><td colspan="4">2. DATOS DE CONTACTO Y RESIDENCIA</td></tr>
                    <tr><td class="label-td">Dirección de residencia</td><td><input class="input-corporativo" wire:model="datos.direccion" required></td><td class="label-td">Teléfono celular</td><td><input class="input-corporativo" wire:model="datos.telefono" required></td></tr>
                    <tr><td class="label-td">Departamento</td><td><select class="input-corporativo" wire:model.live="datos.departamento" wire:change="cambioDepartamento($event.target.value)" required><option value="">Seleccione</option>@foreach ($departamentos as $departamento)<option value="{{ $departamento }}">{{ $departamento }}</option>@endforeach</select></td><td class="label-td">Municipio</td><td><select class="input-corporativo" wire:model.live="datos.municipio" required><option value="">Seleccione</option>@foreach ($municipios as $municipio)<option value="{{ $municipio }}">{{ $municipio }}</option>@endforeach</select></td></tr>
                    <tr><td class="label-td">Correo electrónico</td><td colspan="3"><input class="input-corporativo" wire:model="datos.correo" type="email"></td></tr>

                    <tr class="titulo-seccion"><td colspan="4">3. DATOS LABORALES/INGRESOS/EGRESOS/PATRIMONIO</td></tr>
                    <tr><td class="label-td">Nombre del Cargo</td><td colspan="3"><input class="input-corporativo" wire:model="datos.cargo" required></td></tr>
                    @foreach ([['salario_mensual','Salario mensual'],['egresos_mensuales','Egresos mensuales'],['otros_ingresos','Otros ingresos mensuales']] as [$campo,$label])<tr><td class="label-td">{{ $label }}</td><td colspan="3"><div class="money-cell"><span>$</span><input class="input-corporativo" x-on:input="formatearPesos($event)" wire:model="datos.{{ $campo }}" inputmode="numeric" required></div></td></tr>@endforeach
                    <tr><td class="label-td">Descripción otros ingresos</td><td colspan="3"><textarea class="input-corporativo" wire:model="datos.descripcion_otros_ingresos"></textarea></td></tr>

                    <tr class="titulo-seccion"><td colspan="4">4. INFORMACIÓN FINANCIERA</td></tr>
                    @foreach ([['total_activos','Total activos'],['total_pasivos','Total pasivos'],['patrimonio_neto','Patrimonio neto']] as [$campo,$label])<tr><td class="label-td">{{ $label }}</td><td colspan="3"><div class="money-cell"><span>$</span><input class="input-corporativo" x-on:input="formatearPesos($event)" wire:model="datos.{{ $campo }}" inputmode="numeric" required></div></td></tr>@endforeach
                    <tr><td class="label-td">¿Posee productos en el exterior?</td><td><label><input type="radio" wire:model="datos.productos_exterior" value="si" required> SI</label><label class="ml-3"><input type="radio" wire:model="datos.productos_exterior" value="no"> NO</label></td><td class="label-td">¿Cuáles?</td><td><input class="input-corporativo" wire:model="datos.cuales_productos_exterior"></td></tr>
                    <tr><td class="label-td">¿Realiza transacciones en moneda extranjera?</td><td colspan="3"><label><input type="radio" wire:model="datos.transacciones_extranjera" value="si" required> SI</label><label class="ml-3"><input type="radio" wire:model="datos.transacciones_extranjera" value="no"> NO</label></td></tr>
                    <tr><td class="label-td">En caso de realizar transacciones en moneda extranjera, indique cuales</td><td colspan="3"><label class="mr-3"><input type="checkbox" wire:model="datos.operaciones_extranjeras" value="Importaciones"> Importaciones</label><label class="mr-3"><input type="checkbox" wire:model="datos.operaciones_extranjeras" value="Exportaciones"> Exportaciones</label><label class="mr-3"><input type="checkbox" wire:model="datos.operaciones_extranjeras" value="Inversiones"> Inversiones</label><label><input type="checkbox" wire:model="datos.operaciones_extranjeras" value="Otra"> Otra</label><input class="input-corporativo mt-2" wire:model="datos.otra_operacion_extranjera" placeholder="Cuál"></td></tr>

                    <tr class="titulo-seccion"><td colspan="4">5. VÍNCULOS POLÍTICOS (PEP) / CONFLICTOS DE INTERÉS</td></tr>
                    @foreach ([['reconocimiento_publico','¿Goza usted de reconocimiento público?'],['pep_cargo_publico','¿Ejerce o ha ejercido algun cargo público en los últimos 2 años?'],['familiares_publicos','¿Tiene familiares en cargos públicos?'],['conflicto_interes','¿Tiene usted algún conflicto de interés con alguna contraparte de logisticarga o de alguna de las empresas para las cuales trabajamos?']] as [$campo,$pregunta])<tr><td class="label-td" colspan="3">{{ $pregunta }}</td><td><label><input type="radio" wire:model="datos.{{ $campo }}" value="si" required> SI</label><label class="ml-3"><input type="radio" wire:model="datos.{{ $campo }}" value="no"> NO</label></td></tr>@endforeach
                    <tr><td class="label-td" colspan="4">Si respondió SI a alguna de las anteriores, describa brevemente:<textarea class="input-corporativo mt-2" wire:model="datos.detalle_vinculos"></textarea></td></tr>

                    <tr class="titulo-seccion"><td colspan="4">6. DECLARACIÓN DE ORIGEN DE FONDOS</td></tr>
                    <tr><td colspan="4" class="declaracion">Declaro: Que los dineros y recursos que manejo y que están a mi nombre NO provienen de ninguna actividad ilícita contemplada en el código penal colombiano o cualquier norma que lo modifique y tampoco son utilizados ni destinados a la financiación del terrorismo, grupos terroristas, actividades terroristas, corrupción y/o soborno, ni ningún otro fin ilícito.</td></tr>
                    <tr><td class="label-td" colspan="4">Declaro que los recursos que poseo provienen de las siguientes fuentes:<textarea class="input-corporativo mt-2" wire:model="datos.fuente_recursos" required></textarea></td></tr>

                    <tr class="titulo-seccion"><td colspan="4">7. AUTORIZACIÓN PARA TRATAMIENTO DE DATOS PERSONALES</td></tr>
                    <tr><td colspan="4" class="declaracion">Por medio del presente documento y de conformidad con lo dispuesto en las normas vigentes sobre protección de datos personales, en especial la Ley 1581 de 2012 y el Decreto 1377 de 2013, autorizo libre, expresa e inequívocamente a LOGISTICARGA S.A.S para que realice la recolección y tratamiento de mis datos personales y declaro que soy conocedor de que tengo el derecho de conocer, actualizar, rectificar mi información, y/o revocar la autorización para el tratamiento. En particular, son derechos de los titulares según se establece en el artículo 8 de la Ley 1581 de 2012: a) Conocer, actualizar y rectificar sus datos personales b) Solicitar prueba de la autorización otorgada c) Ser informado, previa solicitud, respecto del uso que le ha dado a sus datos personales; d) Presentar ante la Superintendencia de Industria y Comercio quejas por infracciones a lo dispuesto en la ley e) Revocar la autorización y/o solicitar la supresión del dato.<br><br><strong>TRATAMIENTO DE DATOS FINANCIEROS:</strong> Autorizo a LOGISTICARGA JM S.A.S. para que con fines estadísticos de verificación del riesgo crediticio o de reporte histórico de comportamiento comercial, solicite, procese, conserve, verifique, consulte, suministre, reporte o actualice cualquier información relacionada con el comportamiento financiero, crediticio o comercial a los operadores de bancos de datos o centrales de información autorizados por la legislación, incluidos DATACRÉDITO y CIFIN, en los términos y durante el tiempo que la ley establezca.<br><br><strong>TRATAMIENTO DE DATOS JUDICIALES:</strong> Teniendo en cuenta lo anterior, autorizo LOGISTICARGA JM S.A.S. de manera voluntaria, previa, explícita e informada, sobre mis aspectos judiciales nacionales como internacionales de acuerdo con el Consejo de Seguridad de las Naciones Unidas, relacionados con el Financiamiento del Terrorismo, en consonancia con el artículo 20 de la Ley 1121 de 2006 y las Recomendaciones GAFI. Como empleado de LOGISTICARGA JM S.A.S. doy mi consentimiento para que me sean practicadas las pruebas de alcohol y drogas, siempre que la organización lo considere necesario y sin necesidad de aviso previo.</td></tr>
                    <tr><td colspan="4" class="declaracion">En caso de requerir actualizar y/o rectificar o solicitar información sobre el uso de sus datos personales lo puede solicitar al correo electrónico: Servicios@logisticargajm.com; o puede comunicarse al teléfono: 311 332 92 37.</td></tr>
                    <tr><td colspan="4"><div class="authorization-options"><label class="check-option"><input type="checkbox" wire:model="autoriza_datos"> <span>Acepto la autorización para tratamiento de datos personales</span></label><label class="check-option"><input type="checkbox" wire:model="autoriza_financieros"> <span>Acepto el tratamiento de datos financieros</span></label><label class="check-option"><input type="checkbox" wire:model="autoriza_judiciales"> <span>Acepto el tratamiento de datos judiciales</span></label></div></td></tr>

                    <tr class="titulo-seccion"><td colspan="4">8. DECLARACIÓN JURAMENTADA Y FIRMAS</td></tr>
                    <tr><td colspan="4" class="declaracion">Con la firma al final del documento, declaro bajo la gravedad de juramento que la información suministrada es veraz, completa y verificable. Autorizo a LOGISTICARGA S.A.S para consultar con mi nombre y cédula en centrales de riesgo, listas restrictivas y demás bases de datos pertinentes en caso de ser necesario y me comprometo a notificar cualquier cambio sustancial en un plazo máximo de 30 días hábiles.</td></tr>
                    <tr><td class="label-td">Firma del colaborador</td><td colspan="3">@if (!$this->firmaDataUri() || !$this->soloLectura)<div wire:ignore><canvas id="firma-colaborador" class="firma-manuscrita"></canvas></div><button type="button" class="btn btn-outline-secondary btn-sm mt-2 no-print" x-on:click="limpiarFirma">Limpiar firma</button>@endif @if ($this->firmaDataUri())<img class="firma-visible d-block mt-2" src="{{ $this->firmaDataUri() }}" alt="Firma manuscrita">@endif</td></tr>
                </table>
                @if ($errors->any())
                    <div class="alert alert-danger mt-3">
                        <strong>No se pudo enviar el formulario:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class="text-center mt-4 no-print"><button class="btn btn-primary btn-lg" type="submit" wire:loading.attr="disabled">Enviar formulario</button></div>
            </form>
            </fieldset>

            @if ($modo === 'auditoria')
                <div class="audit-card no-print">
                    @if ($formulario?->estado === 'enviado')
                        <h4 class="audit-title">Revisión del formulario</h4>
                        <p class="audit-subtitle">El formulario está en modo de solo lectura.</p>
                        @can('aprobar formularios colaboradores')
                            <div class="audit-actions">
                                <button type="button" class="btn-approve" wire:click="aprobar"><i class="la la-check-circle"></i> Aprobar formulario</button>
                                <button type="button" class="btn-reject" onclick="motivoRechazoColaborador(this)"><i class="la la-times-circle"></i> Rechazar formulario</button>
                            </div>
                        @endcan
                    @else
                        <div class="status-box"><strong>Estado:</strong> {{ ucfirst($formulario?->estado ?? 'borrador') }}</div>
                    @endif
                    <div class="back-container"><a href="{{ route('formularios.colaboradores') }}" class="btn-back">Volver al listado</a></div>
                </div>
            @endif
        </div>
    </div>

    @script
    <script>
        Alpine.data('conocimientoFormulario', () => ({
            canvas: null, contexto: null, dibujando: false,
            init() { this.formatearPesosIniciales(); this.reinicializarFirma(); },
            reinicializarFirma() { const c = document.getElementById('firma-colaborador'); if (!c || c === this.canvas) return; this.canvas = c; this.canvas.width = this.canvas.offsetWidth; this.canvas.height = this.canvas.offsetHeight; this.contexto = this.canvas.getContext('2d'); this.contexto.lineWidth = 2; this.contexto.lineCap = 'round'; this.contexto.strokeStyle = '#173f5f'; this.eventosFirma(); },
            formatearPesosIniciales() { this.$el.querySelectorAll('.money-cell input').forEach(input => { if (input.value) this.formatearPesos({ target: input }); }); },
            posicion(e) { const r = this.canvas.getBoundingClientRect(); const p = e.touches ? e.touches[0] : e; return { x: p.clientX - r.left, y: p.clientY - r.top }; },
            iniciar(e) { e.preventDefault(); this.dibujando = true; const p = this.posicion(e); this.contexto.beginPath(); this.contexto.moveTo(p.x, p.y); },
            dibujar(e) { if (!this.dibujando) return; e.preventDefault(); const p = this.posicion(e); this.contexto.lineTo(p.x, p.y); this.contexto.stroke(); },
            eventosFirma() { this.canvas.addEventListener('mousedown', e => this.iniciar(e)); this.canvas.addEventListener('mousemove', e => this.dibujar(e)); this.canvas.addEventListener('mouseup', () => this.dibujando = false); this.canvas.addEventListener('mouseleave', () => this.dibujando = false); this.canvas.addEventListener('touchstart', e => this.iniciar(e), { passive: false }); this.canvas.addEventListener('touchmove', e => this.dibujar(e), { passive: false }); this.canvas.addEventListener('touchend', () => this.dibujando = false); },
            limpiarFirma() { this.contexto.clearRect(0, 0, this.canvas.width, this.canvas.height); @this.set('firmaBase64', ''); },
            formatearPesos(e) { const limpio = e.target.value.replace(/\D/g, ''); const valor = limpio ? new Intl.NumberFormat('es-CO').format(Number(limpio)) : ''; if (e.target.value !== valor) { e.target.value = valor; e.target.dispatchEvent(new Event('input', { bubbles: true })); } },
            estaVacia() { const b = document.createElement('canvas'); b.width = this.canvas.width; b.height = this.canvas.height; return this.canvas.toDataURL() === b.toDataURL(); },
            async enviarFormulario() { if (this.canvas && !this.estaVacia()) await @this.set('firmaBase64', this.canvas.toDataURL('image/png')); await @this.call('enviarFormulario'); }
        }));

        window.addEventListener('toast-ok', evento => Swal.fire({ icon: 'success', text: evento.detail.msg, timer: 2500, showConfirmButton: false }));
        window.addEventListener('toast-error', evento => Swal.fire({ icon: 'error', text: evento.detail.msg, timer: 3000, showConfirmButton: false }));
    </script>
    @endscript

</div>
