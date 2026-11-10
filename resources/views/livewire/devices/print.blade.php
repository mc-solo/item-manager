<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Repair Order {{ $device->order_number }} - The Phone Boss</title>
    <style>
        body { font-family: Arial, Helvetica, sans-serif; color: #111; margin: 0; padding: 24px; }
        .receipt { max-width: 640px; margin: 0 auto; border: 2px solid #111; padding: 24px; }
        h1 { font-size: 22px; margin: 0; }
        .subtitle { color: #555; margin: 4px 0 0; }
        .order-no { font-size: 18px; font-weight: bold; }
        hr { border: none; border-top: 1px solid #ccc; margin: 16px 0; }
        table { width: 100%; border-collapse: collapse; }
        td { padding: 6px 0; vertical-align: top; }
        td:first-child { color: #555; width: 160px; }
        .amount { font-size: 20px; font-weight: bold; }
        .signatures { display: flex; justify-content: space-between; margin-top: 48px; }
        .signatures div { width: 45%; border-top: 1px solid #111; padding-top: 6px; font-size: 13px; }
        .note { font-size: 13px; color: #555; margin-top: 24px; }
        .no-print { text-align: center; margin-bottom: 16px; }
        .no-print button { padding: 10px 20px; font-size: 15px; cursor: pointer; }
        @media print {
            body { padding: 0; }
            .no-print { display: none; }
            .receipt { border: 2px solid #111; }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button type="button" onclick="window.print()">Print this order</button>
        <button type="button" onclick="window.close()">Close</button>
    </div>

    <div class="receipt">
        <h1>The Phone Boss</h1>
        <p class="subtitle">Device Repair Order</p>
        <hr>
        <p><span class="order-no">Order: {{ $device->order_number }}</span><br>
        Received: {{ $device->created_at->format('Y-m-d H:i') }}<br>
        Status: {{ ucfirst(str_replace('_', ' ', $device->repair_status)) }}</p>

        <hr>
        <table>
            <tr><td>Client</td><td>{{ $device->client_name }}</td></tr>
            <tr><td>Client Number</td><td>{{ $device->client_number }}</td></tr>
            <tr><td>Device</td><td>{{ ucfirst($device->device_type) }} - {{ $device->device_model }}</td></tr>
            @if ($device->serial_number)
                <tr><td>Serial Number</td><td>{{ $device->serial_number }}</td></tr>
            @endif
            @if ($device->imei)
                <tr><td>IMEI</td><td>{{ $device->imei }}</td></tr>
            @endif
            @if ($device->issue_description)
                <tr><td>Issue</td><td>{{ $device->issue_description }}</td></tr>
            @endif
        </table>

        <hr>
        <p>Amount to pay: <span class="amount">{{ number_format((float) $device->amount, 2) }} ETB</span></p>
        @if ($device->transaction->count())
            <table>
                @foreach ($device->transaction as $payment)
                    <tr><td>{{ ucfirst($payment->type) }}</td><td>{{ number_format((float) $payment->amount, 2) }} ETB - {{ $payment->created_at->format('Y-m-d') }}</td></tr>
                @endforeach
            </table>
        @endif

        <div class="signatures">
            <div>Customer signature</div>
            <div>Received by</div>
        </div>

        <p class="note">Please bring this receipt when you collect your device.</p>
    </div>
</body>
</html>
