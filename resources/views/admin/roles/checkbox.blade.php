<div class="form-check">
    <input
        type="checkbox"
        wire:model="selectedPermissions"
        value="{{ $p->name }}"
        class="form-check-input"
        id="permiso_{{ $p->id }}"
    >
    <label class="form-check-label" for="permiso_{{ $p->id }}">
        {{ $p->name }}
    </label>
</div>
