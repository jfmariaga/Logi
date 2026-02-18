@props(['campo'])

<input class="input-corporativo text-end" wire:model.defer="datos.{{ $campo }}"
    wire:change="guardar('{{ $campo }}','number')"
    oninput="
        let val = this.value.replace(/[^0-9]/g,'');
        this.value = val.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    ">
