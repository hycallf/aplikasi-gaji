<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Slip Gaji - {{ $payroll->employee->nama }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            color: #333;
            font-size: 12px;
        }

        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 20px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, .15);
        }

        .invoice-box table {
            width: 100%;
            line-height: inherit;
            text-align: left;
            border-collapse: collapse;
        }

        .invoice-box table td {
            padding: 5px;
            vertical-align: top;
        }

        .invoice-box table tr.top table td.title {
            font-size: 35px;
            line-height: 35px;
            color: #333;
        }

        .invoice-box table tr.information table td {
            padding-bottom: 20px;
        }

        .invoice-box table tr.heading td {
            background: #eee;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
            padding: 8px;
        }

        .invoice-box table tr.details td {
            padding-bottom: 15px;
        }

        .invoice-box table tr.item td {
            border-bottom: 1px solid #eee;
            padding: 8px;
        }

        .invoice-box table tr.item.last td {
            border-bottom: none;
        }

        .invoice-box table tr.total td {
            border-top: 2px solid #eee;
            font-weight: bold;
            padding: 8px;
        }

        .text-right {
            text-align: right;
        }

        .bold {
            font-weight: bold;
        }

        .sub-item {
            padding-left: 20px !important;
            font-size: 11px;
            color: #555;
        }

        .sub-item td {
            border: none;
            padding: 2px 8px;
        }
    </style>
</head>

