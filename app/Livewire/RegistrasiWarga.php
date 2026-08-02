<?php

namespace App\Livewire;

use App\Models\Desa;
use App\Services\RegistrationService;
use Livewire\Component;
use Livewire\WithFileUploads;

class RegistrasiWarga extends Component
{
    use WithFileUploads;

    public $nama_lengkap = '';
    public $nik = '';
    public $email = '';
    public $provinsi = '';
    public $kabupaten = '';
    public $kecamatan = '';
    public $desa_id = '';
    public $ktp;
    public $password = '';
    public $password_confirmation = '';
    public $setuju_pernyataan = false;

    // Form status
    public $registrationComplete = false;
    public $registrationCode = '';

    public function updatedProvinsi()
    {
        $this->kabupaten = '';
        $this->kecamatan = '';
        $this->desa_id = '';
    }

    public function updatedKabupaten()
    {
        $this->kecamatan = '';
        $this->desa_id = '';
    }

    public function updatedKecamatan()
    {
        $this->desa_id = '';
    }

    public function register(RegistrationService $service)
    {
        $this->validate([
            'nama_lengkap' => 'required|min:3|max:150',
            'nik' => 'required|numeric|digits:16',
            'email' => 'required|email|max:255',
            'provinsi' => 'required',
            'kabupaten' => 'required',
            'kecamatan' => 'required',
            'desa_id' => 'required|exists:desas,id',
            'ktp' => 'required|file|mimes:jpg,jpeg,png,webp,pdf|max:5120',
            'password' => 'required|min:8|confirmed',
            'setuju_pernyataan' => 'accepted',
        ], [
            'nik.digits' => 'NIK harus berisi tepat 16 digit angka.',
            'ktp.max' => 'Ukuran file KTP maksimal 5MB.',
            'setuju_pernyataan.accepted' => 'Anda harus menyetujui pernyataan kebenaran data.',
        ]);

        $selectedDesa = Desa::findOrFail($this->desa_id);

        // Upload KTP to private storage
        $path = $this->ktp->store('ktp', 'local');

        $registrasi = $service->submitRegistration([
            'desa_id' => $selectedDesa->id,
            'nama_lengkap' => $this->nama_lengkap,
            'nik' => $this->nik,
            'email' => $this->email,
            'provinsi' => $this->provinsi,
            'kabupaten' => $this->kabupaten,
            'kecamatan' => $this->kecamatan,
            'password' => $this->password,
            'ktp_path' => $path,
            'ktp_nama_asli' => $this->ktp->getClientOriginalName(),
            'ktp_mime_type' => $this->ktp->getClientMimeType(),
            'ktp_size' => $this->ktp->getSize(),
        ], request()->ip(), request()->userAgent());

        $this->registrationCode = $registrasi->kode_registrasi;
        $this->registrationComplete = true;
    }

    public function render()
    {
        $hasAnyDesa = Desa::where('is_active', true)->exists();

        $provinsiList = Desa::where('is_active', true)->distinct()->pluck('provinsi');

        $kabupatenList = $this->provinsi
            ? Desa::where('is_active', true)->where('provinsi', $this->provinsi)->distinct()->pluck('kabupaten')
            : collect();

        $kecamatanList = ($this->provinsi && $this->kabupaten)
            ? Desa::where('is_active', true)->where('provinsi', $this->provinsi)->where('kabupaten', $this->kabupaten)->distinct()->pluck('kecamatan')
            : collect();

        $desasList = ($this->provinsi && $this->kabupaten && $this->kecamatan)
            ? Desa::where('is_active', true)->where('provinsi', $this->provinsi)->where('kabupaten', $this->kabupaten)->where('kecamatan', $this->kecamatan)->get()
            : collect();

        return view('livewire.registrasi-warga', [
            'hasAnyDesa' => $hasAnyDesa,
            'provinsiList' => $provinsiList,
            'kabupatenList' => $kabupatenList,
            'kecamatanList' => $kecamatanList,
            'desasList' => $desasList,
        ])->layout('layouts.guest');
    }
}
