<div class="mb-4">
    <label class="form-label">Título</label>
    <input type="text"
           class="form-control"
           wire:model.live="settings.title">
</div>

<div class="mb-4">
    <label class="form-label">Párrafo descriptivo</label>
    <textarea class="form-control"
              rows="3"
              wire:model.live="settings.paragraph"
              placeholder="Velamos por el bienestar, la calidad de vida e integridad de nuestros empleados...">
    </textarea>
</div>

<div class="mb-4">
    <label class="form-label">Imagen lateral</label>

    <input type="file"
           class="form-control"
           wire:model="featureImage">

    <div class="mt-2">
        @if ($featureImage)
            <img src="{{ $featureImage->temporaryUrl() }}"
                 class="img-fluid rounded"
                 style="max-height:200px">
        @elseif (!empty($settings['image']))
            <img src="{{ asset($settings['image']) }}"
                 class="img-fluid rounded"
                 style="max-height:200px">
        @endif
    </div>
</div>

<hr>
<h5 class="mb-3">Checks</h5>

@foreach (($settings['items'] ?? []) as $index => $item)
    <div class="border rounded p-3 mb-3">

        <label>Texto</label>
        <textarea class="form-control"
                  rows="2"
                  wire:model.live="settings.items.{{ $index }}.text"></textarea>

        <button class="btn btn-sm btn-danger mt-2"
                wire:click="removeFeatureItem({{ $index }})">
            Eliminar
        </button>
    </div>
@endforeach

<button class="btn btn-outline-primary"
        wire:click="addFeatureItem">
    + Agregar check
</button>
