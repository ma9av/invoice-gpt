
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $invoice->invoice_number }}</title>
    <style>
        @page {
            margin: 30px;
        }
        body {
            font-family: Arial, sans-serif;
            line-height: 1.5;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
        }
        .invoice-header {
            margin-bottom: 30px;
        }
        .header-row {
            width: 100%;
            clear: both;
            margin-bottom: 20px;
        }
        .logo-section {
            align-items: center;
            text-align: center;
            float: left;
        }
        .invoice-title-section {
            align-items: center;
            float: left;
            width: 50%;
            text-align: center;
        }
        .details-section {
            float: right;
            width: 25%;
        }
        .logo-container {
            width: 100px;
            height: 100px;
            margin: 0 auto;
            text-align: center;
            line-height: 100px;
        }
        .logo {
            max-width: 90px;
            max-height: 90px;
            vertical-align: middle;
        }
        .invoice-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 8px;
        }
        .company-name {
            font-size: 16px;
            color: #666;
        }
        .detail-row {
            margin-bottom: 8px;
        }
        .detail-label {
            font-weight: bold;
            display: inline-block;
            width: 80px;
        }
        .detail-value {
            display: inline-block;
        }
        .address-container {
            width: 100%;
            clear: both;
            margin-bottom: 30px;
        }
        .address-column {
            float: left;
            width: 45%;
        }
        .address-heading {
            font-weight: bold;
            margin-bottom: 5px;
        }
        .address-content {
            padding: 10px;
            border: 1px solid #ddd;
            min-height: 60px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .items-table th {
            background-color: #f5f5f5;
            border-bottom: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .items-table td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }
        .text-right {
            text-align: right;
        }
        .totals-container {
            width: 100%;
            margin-top: 20px;
        }
        .totals-table {
            width: 40%;
            float: right;
        }
        .total-row {
            margin-bottom: 5px;
        }
        .total-label {
            display: inline-block;
            width: 60%;
            text-align: right;
            padding-right: 10px;
        }
        .total-value {
            display: inline-block;
            width: 35%;
            text-align: right;
        }
        .final-total {
            font-weight: bold;
            border-top: 1px solid #ddd;
            padding-top: 5px;
            margin-top: 5px;
        }
        .notes-container {
            width: 100%;
            clear: both;
            padding-top: 20px;
            margin-top: 30px;
        }
        .notes-heading {
            font-weight: bold;
            margin-bottom: 5px;
        }
        .notes-content {
            border: 1px solid #ddd;
            padding: 10px;
            min-height: 60px;
        }
        /* Clearfix */
        .clearfix:after {
            content: "";
            display: table;
            clear: both;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="invoice-header clearfix">
            <div class="header-row clearfix">
                <div class="logo-section">
                    <div class="logo-container">
                        @if(isset($logo_base64))
                            <img src="{{ $logo_base64 }}" alt="Company Logo" class="logo">
                        @endif
                    </div>
                    <div class="invoice-title-section">
                        <div class="invoice-title">INVOICE</div>
                        <div class="company-name">{{ $invoice->company_name }}</div>
                    </div>
    
                </div>

                <div class="details-section">
                    <div class="detail-row">
                        <span class="detail-label">Invoice #:</span>
                        <span class="detail-value">{{ $invoice->invoice_number }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Date:</span>
                        <span class="detail-value">{{ date('d-m-Y', strtotime($invoice->invoice_date)) }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Due Date:</span>
                        <span class="detail-value">{{ date('d-m-Y', strtotime($invoice->due_date)) }}</span>
                    </div>
                    @if(isset($invoice->po_number) && $invoice->po_number)
                    <div class="detail-row">
                        <span class="detail-label">PO #:</span>
                        <span class="detail-value">{{ $invoice->po_number }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="address-container clearfix">
            <div class="address-column" style="margin-right: 10%;">
                <div class="address-heading">Bill To:</div>
                <div class="address-content">
                    {{ $invoice->bill_to_name }}<br>
                    {!! nl2br(e($invoice->bill_to_address)) !!}
                </div>
            </div>
            <div class="address-column">
                <div class="address-heading">Ship To:</div>
                <div class="address-content">
                    {{ $invoice->ship_to_name }}<br>
                    {!! nl2br(e($invoice->ship_to_address)) !!}
                </div>
            </div>
        </div>

        <table class="items-table">
            <thead>
                <tr>
                    <th width="20%">Item</th>
                    <th width="35%">Description</th>
                    <th width="15%" class="text-right">Quantity</th>
                    <th width="15%" class="text-right">Rate</th>
                    <th width="15%" class="text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                <tr>
                    <td>{{ $item['name'] }}</td>
                    <td>{{ $item['description'] }}</td>
                    <td class="text-right">{{ $item['quantity'] }}</td>
                    <td class="text-right">${{ number_format($item['rate'], 2) }}</td>
                    <td class="text-right">${{ number_format($item['amount'], 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals-container clearfix">
            <div class="totals-table">
                <div class="total-row">
                    <span class="total-label">Subtotal:</span>
                    <span class="total-value">${{ number_format($invoice->subtotal, 2) }}</span>
                </div>
                
                @if(isset($invoice->discount_rate) && $invoice->discount_rate > 0)
                <div class="total-row">
                    <span class="total-label">Discount ({{ $invoice->discount_rate }}%):</span>
                    <span class="total-value">${{ number_format($invoice->discount_amount, 2) }}</span>
                </div>
                @endif
                
                @if(isset($invoice->tax_rate) && $invoice->tax_rate > 0)
                <div class="total-row">
                    <span class="total-label">Tax ({{ $invoice->tax_rate }}%):</span>
                    <span class="total-value">${{ number_format($invoice->tax_amount, 2) }}</span>
                </div>
                @endif
                
                <div class="total-row final-total">
                    <span class="total-label">Total:</span>
                    <span class="total-value">${{ number_format($invoice->total, 2) }}</span>
                </div>
            </div>
        </div>

        @if(isset($invoice->notes) && $invoice->notes)
        <div class="notes-container">
            <div class="notes-heading">Notes:</div>
            <div class="notes-content">
                {!! nl2br(e($invoice->notes)) !!}
            </div>
        </div>
        @endif
    </div>
</body>
</html>
