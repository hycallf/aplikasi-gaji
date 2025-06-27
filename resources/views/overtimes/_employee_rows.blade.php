{{-- resources/views/overtimes/_employee_rows.blade.php --}}

@forelse ($employees as $employee)
    @php
        // Cek apakah ada data lembur untuk karyawan ini
        $existingOvertime = $overtimes->get($employee->id);
    @endphp
    <tr class="border-b">
        <td class="px-4 py-3 font-medium">{{ $employee->nama }}</td>
        <td class="px-4 py-3 text-center">
            <input type="checkbox" name="overtimes[{{ $employee->id }}][checked]" class="overtime-checkbox rounded"
                data-employee-id="{{ $employee->id }}" {{ $existingOvertime ? 'checked' : '' }}>
        </td>
        <td class="px-4 py-3">
            <div class="overtime-details-{{ $employee->id }} {{ $existingOvertime ? '' : 'hidden' }}">
                <x-text-input type="text" name="overtimes[{{ $employee->id }}][deskripsi_lembur]" class="w-full"
                    placeholder="Cth: Menyelesaikan laporan X" :value="$existingOvertime->deskripsi_lembur ?? ''" />
            </div>
        </td>
        <td class="px-4 py-3">
            <div class="overtime-details-{{ $employee->id }} {{ $existingOvertime ? '' : 'hidden' }}">
                <x-text-input type="number" name="overtimes[{{ $employee->id }}][upah_lembur]" class="w-full"
                    placeholder="50000" :value="$existingOvertime->upah_lembur ?? ''" />
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="4" class="text-center p-4">Tidak ada data karyawan yang cocok dengan pencarian.</td>
    </tr>
@endforelse
