<div>

    <div class="row g-4">

        {{-- AREAS --}}
        <div class="col-12">

            <div class="card shadow border-0">

                <div class="card-header bg-white border-0 pb-0">
                    <h5 class="mb-3">Áreas</h5>
                </div>

                <div class="card-body">

                    <div class="row mb-4">

                        <div class="col-md-6">

                            <div class="input-group">

                                <input type="text" class="form-control" wire:model="nombreArea"
                                    placeholder="Nueva área">

                                <button class="btn btn-dark btn-sm"
                                    style="height:38px;width:38px;color:white;font-size:18px" wire:click="crearArea">

                                    +

                                </button>

                            </div>

                        </div>

                    </div>


                    <table class="table table-borderless align-middle">

                        <thead>

                            <tr>
                                <th width="90%">Área</th>
                                <th width="10%" class="text-center"></th>
                            </tr>

                        </thead>

                        <tbody>

                            @foreach ($areas as $area)
                                <tr>

                                    <td>{{ $area->nombre }}</td>

                                    <td class="text-center">

                                        <button wire:click="eliminarArea({{ $area->id }})"
                                            class="btn btn-sm btn-light-danger" title="Eliminar">

                                            <i class="la la-trash"></i>

                                        </button>

                                    </td>

                                </tr>
                            @endforeach

                        </tbody>

                    </table>

                </div>

            </div>

        </div>


        {{-- CARGOS --}}
        <div class="col-12">

            <div class="card shadow border-0">

                <div class="card-header bg-white border-0 pb-0">
                    <h5 class="mb-3">Cargos</h5>
                </div>

                <div class="card-body">

                    <div class="row mb-4 align-items-end">

                        <div class="col-md-4">

                            <select class="form-control" wire:model="areaCargo">

                                <option value="">Área</option>

                                @foreach ($areas as $area)
                                    <option value="{{ $area->id }}">
                                        {{ $area->nombre }}
                                    </option>
                                @endforeach

                            </select>

                        </div>


                        <div class="col-md-6">

                            <input type="text" class="form-control" wire:model="nombreCargo" placeholder="Cargo">

                        </div>


                        <div class="col-md-2 d-flex align-items-end">

                            <button class="btn btn-dark btn-sm d-flex align-items-center justify-content-center"
                                style="height:38px;width:40px;font-size:18px;color:white" wire:click="crearCargo">
                                +
                            </button>

                        </div>

                    </div>


                    <table class="table table-borderless align-middle">

                        <thead>

                            <tr>
                                <th width="45%">Cargo</th>
                                <th width="45%">Área</th>
                                <th width="10%" class="text-center"></th>
                            </tr>

                        </thead>

                        <tbody>

                            @foreach ($cargos as $cargo)
                                <tr>

                                    <td>{{ $cargo->nombre }}</td>

                                    <td>{{ $cargo->area->nombre }}</td>

                                    <td class="text-center">

                                        <button wire:click="eliminarCargo({{ $cargo->id }})"
                                            class="btn btn-sm btn-light-danger" title="Eliminar">

                                            <i class="la la-trash"></i>

                                        </button>

                                    </td>

                                </tr>
                            @endforeach

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('toast', (data) => {
                toastRight(data.type, data.message);
            });
        });
    </script>
</div>
