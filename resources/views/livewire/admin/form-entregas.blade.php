<div wire:ignore.self class="modal fade" id="form_entregas">    
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            {{-- HEADER --}}
            <div class="modal-header border-0">
                <h5 class="modal-title font-weight-bold">
                    {{ $editing ? 'Editar Entrega' : 'Nueva Entrega' }}
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            {{-- BODY --}}
            {{-- <div class="modal-body">

                <div class="mb-3">
                    <label>Empleado</label>
                    <select id="user_select" class="form-control">
                        <option value="">Seleccione</option>
                        @foreach ($usuarios as $u)
                            <option value="{{ $u->id }}">
                                {{ $u->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <hr>

                <div class="d-flex justify-content-between mb-2">
                    <h6>Items</h6>
                    <button class="btn btn-sm btn-secondary" wire:click="addItem" wire:loading.attr="disabled">
                        + Agregar línea
                    </button>
                </div>

                @foreach ($items as $index => $item)
                    <div class="card p-2 mb-2">
                        <div class="row">

                            <div class="col-md-8">
                                <select wire:model="items.{{ $index }}.producto_id" class="form-control">
                                    <option value="">Producto</option>
                                    @foreach ($productos as $p)
                                        <option value="{{ $p->id }}">
                                            {{ $p->nombre }}
                                            @if ($p->requiere_talla)
                                                (Talla {{ $p->talla }})
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2">
                                <input type="number" wire:model="items.{{ $index }}.cantidad" min="1"
                                    class="form-control">
                            </div>

                            <div class="col-md-2">
                                <button class="btn btn-danger btn-sm w-100" wire:click="removeItem({{ $index }})"
                                    wire:loading.attr="disabled">
                                    Eliminar
                                </button>
                            </div>

                        </div>
                    </div>
                @endforeach

            </div> --}}

            <div class="modal-body">

                {{-- 🔥 LOADER --}}
                <div x-show="modal_loading" class="text-center py-4">
                    <i class="la la-spinner la-spin" style="font-size:28px"></i>
                    <div class="mt-2">Cargando información...</div>
                </div>

                {{-- CONTENIDO --}}
                <div x-show="!modal_loading">

                    <div class="mb-3">
                        <label>Empleado</label>
                        <select id="user_select" class="form-control">
                            <option value="">Seleccione</option>
                            @foreach ($usuarios as $u)
                                <option value="{{ $u->id }}">
                                    {{ $u->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between mb-2">
                        <h6>Items</h6>
                        <button class="btn btn-sm btn-secondary" wire:click="addItem" wire:loading.attr="disabled">
                            + Agregar línea
                        </button>
                    </div>

                    @foreach ($items as $index => $item)
                        <div class="card p-2 mb-2">
                            <div class="row">

                                <div class="col-md-8">
                                    <select wire:model="items.{{ $index }}.producto_id" class="form-control">
                                        <option value="">Producto</option>
                                        @foreach ($productos as $p)
                                            <option value="{{ $p->id }}">
                                                {{ $p->nombre }}
                                                @if ($p->requiere_talla)
                                                    (Talla {{ $p->talla }})
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-2">
                                    <input type="number" wire:model="items.{{ $index }}.cantidad" min="1"
                                        class="form-control">
                                </div>

                                <div class="col-md-2">
                                    <button class="btn btn-danger btn-sm w-100"
                                        wire:click="removeItem({{ $index }})" wire:loading.attr="disabled">
                                        Eliminar
                                    </button>
                                </div>

                            </div>
                        </div>
                    @endforeach

                </div>
            </div>

            {{-- FOOTER --}}
            <div class="modal-footer border-0">

                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal" :disabled="loading_form">
                    Cancelar
                </button>

                <button type="button" class="btn btn-outline-primary" @click="saveFront()" :disabled="loading_form">

                    <span x-show="!loading_form">
                        {{ $editing ? 'Actualizar Entrega' : 'Guardar Entrega' }}
                    </span>

                    <span x-show="loading_form">
                        <i class="la la-spinner la-spin"></i> Guardando...
                    </span>

                </button>

            </div>
        </div>
    </div>
</div>