<body>
    <div class="invoice-box">
        <table cellpadding="0" cellspacing="0">
            <tr class="top">
                <td colspan="2">
                    <table>
                        <tr>
                            <td colspan="2" class="text-right">Tanggal Cetak:
                                {{ \Carbon\Carbon::now()->isoFormat('D MMMM YYYY') }}</td>
                        </tr>
                        <tr>
                            <td class="title" style="width: 10%; vertical-align: top;">
                                <x-pdf-logo :logoPath="$companyProfile?->logo" />
                            </td>
                            <td style="vertical-align: middle; padding-left: 20px;">
                                <div style="font-size: 18px; font-weight: bold;">
                                    {{ $companyProfile->nama_perusahaan ?? 'Nama Perusahaan' }}</div>
                                <div style="font-size: 12px;">{{ $companyProfile->alamat ?? 'Alamat Perusahaan' }}</div>
                                <div style="font-size: 12px;">{{ $companyProfile->email_kontak ?? '' }}</div>
                            </td>

                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="padding-top: 10px; padding-bottom: 20px; text-align: center;">
                    {{-- Tabel baru di dalam untuk menampung konten agar bisa diatur lebarnya --}}
                    <table style="width: 50%; margin: auto; text-align: left; border: 1px solid #ddd;">

                        {{-- Bagian Header: "SLIP GAJI" tanpa border bawah --}}
                        <thead style="background-color: #eee;">
                            <tr>
                                <th colspan="2" style="text-align: center; padding: 8px; font-size: 14px;">
                                    SLIP GAJI KARYAWAN
                                </th>
                            </tr>
                        </thead>

                        {{-- Bagian Body: Data karyawan dengan border di setiap baris --}}
                        <tbody>
                            <tr style="border-top: 1px solid #ddd;">
                                <td style="width: 40%; padding: 8px;">Nama Karyawan</td>
                                <td style="width: 60%; padding: 8px;"><span class="bold">:
                                        {{ $payroll->employee->nama }}</span></td>
                            </tr>
                            <tr style="border-top: 1px solid #ddd;">
                                <td style="padding: 8px;">Jabatan</td>
                                <td style="padding: 8px;"><span class="bold">:
                                        {{ $payroll->employee->jabatan }}</span></td>
                            </tr>
                            <tr style="border-top: 1px solid #ddd;">
                                <td style="padding: 8px;">Departemen</td>
                                <td style="padding: 8px;"><span class="bold">:
                                        {{ $payroll->employee->departemen }}</span></td>
                            </tr>
                            <tr style="border-top: 1px solid #ddd;">
                                <td style="padding: 8px;">Periode</td>
                                <td style="padding: 8px;">:
                                    {{ \Carbon\Carbon::create($payroll->periode_tahun, $payroll->periode_bulan)->isoFormat('MMMM Y') }}
                                </td>
                            </tr>
                        </tbody>

                    </table>
                </td>
            </tr>
            <tr class="heading">
                <td>Gaji</td>
                <td class="text-right">Jumlah</td>
            </tr>
            <tr class="item">
                <td>Gaji Pokok</td>
                <td class="text-right">Rp {{ number_format($payroll->gaji_pokok, 0, ',', '.') }}</td>
            </tr>
            @if ($payroll->employee->tunjangan > 0)
                <tr class="item">
                    <td>Tunjangan Tetap</td>
                    <td class="text-right">Rp {{ number_format($payroll->employee->tunjangan, 0, ',', '.') }}</td>
                </tr>
            @endif
            @php
                // Filter koleksi absensi untuk mendapatkan data hanya pada bulan dan tahun payroll
                $daysInMonth = $attendances->filter(function ($att) use ($payroll) {
                    $attDate = \Carbon\Carbon::parse($att->date);
                    return $attDate->month == $payroll->periode_bulan && $attDate->year == $payroll->periode_tahun;
                });
            @endphp
            <tr class="item">
                <td>Tunjangan Transport ({{ $daysInMonth->count() }} hari)</td>
                <td class="text-right">Rp {{ number_format($payroll->total_tunjangan_transport, 0, ',', '.') }}</td>
            </tr>
            @php
                // Hitung sub-total gaji + tunjangan
                $totalGajiTunjangan =
                    $payroll->gaji_pokok + $payroll->total_tunjangan_transport + $payroll->employee->tunjangan;
            @endphp
            <tr class="item bold">
                <td>Total Gaji</td>
                <td class="text-right">Rp {{ number_format($totalGajiTunjangan, 0, ',', '.') }}</td>
            </tr>

            @if ($payroll->total_upah_lembur > 0 || $payroll->total_insentif > 0)
                @if ($payroll->total_upah_lembur > 0)
                    <tr class="heading">
                        <td>Bonus</td>
                        <td class="text-right">Jumlah</td>
                    </tr>
                    <tr class="item">
                        <td>Upah Lembur</td>
                        <td class="text-right">Rp {{ number_format($payroll->total_upah_lembur, 0, ',', '.') }}</td>
                    </tr>
                    {{-- @foreach ($overtimes as $overtime)
                        <tr class="sub-item">
                            <td colspan="2">
                                {{ \Carbon\Carbon::parse($overtime->tanggal_lembur)->isoFormat('D MMM') }}:
                                {{ $overtime->deskripsi_lembur }} (Rp
                                {{ number_format($overtime->upah_lembur, 0, ',', '.') }})</td>
                        </tr>
                    @endforeach --}}
                @endif

                @if ($incentiveSummary->count() > 0)
                    {{-- <tr class="item">
                        <td>Insentif</td>
                        <td class="text-right">Rp {{ number_format($payroll->total_insentif, 0, ',', '.') }}</td>
                    </tr> --}}
                    {{-- Loop untuk menampilkan setiap jenis event yang diringkas --}}
                    @foreach ($incentiveSummary as $summary)
                        <tr class="item">
                            {{-- Tampilkan nama event dan jumlah kejadiannya --}}
                            <td colspan="2">
                                {{ $summary['event_name'] }} ({{ $summary['count'] }}x)
                                <span style="float: right;">
                                    Rp {{ number_format($summary['total_amount'], 0, ',', '.') }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                @endif

                @php
                    // Hitung sub-total bonus
                    $totalBonus = $payroll->total_upah_lembur + $payroll->total_insentif;
                @endphp
                <tr class="item bold">
                    <td>Total Bonus</td>
                    <td class="text-right">Rp {{ number_format($totalBonus, 0, ',', '.') }}</td>
                </tr>
            @endif

            <tr class="total">
                <td>Total Pendapatan (Gaji Kotor)</td>
                <td class="text-right">Rp {{ number_format($payroll->gaji_kotor, 0, ',', '.') }}</td>
            </tr>

            <tr class="heading">
                <td>Potongan</td>
                <td class="text-right">Jumlah</td>
            </tr>

            {{-- DIUBAH: Logika untuk Potongan --}}
            @if ($deductionSummary['pulang_awal_count'] > 0)
                <tr class="item">
                    <td>Potongan Transport (Pulang Awal {{ $deductionSummary['pulang_awal_count'] }}x)</td>
                    <td class="text-right">- Rp
                        {{ number_format($deductionSummary['pulang_awal_total'], 0, ',', '.') }}</td>
                </tr>
            @endif
            @foreach ($deductionSummary['manual_deductions'] as $deduction)
                <tr class="item">
                    <td>{{ $deduction->jenis_potongan }}</td>
                    <td class="text-right">- Rp {{ number_format($deduction->jumlah_potongan, 0, ',', '.') }}</td>
                </tr>
            @endforeach

            <tr class="total">
                <td>Total Potongan</td>
                <td class="text-right">- Rp {{ number_format($payroll->total_potongan, 0, ',', '.') }}</td>
            </tr>

            <tr class="total" style="background-color: #f0f9ff;">
                <td class="bold">GAJI DITERIMA (GAJI BERSIH)</td>
                <td class="text-right bold">Rp {{ number_format($payroll->gaji_bersih, 0, ',', '.') }}</td>
            </tr>
        </table>

        <table style="width: 100%; margin-top: 50px; text-align: center;">
            <tr>
                {{-- Kolom Kiri: Karyawan --}}
                <td style="width: 50%;">
                    <div>Diterima oleh,</div>
                    <div style="margin-top: 60px;">
                        <span class="bold" style="border-bottom: 1px solid #333; padding: 0 40px;">
                            {{ $payroll->employee->nama }}
                        </span>
                    </div>
                    <div>(Karyawan)</div>
                </td>

                {{-- Kolom Kanan: Administrasi/Pimpinan --}}
                <td style="width: 50%;">
                    <div>Mengetahui,</div>
                    <div style="margin-top: 60px;">
                        <span class="bold" style="border-bottom: 1px solid #333; padding: 0 40px;">
                            {{-- DIUBAH: Mengambil nama perwakilan secara dinamis --}}
                            {{ $companyProfile->nama_perwakilan ?? '(...........................)' }}
                        </span>
                    </div>
                    <div>(PLT Kepala BAUK)</div>
                </td>
            </tr>
        </table>
    </div>
</body>

</html>
