<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class Dashboard extends Component
{
    public function mount()
    {

    }

    public function render()
    {
        return view('livewire.dashboard.dashboard')->title('Dashboard');
    }
}
