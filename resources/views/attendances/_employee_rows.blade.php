{{-- resources/views/attendances/_employee_rows.blade.php --}}

@forelse ($employees as $employee)
    <tr class="border-b">
        <td class="px-4 py-3 font-medium">{{ $employee->nama }}</td>
        <td class="px-4 py-3">
            <select name="attendances[{{ $employee->id }}]"
                class="attendance-status border-gray-300 rounded-md shadow-sm w-full" data-row-id="{{ $employee->id }}">
                @php
                    $currentStatus = $attendances[$employee->id] ?? 'hadir';
                @endphp
                <option value="hadir" {{ $currentStatus == 'hadir' ? 'selected' : '' }}>Hadir</option>
                <option value="sakit" {{ $currentStatus == 'sakit' ? 'selected' : '' }}>Sakit</option>
                <option value="izin" {{ $currentStatus == 'izin' ? 'selected' : '' }}>Izin</option>
                <option value="telat" {{ $currentStatus == 'telat' ? 'selected' : '' }}>Telat</option>
                <option value="pulang_awal" {{ $currentStatus == 'pulang_awal' ? 'selected' : '' }}>Pulang Awal</option>
            </select>
        </td>
        <td class="keterangan-cell hidden px-4 py-3">
            <input type="text" name="descriptions[{{ $employee->id }}]"
                class="border-gray-300 rounded-md shadow-sm w-full" placeholder="Isi keterangan..."
                value="{{ $descriptions[$employee->id] ?? '' }}">
        </td>
    </tr>
@empty
    <tr>
        <td colspan="3" class="text-center p-4">Tidak ada data karyawan yang cocok dengan pencarian.</td>
    </tr>
@endforelse
