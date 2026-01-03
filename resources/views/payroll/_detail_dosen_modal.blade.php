{{-- resources/views/payroll/_detail_dosen_modal.blade.php --}}
<div class="p-4">
    <h4 class="font-bold text-lg mb-4 text-gray-800">
        Detail Transport & Honorarium: {{ $payroll->employee->nama_lengkap }}
    </h4>

    {{-- Info Settings Rate --}}
    <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
        <div class="flex items-center text-sm text-blue-800">
            <i class="fa-solid fa-info-circle mr-2"></i>
            <span>Tarif Honorarium: <strong>Rp {{ number_format($dosenRatePerSks, 0, ',', '.') }}</strong> per SKS per
                pertemuan</span>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mata Kuliah</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Kelas</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">SKS</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Pertemuan</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">
                        Transport<br><small>(per pertemuan)</small></th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">
                        Honorarium<br><small>(SKS)</small></th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @php
                    $grandTotalTransport = 0;
                    $grandTotalHonorarium = 0;
                @endphp
                @foreach ($dosenAttendances as $attendance)
                    @php
                        $sks = $attendance->enrollment->matkul->sks ?? 0;
                        $pertemuan = $attendance->jumlah_pertemuan;
                        $transportPerPertemuan = $payroll->employee->transport ?? 0;
                        $totalTransport = $transportPerPertemuan * $pertemuan;
                        $honorariumSks = $dosenRatePerSks * $sks * $pertemuan;
                        $totalBaris = $totalTransport + $honorariumSks;

                        $grandTotalTransport += $totalTransport;
                        $grandTotalHonorarium += $honorariumSks;
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm text-gray-900">
                            {{ $attendance->enrollment->matkul->nama_matkul ?? 'N/A' }}
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-900 text-center">
                            {{ $attendance->kelas ?? '-' }}
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-900 text-center font-semibold">
                            {{ $sks }}
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-900 text-center font-semibold">
                            {{ $pertemuan }}×
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-900 text-center">
                            Rp {{ number_format($transportPerPertemuan, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-900 text-right">
                            Rp {{ number_format($honorariumSks, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3 text-sm font-semibold text-gray-900 text-right">
                            Rp {{ number_format($totalBaris, 0, ',', '.') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-gray-100">
                <tr class="font-bold">
                    <td colspan="5" class="px-4 py-3 text-sm text-gray-900 text-right">
                        Total Transport:
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-900 text-right">
                        Rp {{ number_format($grandTotalTransport, 0, ',', '.') }}
                    </td>
                    <td></td>
                </tr>
                <tr class="font-bold">
                    <td colspan="5" class="px-4 py-3 text-sm text-gray-900 text-right">
                        Total Honorarium SKS:
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-900 text-right">
                        Rp {{ number_format($grandTotalHonorarium, 0, ',', '.') }}
                    </td>
                    <td></td>
                </tr>
                <tr class="font-bold bg-indigo-50">
                    <td colspan="6" class="px-4 py-3 text-sm text-gray-900 text-right">
                        GRAND TOTAL:
                    </td>
                    <td class="px-4 py-3 text-lg text-indigo-700 text-right">
                        Rp {{ number_format($grandTotalTransport + $grandTotalHonorarium, 0, ',', '.') }}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

    {{-- Breakdown Formula --}}
    <div class="mt-4 p-3 bg-gray-50 border border-gray-200 rounded-lg text-xs text-gray-600">
        <div class="font-semibold mb-1">📋 Rumus Perhitungan:</div>
        <ul class="list-disc list-inside space-y-1">
            <li><strong>Transport</strong> = Transport per Pertemuan × Jumlah Pertemuan</li>
            <li><strong>Honorarium SKS</strong> = Rp {{ number_format($dosenRatePerSks, 0, ',', '.') }} × SKS × Jumlah
                Pertemuan</li>
            <li><strong>Total per Matkul</strong> = Transport + Honorarium SKS</li>
        </ul>
        <div class="mt-2 text-blue-600">
            <i class="fa-solid fa-cog mr-1"></i>
            <em>Tarif dapat diubah di menu <a href="{{ route('settings.index') }}"
                    class="underline">Pengaturan</a></em>
        </div>
    </div>
</div>
