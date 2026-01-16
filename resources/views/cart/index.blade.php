@extends('layouts.user.app')

@section('title', 'Keranjang Pesanan')

@section('content')
<div class="container py-5">

    <h2 class="fw-bold mb-4">Keranjang Pesanan</h2>

    {{-- Pesan sukses --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($cart && count($cart) > 0)

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Produk</th>
                    <th>Harga</th>
                    <th>Jumlah</th>
                    <th>Subtotal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @php $grandTotal = 0; @endphp
                @foreach($cart as $id => $item)
                    @php $subtotal = $item['harga'] * $item['quantity']; @endphp
                    @php $grandTotal += $subtotal; @endphp
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $item['nama'] }}</td>
                        <td>Rp {{ number_format($item['harga'], 0, ',', '.') }}</td>
                        <td>
                            <form action="{{ route('cart.update', $id) }}" method="POST" class="d-flex">
                                @csrf
                                <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="1" class="form-control me-2" style="width: 80px;">
                                <button type="submit" class="btn btn-sm btn-primary">Update</button>
                            </form>
                        </td>
                        <td>Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
                        <td>
                            <form action="{{ route('cart.remove', $id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="4" class="text-end">Total</th>
                    <th colspan="2">Rp {{ number_format($grandTotal, 0, ',', '.') }}</th>
                </tr>
            </tfoot>
        </table>

        {{-- Bagian Total + Tombol Checkout --}}
        <div class="d-flex justify-content-between align-items-center mt-4">
            <h4 class="fw-bold">Total: Rp {{ number_format($grandTotal, 0, ',', '.') }}</h4>
            <a href="{{ route('checkout.index') }}" class="btn btn-primary btn-lg">
                Lanjut ke Pembayaran
            </a>
        </div>

    @else
        <div class="alert alert-warning">Keranjang masih kosong.</div>
    @endif

    <a href="/" class="btn btn-secondary mt-3">Lanjut Belanja</a>

</div>
@endsection
