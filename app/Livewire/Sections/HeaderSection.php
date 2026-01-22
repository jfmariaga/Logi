<?php

namespace App\Livewire\Sections;

use App\Models\PageSection;
use Livewire\Component;

class HeaderSection extends Component
{
     public PageSection $section;
    public array $override = [];

    public function render()
    {
        return view('livewire.sections.header-section', [
            'data' => array_merge(
                $this->section->settings ?? [],
                $this->override
            ),
            'isPreview' => !empty($this->override),
        ]);
    }
}
