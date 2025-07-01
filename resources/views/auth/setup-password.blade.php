<x-guest-layout>
    <form method="POST" action="{{ route('password.submit') }}">
        @csrf
        {{-- Hidden input untuk email, diambil dari user yang dikirim controller --}}
        <input type="hidden" name="email" value="{{ $user->email }}">

        <div class="text-center mb-4">
            <h3 class="text-lg font-bold">Setup Password Akun</h3>
            <p class="text-sm text-gray-600">untuk: {{ $user->email }}</p>
        </div>

        <div class="mt-4">
            <x-input-label for="password" value="Password Baru" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password_confirmation" value="Konfirmasi Password Baru" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password"
                name="password_confirmation" required />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>Simpan Password & Login</x-primary-button>
        </div>
    </form>
</x-guest-layout>
