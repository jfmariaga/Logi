<?php

namespace App\Livewire\PaginaWeb;

use App\Models\Page;
use Livewire\Component;

class Pagina extends Component
{
      public Page $page;

    public function mount()
    {
        $this->page = Page::active()
            ->where('slug', 'home')
            ->with([
                'sections' => fn ($q) =>
                    $q->where('is_active', true)->orderBy('order')
            ])
            ->firstOrFail();
    }

    public function render()
    {
        return view('livewire.pagina-web.pagina', [
                'page' => $this->page
            ])
            ->layout('components.layouts.pagina_web')
            ->title($this->page->name);
    }
    // public function render()
    // {
    //     return view('livewire.pagina-web.pagina')
    //                 ->layout('components.layouts.pagina_web')
    //                 ->title('Logisticarga JM S.A.S');
    // }
}
