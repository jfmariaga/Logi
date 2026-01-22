
{{-- SUBTÍTULO --}}
<div class="mb-3">
    <label class="form-label">Subtítulo</label>
    <input type="text"
           class="form-control"
           wire:model.live="settings.subtitle">
</div>

{{-- TÍTULO --}}
<div class="mb-4">
    <label class="form-label">Título</label>
    <input type="text"
           class="form-control"
           wire:model.live="settings.title">
</div>

<hr>

<h5 class="mb-3">Servicios (máx. 8)</h5>

@foreach (($settings['items'] ?? []) as $index => $item)
    <div class="border rounded p-3 mb-3">

        {{-- TÍTULO INTERNO --}}
        <div class="mb-2">
            <label>Título interno (admin)</label>
            <input type="text"
                   class="form-control"
                   wire:model.live="settings.items.{{ $index }}.title">
        </div>

        {{-- DESCRIPCIÓN (VISIBLE EN TARJETA) --}}
        <div class="mb-2">
            <label>Descripción / Comentario</label>
            <textarea class="form-control"
                      rows="2"
                      wire:model.live="settings.items.{{ $index }}.description"></textarea>
        </div>

        {{-- IMAGEN --}}
        <div class="mb-2">
            <label>Imagen</label>
            <input type="file"
                   class="form-control"
                   wire:model="serviceImages.{{ $index }}">
        </div>

        <button class="btn btn-sm btn-danger mt-2"
                wire:click="removeService({{ $index }})">
            Eliminar
        </button>
    </div>
@endforeach

@if (count($settings['items'] ?? []) < 8)
    <button class="btn btn-outline-primary"
            wire:click="addService">
        + Agregar servicio
    </button>
@endif
