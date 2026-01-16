<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class CheckoutController extends Controller
{
    /**
     * Tampilkan halaman checkout
     */
    public function index()
    {
        $cart = Session::get('cart', []);
        $user = Auth::user();

        return view('user.checkout', compact('cart', 'user'));
    }

    /**
     * Proses checkout dan buat order
     */
    public function process(Request $request)
    {
        $cart = session()->get('cart', []);

        if (!$cart || count($cart) === 0) {
            return redirect()->route('cart.index')
                             ->with('error', 'Keranjang masih kosong.');
        }

        // Validasi input
        $validated = $request->validate([
            'alamat'  => 'required|string',
            'telepon' => 'required|string|max:20',
            'metode'  => 'required|string',
        ]);

        // Hitung total
        $total = collect($cart)->sum(fn($item) => $item['harga'] * $item['quantity']);

        // Buat ID unik untuk order
        $orderId = 'ORD-' . time() . '-' . rand(1000, 9999);

        // Simpan order
        $order = Order::create([
            'id'                => $orderId,
            'user_id'           => Auth::id(),
            'tanggal'           => now(),
            'total'             => $total,
            'alamat'            => $validated['alamat'],
            'telepon'           => $validated['telepon'],
            'metode'            => $validated['metode'],
            'status_pembayaran' => 'pending',
        ]);

        // Simpan produk ke pivot table & update stok
        foreach ($cart as $productId => $item) {

            $order->products()->attach($productId, [
                'jumlah'       => $item['quantity'],
                'harga_satuan' => $item['harga'],
            ]);

            $product = Product::find($productId);

            if ($product) {
                if ($product->stok < $item['quantity']) {
                    return redirect()->back()->with('error',
                        "Stok produk {$product->nama} tidak mencukupi."
                    );
                }

                $product->stok -= $item['quantity'];
                $product->save();
            }
        }

        // Bersihkan keranjang
        session()->forget('cart');

        return redirect()->route('checkout.sukses')
                         ->with('success', 'Pesanan berhasil diproses!');
    }

    /**
     * Halaman sukses checkout
     */
    public function sukses()
    {
        $order = Order::where('user_id', Auth::id())
                      ->latest()
                      ->first();

        return view('user.sukses', compact('order'));
    }

    /**
     * Upload bukti pembayaran
     */
    public function updatePaymentProof(Request $request, Order $order)
    {
        // Cek user
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'bukti_pembayaran' => 'required|file|mimes:jpg,png,pdf|max:2048',
        ]);

        if ($request->hasFile('bukti_pembayaran')) {

            // Hapus file lama jika ada
            if ($order->bukti_pembayaran && Storage::disk('public')->exists('payment/' . $order->bukti_pembayaran)) {
                Storage::disk('public')->delete('payment/' . $order->bukti_pembayaran);
            }

            // Simpan file baru
            $file     = $request->file('bukti_pembayaran');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('payment', $filename, 'public');

            // Update order
            $order->bukti_pembayaran = $filename;
            $order->status_pembayaran = 'lunas';
            $order->save();
        }

        return redirect()->route('orders.history')
                         ->with('success', 'Bukti pembayaran berhasil diupload.');
    }
}
