<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Manajemen User') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    {{-- Tombol untuk menambah user baru --}}
                    <div class="mb-4">
                        <a href="{{ route('users.create') }}"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            + Tambah User Baru
                        </a>
                    </div>

                    {{-- Pesan Sukses --}}
                    @if (session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4"
                            role="alert">
                            <strong class="font-bold">Sukses!</strong>
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    {{-- Tabel untuk menampilkan data user --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        No</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Nama</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Email</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Role</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse ($users as $index => $user)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $index + 1 }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $user->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900 dark:text-gray-200">{{ $user->email }}
                                            </div>

                                            {{-- Logika untuk menampilkan badge dan tombol --}}
                                            @if ($user->role !== 'operator')
                                                @if (!$user->hasVerifiedEmail())
                                                    <div class="flex items-center gap-x-2 mt-1">
                                                        <span
                                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                            Belum Diverifikasi
                                                        </span>

                                                        {{-- Form dengan tombol Kirim Ulang --}}
                                                        <form action="{{ route('users.resend_invitation', $user->id) }}"
                                                            method="POST">
                                                            @csrf
                                                            <x-primary-button>
                                                                Resend Verification
                                                            </x-primary-button>
                                                        </form>
                                                    </div>
                                                @else
                                                    <div class="flex items-center gap-x-2 mt-1">
                                                        <span
                                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                            Terverifikasi
                                                        </span>
                                                    </div>
                                                @endif
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{-- Kode ini untuk mengambil satu string role dari kolom database --}}
                                            {{-- ucfirst() digunakan agar huruf pertamanya menjadi besar, misal 'operator' -> 'Operator' --}}
                                            {{ ucfirst($user->role) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            {{-- Tambahkan @if untuk mengecek role --}}
                                            @if ($user->role !== 'operator')
                                                <div class="flex items-center justify-center">
                                                    <x-action-button type="edit" :href="route('users.edit', $user->id)" />
                                                    <x-action-button type="delete" :route="route('users.destroy', $user->id)" />
                                                </div>
                                            @else
                                                <span class="text-xs text-gray-500 italic">Tidak ada aksi</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 whitespace-nowrap text-center">Tidak ada
                                            data user.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
