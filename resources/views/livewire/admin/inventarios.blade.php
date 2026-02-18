<div>
    <div x-data="inventarios">

        <div class="content-wrapper p-3">

            <div class="d-flex justify-content-between mb-3">
                <h4>📦 Inventario</h4>

                <button class="btn btn-dark" @click="openForm()">
                    Ajustar stock
                </button>
            </div>

            <div class="card">
                <div x-show="!loading">
                    <x-table id="table_inventarios">
                        <tr>
                            <th>Producto</th>
                            <th>Talla</th>
                            <th>Cantidad</th>
                        </tr>
                    </x-table>
                </div>

                <div x-show="loading">
                    <x-spinner />
                </div>
            </div>

        </div>

        @include('livewire.admin.form-inventarios')

        @script
            <script>
                Alpine.data('inventarios', () => ({
                    inventarios: [],
                    loading: true,
                    loading_form: false,

                    init() {

                        this.getInventarios();

                        // select producto → setea talla
                        $('#producto').change(function() {
                            let option = $(this).find(':selected');
                            let id = option.val();
                            let talla = option.data('talla');

                            @this.producto_id = id;
                            @this.talla = talla ?? null;
                        });

                        // modo
                        $('#modo').change(() => {
                            @this.modo = $('#modo').val()
                        });

                        document.addEventListener('reset-inventario-form', () => {

                            // limpiar select producto
                            $('#producto').val('').trigger('change');

                            // limpiar modo
                            $('#modo').val('sumar').trigger('change');

                            // limpiar input cantidad
                            $('input[name="cantidad"]').val('');

                        });

                    },

                    async getInventarios() {

                        this.loading = true;

                        this.inventarios = await @this.getInventarios();

                        setTimeout(() => {

                            __resetTable('#table_inventarios');

                            let table = $('#table_inventarios').DataTable();
                            table.clear().draw();

                            this.inventarios.forEach(i => this.addInventario(i));

                            this.loading = false;

                        }, 400);
                    },

                    addInventario(i) {

                        let table = $('#table_inventarios').DataTable();

                        let rowNode = table.row.add([
                            `<b>${i.producto}</b>`,
                            `${i.requiere_talla ? (i.talla ?? '-') : '-'}`,
                            `<b>${i.cantidad}</b>`
                        ]).draw(false).node();

                        $(rowNode).addClass('cursor-pointer');
                        $(rowNode).attr('data-id', i.id);

                        // CLICK → VER HISTORIAL (KARDEX)
                        $(rowNode).on('click', function() {

                            let row = table.row(this);

                            if (row.child.isShown()) {
                                row.child.hide();
                                return;
                            }

                            let html = `
                    <div class="p-2 bg-light">
                        <b>Historial de movimientos</b>
                        <table class="table table-sm table-bordered mt-2">
                        <thead class="bg-white">
                        <tr>
                            <th>Fecha</th>
                            <th>Usuario</th>
                            <th>Tipo</th>
                            <th>Antes</th>
                            <th>Movimiento</th>
                            <th>Después</th>
                            <th>Detalle</th>
                        </tr>
                        </thead>
                        <tbody>
                    `;

                            i.movimientos.forEach(m => {

                                let color = 'secondary';
                                if (m.tipo === 'entrada') color = 'success';
                                if (m.tipo === 'salida') color = 'danger';
                                if (m.tipo === 'ajuste') color = 'warning';

                                html += `
                        <tr>
                            <td>${m.fecha}</td>
                            <td>${m.usuario}</td>
                            <td><span class="badge badge-${color}">${m.tipo}</span></td>
                            <td>${m.antes}</td>
                            <td>${m.movimiento}</td>
                            <td><b>${m.despues}</b></td>
                            <td>${m.descripcion ?? ''}</td>
                        </tr>`;
                            });

                            html += `</tbody></table></div>`;

                            row.child(html).show();
                        });
                    },

                    // async saveFront() {
                    //     this.loading_form = true;
                    //     const inv = await @this.save();
                    //     if (!inv) return;

                    //     let table = $('#table_inventarios').DataTable();

                    //     let found = false;

                    //     table.rows().every(function() {

                    //         let rowNode = this.node();

                    //         if ($(rowNode).attr('data-id') == inv.id) {

                    //             this.data([
                    //                 `<b>${inv.producto}</b>`,
                    //                 `${inv.requiere_talla ? (inv.talla ?? '-') : '-'}`,
                    //                 `<b>${inv.cantidad}</b>`
                    //             ]).draw(false);

                    //             found = true;
                    //         }
                    //     });

                    //     // si no existe la fila la crea
                    //     if (!found) {

                    //         let rowNode = table.row.add([
                    //             `<b>${inv.producto}</b>`,
                    //             `${inv.requiere_talla ? (inv.talla ?? '-') : '-'}`,
                    //             `<b>${inv.cantidad}</b>`
                    //         ]).draw(false).node();

                    //         $(rowNode).attr('data-id', inv.id);
                    //     }

                    //     $('#form_inventarios').modal('hide');
                    //     toastRight('success', 'Inventario actualizado');
                    //     this.loading_form = false;
                    // },

                    async saveFront() {

                        this.loading_form = true;

                        const inv = await @this.save();
                        if (!inv) return;

                        let table = $('#table_inventarios').DataTable();
                        let found = false;

                        table.rows().every(function() {

                            let rowNode = this.node();

                            if ($(rowNode).attr('data-id') == inv.id) {

                                this.data([
                                    `<b>${inv.producto}</b>`,
                                    `${inv.requiere_talla ? (inv.talla ?? '-') : '-'}`,
                                    `<b>${inv.cantidad}</b>`
                                ]).draw(false);

                                found = true;
                            }
                        });

                        if (!found) {

                            let rowNode = table.row.add([
                                `<b>${inv.producto}</b>`,
                                `${inv.requiere_talla ? (inv.talla ?? '-') : '-'}`,
                                `<b>${inv.cantidad}</b>`
                            ]).draw(false).node();

                            $(rowNode).attr('data-id', inv.id);
                        }

                        // Traer historial nuevo SOLO del item actualizado
                        let movimientos = await @this.getMovimientosInventario(inv.id);

                        // actualizar memoria local sin recargar tabla
                        let item = this.inventarios.find(i => i.id == inv.id);
                        if (item) item.movimientos = movimientos;

                        $('#form_inventarios').modal('hide');
                        toastRight('success', 'Inventario actualizado');

                        this.loading_form = false;
                    },


                    openForm() {
                        @this.limpiar();
                        $('#form_inventarios').modal('show');
                    }


                }));
            </script>
        @endscript

    </div>
</div>
