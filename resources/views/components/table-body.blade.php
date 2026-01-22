@props(['id', 'extra', 'head'])

<div wire:ignore class="card-content collapse show">
    <div class="card-body card-dashboard">
        <table class="table table-striped w-100 {{ $extra ?? '' }}" id="{{ $id }}">
            <thead>
                {{ $head }}
            </thead>

            {{-- el body es el slot --}}
            {{ $slot }}
        </table>
    </div>
</div>