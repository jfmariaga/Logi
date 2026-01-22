<div>
    <div class="content-wrapper p-3">

        <h4 class="mb-3">🎓 Mis Cursos</h4>

        <div class="row">

            @forelse ($cursos as $c)
                <div class="col-md-4 mb-3">
                    <div class="card h-100 shadow-sm">

                        <div class="card-body">

                            <h5>{{ $c['titulo'] }}</h5>

                            <p class="text-muted small">
                                {{ $c['descripcion'] }}
                            </p>

                            <ul class="list-unstyled small mb-2">
                                <li>📘 Materiales: {{ $c['materiales'] }}</li>
                                <li>📝 Preguntas: {{ $c['preguntas'] }}</li>
                                <li>🎯 Intentos: {{ $c['intentos'] }}/{{ $c['max_intentos'] }}</li>
                            </ul>

                            {{-- @if ($c['aprobado'])
                                <span class="badge badge-success">Aprobado</span>
                                <p class="mt-1 small">Nota: {{ $c['nota'] }}</p>
                            @elseif ($c['intentos'] >= $c['max_intentos'])
                                <span class="badge badge-danger">Sin intentos</span>
                            @elseif ($c['intentos'] > 0)
                                <span class="badge badge-warning">Reintento</span>
                            @else
                                <span class="badge badge-info">Pendiente</span>
                            @endif --}}
                            @if (!$c['activo'])
                                <span class="badge badge-secondary">Histórico</span>
                            @endif

                            @if ($c['aprobado'])
                                <span class="badge badge-success">Aprobado</span>
                                <p class="mt-1 small">Nota: {{ $c['nota'] }}</p>
                            @elseif ($c['intentos'] >= $c['max_intentos'])
                                <span class="badge badge-danger">Sin intentos</span>
                            @elseif ($c['intentos'] > 0)
                                <span class="badge badge-warning">Reintento</span>
                            @else
                                <span class="badge badge-info">Pendiente</span>
                            @endif

                        </div>

                        <div class="card-footer bg-white text-right">
                            @if ($c['activo'])
                                <a href="{{ route('mis-cursos.player', $c['id']) }}" class="btn btn-sm btn-primary">
                                    Iniciar
                                </a>
                            @else
                                <button class="btn btn-sm btn-secondary" disabled>
                                    Curso cerrado
                                </button>
                            @endif
                        </div>

                    </div>
                </div>

            @empty

                <div class="col-12">
                    <div class="alert alert-info">
                        No tienes cursos asignados aún.
                    </div>
                </div>
            @endforelse

        </div>
    </div>
</div>
