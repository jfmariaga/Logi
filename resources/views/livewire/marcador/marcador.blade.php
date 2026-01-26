<div>
    <div class="container mt-5" style="max-width: 500px">

        <h3 class="text-center mb-3">🕒 Terminal de Marcación</h3>

        {{-- Documento --}}
        @if ($modoTerminal)
            <div class="form-group">
                <label>Número de Documento</label>
                <input type="text" class="form-control" placeholder="Digite número de documento"
                    wire:model.live.debounce.2000ms="documento" wire:keydown.escape="$set('documento','')" autofocus>
            </div>

            <div wire:loading wire:target="documento" class="text-center text-muted">
                🔍 Buscando...
            </div>
        @endif

        {{-- Usuario --}}
        @if ($usuario)
            <div class="card terminal-card mt-4">
                <div class="card-body text-center">

                    <h5>{{ $usuario->name }} {{ $usuario->last_name }}</h5>
                    <div class="text-muted mb-2">Documento: {{ $usuario->document }}</div>

                    <div class="mb-2">
                        Estado:
                        <b>{{ strtoupper($estadoActual) }}</b>
                    </div>

                    @if ($ultimaEntradaFecha)
                        <div class="text-muted mb-3">
                            Última entrada:
                            {{ \Carbon\Carbon::parse($ultimaEntradaFecha)->format('d/m/Y H:i') }}
                        </div>
                    @endif

                    <div class="mt-4">

                        @if ($estadoActual === 'libre')
                            <button class="btn btn-terminal btn-entrada" wire:click="marcar('entrada')"
                                wire:loading.attr="disabled" wire:target="marcar('entrada')">
                                <span wire:loading.remove wire:target="marcar('entrada')">
                                    ▶ Iniciar Jornada
                                </span>
                                <span wire:loading wire:target="marcar('entrada')">
                                    ⏳ Registrando...
                                </span>
                            </button>
                        @endif

                        @if ($estadoActual === 'trabajando')
                            <button class="btn btn-terminal btn-salida" wire:click="marcar('salida')"
                                wire:loading.attr="disabled" wire:target="marcar('salida')">
                                <span wire:loading.remove wire:target="marcar('salida')">
                                    ■ Finalizar Jornada
                                </span>
                                <span wire:loading wire:target="marcar('salida')">
                                    ⏳ Registrando...
                                </span>
                            </button>
                        @endif
                    </div>

                </div>

                <x-modal id="modal_selfie" size="md">
                    <x-slot name="title">📸 Verificación de Identidad</x-slot>

                    <div class="text-center">
                        <video id="video" autoplay playsinline width="100%" class="rounded"></video>
                        <canvas id="canvas" class="d-none"></canvas>

                        <div class="mt-3">
                            <button type="button" class="btn btn-primary" id="btnFoto">
                                📸 Tomar Foto
                            </button>

                            <div id="estadoProceso" class="mt-3 text-muted d-none">
                                ⏳ Procesando marcación...
                            </div>
                        </div>

                        @error('selfie')
                            <div class="text-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <x-slot name="footer">
                        <button class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    </x-slot>
                </x-modal>

            </div>
        @endif

    </div>

    <script>
        function toast(type, text) {
            toastRight(type, text);
        }

        function confirmar(titulo, texto, icon, okText, cancelText, callback) {
            Swal.fire({
                title: titulo,
                text: texto,
                icon: icon,
                showCancelButton: true,
                confirmButtonText: okText,
                cancelButtonText: cancelText,
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) callback();
            });
        }
    </script>

    <script>
        let stream = null;
        let tipoMarcacion = null;
        let procesandoFoto = false;

        window.addEventListener('abrir-selfie', e => {

            tipoMarcacion = e.detail.tipo;
            procesandoFoto = false;

            $('#estadoProceso').addClass('d-none');

            $('#modal_selfie').modal({
                backdrop: 'static',
                keyboard: false,
                show: true
            });

            $('#modal_selfie').one('shown.bs.modal', function() {

                navigator.mediaDevices.getUserMedia({
                    video: {
                        facingMode: "user"
                    }
                }).then(s => {
                    stream = s;
                    const video = document.getElementById('video');
                    video.srcObject = s;
                    video.play();
                }).catch(err => {
                    console.error(err);
                    alert('No se pudo acceder a la cámara');
                    Livewire.dispatch('liberar-boton');
                });

            });
        });


        /* 🔥 CLICK CON DELEGACIÓN (funciona con Livewire) */
        document.addEventListener('click', function(e) {

            if (!e.target || e.target.id !== 'btnFoto') return;

            if (procesandoFoto) return;
            procesandoFoto = true;

            const btn = e.target;
            btn.disabled = true;
            btn.innerText = '⏳ Procesando...';

            document.getElementById('estadoProceso')?.classList.remove('d-none');

            const video = document.getElementById('video');
            const canvas = document.getElementById('canvas');

            if (!video || video.videoWidth === 0) {
                alert('Cámara no lista, intente nuevamente');
                procesandoFoto = false;
                btn.disabled = false;
                btn.innerText = '📸 Tomar Foto';
                return;
            }

            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;

            canvas.getContext('2d').drawImage(video, 0, 0);

            canvas.toBlob(blob => {

                if (!blob) {
                    alert('No se pudo capturar la foto');
                    procesandoFoto = false;
                    btn.disabled = false;
                    btn.innerText = '📸 Tomar Foto';
                    return;
                }

                const file = new File([blob], 'selfie.jpg', {
                    type: 'image/jpeg'
                });

                @this.upload(
                    'selfie',
                    file,
                    () => {
                        if (stream) stream.getTracks().forEach(t => t.stop());
                        $('#modal_selfie').modal('hide');

                        Livewire.dispatch('selfie-capturada', {
                            tipo: tipoMarcacion
                        });
                    },
                    error => {
                        console.error(error);
                        alert('Error subiendo la foto');

                        procesandoFoto = false;
                        btn.disabled = false;
                        btn.innerText = '📸 Tomar Foto';

                        Livewire.dispatch('liberar-boton');
                    }
                );

            }, 'image/jpeg', 0.9);
        });
    </script>

    {{-- GPS --}}
    <script>
        window.addEventListener('capturar-ubicacion', event => {

            const tipo = event.detail.tipo;

            navigator.geolocation.getCurrentPosition(
                pos => {

                    if (pos.coords.accuracy > 150) {
                        toast('warning', 'Señal GPS débil');
                        Livewire.dispatch('liberar-boton');
                        return;
                    }

                    Livewire.dispatch('validar-ubicacion', {
                        lat: pos.coords.latitude,
                        lng: pos.coords.longitude,
                        tipo: tipo
                    });

                },
                () => toast('error', 'Debe permitir ubicación'), {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                }
            );
        });

        window.addEventListener('confirmar-fuera-sede', e => {
            const d = e.detail;
            console.log(e.detail);

            alertClickCallback(
                'Fuera del sitio',
                `Está a ${d.distancia}m de la sede ${d.sede}. ¿Desea continuar con la marcación?`,
                'warning',
                'Continuar',
                'Cancelar',
                async () => {

                    Livewire.dispatch('ubicacion-capturada', {
                        lat: d.lat,
                        lng: d.lng,
                        tipo: d.tipo
                    });

                }
            );

        });

        window.addEventListener('toast-ok', e => {
            toast('success', e.detail.msg);
        });
    </script>
</div>
