<div class="container py-4">

    <h2 class="mb-4">
        Editor de página:
        <strong>{{ $page->name }}</strong>
    </h2>

    {{-- LISTADO DE SECCIONES --}}
    <ul class="list-group" wire:sortable="updateOrder" wire:sortable.options="{ animation: 150, direction: 'vertical' }">

        @foreach ($sections as $section)
            <li class="list-group-item d-flex justify-content-between align-items-center"
                wire:key="section-{{ $section->id }}" wire:sortable.item="{{ $section->id }}">

                <div class="d-flex align-items-center gap-3">
                    <span wire:sortable.handle style="cursor: grab; user-select:none">
                        ☰
                    </span>

                    <strong>{{ strtoupper($section->type) }}</strong>
                    <span class="text-muted">{{ $section->title ?? 'Sin título' }}</span>
                </div>

                <div class="d-flex gap-2">
                    <button class="btn btn-sm {{ $section->is_active ? 'btn-success' : 'btn-secondary' }}"
                        wire:click="toggleSection({{ $section->id }})">
                        {{ $section->is_active ? 'Activa' : 'Inactiva' }}
                    </button>

                    <button class="btn btn-sm btn-primary" wire:click="editSection({{ $section->id }})">
                        Editar
                    </button>
                </div>
            </li>
        @endforeach
    </ul>


    {{-- MODAL DE EDICIÓN --}}
    @if ($editingSection)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,.5)">

            <div class="modal-dialog modal-xl modal-dialog-scrollable" style="max-height: 90vh;">

                <div class="modal-content">

                    {{-- HEADER --}}
                    <div class="modal-header">
                        <h5 class="modal-title">
                            Editar sección:
                            <strong>{{ strtoupper($editingType) }}</strong>
                        </h5>

                        <button type="button" class="btn-close" wire:click="closeModal"></button>
                    </div>

                    {{-- BODY (SCROLL AQUÍ) --}}
                    <div class="modal-body" style="max-height: calc(90vh - 140px); overflow-y: auto;">

                        {{-- FORMULARIO --}}
                        <div class="mb-4">
                            @includeIf('admin.sections.editors.' . $editingType)
                        </div>

                        <hr>
                        <h5 class="mb-3">Previsualización</h5>

                        {{-- <div class="btn-group mb-3">
                            <button class="btn btn-outline-primary btn-sm" wire:click="$set('previewDevice','desktop')">
                                Desktop
                            </button>
                            <button class="btn btn-outline-primary btn-sm" wire:click="$set('previewDevice','mobile')">
                                Mobile
                            </button>
                        </div> --}}

                        <div class="mx-auto border rounded"
                            style="
                                    width: {{ $previewDevice === 'mobile' ? '375px' : '100%' }};
                                    transition: width .3s;
                                    overflow:hidden;
                                ">
                            @livewire(
                                'sections.' . $editingType . '-section',
                                [
                                    'section' => $editingSection,
                                    'override' => $settings,
                                ],
                                key('preview-' . $editingSection->id . '-' . md5(json_encode($settings)))
                            )
                        </div>
                    </div>

                    {{-- FOOTER --}}
                    <div class="modal-footer d-flex justify-content-between">
                        <button class="btn btn-secondary" wire:click="closeModal">
                            Cancelar
                        </button>

                        <button class="btn btn-success" wire:click="saveSection">
                            Publicar
                        </button>
                    </div>

                </div>
            </div>
        </div>
    @endif


</div>
