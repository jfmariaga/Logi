<div>
    <div class="content-wrapper p-3">

        <h4 class="mb-3">🎓 {{ $curso->titulo }}</h4>

        <div class="row">

            {{-- COLUMNA IZQUIERDA --}}
            <div class="col-md-4">

                {{-- CONTENIDO --}}
                <div class="card p-2 mb-3">
                    <b>📚 Contenido</b>

                    <ul class="list-group mt-2">
                        @foreach ($materiales as $m)
                            <li class="list-group-item pointer d-flex justify-content-between align-items-center
                                {{ $materialActual?->id === $m->id ? 'bg-primary text-white fw-bold' : '' }}"
                                wire:click="cambiarMaterial({{ $m->id }})">
                                <span>{{ $m->orden }}. {{ $m->titulo }}</span>

                                @if ($materialActual?->id === $m->id)
                                    ▶️
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>

                {{-- BLOQUE EVALUACIÓN --}}
                <div class="card p-3">

                    <b>📝 Evaluación</b>
                    <div class="mb-2">
                        Intentos usados: {{ $intentosUsados }} / {{ $curso->max_intentos }}
                        @if ($mejorNota !== null)
                            <br>Mejor nota: {{ $mejorNota }}
                        @endif
                    </div>

                    @if ($puedeEvaluar)
                        <button class="btn btn-success w-100" wire:click="iniciarEvaluacion">
                            Iniciar evaluación
                        </button>
                    @else
                        <div class="alert alert-warning mb-2">
                            {{ $mensajeBloqueo }}
                        </div>

                        <button class="btn btn-secondary w-100" disabled>
                            Evaluación no disponible
                        </button>
                    @endif

                </div>
            </div>

            {{-- VISOR --}}
            <div class="col-md-8">

                <div class="card p-3 mb-3">

                    <h5>{{ $materialActual?->titulo }}</h5>

                    <hr>

                    {{-- PDF --}}
                    @if ($materialActual?->tipo === 'pdf')
                        <iframe src="{{ asset('storage/' . $materialActual->archivo_path) }}" width="100%"
                            height="500">
                        </iframe>
                    @endif

                    {{-- VIDEO SUBIDO --}}
                    @if ($materialActual?->tipo === 'video')
                        <video width="100%" height="450" controls>
                            <source src="{{ asset('storage/' . $materialActual->archivo_path) }}">
                        </video>
                    @endif

                    {{-- PPT --}}
                    @if ($materialActual?->tipo === 'ppt')
                        <a href="{{ asset('storage/' . $materialActual->archivo_path) }}" target="_blank"
                            class="btn btn-primary">
                            Abrir presentación
                        </a>
                    @endif

                    {{-- LINK (YOUTUBE EMBEBIDO SI APLICA) --}}
                    @if ($materialActual?->tipo === 'link')
                        @if ($embedYoutube)
                            <iframe width="100%" height="450" src="{{ $embedYoutube }}" frameborder="0"
                                allowfullscreen>
                            </iframe>
                        @else
                            <a href="{{ $materialActual->url }}" target="_blank" class="btn btn-info">
                                Abrir enlace
                            </a>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
