    <div>
        <div x-data="entregas">

            <div class="content-wrapper p-3">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="mb-0">📦 Entregas</h4>
                    <div class="d-flex gap-2">
                        @if (!$tieneFirma)
                            <button class="btn btn-dark" @click="openFirmaResponsable()">
                                <i class="la la-signature"></i>
                                Firma Responsable
                            </button>
                        @endif
                        <button class="btn btn-dark" @click="openForm()"
                            @if (!$tieneFirma) disabled @endif>
                            <i class="la la-plus"></i>
                            Nueva Entrega
                        </button>
                    </div>
                </div>

                <div class="card">
                    <div x-show="!loading">
                        <x-table id="table_entregas">
                            <tr>
                                <th>ID</th>
                                <th>Usuario</th>
                                <th>Responsable</th>
                                <th>Tipo</th>
                                <th>Items</th>
                                <th>Estado</th>
                                <th>Fecha</th>
                                <th>Acc</th>
                            </tr>
                        </x-table>
                    </div>

                    <div x-show="loading">
                        <x-spinner />
                    </div>
                </div>
            </div>

            {{-- MODAL --}}
            <x-modal id="modal_firma_responsable">

                <x-slot name="title">
                    Agregar Firma
                </x-slot>

                <div>
                    <div class="text-center" wire:ignore>
                        <canvas id="signature-pad" style="border:2px solid #000; width:100%; height:250px;">
                        </canvas>

                        <div class="mt-3">
                            <button class="btn btn-outline-secondary btn-sm" @click="clearCanvas()">
                                Limpiar
                            </button>
                        </div>
                    </div>

                </div> {{-- ← ESTE DIV FALTABA CERRARLO CORRECTAMENTE --}}

                <x-slot name="footer">
                    <button class="btn btn-outline-secondary" data-dismiss="modal">
                        Cancelar
                    </button>

                    <button class="btn btn-outline-primary" @click="saveFirma()">
                        Confirmar Firma
                    </button>
                </x-slot>

            </x-modal>

            @include('livewire.admin.form-entregas')

            @script
                <script>
                    Alpine.data('entregas', () => ({

                        entregas: [],
                        loading: true,
                        loading_form: false,
                        modal_loading: false,


                        // -----------Canvas----------------
                        canvas: null,
                        ctx: null,
                        drawing: false,

                        init() {
                            this.getEntregas();

                            Livewire.on('toast', (data) => {
                                toastRight(data.type, data.message);
                            });

                            $('#user_select').select2({
                                dropdownParent: $('#form_entregas')
                            }).on('change', function() {
                                @this.set('user_id', $(this).val());
                            });

                            // RESET CUANDO SE CIERRE EL MODAL
                            $('#form_entregas').on('hidden.bs.modal', () => {
                                @this.call('limpiar');
                            });
                        },


                        // --------------------------funciones canvas------------------
                        openFirmaResponsable(id) {
                            $('#modal_firma_responsable').modal('show');

                            // Espera al evento shown.bs.modal en vez de un timeout fijo
                            $('#modal_firma_responsable').one('shown.bs.modal', () => {
                                this.initCanvas();
                            });
                        },

                        initCanvas() {
                            this.canvas = document.getElementById('signature-pad');
                            if (!this.canvas) return;

                            // NO uses devicePixelRatio en el transform si vas a calcular
                            // posiciones con getBoundingClientRect (ya están en coordenadas CSS)
                            this.canvas.width = this.canvas.offsetWidth;
                            this.canvas.height = this.canvas.offsetHeight;

                            this.ctx = this.canvas.getContext('2d');
                            this.ctx.lineWidth = 2;
                            this.ctx.lineCap = "round";
                            this.ctx.strokeStyle = "#000";

                            this.bindEvents();
                        },

                        bindEvents() {

                            const getPosition = (e) => {
                                const rect = this.canvas.getBoundingClientRect();

                                if (e.touches) {
                                    return {
                                        x: e.touches[0].clientX - rect.left,
                                        y: e.touches[0].clientY - rect.top
                                    }
                                }

                                return {
                                    x: e.clientX - rect.left,
                                    y: e.clientY - rect.top
                                }
                            };

                            this.canvas.onmousedown = (e) => {
                                this.drawing = true;
                                const pos = getPosition(e);
                                this.ctx.beginPath();
                                this.ctx.moveTo(pos.x, pos.y);
                            };

                            this.canvas.onmousemove = (e) => {
                                if (!this.drawing) return;
                                const pos = getPosition(e);
                                this.ctx.lineTo(pos.x, pos.y);
                                this.ctx.stroke();
                            };

                            this.canvas.onmouseup = () => this.drawing = false;
                            this.canvas.onmouseleave = () => this.drawing = false;

                            this.canvas.ontouchstart = (e) => {
                                e.preventDefault();
                                this.drawing = true;
                                const pos = getPosition(e);
                                this.ctx.beginPath();
                                this.ctx.moveTo(pos.x, pos.y);
                            };

                            this.canvas.ontouchmove = (e) => {
                                e.preventDefault();
                                if (!this.drawing) return;
                                const pos = getPosition(e);
                                this.ctx.lineTo(pos.x, pos.y);
                                this.ctx.stroke();
                            };

                            this.canvas.ontouchend = () => this.drawing = false;
                        },

                        clearCanvas() {
                            if (!this.ctx) return;
                            this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
                        },

                        isBlank() {
                            const blank = document.createElement('canvas');
                            blank.width = this.canvas.width;
                            blank.height = this.canvas.height;
                            return this.canvas.toDataURL() === blank.toDataURL();
                        },

                        saveFirma() {

                            if (this.isBlank()) {
                                toastRight('warning', 'Debe firmar antes de continuar.');
                                return;
                            }

                            @this.set('firma_base64', this.canvas.toDataURL('image/png'));
                            @this.call('firmar_responsable');

                            $('#modal_firma_responsable').modal('hide');
                        },


                        // --------------------------fin canvas----------------------------

                        async getEntregas() {

                            this.loading = true;
                            $('#body_table_entregas').html('');

                            this.entregas = await @this.getEntregas();

                            for (const e of this.entregas) {
                                this.addEntrega(e);
                            }

                            setTimeout(() => {
                                __resetTable('#table_entregas');
                                this.loading = false;
                            }, 400);
                        },

                        addEntrega(entrega, is_update = false) {

                            let estadoBadge = entrega.estado === 'finalizada' ?
                                `<span class="text-success">Finalizada</span>` :
                                `<span class="text-warning">Pendiente</span>`;

                            let tr = `
                                        <td>${entrega.id}</td>
                                        <td>${entrega.usuario}</td>
                                        <td>${entrega.responsable}</td>
                                        <td>${entrega.tipo}</td>
                                        <td>${entrega.items_count}</td>
                                        <td>${estadoBadge}</td>
                                        <td>${entrega.fecha}</td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                ${
                                                    entrega.estado === 'pendiente_firma'
                                                    ? `
                                                                                                                                        <x-buttonsm  class="btn btn-sm btn-outline-primary"
                                                                                                                                            click="openForm('${entrega.id}')">
                                                                                                                                            <i class="la la-edit"></i>
                                                                                                                                        </x-buttonsm>
                                                                                                                                    `
                                                    : ''
                                                }

                                                ${
                                                    entrega.estado === 'finalizada'
                                                    ? `
                                                                                                                                        <button class="btn btn-sm btn-outline-success"
                                                                                                                                            @click="$wire.generarPdf(${entrega.id})">
                                                                                                                                            PDF
                                                                                                                                        </button>
                                                                                                                                    `
                                                    : ''
                                                }
                                            </div>
                                        </td>
                                    `;

                            if (!is_update) {
                                $('#body_table_entregas').prepend(`<tr id="entrega_${entrega.id}">${tr}</tr>`);
                            } else {
                                $('#entrega_' + entrega.id).html(tr);
                            }
                        },

                        openForm(entrega_id = null) {

                            this.modal_loading = true;

                            // limpiar primero
                            @this.editing = false;
                            @this.entrega_id = null;
                            @this.user_id = null;
                            @this.observaciones = null;
                            @this.items = [];

                            $('#form_entregas').modal('show');

                            if (!entrega_id) {

                                this.modal_loading = false;
                                $('#user_select').val(null).trigger('change');
                                return;
                            }

                            let entrega = this.entregas.find(e => e.id == entrega_id) ?? {};

                            @this.editing = true;
                            @this.entrega_id = entrega.id ?? null;
                            @this.user_id = entrega.user_id ?? null;
                            @this.observaciones = entrega.observaciones ?? null;
                            @this.items = entrega.items ?? [];

                            setTimeout(() => {
                                $('#user_select').val(@this.user_id).trigger('change');
                                this.modal_loading = false;
                            }, 200);
                        },

                        async saveFront() {

                            this.loading_form = true;

                            const is_update = @this.entrega_id ? true : false;
                            const entrega = await @this.save();

                            if (entrega) {

                                if (is_update) {

                                    // actualizar en memoria
                                    for (let i in this.entregas) {
                                        if (this.entregas[i].id == entrega.id) {
                                            this.entregas[i] = entrega;
                                        }
                                    }

                                    this.addEntrega(entrega, true);

                                } else {

                                    this.entregas.unshift(entrega);
                                    this.addEntrega(entrega, false);
                                }

                                $('#form_entregas').modal('hide');

                                toastRight('success',
                                    is_update ? 'Entrega actualizada' : 'Entrega creada'
                                );
                            }

                            this.loading_form = false;
                        }

                    }));
                </script>
            @endscript
        </div>
    </div>
