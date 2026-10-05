{{--
    resources/views/components/guest-order-expiry-notice.blade.php

    Dipakai di halaman yang menampilkan status pesanan tamu (checkout
    selesai, form konfirmasi pembayaran, detail pesanan). Cuma tampil kalau
    pesanan ini benar-benar kandidat untuk dihapus otomatis oleh command
    `app:expire-guest-orders` (lihat routes/console.php): dipesan tanpa
    login, metode transfer bank, dan belum ada konfirmasi pembayaran sama
    sekali. Begitu ada bukti transfer diunggah, status berubah jadi
    "processing" dan notice ini otomatis berhenti tampil dengan sendirinya.

    Usage: <x-guest-order-expiry-notice :order="$order" />
--}}
@props(['order'])

@if (!$order->user_id && $order->payment_method === 'bank_transfer' && $order->payment_status === 'pending')
    @php
        $expiresAt = $order->created_at->copy()->addHour();
    @endphp

    <div class="alert alert-warning d-flex align-items-start gap-2 guest-order-expiry-notice">
        <i class="fas fa-hourglass-half mt-1"></i>
        <div>
            <strong>Selesaikan pembayaran sebelum waktu habis.</strong>
            Karena pesanan ini dibuat tanpa login, sistem akan membatalkannya secara otomatis dan mengembalikan stok
            jika belum ada konfirmasi pembayaran dalam
            <strong class="guest-order-countdown" data-expires-at="{{ $expiresAt->toIso8601String() }}">1 jam</strong>.
            Sudah transfer? Segera unggah bukti pembayarannya di bawah supaya tidak ikut terhapus.
        </div>
    </div>

    @once
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                document.querySelectorAll('.guest-order-countdown').forEach(function (el) {
                    var expiresAt = new Date(el.dataset.expiresAt).getTime();

                    function tick() {
                        var remaining = expiresAt - Date.now();

                        if (remaining <= 0) {
                            el.textContent = 'beberapa saat lagi';
                            return;
                        }

                        var minutes = Math.floor(remaining / 60000);
                        var seconds = Math.floor((remaining % 60000) / 1000);
                        el.textContent = minutes + ' menit ' + String(seconds).padStart(2, '0') + ' detik';

                        setTimeout(tick, 1000);
                    }

                    tick();
                });
            });
        </script>
    @endonce
@endif
