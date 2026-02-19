<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Curso;
use App\Models\CursoMaterial;
use Illuminate\Support\Facades\Storage;

class CursoMateriales extends Component
{
    use WithFileUploads;

    public $curso;
    public $material_id, $tipo, $titulo, $archivo, $url, $orden = 1;
    public $archivo_actual;
    public $previewMaterial;
    public $previewEmbed = null;

    protected $rules = [
        'tipo' => 'required',
        'titulo' => 'required|string|max:255',
        'orden' => 'required|integer|min:1',
        'url' => 'nullable|string',
    ];

    public function mount($curso_id)
    {
        $this->curso = Curso::findOrFail($curso_id);
    }

    public function getMateriales()
    {
        $this->skipRender();

        return CursoMaterial::where('curso_id', $this->curso->id)
            ->orderBy('orden')
            ->get()
            ->toArray();
    }

    /* ================= PREVIEW ================= */

    public function preview($id)
    {
        $this->previewMaterial = CursoMaterial::findOrFail($id);
        $this->previewEmbed = null;

        if ($this->previewMaterial->tipo === 'link') {
            $this->previewEmbed = $this->youtubeEmbed($this->previewMaterial->url);
        }
    }

    private function youtubeEmbed($url)
    {
        if (str_contains($url, 'youtu.be/')) {
            $id = explode('youtu.be/', $url)[1];
        } elseif (str_contains($url, 'watch?v=')) {
            parse_str(parse_url($url, PHP_URL_QUERY), $vars);
            $id = $vars['v'] ?? null;
        } else {
            return null;
        }

        return $id ? "https://www.youtube.com/embed/" . $id : null;
    }

    /* ================= CRUD ================= */
    public function save()
    {
        $this->validate();

        $path = null;

        /* ===================== ARCHIVOS NORMALES ===================== */
        if (in_array($this->tipo, ['pdf', 'ppt']) && $this->archivo) {
            $path = $this->archivo->store('cursos/materiales', 'public');
        }

        /* ===================== VIDEO ===================== */
        if ($this->tipo === 'video') {

            if (!$this->url) {
                $this->addError('archivo', 'Primero debes subir el video');
                return false;
            }

            // convertir /storage/cursos/videos/xxx.mp4 → cursos/videos/xxx.mp4
            $path = str_replace('/storage/', '', $this->url);
        }

        /* ===================== UPDATE ===================== */
        if ($this->material_id) {

            $material = CursoMaterial::find($this->material_id);

            if ($material) {

                if ($path && $material->archivo_path && $this->tipo !== 'video') {
                    Storage::disk('public')->delete($material->archivo_path);
                }

                $material->update([
                    'tipo' => $this->tipo,
                    'titulo' => $this->titulo,
                    'archivo_path' => $path ?? $material->archivo_path,
                    'url' => $this->tipo === 'link' ? $this->url : null,
                    'orden' => $this->orden,
                ]);
            }
        } else {

            /* ===================== CREATE ===================== */
            $material = CursoMaterial::create([
                'curso_id' => $this->curso->id,
                'tipo' => $this->tipo,
                'titulo' => $this->titulo,
                'archivo_path' => $path,
                'url' => $this->tipo === 'link' ? $this->url : null,
                'orden' => $this->orden,
            ]);
        }

        if ($material) {
            $this->dispatch('material-guardado', material: $material->id);
            $this->limpiar();
            return;
        }
    }


    public function getMaterialById($id)
    {
        $this->skipRender();
        return CursoMaterial::find($id)->toArray();
    }

    public function eliminar($id)
    {
        $material = CursoMaterial::find($id);

        if ($material) {
            if ($material->archivo_path) {
                Storage::disk('public')->delete($material->archivo_path);
            }
            $material->delete();
            $this->limpiar();
            return true;
        }
        return false;
    }

    public function limpiar()
    {
        $this->reset(['material_id', 'tipo', 'titulo', 'archivo', 'url', 'orden', 'archivo_actual', 'previewMaterial', 'previewEmbed']);
        $this->orden = 1;
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.admin.curso-materiales')->title('Materiales del Curso');
    }
}
