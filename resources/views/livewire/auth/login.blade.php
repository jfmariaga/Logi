<div x-data="login">
    <style>
        .text-gold {
            color: #beb26b !important;
            /* Color dorado */
        }
        .mw_400{
            max-width:400px;
        }
        body{
            background-color: #b4bec9 !important;
        }
    </style>
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-body">
                <section class="flexbox-container">
                    <div class="col-12 d-flex align-items-center justify-content-center">
                        <div class="col-md-4 col-10 box-shadow-2 p-0 mw_400">
                            <div class="card border-grey border-lighten-3 px-1 py-1 m-0">
                                <div class="card-header border-0">
                                    <img src="{{ asset('img-logisticarga/logo.png') }}" alt="Logo"
                                        style="width: 280px; height: auto; display: block; margin: 0 auto; border-radius: 10px;">
                                    <h6 class="card-subtitle line-on-side text-muted text-center font-small-3 pt-2">
                                        <span>LOGISTICARGA JM S.A.S</span>
                                    </h6>
                                </div>
                                <div class="card-content">
                                    <div class="card-body">
                                        @if (session('status'))
                                            <div class="alert alert-danger">
                                                {{ session('status') }}
                                            </div>
                                        @endif
                                        <form class="form-horizontal">
                                            <fieldset class="form-group position-relative has-icon-left">
                                                <input type="text" class="form-control" x-model="$wire.user"
                                                    placeholder="Usuarios..." autocomplete="off">
                                                <div class="form-control-position">
                                                    <i class="ft-user"></i>
                                                </div>
                                                @error('user')
                                                    <span style="color:red;">{{ $message }}</span>
                                                @enderror
                                            </fieldset>
                                            <fieldset class="form-group position-relative has-icon-left">
                                                <input :type=" show_pass ? 'text' : 'password'" class="form-control" x-model="$wire.password"
                                                    placeholder="Contraseña...">
                                                <div x-on:click="showPass()" class="form-control-position pointer btn-dark" style="height: 39px; border-radius: 3px 0 0 3px;">
                                                    <i class="la" :class="show_pass ? 'la-eye' : 'la-eye-slash' "></i>
                                                </div>
                                                @error('password')
                                                    <span style="color:red;">{{ $message }}</span>
                                                @enderror
                                            </fieldset>
                                            <div style="height:40px;">
                                                <template x-if="!loading">
                                                    <button type="button" x-on:click="loginFront()" class="btn btn-dark btn-block text-white">Iniciar sesión</button>
                                                </template>
                                                <template x-if="loading">
                                                    <button  disabled class="btn btn-block">
                                                        <x-spinner></x-spinner>
                                                    </button>
                                                </template>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>

    @script
        <script>
            Alpine.data('login', () => ({
                loading : false,
                show_pass : false,

                init(){
                    @this.on('entrando', i => {
                        console.log('OK')
                        // toastRight('warning', 'Contraseña incorrecta!');
                    });
                },
                showPass(){
                    this.show_pass = !this.show_pass
                },
                async loginFront(){
                    this.loading = !this.loading
                    const res = await @this.login()
                    // console.log({res})
                    if( !res ){
                        this.loading = false
                    } else if( res == 'login_fail' ){
                        toastRight('warning', 'Contraseña incorrecta!');
                        this.loading = false
                    }else if( res == 'no_register' ){
                        toastRight('warning', 'Usuario no encontrado!');
                        this.loading = false
                    }
                }
            }))
        </script>
    @endscript
</div>
