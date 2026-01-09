<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Settlement Receipt - {{ $settlement->settlement_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            color: #333;
            margin: 25px 40px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #333;
        }
        .header img {
            height: 45px;
            margin-bottom: 10px;
        }
        .header h1 {
            color: #333;
            margin: 8px 0 3px 0;
            font-size: 20px;
            font-weight: bold;
            letter-spacing: 1px;
        }
        .header p {
            margin: 0;
            color: #666;
            font-size: 9px;
        }
        .two-column {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        .column {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding: 0 8px;
        }
        .info-box {
            background-color: #f8f9fa;
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 15px;
        }
        .info-box h3 {
            margin: 0 0 8px 0;
            font-size: 11px;
            color: #333;
            border-bottom: 1px solid #ddd;
            padding-bottom: 4px;
        }
        .info-row {
            margin-bottom: 5px;
            display: flex;
            justify-content: space-between;
        }
        .label {
            font-weight: 600;
            color: #555;
            width: 45%;
        }
        .value {
            width: 55%;
            text-align: right;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        th {
            background-color: #333;
            color: white;
            padding: 8px 6px;
            text-align: left;
            font-weight: bold;
            font-size: 9px;
            text-transform: uppercase;
        }
        td {
            padding: 8px 6px;
            border-bottom: 1px solid #e0e0e0;
        }
        tr:hover {
            background-color: #f9f9f9;
        }
        .total-row {
            background-color: #f0f0f0;
            font-weight: bold;
            border-top: 2px solid #333;
        }
        .summary-box {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 4px;
            margin-top: 20px;
            border: 2px solid #e0e0e0;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 6px 0;
            border-bottom: 1px solid #ddd;
        }
        .summary-row:last-child {
            border-bottom: none;
            border-top: 2px solid #333;
            padding-top: 10px;
            margin-top: 6px;
            font-size: 12px;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 8px;
            color: #888;
            border-top: 1px solid #ddd;
            padding-top: 15px;
        }
    </style>
</head>
<body>
    <!-- Header with Logo -->
    <div class="header">
        <img src="{{ public_path('logos/mymember_logo.png') }}" alt="Logo">
        <h1>SETTLEMENT RECEIPT</h1>
        <p>Official Receipt</p>
    </div>

    <!-- Two Column Layout for Settlement and Organization Info -->
    <div class="two-column">
        <div class="column">
            <div class="info-box">
                <h3>Settlement Information</h3>
                <div class="info-row">
                    <span class="label">Settlement No:</span>
                    <span class="value">{{ $settlement->settlement_number }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Date:</span>
                    <span class="value">{{ $settlement->completed_at ? $settlement->completed_at->format('d/m/Y H:i') : \Carbon\Carbon::parse($settlement->settlement_date)->format('d/m/Y') }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Status:</span>
                    <span class="value">{{ ucfirst($settlement->status) }}</span>
                </div>
            </div>
        </div>
        <div class="column">
            <div class="info-box">
                <h3>Organization Details</h3>
                <div class="info-row">
                    <span class="label">Name:</span>
                    <span class="value">{{ $settlement->organization->name }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Bank:</span>
                    <span class="value">{{ $settlement->organization->bank_name ?? 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Account No:</span>
                    <span class="value">{{ $settlement->organization->bank_account_number ?? 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Account Holder:</span>
                    <span class="value">{{ $settlement->organization->bank_account_holder ?? 'N/A' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Transactions Table -->
    <table>
        <thead>
            <tr>
                <th style="width: 15%;">Transaction ID</th>
                <th style="width: 35%;">Charge/Plan</th>
                <th style="width: 17%; text-align: right;">Amount</th>
                <th style="width: 17%; text-align: right;">Platform Fee</th>
                <th style="width: 16%; text-align: right;">Net Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($settlement->transactions as $txn)
            <tr>
                <td>#{{ str_pad($txn->id, 6, '0', STR_PAD_LEFT) }}</td>
                <td>{{ $txn->charge ? $txn->charge->title : '-' }}</td>
                <td style="text-align: right;">RM {{ number_format($txn->amount, 2) }}</td>
                <td style="text-align: right;">RM {{ number_format($txn->platform_fee, 2) }}</td>
                <td style="text-align: right;">RM {{ number_format($txn->amount - $txn->platform_fee, 2) }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="2" style="text-align: right;"><strong>TOTAL:</strong></td>
                <td style="text-align: right;"><strong>RM {{ number_format($totalAmount, 2) }}</strong></td>
                <td style="text-align: right;"><strong>RM {{ number_format($totalPlatformFee, 2) }}</strong></td>
                <td style="text-align: right;"><strong>RM {{ number_format($netAmount, 2) }}</strong></td>
            </tr>
        </tbody>
    </table>

    <!-- Summary Section -->
    <div class="summary-box">
        <div class="summary-row">
            <span><strong>Total Transactions:</strong></span>
            <span>{{ $settlement->transactions->count() }}</span>
        </div>
        <div class="summary-row">
            <span><strong>Total Amount:</strong></span>
            <span>RM {{ number_format($totalAmount, 2) }}</span>
        </div>
        <div class="summary-row">
            <span><strong>Platform Fee:</strong></span>
            <span style="color: #DC2626;">RM {{ number_format($totalPlatformFee, 2) }}</span>
        </div>
        <div class="summary-row">
            <span><strong>Net Settlement Amount:</strong></span>
            <span style="color: #059669;"><strong>RM {{ number_format($netAmount, 2) }}</strong></span>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>This is an official computer-generated receipt. No signature is required.</p>
        <p>Generated on {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html>
