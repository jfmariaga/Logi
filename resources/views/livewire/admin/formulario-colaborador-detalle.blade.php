<div class="content-wrapper p-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="content-header-title">Detalle del formulario</h3>
        <a href="{{ route('formularios.colaboradores') }}" class="btn btn-outline-secondary">Volver</a>
    </div>

    <div class="card mb-3"><div class="card-body">
        <strong>Colaborador:</strong> {{ trim($formulario->colaborador->name . ' ' . $formulario->colaborador->last_name) }}
        <span class="ml-3"><strong>Documento:</strong> {{ $formulario->colaborador->document }}</span>
        <span class="ml-3"><strong>Estado:</strong> {{ ucfirst($formulario->estado) }}</span>
    </div></div>

    @foreach ($formulario->datos as $campo => $valor)
        @continue(in_array($campo, ['autoriza_datos', 'autoriza_financieros', 'autoriza_judiciales']))
        <div class="card mb-2"><div class="card-body py-2">
            <div class="row"><div class="col-md-5 font-weight-bold">{{ ucwords(str_replace('_', ' ', $campo)) }}</div><div class="col-md-7">{{ is_bool($valor) ? ($valor ? 'Sí' : 'No') : ($valor ?: '—') }}</div></div>
        </div></div>
    @endforeach

    <div class="card mb-3"><div class="card-body">
        <h5>Autorizaciones y firma</h5>
        <p class="mb-1">Tratamiento de datos: {{ $formulario->datos['autoriza_datos'] ? 'Aceptada' : 'No aceptada' }}</p>
        <p class="mb-1">Datos financieros: {{ $formulario->datos['autoriza_financieros'] ? 'Aceptada' : 'No aceptada' }}</p>
        <p class="mb-1">Datos judiciales: {{ $formulario->datos['autoriza_judiciales'] ? 'Aceptada' : 'No aceptada' }}</p>
        <p class="mb-2"><strong>Firma manuscrita:</strong></p>
        @if ($formulario->firma && str_starts_with($formulario->firma, 'formularios_colaboradores/'))
            <img src="{{ Storage::url($formulario->firma) }}" alt="Firma manuscrita" style="max-width:320px; max-height:120px; border:1px solid #d8e0e8; padding:8px; background:#fff;">
        @else
            <span class="text-muted">No disponible</span>
        @endif
    </div></div>
</div>
