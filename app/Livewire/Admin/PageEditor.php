<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Page;
use App\Models\PageSection;

class PageEditor extends Component
{
    use WithFileUploads;

    public Page $page;
    public $sections = [];
    public $editingSection = null;
    public $editingType = null;
    public $settings = [];
    public $heroBackground;
    public $previewDevice = 'desktop';
    public $serviceImages = [];
    public $featureImage;
    public $clientImages = [];

    public function mount(string $slug)
    {
        $this->page = Page::where('slug', $slug)
            ->with('sections')
            ->firstOrFail();

        $this->refreshSections();
    }

    // --------------------- Para el header-----------------------------------

    public function updatedLogoUpload()
    {
        $path = $this->logoUpload->store('headers', 'public');
        $this->settings['logo'] = 'storage/' . $path;
    }

    public function toggleSection($sectionId)
    {
        PageSection::findOrFail($sectionId)
            ->update(['is_active' => !PageSection::find($sectionId)->is_active]);

        $this->refreshSections();
    }

    protected function refreshSections()
    {
        $this->sections = $this->page->sections()->orderBy('order')->get();
    }

    public function editSection($id)
    {
        $section = PageSection::findOrFail($id);

        $this->editingSection = $section;
        $this->editingType = $section->type;
        $this->settings = $section->settings ?? [];
        // dd($this->settings);
    }

    // --------------------------visualiza la imagen del banner-------------------------------

    public function updatedHeroBackground()
    {
        if ($this->heroBackground) {
            //clave para preview inmediato
            $this->settings['_temp_background'] = $this->heroBackground->temporaryUrl();
        }
    }

    // -------------------------------Agregar servicios--------------------------------------------
    public function addService()
    {
        $this->settings['items'][] = [
            'title'       => '',
            'description' => '',
            'image'       => '',
            'link'        => '',
        ];
    }

    public function removeService($index)
    {
        unset($this->settings['items'][$index]);
        $this->settings['items'] = array_values($this->settings['items']);
    }

    public function updatedServiceImages($file, $index)
    {
        $path = $file->store('services', 'public');
        $this->settings['items'][$index]['image'] = 'storage/' . $path;
    }


    // -------------------------------------------------FeaturSection----------------------------------------------
    public function addFeatureItem()
    {
        $this->settings['items'][] = [
            'text' => ''
        ];
    }

    public function removeFeatureItem($index)
    {
        unset($this->settings['items'][$index]);
        $this->settings['items'] = array_values($this->settings['items']);
    }

    public function updatedFeatureImage()
    {
        if ($this->featureImage) {
            // preview inmediato
            $this->settings['_temp_image'] = $this->featureImage->temporaryUrl();
        }
    }

    // ------------------------------------Clientes---------------------------------------------------

    public function addClient()
    {
        $this->settings['items'][] = [
            'image' => null
        ];
    }

    public function removeClient($index)
    {
        unset($this->settings['items'][$index]);
        $this->settings['items'] = array_values($this->settings['items']);
    }

    public function updatedClientImages($value, $key)
    {
        $this->settings['_temp_images'][$key] = $value->temporaryUrl();
    }


    public function saveSection()
    {
        if ($this->editingType === 'hero' && $this->heroBackground) {
            $path = $this->heroBackground->store('hero', 'public');
            $this->settings['background'] = 'storage/' . $path;
        }
        unset($this->settings['_temp_background']);


        if ($this->editingType === 'Feature' && $this->featureImage) {
            $path = $this->featureImage->store('feature', 'public');
            $this->settings['image'] = 'storage/' . $path;
        }

        unset($this->settings['_temp_image']);

        $this->editingSection->update([
            'settings' => $this->settings,
        ]);

        if ($this->editingType === 'clients' && !empty($this->clientImages)) {

            foreach ($this->clientImages as $index => $image) {
                if ($image) {
                    $path = $image->store('clients', 'public');
                    $this->settings['items'][$index]['image'] = 'storage/' . $path;
                }
            }
        }

        unset($this->settings['_temp_images']);


        $this->reset(['editingSection', 'editingType', 'settings', 'heroBackground','featureImage','clientImages']);
        $this->refreshSections();
    }

    public function closeModal()
    {
        $this->reset(['editingSection', 'editingType', 'settings', 'heroBackground']);
    }

    public function render()
    {
        return view('livewire.admin.page-editor');
    }
}
