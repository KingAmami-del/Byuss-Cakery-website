@extends('admin.layouts.app')

@section('title', 'Pesanan')

@section('content')
    @php
        $statusLabels = [
            \App\Models\Order::STATUS_AWAITING_PAYMENT => 'Menunggu pembayaran',
            \App\Models\Order::STATUS_AWAITING_CONFIRMATION => 'Menunggu konfirmasi pembayaran',
            \App\Models\Order::STATUS_PAID => 'Terkonfirmasi',
            \App\Models\Order::STATUS_SHIPPED => 'Pesanan dikirim',
            \App\Models\Order::STATUS_DELIVERED => 'Pesanan tiba',
        ];
    @endphp

    <header class="admin-page__head">
        <div>
            <p class="admin-eyebrow">Transaksi toko</p>
            <h1 class="admin-title">Pesanan pelanggan</h1>
            <p class="admin-subtitle">Periksa pembayaran dan kelola status pesanan.</p>
        </div>
    </header>

    <section class="admin-panel">
        @if ($orders->isEmpty())
            <div class="admin-empty">
                <strong>Belum ada pesanan</strong>
                <p>Pesanan pelanggan akan tampil di halaman ini.</p>
            </div>
        @else
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Nomor pesanan</th>
                            <th>Pelanggan</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th><span class="sr-only">Detail</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orders as $order)
                            <tr>
                                <td><strong>{{ $order->order_number }}</strong></td>
                                <td>{{ $order->customer_name }}<small class="admin-cell-sub">{{ $order->customer_phone }}</small></td>
                                <td>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                                <td><span class="admin-badge admin-badge--available">{{ $statusLabels[$order->status] ?? str_replace('_', ' ', $order->status) }}</span></td>
                                <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                <td><a class="admin-link-button" href="{{ route('admin.orders.show', $order) }}">Lihat detail</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="admin-pagination">{{ $orders->links() }}</div>
        @endif
    </section>
@endsection
