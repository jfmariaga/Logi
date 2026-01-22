<div class="mb-3">
    <label class="form-label">Dirección</label>
    <textarea class="form-control"
              rows="2"
              wire:model.live="settings.address"></textarea>
</div>
<div class="mb-3">
    <label class="form-label">Email</label>
    <input type="email"
           class="form-control"
           wire:model.live="settings.email">
</div>
<div class="mb-3">
    <label class="form-label">Teléfono</label>
    <input type="text"
           class="form-control"
           wire:model.live="settings.phone">
</div>
<div class="mb-3">
    <label class="form-label">Google Maps (iframe)</label>
    <textarea class="form-control"
              rows="4"
              wire:model.live="settings.map_embed"
              placeholder="<iframe ...></iframe>"></textarea>

    <small class="text-muted">
        Pega aquí el iframe completo de Google Maps
    </small>
</div>
