@props(['index', 'campo'])

<input class="input-corporativo text-end"
    wire:model.defer="operaciones.{{ $index }}.{{ $campo }}"
    wire:change="guardarOperacion({{ $index }}, '{{ $campo }}')"
    oninput="
        let val = this.value.replace(/[^0-9]/g,'');
        this.value = val.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    ">
