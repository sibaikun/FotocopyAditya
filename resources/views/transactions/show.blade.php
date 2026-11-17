<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>{{ $transaction->invoice_no }}</title>
    <style>
        body { font-family: monospace; font-size: 13px; max-width: 320px; margin: 20px auto; color: #000; }
        .c { text-align: center; }
        .row { display: flex; justify-content: space-between; }
        hr { border: 0; border-top: 1px dashed #000; margin: 8px 0; }
        .item { margin-bottom: 6px; }
        .actions { margin-top: 20px; text-align: center; }
        .actions a, .actions button { margin: 0 6px; font-size: 13px; cursor: pointer; }
        @media print { .actions { display: none; } body { margin: 0; } }
    </style>
</head>
<body>
    <div class="c">
        <strong>FOTO COPY ADITYA</strong><br>
        Jl.Kramatsari 1 No.12A, Kel.Kramatsari, Kec.Pekalongan Barat, Kota Pekalongan
    </div>
    <hr>
    <div class="row"><span>No</span><span>{{ $transaction->invoice_no }}</span></div>
    <div class="row"><span>Tanggal</span><span>{{ $transaction->created_at->translatedFormat('d/m/Y H:i') }}</span></div>
    <div class="row"><span>Kasir</span><span>{{ $transaction->user->name }}</span></div>
    <hr>

    @foreach ($transaction->items as $it)
        <div class="item">
            {{ $it->item_name }}<br>
            <div class="row">
                <span>{{ $it->qty }} {{ $it->unit }} x {{ number_format($it->price, 0, ',', '.') }}</span>
                <span>{{ number_format($it->subtotal, 0, ',', '.') }}</span>
            </div>
        </div>
    @endforeach

    <hr>
    <div class="row"><strong>TOTAL</strong><strong>Rp {{ number_format($transaction->total, 0, ',', '.') }}</strong></div>
    <div class="row"><span>Bayar</span><span>Rp {{ number_format($transaction->paid, 0, ',', '.') }}</span></div>
    <div class="row"><span>Kembali</span><span>Rp {{ number_format($transaction->paid - $transaction->total, 0, ',', '.') }}</span></div>
    <hr>
    <div class="c">Terima kasih</div>

    <div class="actions">
        <button onclick="window.print()">Cetak</button>
        <a href="{{ route('transactions.create') }}">Transaksi Baru</a>
        <a href="{{ route('transactions.index') }}">Riwayat</a>
    </div>
</body>
</html>