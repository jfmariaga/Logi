
<div class="mb-4">
    <label class="form-label">Título</label>
    <input type="text"
           class="form-control"
           wire:model.live="settings.title">
</div>
<hr>
<h5 class="mb-3">Clientes</h5>

@foreach (($settings['items'] ?? []) as $index => $item)
    <div class="border rounded p-3 mb-3">

        <label>Logo</label>
        <input type="file"
               class="form-control"
               wire:model="clientImages.{{ $index }}">

        <div class="mt-2">
            @if (!empty($item['image']))
                <img src="{{ asset($item['image']) }}"
                     style="max-height:80px">
            @endif
        </div>

        <button class="btn btn-sm btn-danger mt-2"
                wire:click="removeClient({{ $index }})">
            Eliminar
        </button>
    </div>
@endforeach

<button class="btn btn-outline-primary"
        wire:click="addClient">
    + Agregar cliente
</button>
