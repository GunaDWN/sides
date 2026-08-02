<div class="min-h-screen flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-md space-y-8">
        <div class="text-center space-y-3">
            <img src="{{ asset('images/logo.png') }}" alt="SIDES Logo" class="w-16 h-16 object-contain mx-auto">
            <h1 class="text-3xl font-extrabold text-slate-900">Masuk ke <span class="bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">SIDES</span></h1>
            <p class="text-sm text-slate-500">Sistem Informasi Desa — Portal Administrasi</p>
        </div>

        @if(session('status'))
            <div class="p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-semibold text-center">{{ session('status') }}</div>
        @endif

        <form wire:submit="login" class="bg-white rounded-2xl border border-slate-200 shadow-xl p-8 space-y-5">
            <div>
                <label for="email" class="block text-xs font-bold text-slate-700 mb-1 uppercase tracking-wider">Email</label>
                <input id="email" type="email" wire:model="email" autofocus
                    class="w-full rounded-xl border-slate-300 shadow-sm text-sm focus:border-emerald-500 focus:ring-emerald-500 py-3 px-4"
                    placeholder="admin@desa.id">
                @error('email') <p class="mt-1 text-xs text-rose-500 font-medium">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="password" class="block text-xs font-bold text-slate-700 mb-1 uppercase tracking-wider">Password</label>
                <input id="password" type="password" wire:model="password"
                    class="w-full rounded-xl border-slate-300 shadow-sm text-sm focus:border-emerald-500 focus:ring-emerald-500 py-3 px-4"
                    placeholder="••••••••">
                @error('password') <p class="mt-1 text-xs text-rose-500 font-medium">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center justify-between">
                <label class="flex items-center gap-2 text-sm text-slate-600">
                    <input type="checkbox" wire:model="remember" class="rounded text-emerald-600 focus:ring-emerald-500 border-slate-300">
                    <span>Ingat saya</span>
                </label>
            </div>

            <button type="submit" class="w-full py-3 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold text-sm shadow-lg shadow-emerald-600/30 transition-all">
                <span wire:loading.remove wire:target="login">Masuk</span>
                <span wire:loading wire:target="login">Memproses...</span>
            </button>
        </form>

        <div class="text-center text-sm text-slate-500 space-y-2">
            <p>Belum punya akun? <a href="{{ route('registrasi-warga') }}" wire:navigate class="font-semibold text-emerald-600 hover:underline">Daftar di sini</a></p>
            <p>Sudah registrasi? <a href="{{ route('cek-status-registrasi') }}" wire:navigate class="font-semibold text-emerald-600 hover:underline">Cek status registrasi</a></p>
            <a href="{{ route('landing') }}" wire:navigate class="font-medium text-slate-400 hover:text-slate-600 inline-flex items-center gap-1 text-xs">&larr; Kembali ke Beranda</a>
        </div>
    </div>
</div>
