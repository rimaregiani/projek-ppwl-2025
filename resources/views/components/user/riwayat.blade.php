@extends('layouts.user.app')

@section('title', 'Riwayat Pesanan')

@section('content')
<div class="container py-5">

    <h2 class="mb-4">Riwayat Pesanan</h2>

    @if($orders->isEmpty())
        <div class="alert alert-info">Belum ada pesanan.</div>
    @else
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Produk</th>
                    <th>Tanggal</th>
                    <th>Total</th>
                    <th>Status Pembayaran</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $index => $order)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            @foreach($order->products as $product)
                                {{ $product->nama }}<br>
                            @endforeach
                        </td>
                        <td>{{ $order->tanggal }}</td>
                        <td>Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                        <td>
                            @if($order->status_pembayaran === 'pending')
                                <span class="badge bg-warning text-dark">Pending</span>
                            @elseif($order->status_pembayaran === 'lunas')
                                <span class="badge bg-primary">Lunas</span>
                            @elseif($order->status_pembayaran === 'gagal')
                                <span class="badge bg-danger">Gagal</span>
                            @elseif($order->status_pembayaran === 'diproses')
                                <span class="badge bg-info text-dark">Diproses</span>
                            @endif
                        </td>
                        <td>
                            @if($order->status_pembayaran === 'pending')
                                <a href="{{ route('checkout.sukses') }}" class="btn btn-sm btn-primary">Upload Bukti</a>
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <a href="{{ route('home') }}" class="btn btn-secondary mt-3">Kembali ke Beranda</a>

</div>
@endsection
