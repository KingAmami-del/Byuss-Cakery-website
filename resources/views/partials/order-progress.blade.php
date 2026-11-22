@php
    $isRejected = $order->status === \App\Models\Order::STATUS_REJECTED;
    $isConfirmed = in_array($order->status, [
        \App\Models\Order::STATUS_PAID,
        \App\Models\Order::STATUS_SHIPPED,
        \App\Models\Order::STATUS_DELIVERED,
    ], true);
    $isShipped = in_array($order->status, [
        \App\Models\Order::STATUS_SHIPPED,
        \App\Models\Order::STATUS_DELIVERED,
    ], true);
    $isDelivered = $order->status === \App\Models\Order::STATUS_DELIVERED;
@endphp

<ol class="order-progress" aria-label="Tahapan pesanan">
    <li @class([
        'order-progress__step',
        'is-rejected' => $isRejected,
        'is-complete' => $isConfirmed,
        'is-current' => ! $isRejected && ! $isConfirmed,
    ])>
        <span class="order-progress__number">1</span>
        <span class="order-progress__copy">
            <strong>Terkonfirmasi / ditolak</strong>
            <small>
                @if ($isRejected)
                    Bukti pembayaran ditolak
                @elseif ($isConfirmed)
                    Pesanan terkonfirmasi
                @elseif ($order->status === \App\Models\Order::STATUS_AWAITING_CONFIRMATION)
                    Menunggu konfirmasi admin
                @else
                    Menunggu pembayaran
                @endif
            </small>
        </span>
    </li>
    <li @class([
        'order-progress__step',
        'is-complete' => $isShipped,
        'is-current' => $order->status === \App\Models\Order::STATUS_PAID,
        'is-pending' => ! $isShipped && $order->status !== \App\Models\Order::STATUS_PAID,
    ])>
        <span class="order-progress__number">2</span>
        <span class="order-progress__copy">
            <strong>Pesanan dikirim</strong>
            <small>{{ $isShipped ? 'Pesanan sudah dikirim' : 'Menunggu pengiriman' }}</small>
        </span>
    </li>
    <li @class([
        'order-progress__step',
        'is-complete' => $isDelivered,
        'is-current' => $order->status === \App\Models\Order::STATUS_SHIPPED,
        'is-pending' => ! $isDelivered && $order->status !== \App\Models\Order::STATUS_SHIPPED,
    ])>
        <span class="order-progress__number">3</span>
        <span class="order-progress__copy">
            <strong>Pesanan tiba</strong>
            <small>{{ $isDelivered ? 'Pesanan telah tiba' : 'Menunggu pesanan tiba' }}</small>
        </span>
    </li>
</ol>
