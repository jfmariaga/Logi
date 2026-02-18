<div>
    <div x-data="productos">

        <div class="content-wrapper p-3">

            <div class="d-flex justify-content-between mb-3">
                <h4>📦 Productos (EPP / Dotación)</h4>

                <button class="btn btn-dark" @click="openForm()">
                    Nuevo
                </button>
            </div>

            <div class="card">
                <div x-show="!loading">
                    <x-table id="table_productos">
                        <tr>
                            <th>Nombre</th>
                            <th>Tipo</th>
                            <th>Referencia</th>
                            <th>Requiere talla</th>
                            <th>Talla</th>
                            <th>Estado</th>
                            <th>Acc</th>
                        </tr>
                    </x-table>
                </div>

                <div x-show="loading">
                    <x-spinner />
                </div>
            </div>

        </div>

        @include('livewire.admin.form-productos')

        @script
            <script>
                Alpine.data('productos', () => ({
                    productos: [],
                    loading: true,
                    loading_form: false,

                    init() {
                        this.getProductos();

                        $('#activo').change(() => {
                            val = $('#activo').val()
                            @this.activo = val
                        })

                        $('#tipo').change(() => {
                            val = $('#tipo').val()
                            @this.tipo = val
                        })

                        $('#requiere_talla').change(() => {
                            val = $('#requiere_talla').val()
                            @this.requiere_talla = val

                            if (val == 0) {
                                @this.talla = null
                            }
                        })
                    },

                    async getProductos() {
                        this.loading = true;

                        this.productos = await @this.getProductos();

                        for (const p of this.productos) {
                            this.addProducto(p);
                        }

                        setTimeout(() => {
                            __resetTable('#table_productos');
                            this.loading = false;
                        }, 400);
                    },

                    addProducto(producto, is_update = false) {
                        let tr = ``;
                        if (!is_update) tr += `<tr id="producto_${producto.id}">`;
                        tr += `
                        <td>${producto.nombre}</td>
                        <td>${producto.tipo}</td>
                        <td>${producto.referencia}</td>
                        <td>${producto.requiere_talla == 1 ? 'Sí' : 'No'}</td>
                        <td>${producto.talla ? producto.talla : '-'}</td>
                        <td>
                            ${producto.activo == 1
                                ? '<span class="text-success">Activo</span>'
                                : '<span class="text-danger">Inactivo</span>'}
                        </td>
                        <td>
                            <div class="d-flex">

                                <x-buttonsm click="openForm('${producto.id}')" title="Editar">
                                    <i class="la la-edit"></i>
                                </x-buttonsm>

                                <x-buttonsm click="confirmDelete('${producto.id}')" color="danger" title="Activar/Desactivar">
                                    <i class="la la-trash"></i>
                                </x-buttonsm>

                            </div>
                        </td>
                    `;

                        if (!is_update) {
                            tr += `</tr>`;
                            $('#body_table_productos').prepend(tr);
                        } else {
                            $(`#producto_${producto.id}`).html(tr);
                        }
                    },

                    async saveFront() {

                        this.loading_form = true;

                        const is_update = @this.producto_id ? true : false;
                        const producto = await @this.save();

                        if (producto) {

                            this.addProducto(producto, is_update);
                            $('#form_productos').modal('hide');

                            if (is_update) {

                                for (const key in this.productos) {
                                    if (this.productos[key].id == producto.id) {
                                        this.productos[key] = producto;
                                    }
                                }

                                toastRight('success', 'Producto actualizado con éxito');

                            } else {

                                this.productos.push(producto);
                                toastRight('success', 'Producto registrado con éxito');

                            }
                        }

                        this.loading_form = false;
                    },

                    openForm(producto_id = null) {

                        let producto_edit = this.productos.find((producto) => producto.id == producto_id);
                        producto_edit = producto_edit ?? {};

                        @this.producto_id = producto_edit ? producto_edit.id : null;
                        @this.nombre = producto_edit ? producto_edit.nombre : null;
                        @this.tipo = producto_edit ? producto_edit.tipo : null;
                        @this.referencia = producto_edit ? producto_edit.referencia : null;
                        @this.descripcion = producto_edit ? producto_edit.descripcion : null;

                        @this.requiere_talla = producto_edit ? producto_edit.requiere_talla : null;
                        @this.talla = producto_edit ? producto_edit.talla : null;
                        @this.activo = producto_edit ? producto_edit.activo : null;

                        $('#form_productos').modal('show');

                        setTimeout(() => {

                            const tipoElement = document.getElementById('tipo');
                            if (tipoElement) {
                                $(tipoElement).val(@this.tipo).trigger('change');
                            }

                            const activoElement = document.getElementById('activo');
                            if (activoElement) {
                                $(activoElement).val(@this.activo).trigger('change');
                            }

                            const tallaElement = document.getElementById('requiere_talla');
                            if (tallaElement) {
                                $(tallaElement).val(@this.requiere_talla).trigger('change');
                            }

                        }, 500);
                    },

                    confirmDelete(id) {
                        alertClickCallback(
                            'Cambiar estado',
                            '¿Desea activar o desactivar este producto?',
                            'warning',
                            'Confirmar',
                            'Cancelar',
                            async () => {
                                const res = await @this.desactivar(id);
                                if (res) {
                                    this.addProducto(res, true);
                                    toastRight('success', 'Estado actualizado');
                                }
                            }
                        );
                    }

                }));
            </script>
        @endscript
    </div>
</div>
