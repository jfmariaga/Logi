<div>
    <div x-data="misEntregas()" x-init="init()">

        <div class="content-wrapper p-3">
            <h4 class="mb-3">📦 Mis Entregas</h4>

            <div class="card">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Items</th>
                            <th>Estado</th>
                            <th>Fecha</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($entregas as $e)
                            <tr>
                                <td>{{ $e['id'] }}</td>

                                <td>
                                    <strong>Items para recibir: {{ $e['total_items'] }}</strong>
                                </td>

                                <td>
                                    @if ($e['estado'] === 'pendiente_firma')
                                        <span class="text-warning">Pendiente</span>
                                    @else
                                        <span class="text-success">Finalizada</span>
                                    @endif
                                </td>

                                <td>{{ \Carbon\Carbon::parse($e['created_at'])->format('d/m/Y') }}</td>

                                <td>
                                    @if ($e['estado'] === 'pendiente_firma')
                                        <button class="btn btn-outline-primary btn-sm"
                                            @click="openFirma({{ $e['id'] }})">
                                            Firmar
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- MODAL --}}
        <x-modal id="modal_firma">

            <x-slot name="title">
                Confirmar Recepción
            </x-slot>

            <div>

                <h6 class="mb-3">Detalle de la entrega</h6>

                <ul class="list-group mb-4">
                    @foreach ($detalleEntrega as $item)
                        <li class="list-group-item d-flex justify-content-between">
                            {{ $item['producto'] }}
                            <strong>x{{ $item['cantidad'] }}</strong>
                        </li>
                    @endforeach
                </ul>

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
        @script
            <script>
                Alpine.data('misEntregas', () => ({

                    canvas: null,
                    ctx: null,
                    drawing: false,

                    init() {
                        Livewire.on('toast', (data) => {
                            toastRight(data.type, data.message);
                        });
                    },

                    openFirma(id) {
                        @this.call('cargarDetalle', id);
                        $('#modal_firma').modal('show');

                        // Espera al evento shown.bs.modal en vez de un timeout fijo
                        $('#modal_firma').one('shown.bs.modal', () => {
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
                        @this.call('firmar');

                        $('#modal_firma').modal('hide');
                    }

                }))
            </script>
        @endscript

    </div>
</div>
