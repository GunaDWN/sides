<?php

namespace App\Livewire;

use App\Models\Desa;
use App\Models\JenisSurat;
use Livewire\Component;

class LandingPage extends Component
{
    public function render()
    {
        $activeDesas = Desa::where('is_active', true)->get();
        $sampleDesa = $activeDesas->first();
        $jenisSurats = $sampleDesa
            ? JenisSurat::where('desa_id', $sampleDesa->id)->where('is_active', true)->get()
            : collect();

        return view('livewire.landing-page', [
            'activeDesas' => $activeDesas,
            'sampleDesa' => $sampleDesa,
            'jenisSurats' => $jenisSurats,
        ])->layout('layouts.guest');
    }
}
