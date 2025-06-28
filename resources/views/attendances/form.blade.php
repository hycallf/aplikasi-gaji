<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Input Absensi Harian: {{ \Carbon\Carbon::parse($selectedDate)->isoFormat('dddd, D MMMM Y') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('attendances.store') }}">
                        @csrf
                        <input type="hidden" name="date" value="{{ $selectedDate }}">

                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    {{-- ... header tabel Anda ... --}}
                                </thead>
                                <tbody>
                                    @foreach ($employees as $employee)
                                        <tr>
                                            <td>{{ $employee->nama }}</td>
                                            <td>
                                                <select name="attendances[{{ $employee->id }}]">
                                                    {{-- ... options dropdown Anda ... --}}
                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" name="descriptions[{{ $employee->id }}]"
                                                    value="{{ $descriptions[$employee->id] ?? '' }}">
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="flex justify-end mt-4">
                            <x-primary-button>Simpan Absensi</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
