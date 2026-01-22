<div class="card mb-4">
    <div class="card-header fw-bold">
        Contenido del Hero
    </div>

    <div class="card-body row">

        <div class="col-md-6 mb-3">
            <label class="form-label">Título principal</label>
            <input type="text" class="form-control form-control-lg" wire:model.live="settings.title">
        </div>

        {{-- <div class="col-md-6 mb-3">
            <label class="form-label">Color del texto</label>
            <input type="color" class="form-control form-control-color w-100" wire:model.live="settings.text_color">
        </div> --}}

        <div class="col-md-6 mb-3">
            <label class="form-label">Color del texto</label>
            <input type="color" class="form-control form-control-color w-100"
                wire:model.live.debounce.50ms="settings.text_color">
        </div>


        <div class="col-12 mb-3">
            <label class="form-label">Subtítulo</label>
            <input type="text" class="form-control" wire:model.live="settings.subtitle">
        </div>

    </div>
</div>

<div class="card mb-4">
    <div class="card-header fw-bold">
        Imagen de fondo
    </div>

    <div class="card-body row align-items-center">

        <div class="col-md-5">
            <input type="file" class="form-control" wire:model="heroBackground" accept="image/*">

            <small class="text-muted d-block mt-1">
                Recomendado: 1920 × 700 px
            </small>

            <div wire:loading wire:target="heroBackground" class="mt-2">
                <div class="spinner-border spinner-border-sm"></div>
                Cargando imagen…
            </div>
        </div>

        <div class="col-md-7 text-center">
            @if ($heroBackground)
                <img src="{{ $heroBackground->temporaryUrl() }}" class="img-fluid rounded"
                    style="max-height:220px; object-fit:cover;">
            @elseif (!empty($settings['background']))
                <img src="{{ asset($settings['background']) }}" class="img-fluid rounded"
                    style="max-height:220px; object-fit:cover;">
            @else
                <span class="text-muted">Sin imagen</span>
            @endif
        </div>

    </div>
</div>
