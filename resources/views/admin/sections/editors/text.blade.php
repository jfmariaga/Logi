<div class="mb-3">
    <label>Título</label>
    <input type="text" class="form-control" wire:model.live.debounce.300ms="settings.title">
</div>

<div class="mb-3">
    <label>Contenido</label>
    <textarea class="form-control" rows="6" wire:model.live.debounce.300ms="settings.content"></textarea>
</div>
