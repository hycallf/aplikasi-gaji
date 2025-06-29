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
                            <td class="title">
                                <x-pdf-logo width="100" />
                            </td>
                            <td class="title" style="vertical-align: middle;">
                                STIMIK Mercusuar
                            </td>
                            <td class="text-right">
                                <span class="bold">SLIP GAJI</span><br>
                                Periode:
                                {{ \Carbon\Carbon::create($payroll->periode_tahun, $payroll->periode_bulan)->isoFormat('MMMM YYYY') }}<br>
                                Tanggal Cetak: {{ \Carbon\Carbon::now()->isoFormat('D MMMM YYYY') }}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr class="information">
                <td colspan="2">
                    <table>
                        <tr>
                            <td>
                                <span class="bold">{{ $payroll->employee->nama }}</span><br>
                                {{ $payroll->employee->jabatan }}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr class="heading">
                <td>Pendapatan</td>
                <td class="text-right">Jumlah</td>
            </tr>
            <tr class="item">
                <td>Gaji Pokok</td>
                <td class="text-right">Rp {{ number_format($payroll->gaji_pokok, 0, ',', '.') }}</td>
            </tr>
            <tr class="item">
                <td>Tunjangan Transport ({{ $attendances->count() }} hari)</td>
                <td class="text-right">Rp {{ number_format($payroll->total_tunjangan_transport, 0, ',', '.') }}</td>
            </tr>

            {{-- Rincian Lembur --}}
            @if ($overtimes->count() > 0)
                <tr class="item">
                    <td>Upah Lembur</td>
                    <td class="text-right">Rp {{ number_format($payroll->total_upah_lembur, 0, ',', '.') }}</td>
                </tr>
                @foreach ($overtimes as $overtime)
                    <tr class="sub-item">
                        <td colspan="2">{{ \Carbon\Carbon::parse($overtime->tanggal_lembur)->isoFormat('D MMM') }}:
                            {{ $overtime->deskripsi_lembur }} (Rp
                            {{ number_format($overtime->upah_lembur, 0, ',', '.') }})</td>
                    </tr>
                @endforeach
            @endif

            {{-- DITAMBAHKAN: Rincian Insentif --}}
            @if ($incentives->count() > 0)
                <tr class="item">
                    <td>Insentif</td>
                    <td class="text-right">Rp {{ number_format($payroll->total_insentif, 0, ',', '.') }}</td>
                </tr>
                {{-- Loop untuk menampilkan setiap detail insentif --}}
                @foreach ($incentives as $incentive)
                    <tr class="sub-item">
                        {{-- Tampilkan nama event dan jumlahnya --}}
                        <td colspan="2">
                            {{ $incentive->event->nama_event }};
                            (Rp
                            {{ number_format($incentive->jumlah_insentif, 0, ',', '.') }})
                        </td>
                    </tr>
                @endforeach
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
    </div>
</body>

</html>
