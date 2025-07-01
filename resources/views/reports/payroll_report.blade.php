<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan Gaji {{ $period }}</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }

        th {
            background-color: #f2f2f2;
        }

        .text-right {
            text-align: right;
        }

        h1 {
            text-align: center;
        }
    </style>
</head>

<body>
    <table style="width: 100%;">
        <tr>
            <td style="width: 10%;">
                <x-pdf-logo :logoPath="$companyProfile?->logo" width="80" />
            </td>
            <td style="text-align: center; vertical-align: middle;">
                <div style="font-size: 18px; font-weight: bold;">
                    {{ $companyProfile->nama_perusahaan ?? 'Nama Perusahaan' }}</div>
                <h1>Laporan Gaji Periode {{ $period }}</h1>
            </td>

        </tr>
    </table>
    <br>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Karyawan</th>
                <th>Jabatan</th>
                <th class="text-right">Gaji Bersih</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($payrolls as $index => $payroll)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $payroll->employee->nama }}</td>
                    <td>{{ $payroll->employee->jabatan }}</td>
                    <td class="text-right">Rp {{ number_format($payroll->gaji_bersih, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="font-weight: bold;">
                <td colspan="3" class="text-right">Total</td>
                <td class="text-right">Rp {{ number_format($payrolls->sum('gaji_bersih'), 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>
</body>

</html>
