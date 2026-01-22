<x-modal id="form_preguntas" size="xl">

    <div x-data="{
        tipo: @entangle('tipo'),
        respuestas: @entangle('respuestas')
    }" x-cloak>

        <x-slot name="title">
            <span x-show="!$wire.pregunta_id">Agregar pregunta</span>
            <span x-show="$wire.pregunta_id">Editar pregunta</span>
        </x-slot>

        <div class="row">

            <div class="col-md-12 mt-1">
                <x-input model="$wire.pregunta" label="Pregunta" required="true" />
            </div>

            <div class="col-md-6 mt-1">
                <label class="form-label">Tipo *</label>
                <select class="form-control" x-model="tipo" wire:model="tipo"
                    @change="
                        if(tipo === 'verdadero_falso'){
                            respuestas = [
                                {texto:'Verdadero', correcta:false},
                                {texto:'Falso', correcta:false}
                            ];
                        }else{
                            if(respuestas.length < 2){
                                respuestas = [
                                    {texto:'', correcta:false},
                                    {texto:'', correcta:false}
                                ];
                            }
                        }
                    "
                >
                    <option value="opcion_multiple">Opción múltiple</option>
                    <option value="verdadero_falso">Verdadero / Falso</option>
                </select>
            </div>

        </div>

        {{-- RESPUESTAS --}}
        <div class="row mt-3">

            <div class="col-12 mb-2">
                <b>Respuestas (una correcta)</b>
                @error('respuestas') <span class="c_red d-block">{{ $message }}</span> @enderror
            </div>

            <template x-for="(r,index) in respuestas" :key="index">
                <div class="col-md-6 mb-2">
                    <div class="d-flex align-items-center">

                        <input type="text"
                               class="form-control mr-2"
                               x-model="r.texto"
                               :readonly="tipo === 'verdadero_falso'"
                               placeholder="Respuesta">

                        <input type="radio"
                               name="correcta"
                               @click="
                                   respuestas.forEach(x=>x.correcta=false);
                                   r.correcta = true;
                               "
                               :checked="r.correcta">

                        <button class="btn btn-sm btn-danger ml-2"
                                x-show="tipo === 'opcion_multiple'"
                                @click="respuestas.splice(index,1)">
                            ✖
                        </button>

                    </div>
                </div>
            </template>

            <div class="col-12">
                <button class="btn btn-sm btn-primary"
                        x-show="tipo === 'opcion_multiple'"
                        @click="respuestas.push({texto:'',correcta:false})">
                    + Agregar respuesta
                </button>
            </div>

        </div>

        <x-slot name="footer">
            <button class="btn btn-secondary" data-dismiss="modal">
                Cancelar
            </button>

            <button class="btn btn-primary" x-on:click="saveFront()">
                Guardar
            </button>
        </x-slot>

    </div>
</x-modal>
