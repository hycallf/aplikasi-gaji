<div class="p-6">
    <h3 class="text-lg font-bold mb-4">Rincian Transport & Honorarium</h3>
    <table class="w-full text-sm text-left">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
            <tr>
                <th class="px-6 py-3">Keterangan</th>
                <th class="px-6 py-3">Detail Perhitungan</th>
                <th class="px-6 py-3 text-right">Sub-total</th>
            </tr>
        </thead>
        <tbody>
            @php $totalPertemuan = $dosenAttendances->sum('jumlah_pertemuan'); @endphp
            @if ($totalPertemuan > 0)
                <tr class="border-b">
                    <td class="px-6 py-4">Transport Utama</td>
                    <td class="px-6 py-4">{{ $totalPertemuan }} Pertemuan x Rp
                        {{ number_format($payroll->employee->transport, 0, ',', '.') }}</td>
                    <td class="px-6 py-4 text-right">Rp
                        {{ number_format($totalPertemuan * $payroll->employee->transport, 0, ',', '.') }}</td>
                </tr>
            @endif
            @foreach ($dosenAttendances as $attendance)
                @php
                    $sks = $attendance->matkul->sks ?? 0;
                    $honor = 7500 * $sks * $attendance->jumlah_pertemuan;
                @endphp
                <tr class="border-b">
                    <td class="px-6 py-4">Honorarium: {{ $attendance->matkul->nama_matkul ?? 'N/A' }}</td>
                    <td class="px-6 py-4">{{ $attendance->jumlah_pertemuan }} Pertemuan x {{ $sks }} SKS x Rp
                        7.500</td>
                    <td class="px-6 py-4 text-right">Rp {{ number_format($honor, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot class="font-bold bg-gray-100">
            <tr>
                <td colspan="2" class="px-6 py-3 text-right">Total Tunjangan Transport & Honorarium</td>
                <td class="px-6 py-3 text-right">Rp
                    {{ number_format($payroll->total_tunjangan_transport, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>
</div>
