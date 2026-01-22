<?php

namespace App\Livewire\Sections;

use App\Models\PageSection;
use Livewire\Component;

class TextSection extends Component
{
    public PageSection $section;
    public array $override = [];

    public function render()
    {
        return view('livewire.sections.text-section', [
            'data' => $this->override ?: ($this->section->settings ?? [])
        ]);
    }
}
