<div>
    <div x-data="asignaciones">

        <div class="content-wrapper p-3">

            <div class="d-flex justify-content-between mb-3">
                <h4>👥 Asignaciones — {{ $curso->titulo }}</h4>
                <a href="{{ route('cursos') }}" class="btn btn-secondary">
                    ← Volver
                </a>
            </div>

            <div class="card p-3 mb-3">
                @if (request('filtro'))
                    <div class="alert alert-info py-2 mb-2">
                        Mostrando:
                        <b>
                            {{ ucfirst(request('filtro')) }}
                        </b>
                        <a href="{{ route('admin.cursos.resultados', $curso->id) }}" class="ml-2">
                            (ver todos)
                        </a>
                    </div>
                @endif
                <div class="row">

                    <div class="col-md-4">
                        <label>Tipo</label>
                        <select class="form-control" wire:model="tipo">
                            <option value="usuario">Usuario</option>
                            <option value="rol">Rol</option>
                        </select>
                    </div>

                    <div class="col-md-6" x-show="$wire.tipo === 'usuario'">
                        <label>Usuario</label>
                        <select class="form-control" wire:model="user_id">
                            <option value="">Seleccione</option>
                            @foreach ($usuarios as $u)
                                <option value="{{ $u->id }}">
                                    {{ $u->name }}
                                    — {{ $u->roles->pluck('name')->join(', ') ?: 'Sin rol' }}
                                </option>
                            @endforeach
                        </select>
                        @error('user_id')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>


                    <div class="col-md-6" x-show="$wire.tipo === 'rol'">
                        <label>Rol</label>
                        <select class="form-control" wire:model="rol_id">
                            <option value="">Seleccione</option>
                            @foreach ($roles as $r)
                                <option value="{{ $r->id }}">{{ $r->name }}</option>
                            @endforeach
                        </select>
                        @error('rol_id')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-2 d-flex align-items-end">
                        <button class="btn btn-dark w-100" @click="saveFront()">Asignar</button>
                    </div>

                </div>

                @error('dup')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
                @error('tipo')
                    <span class="text-danger">{{ $message }}</span>
                @enderror

            </div>

            <div class="card">
                <x-table id="table_asignaciones">
                    <tr>
                        <th>Tipo</th>
                        <th>Asignado a</th>
                        <th>Rol</th>
                        <th>Acc</th>
                    </tr>
                </x-table>
            </div>

        </div>

        @script
            <script>
                Alpine.data('asignaciones', () => ({

                    data: [],

                    init() {
                        this.load();
                    },

                    async load() {
                        this.data = await @this.getAsignaciones();
                        for (const a of this.data) {
                            this.addRow(a);
                        }
                        setTimeout(() => __resetTable('#table_asignaciones'), 300);
                    },

                    addRow(a) {

                        let tipo = a.user ? 'Usuario' : 'Rol';
                        let nombre = a.user ? a.user.name : a.rol_nombre;
                        let rol = a.user ? (a.roles_usuario ?? '—') : '—';

                        let tr = `
                                    <tr id="asig_${a.id}">
                                        <td>${tipo}</td>
                                        <td>${nombre}</td>
                                        <td>${rol}</td>
                                        <td>
                                            <x-buttonsm click="del('${a.id}')" color="danger">
                                                <i class="la la-trash"></i>
                                            </x-buttonsm>
                                        </td>
                                    </tr>
                                `;

                        $('#body_table_asignaciones').prepend(tr);
                    },

                    async saveFront() {
                        const a = await @this.save();
                        if (a) {
                            this.addRow(a);
                            toastRight('success', 'Asignado correctamente');
                        }
                    },

                    del(id) {

                        alertClickCallback(
                            'Eliminar asignación',
                            'Este usuario o rol dejará de tener acceso al curso',
                            'warning',
                            'Eliminar',
                            'Cancelar',
                            async () => {
                                const ok = await @this.eliminar(id);
                                if (ok) {
                                    $(`#asig_${id}`).remove();
                                    toastRight('error', 'Asignación eliminada');
                                }
                            }
                        );
                    }

                }))
            </script>
        @endscript

    </div>
</div>
