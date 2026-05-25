<?php

namespace App\Livewire\EmpresaParametro;

use App\Services\EmpresaParametroService;
use Illuminate\Support\Facades\Crypt;
use Livewire\Component;

class Show extends Component
{
    public $empresa;
    public $idEnc;

    public function mount(): void
    {
        $this->empresa = (new EmpresaParametroService())->show();

        if ($this->empresa) {
            $this->idEnc = Crypt::encrypt($this->empresa->id);
        }
    }

    public function render()
    {
        return view('livewire.empresa-parametro.show');
    }
}
