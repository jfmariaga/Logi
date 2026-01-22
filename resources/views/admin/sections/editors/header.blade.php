

<div class="row">

    {{-- TELÉFONO --}}
    <div class="col-md-6 mb-3">
        <label class="form-label">Teléfono</label>
        <input type="text"
               class="form-control"
               placeholder="(+57) 311 332 9237"
               wire:model.live="settings.phone">
    </div>

    {{-- EMAIL --}}
    <div class="col-md-6 mb-3">
        <label class="form-label">Email</label>
        <input type="email"
               class="form-control"
               placeholder="correo@correo.com"
               wire:model.live="settings.email">
    </div>

    {{-- DIRECCIÓN --}}
    <div class="col-12 mb-3">
        <label class="form-label">Dirección</label>
        <input type="text"
               class="form-control"
               placeholder="Km 35 Autopista Medellín - Bogotá"
               wire:model.live="settings.address">
    </div>

</div>

<hr class="my-4">

{{-- LOGO --}}
<div class="row align-items-center">

    <div class="col-md-6 mb-3">
        <label class="form-label">Logo</label>
        <input type="file"
               class="form-control"
               wire:model="logoUpload"
               accept="image/*">
    </div>

    <div class="col-md-6 text-center">
        @if (!empty($settings['logo']))
            <div class="border rounded p-2 d-inline-block bg-light">
                <img src="{{ asset($settings['logo']) }}"
                     alt="Logo"
                     style="max-height:80px; max-width:100%;">
            </div>
        @else
            <small class="text-muted d-block mt-4">
                No hay logo cargado
            </small>
        @endif
    </div>

</div>
