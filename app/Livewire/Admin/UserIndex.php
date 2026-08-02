<?php

namespace App\Livewire\Admin;

use App\Models\Desa;
use App\Models\User;
use App\Models\Warga;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithPagination;

class UserIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $roleFilter = '';

    public $user_id = null;
    public $name = '';
    public $email = '';
    public $password = '';
    public $role = 'warga';
    public $desa_id = '';
    public $warga_id = '';
    public $is_active = true;

    public $showModal = false;

    public function openModal($id = null)
    {
        $this->resetValidation();
        $this->reset(['user_id', 'name', 'email', 'password', 'role', 'desa_id', 'warga_id']);
        $this->is_active = true;

        if ($id) {
            $user = User::findOrFail($id);
            $this->user_id = $user->id;
            $this->name = $user->name;
            $this->email = $user->email;
            $this->role = $user->role;
            $this->desa_id = $user->desa_id;
            $this->warga_id = $user->warga_id;
            $this->is_active = $user->is_active;
        }

        $this->showModal = true;
    }

    public function save()
    {
        $rules = [
            'name' => 'required|max:150',
            'email' => 'required|email|unique:users,email,' . $this->user_id,
            'role' => 'required|in:admin,warga',
        ];

        if (!$this->user_id) {
            $rules['password'] = 'required|min:8';
        }

        $this->validate($rules);

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'desa_id' => $this->desa_id ?: null,
            'warga_id' => $this->warga_id ?: null,
            'is_active' => $this->is_active,
            'status' => $this->is_active ? 'active' : 'nonaktif',
        ];

        if (!empty($this->password)) {
            $data['password'] = Hash::make($this->password);
        }

        if ($this->user_id) {
            User::findOrFail($this->user_id)->update($data);
            session()->flash('success', 'Pengguna berhasil diperbarui!');
        } else {
            $data['approved_at'] = now();
            $data['approved_by'] = auth()->id();
            User::create($data);
            session()->flash('success', 'Pengguna baru berhasil ditambahkan!');
        }

        $this->showModal = false;
    }

    public function toggleActive($id)
    {
        $u = User::findOrFail($id);
        $u->update([
            'is_active' => !$u->is_active,
            'status' => !$u->is_active ? 'active' : 'nonaktif',
        ]);
        session()->flash('success', 'Status akun pengguna diubah.');
    }

    public function render()
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $query = User::query()->with(['desa', 'warga']);

        if ($this->search) {
            $s = '%' . trim($this->search) . '%';
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', $s)->orWhere('email', 'like', $s);
            });
        }

        if ($this->roleFilter) {
            $query->where('role', $this->roleFilter);
        }

        $users = $query->latest()->paginate(10);
        $desasList = Desa::where('is_active', true)->get();
        $wargasList = Warga::latest()->take(50)->get();

        return view('livewire.admin.user-index', [
            'users' => $users,
            'desasList' => $desasList,
            'wargasList' => $wargasList,
        ])->layout('layouts.app');
    }
}
