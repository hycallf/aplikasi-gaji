{{-- File: resources/views/payroll/_detail_modal.blade.php --}}

<div class="overflow-x-auto">
    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            @switch($type)
                @case('transport')
                    <tr>
                        <th scope="col" class="px-6 py-3">Tanggal</th>
                        <th scope="col" class="px-6 py-3">Status</th>
                        {{-- DITAMBAHKAN: Kolom header untuk transport --}}
                        <th scope="col" class="px-6 py-3 text-right">Transport Diterima</th>
                    </tr>
                @break

                @case('lembur')
                    <tr>
                        <th scope="col" class="px-6 py-3">Tanggal</th>
                        <th scope="col" class="px-6 py-3">Deskripsi</th>
                        <th scope="col" class="px-6 py-3 text-right">Upah</th>
                    </tr>
                @break

                @case('insentif')
                    <tr>
                        <th scope="col" class="px-6 py-3">Nama Event</th>
                        <th scope="col" class="px-6 py-3">Tanggal</th>
                        <th scope="col" class="px-6 py-3">Upah/Event</th>
                        <th scope="col" class="px-6 py-3">Trigger</th>
                        <th scope="col" class="px-6 py-3 text-right">Jumlah</th>
                    </tr>
                @break

                @case('potongan')
                    <tr>
                        <th scope="col" class="px-6 py-3">Tanggal</th>
                        <th scope="col" class="px-6 py-3">Jenis Potongan</th>
                        <th scope="col" class="px-6 py-3 text-right">Jumlah</th>
                    </tr>
                @break
            @endswitch
        </thead>
        <tbody>
            @forelse($data as $item)
                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                    @switch($type)
                        @case('transport')
                            <td class="px-6 py-4">{{ \Carbon\Carbon::parse($item->date)->isoFormat('dddd, D MMM YYYY') }}</td>
                            <td class="px-6 py-4"><span
                                    class="font-semibold capitalize">{{ str_replace('_', ' ', $item->status) }}</span></td>
                            {{-- DITAMBAHKAN: Kolom isi untuk transport --}}
                            <td class="px-6 py-4 text-right text-green-600 font-semibold">
                                Rp {{ number_format($payroll->employee->transport, 0, ',', '.') }}
                            </td>
                        @break

                        @case('lembur')
                            <td class="px-6 py-4">{{ \Carbon\Carbon::parse($item->tanggal_lembur)->isoFormat('D MMM YYYY') }}
                            </td>
                            <td class="px-6 py-4">{{ $item->deskripsi_lembur }}</td>
                            <td class="px-6 py-4 text-right">Rp {{ number_format($item->upah_lembur, 0, ',', '.') }}</td>
                        @break

                        @case('insentif')
                            <td class="px-6 py-4">{{ $item->event->nama_event }}</td>
                            <td class="px-6 py-4">{{ $item->tanggal_insentif }}</td>
                            <td class="px-6 py-4 text-right">Rp {{ number_format($item->unit_amount, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-center">{{ $item->quantity }}</td>
                            <td class="px-6 py-4 text-right">Rp {{ number_format($item->total_amount, 0, ',', '.') }}</td>
                        @break

                        @case('potongan')
                            <td class="px-6 py-4">{{ \Carbon\Carbon::parse($item->tanggal_potongan)->isoFormat('D MMM YYYY') }}
                            </td>
                            <td class="px-6 py-4">{{ $item->jenis_potongan }}</td>
                            <td class="px-6 py-4 text-right">Rp {{ number_format($item->jumlah_potongan, 0, ',', '.') }}</td>
                        @break
                    @endswitch
                </tr>
                @empty
                    <tr>
                        {{-- Sesuaikan colspan agar pas dengan jumlah kolom header --}}
                        <td colspan="{{ $type == 'transport' || $type == 'insentif' ? 2 : 4 }}"
                            class="px-6 py-4 text-center">Tidak ada data detail untuk ditampilkan.</td>
                    </tr>
                @endforelse
            </tbody>

            {{-- DITAMBAHKAN: Bagian Footer untuk menampilkan TOTAL --}}
            @if (count($data) > 0)
                <tfoot class="font-bold bg-gray-100 dark:bg-gray-700">
                    <tr>
                        @switch($type)
                            @case('transport')
                                <td colspan="2" class="px-6 py-3 text-center">Total ({{ $data->count() }} hari)</td>
                                <td class="px-6 py-3 text-right">Rp {{ number_format($total, 0, ',', '.') }}</td>
                            @break

                            @case('insentif')
                                {{-- Untuk tabel dengan 4 kolom --}}
                                <td colspan="4" class="px-6 py-3 text-center">Total</td>
                                <td class="px-6 py-3 text-right">Rp {{ number_format($total, 0, ',', '.') }}</td>
                            @break

                            @default
                                {{-- Untuk lembur dan potongan --}}
                                <td colspan="2" class="px-6 py-3 text-center">Total</td>
                                <td class="px-6 py-3 text-right">Rp {{ number_format($total, 0, ',', '.') }}</td>
                        @endswitch
                    </tr>
                </tfoot>
            @endif
        </table>
    </div>
