@extends('admin.layouts.app')

@section('title', 'Detail Pesanan')

@section('content')
    <header class="admin-page__head">
        <div>
            <p class="admin-eyebrow">Detail transaksi</p>
            <h1 class="admin-title">{{ $order->order_number }}</h1>
            <p class="admin-subtitle">{{ $order->customer_name }} · {{ $order->customer_phone }}</p>
        </div>
        <a href="{{ route('admin.orders.index') }}" class="admin-button admin-button--secondary">Kembali ke pesanan</a>
    </header>

    <div class="admin-order-grid">
        <section class="admin-panel admin-panel--form">
            <h2 class="admin-section-title">Rincian produk</h2>
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead><tr><th>Produk</th><th>Harga</th><th>Jumlah</th><th>Subtotal</th></tr></thead>
                    <tbody>
                        @foreach ($order->items as $item)
                            <tr>
                                <td>
                                    <strong>{{ $item->product_name }}</strong>
                                    @if ($item->variant)
                                        <small class="admin-cell-sub">{{ $item->variant === 'ice' ? 'Ice' : 'Hot' }}</small>
                                    @endif
                                    @if ($item->product_description)
                                        <small class="admin-cell-sub">{{ $item->product_description }}</small>
                                    @endif
                                </td>
                                <td>Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>Rp {{ number_format($item->line_total, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <p class="admin-order-total">Total: <strong>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</strong></p>
            @if ($order->customer_address)
                <p><strong>Alamat pengiriman:</strong> {{ $order->customer_address }}</p>
            @endif
            @if ($order->customer_note)
                <p><strong>Catatan pelanggan:</strong> {{ $order->customer_note }}</p>
            @endif
        </section>

        <section class="admin-panel admin-panel--form">
            <h2 class="admin-section-title">Bukti pembayaran</h2>
            @if ($order->proof_path)
                <a class="admin-proof" href="{{ route('admin.orders.proof', $order) }}" target="_blank" rel="noopener">
                    <img src="{{ route('admin.orders.proof', $order) }}" alt="Bukti pembayaran untuk pesanan {{ $order->order_number }}">
                    <span>Buka gambar bukti pembayaran</span>
                </a>
            @else
                <p class="admin-subtitle">Pelanggan belum mengirim bukti pembayaran.</p>
            @endif

            @if ($order->status === \App\Models\Order::STATUS_AWAITING_CONFIRMATION)
                <form class="admin-review-form" method="POST" action="{{ route('admin.orders.review', $order) }}">
                    @csrf
                    @method('PATCH')
                    <div class="admin-form-actions">
                        <button class="admin-button" type="submit" name="status" value="{{ \App\Models\Order::STATUS_PAID }}">Konfirmasi pembayaran</button>
                        <button class="admin-button admin-button--danger" type="submit" name="status" value="{{ \App\Models\Order::STATUS_REJECTED }}" data-reject-payment>Tolak bukti</button>
                    </div>
                </form>
            @elseif ($order->status === \App\Models\Order::STATUS_PAID)
                <form class="admin-review-form" method="POST" action="{{ route('admin.orders.review', $order) }}">
                    @csrf
                    @method('PATCH')
                    <button class="admin-button" type="submit" name="status" value="{{ \App\Models\Order::STATUS_SHIPPED }}">Konfirmasi pesanan dikirim</button>
                </form>
            @elseif ($order->status === \App\Models\Order::STATUS_SHIPPED)
                <form class="admin-review-form" method="POST" action="{{ route('admin.orders.review', $order) }}">
                    @csrf
                    @method('PATCH')
                    <button class="admin-button" type="submit" name="status" value="{{ \App\Models\Order::STATUS_DELIVERED }}">Konfirmasi pesanan tiba</button>
                </form>
            @else
                <p class="admin-order-status">Status: <strong>{{ match ($order->status) {
                    \App\Models\Order::STATUS_REJECTED => 'Bukti pembayaran ditolak',
                    \App\Models\Order::STATUS_PAID => 'Pembayaran terkonfirmasi',
                    \App\Models\Order::STATUS_SHIPPED => 'Pesanan dikirim',
                    \App\Models\Order::STATUS_DELIVERED => 'Pesanan tiba',
                    default => str_replace('_', ' ', $order->status),
                } }}</strong></p>
                @if ($order->admin_note)
                    <p><strong>Catatan admin:</strong> {{ $order->admin_note }}</p>
                @endif
            @endif
        </section>
    </div>
@endsection
