<x-modal id="modal_preview_material" size="xl">

    <x-slot name="title">
        📖 Vista previa del material
    </x-slot>

    @if ($previewMaterial)

        <div class="mb-2">
            <h5>{{ $previewMaterial->titulo }}</h5>
            <small class="text-muted">Tipo: {{ strtoupper($previewMaterial->tipo) }}</small>
        </div>

        <div style="min-height:70vh">

            {{-- PDF --}}
            @if ($previewMaterial->tipo === 'pdf')
                <iframe
                    src="{{ asset('storage/'.$previewMaterial->archivo_path) }}"
                    width="100%"
                    height="100%"
                    style="border:1px solid #ccc; min-height:70vh"
                ></iframe>
            @endif

            {{-- VIDEO SUBIDO --}}
            @if ($previewMaterial->tipo === 'video')
                <video width="100%" height="100%" controls style="min-height:70vh">
                    <source src="{{ asset('storage/'.$previewMaterial->archivo_path) }}">
                </video>
            @endif

            {{-- PPT --}}
            @if ($previewMaterial->tipo === 'ppt')
                <div class="text-center mt-5">
                    <a
                        href="{{ asset('storage/'.$previewMaterial->archivo_path) }}"
                        target="_blank"
                        class="btn btn-primary btn-lg"
                    >
                        Abrir presentación
                    </a>
                </div>
            @endif

            {{-- LINK --}}
            @if ($previewMaterial->tipo === 'link')

                {{-- YOUTUBE --}}
                @if ($previewEmbed)
                    <iframe
                        width="100%"
                        height="100%"
                        src="{{ $previewEmbed }}"
                        frameborder="0"
                        allowfullscreen
                        style="min-height:70vh"
                    ></iframe>

                {{-- LINK NORMAL --}}
                @else
                    <div class="text-center mt-5">
                        <h5>🔗 Enlace externo</h5>
                        <p>Este contenido se abre en una nueva pestaña</p>

                        <a
                            href="{{ $previewMaterial->url }}"
                            target="_blank"
                            class="btn btn-primary btn-lg"
                        >
                            Abrir enlace
                        </a>
                    </div>
                @endif

            @endif

        </div>

    @else
        <p class="text-muted">Cargando material...</p>
    @endif

    <x-slot name="footer">
        <button class="btn btn-secondary" data-dismiss="modal">
            Cerrar
        </button>
    </x-slot>

</x-modal>
