<div x-data="form_carpeta">
    <a class="btn btn-outline-dark f_right ml-1" x-on:click="openFormCarpeta()">
        Nueva carpeta
    </a>

    <x-modal id="form_carpeta">
        <x-slot name="title">
            <span x-show="!$wire.form_carpeta.id">Agregar carpeta</span>
            <span x-show="$wire.form_carpeta.id">Editar carpeta</span>
        </x-slot>

        <div class="row">
            <div class="col-md-12 mt-1">
                <x-input model="$wire.form_carpeta.nombre" type="text" label="Nombre" required="true"></x-input>
            </div>
            <div class="col-md-12 mt-1">
                <x-textarea model="$wire.form_carpeta.descripcion" type="text" label="Descripcion" placeholder="Opcional.."></x-textarea>
            </div>
            <div class="col-md-12 mt-1">
                <x-select model="$wire.form_carpeta.roles" label="Compartir con los sgtes Roles" id="roles" multiple="1">
                    <option value="0" disabled selected>Agregar roles... </option>
                    @foreach ( $roles as $role )
                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                    @endforeach
                </x-select>
            </div>
            <div class="col-md-12 mt-1">
                <x-select model="$wire.form_carpeta.usuarios" label="Compartir con los sgtes Usuarios" id="usuarios" multiple="1">
                    <option value="0" disabled selected>Agregar usuarios... </option>
                    @foreach ( $users as $user )
                        <option value="{{ $user->id }}">{{ $user->name . ' ' . $user->last_name }}</option>
                    @endforeach
                </x-select>
            </div>
        </div>

        <x-slot name="footer">
            <span  x-show="!$wire.loading">
                <button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-outline-primary" x-on:click="saveCarpetaFront()">Guardar</button>
            </span>
            <div x-show="$wire.loading">
                <x-spinner></x-spinner>
            </div>
        </x-slot>
    </x-modal>

    @script
        <script>
            Alpine.data('form_carpeta', () => ({
                init() {
                    $('#usuarios').change(() => {
                        val = $('#usuarios').val()
                        @this.form_carpeta.usuarios = val
                    })
  
                    $('#roles').change(() => {
                        val = $('#roles').val()
                        @this.form_carpeta.roles = val
                    })

                    $('#form_carpeta').on('hidden.bs.modal', function (e) {
                        @this.vaciarFormCarpeta()
                        limiparSelect2( 'usuarios' )
                        limiparSelect2( 'roles' )
                    });

                    Livewire.on('editCarpeta', ({ carpeta }) =>{
                        @this.form_carpeta.id           = carpeta.id
                        @this.form_carpeta.nombre       = carpeta.nombre
                        @this.form_carpeta.descripcion  = carpeta.descripcion
                        if( carpeta.usuarios ){
                            const usuarios_select = []
                            carpeta.usuarios.map( (u)=>{
                                usuarios_select.push(u.user_id)
                            })
                            setTimeout(() => {             
                                @this.form_carpeta.usuarios = usuarios_select
                                $('#usuarios').val( usuarios_select ).select2().trigger('change');
                            }, 50);
                        }
                        if( carpeta.roles ){
                            const roles_select = []
                            carpeta.roles.map( (r)=>{
                                roles_select.push(r.role_id)
                            })
                            setTimeout(() => {             
                                @this.form_carpeta.roles = roles_select
                                $('#roles').val( roles_select ).select2().trigger('change');
                            }, 50);
                        }
                    })
                },

                openFormCarpeta(carpeta_old = null) {
                    $('#form_carpeta').modal('show');
                },

                // ejemplo store desde Alpine
                add(){
                    this.$store.repositorio.prueba++
                },

                async saveCarpetaFront() {
                    @this.loading = true; 
                    const carpeta = await @this.saveCarpeta();
                    @this.loading = false; 
                    if (carpeta) {
                        $('#form_carpeta').modal('hide');
                        toastRight('success', 'Acción realizada con éxito');
                    }
                },
            }));

        </script>
    @endscript
</div>
