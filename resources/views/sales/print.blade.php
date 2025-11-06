<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $sale->reference_no }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            font-size: 12px;
        }
        .invoice-header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #000;
            padding-bottom: 20px;
        }
        .invoice-header h1 {
            margin: 0;
            font-size: 24px;
        }
        .invoice-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .invoice-info div {
            flex: 1;
        }
        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .invoice-table th,
        .invoice-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .invoice-table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .invoice-totals {
            margin-top: 20px;
            text-align: right;
        }
        .invoice-totals table {
            margin-left: auto;
            width: 300px;
        }
        .invoice-totals td {
            padding: 5px 10px;
        }
        .invoice-totals .total-row {
            font-weight: bold;
            font-size: 14px;
            border-top: 2px solid #000;
        }
        .invoice-footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
        @media print {
            body {
                padding: 0;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #007bff; color: white; border: none; cursor: pointer; border-radius: 4px;">Print Invoice</button>
        <a href="{{ route('sales.show', $sale) }}" style="margin-left: 10px; padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 4px;">Back to Sale</a>
    </div>

    <div class="invoice-header">
        <h1>PHARMACY INVOICE</h1>
        <p>Invoice #{{ $sale->reference_no }}</p>
    </div>

    <div class="invoice-info">
        <div>
            <strong>Pharmacy Information:</strong><br>
            Your Pharmacy Name<br>
            Address Line 1<br>
            Address Line 2<br>
            Phone: +251 XXX XXX XXX<br>
            Email: info@pharmacy.com
        </div>
        <div style="text-align: right;">
            <strong>Invoice Details:</strong><br>
            Date: {{ $sale->sold_at->format('M d, Y H:i') }}<br>
            Invoice #: {{ $sale->reference_no }}<br>
            @if($sale->customer)
                <br><strong>Customer:</strong><br>
                {{ $sale->customer->name }}<br>
                @if($sale->customer->phone)
                    {{ $sale->customer->phone }}<br>
                @endif
            @endif
        </div>
    </div>

    <table class="invoice-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Item Name</th>
                <th>Quantity</th>
                <th>Unit Price</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sale->saleItems as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->item->name ?? 'N/A' }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>Birr {{ number_format($item->unit_price, 2) }}</td>
                    <td>Birr {{ number_format($item->line_total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="invoice-totals">
        <table>
            <tr>
                <td>Subtotal:</td>
                <td style="text-align: right;">Birr {{ number_format($sale->subtotal ?? $sale->total, 2) }}</td>
            </tr>
            <tr>
                <td>Tax:</td>
                <td style="text-align: right;">Birr 0.00</td>
            </tr>
            <tr class="total-row">
                <td>Total:</td>
                <td style="text-align: right;">Birr {{ number_format($sale->total_amount ?? $sale->total, 2) }}</td>
            </tr>
        </table>
    </div>

    @if($sale->note)
        <div style="margin-top: 20px;">
            <strong>Notes:</strong><br>
            {{ $sale->note }}
        </div>
    @endif

    <div class="invoice-footer">
        <p>Thank you for your business!</p>
        <p>This is a computer-generated invoice.</p>
    </div>
</body>
</html>

