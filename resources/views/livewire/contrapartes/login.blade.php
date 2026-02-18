<div>
    <style>
        .login-warning {
            background: #fff8e1;
            border: 1px solid #ffe082;
            color: #5d4037;
            padding: 12px 14px;
            border-radius: 10px;
            font-size: 13px;
            line-height: 1.45;
            text-align: left;
            margin-top: 7px;
        }

        .login-warning .warning-title {
            font-weight: 700;
            margin-bottom: 4px;
            color: #bf360c;
        }

        /* Normaliza inputs y selects */
        .input-modern,
        .select-modern {
            width: 100%;
            height: 48px;
            /* MISMA ALTURA PARA TODOS */
            padding: 0 14px;
            border-radius: 12px;
            border: 1px solid #d7dee7;
            background: #e9eff6;
            font-size: 14px;
            outline: none;
            box-sizing: border-box;
        }

        /* Quita estilo nativo del select */
        .select-modern {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;

            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' fill='%23666' viewBox='0 0 16 16'%3E%3Cpath d='M1.5 5l6.5 6 6.5-6z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 14px;
            padding-right: 35px;
            /* espacio para flecha */
        }

        /* placeholder alineado */
        .input-modern::placeholder {
            color: #6c7a89;
        }

        .spinner {
            display: inline-block;
            width: 14px;
            height: 14px;
            border: 2px solid #fff;
            border-bottom-color: transparent;
            border-radius: 50%;
            animation: spin .6s linear infinite;
            margin-right: 6px;
            vertical-align: middle;
        }

        @keyframes spin {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        /* botón deshabilitado visual */
        button:disabled {
            opacity: .7;
            cursor: not-allowed;
        }
    </style>
    <div class="login-wrapper">

        <div class="login-box">

            <img src="{{ asset('img-logisticarga/logo.png') }}">

            <div class="subtitle mb-3">
                INGRESO TERCEROS
            </div>

            <hr>

            <select wire:model="tipo" class="input-modern select-modern">
                <option value="">Tipo de persona</option>
                <option value="juridica">Persona Jurídica</option>
                <option value="natural">Persona Natural</option>
            </select>
            @error('tipo')
                <div class="error-text">{{ $message }}</div>
            @enderror

            <input wire:model="identificacion" class="input-modern" placeholder="Identificación">

            @error('identificacion')
                <div class="error-text">{{ $message }}</div>
            @enderror

            <input wire:model="password" type="password" class="input-modern" placeholder="Contraseña">

            @error('password')
                <div class="error-text">{{ $message }}</div>
            @enderror

            <button wire:click="ingresar" wire:loading.attr="disabled" wire:target="ingresar" class="btn-modern">

                <span wire:loading.remove wire:target="ingresar">
                    Iniciar sesión
                </span>

                <span wire:loading wire:target="ingresar">
                    <span class="spinner"></span> Validando...
                </span>

            </button>

            <br>

            <div class="login-warning">
                <div class="warning-title">Condiciones de acceso</div>

                <p>
                    Para diligenciar el formulario debes crear una contraseña personal.
                    Elige una contraseña <b>segura pero fácil de recordar</b>.
                </p>

                <p class="mb-0">
                    ⚠️ El sistema no permite recuperar contraseñas.
                    Si la olvidas tendrás que iniciar un nuevo proceso y
                    <b>se perderá toda la información registrada anteriormente.</b>
                </p>
            </div>

        </div>
    </div>
    @push('scripts')
        <script>
            window.addEventListener('toast-ok', e => {
                Swal.fire({
                    icon: 'success',
                    text: e.detail.msg,
                    timer: 2500,
                    showConfirmButton: false
                });
            });

            window.addEventListener('toast-error', e => {
                Swal.fire({
                    icon: 'error',
                    text: e.detail.msg,
                    timer: 2500,
                    showConfirmButton: false
                });
            });

            window.addEventListener('bienvenida', () => {
                Swal.fire({
                    icon: 'success',
                    title: 'Bienvenido',
                    text: 'Puedes iniciar el diligenciamiento del formulario.',
                    confirmButtonText: 'Entendido'
                });
            });

            window.addEventListener('continuar-proceso', () => {
                Swal.fire({
                    icon: 'info',
                    title: 'Proceso en curso',
                    text: 'Ya tienes un formulario iniciado. Puedes continuar completándolo.',
                    confirmButtonText: 'Continuar'
                });
            });

            window.addEventListener('confirmar-borrado', () => {
                Swal.fire({
                    icon: 'warning',
                    title: 'Datos incorrectos',
                    html: `
                <p>Ya existe un proceso asociado a esta identificación, pero la contraseña ingresada no es correcta.</p>
                <p><strong>Si continúas se perderán todos los datos anteriores.</strong></p>
                `,
                    showCancelButton: true,
                    confirmButtonText: 'Continuar y generar nuevo',
                    cancelButtonText: 'Cancelar',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        Livewire.dispatch('confirmarBorrado');
                    }
                });
            });
        </script>
    @endpush
</div>
