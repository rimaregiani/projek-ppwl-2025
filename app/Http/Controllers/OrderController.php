<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order;

class OrderController extends Controller
{
    /**
     * Menampilkan riwayat pesanan user
     */
    public function history()
    {
        // Ambil semua order milik user yang sedang login
        $orders = Order::where('user_id', auth()->id())
                       ->latest()
                       ->get();

        return view('user.riwayat', compact('orders'));
    }
}
