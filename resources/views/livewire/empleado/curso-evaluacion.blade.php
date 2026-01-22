<div>
    <style>
        .hover-option:hover {
            background: #f4f6f9;
        }

        .answer-option {
            cursor: pointer;
            border: 1px solid #e5e7eb;
            transition: .15s ease;
            background: #fff;
        }

        .answer-option:hover {
            background: #f4f6f9;
        }

        .answer-option input {
            position: static !important;
            margin: 0;
        }
    </style>
    <div class="content-wrapper p-3">

        <h4 class="mb-3">📝 Evaluación — {{ $curso->titulo }}</h4>

        <div class="alert alert-info text-center mb-3">
            ⏱️ Tiempo restante: <b id="timer"></b>
        </div>


        {{-- RESULTADO --}}
        @if ($finalizado)

            <div class="card p-4 text-center">

                <h3 class="{{ $aprobado ? 'text-success' : 'text-danger' }}">
                    {{ $aprobado ? '✅ Aprobado' : '❌ No aprobado' }}
                </h3>

                <h4>Nota: {{ $nota }}</h4>
                <p>Mínimo requerido: {{ $curso->nota_minima }}</p>

                <a href="{{ route('mis-cursos') }}" class="btn btn-primary mt-3">
                    Volver a mis cursos
                </a>

            </div>
        @else
            {{-- FORMULARIO --}}
            <form wire:submit.prevent="enviar">

                @foreach ($preguntas as $i => $p)
                    <div class="card p-4 mb-4 shadow-sm">
                        <b>{{ $i + 1 }}. {{ $p->pregunta }}</b>
                        <div class="mt-2">
                            @foreach ($p->respuestas as $r)
                                <label class="answer-option d-flex align-items-center mb-2 rounded px-3 py-2">

                                    <input type="radio" class="mr-3" name="pregunta_{{ $p->id }}"
                                        wire:model="respuestasUsuario.{{ $p->id }}" value="{{ $r->id }}">

                                    <span>{{ $r->respuesta }}</span>

                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach

                <div class="text-center mt-4">
                    <button class="btn btn-success btn-lg">
                        Enviar evaluación
                    </button>
                </div>
            </form>

        @endif

    </div>
    <script>
        let seconds = Math.floor(@json($segundosRestantes));

        const el = document.getElementById('timer');

        function format(s) {
            const m = Math.floor(s / 60);
            const r = s % 60;
            return `${m}:${r.toString().padStart(2,'0')}`;
        }

        el.innerText = format(seconds);

        const interval = setInterval(() => {
            seconds--;
            el.innerText = format(seconds);

            if (seconds <= 0) {
                clearInterval(interval);
                Livewire.dispatch('tiempoAgotado');
            }
        }, 1000);
    </script>
</div>
