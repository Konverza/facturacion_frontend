<?php

namespace App\Livewire\Business\Tables;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\BusinessCustomer;


class Clients extends Component
{
    public function render()
    {
        return view('livewire.business.tables.clients');
    }
}
