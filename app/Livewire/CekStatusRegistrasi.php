<?php

namespace App\Livewire;

use App\Models\RegistrasiAkun;
use Livewire\Component;

class CekStatusRegistrasi extends Component
{
    public $kode_registrasi = '';
    public $identifier = ''; // NIK or Email

    public $hasilPencarian = null;
    public $notFound = false;

    public function cari()
    {
        $this->validate([
            'kode_registrasi' => 'required',
            'identifier' => 'required',
        ], [
            'kode_registrasi.required' => 'Kode registrasi wajib diisi.',
            'identifier.required' => 'NIK atau Email wajib diisi.',
        ]);

        $this->notFound = false;

        $registrasi = RegistrasiAkun::where('kode_registrasi', trim($this->kode_registrasi))
            ->where(function ($q) {
                $trimmed = trim($this->identifier);
                $q->where('nik', $trimmed)->orWhere('email', $trimmed);
            })
            ->with(['desa', 'diprosesOleh'])
            ->first();

        if (!$registrasi) {
            $this->hasilPencarian = null;
            $this->notFound = true;
        } else {
            $this->hasilPencarian = $registrasi;
        }
    }

    public function render()
    {
        return view('livewire.cek-status-registrasi')->layout('layouts.guest');
    }
}
