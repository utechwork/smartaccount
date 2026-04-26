<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Vendor Details - {{ $vendor->name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 15px;
        }
        h1 {
            margin: 0;
            color: #1e40af;
        }
        .date {
            color: #666;
            font-size: 12px;
            margin-top: 5px;
        }
        .vendor-info {
            background-color: #dbeafe;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 30px;
            border-left: 4px solid #2563eb;
        }
        .info-row {
            display: grid;
            grid-template-columns: 200px 1fr;
            margin-bottom: 10px;
            font-size: 13px;
        }
        .info-label {
            font-weight: bold;
            color: #1e40af;
        }
        .info-value {
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 12px;
        }
        th {
            background-color: #e5e7eb;
            padding: 10px;
            text-align: left;
            font-weight: bold;
            border-bottom: 2px solid #2563eb;
        }
        td {
            padding: 8px 10px;
            border-bottom: 1px solid #e5e7eb;
        }
        tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .amount {
            text-align: right;
        }
        h2 {
            color: #1e40af;
            margin-top: 30px;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 10px;
        }
        .summary {
            margin-top: 20px;
            padding: 15px;
            background-color: #f0f9ff;
            border-left: 4px solid #2563eb;
        }
        .summary-item {
            display: inline-block;
            margin-right: 30px;
        }
        .summary-label {
            font-weight: bold;
            color: #1e40af;
        }
        .summary-value {
            color: #2563eb;
            font-size: 18px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Vendor Details - {{ $vendor->name }}</h1>
        <div class="date">Generated on {{ now()->format('d/m/Y H:i:s') }}</div>
    </div>

    <div class="vendor-info">
        <div class="info-row">
            <div class="info-label">Vendor Name:</div>
            <div class="info-value">{{ $vendor->name }}</div>
        </div>
        @if($vendor->email)
            <div class="info-row">
                <div class="info-label">Email:</div>
                <div class="info-value">{{ $vendor->email }}</div>
            </div>
        @endif
        @if($vendor->phone)
            <div class="info-row">
                <div class="info-label">Phone:</div>
                <div class="info-value">{{ $vendor->phone }}</div>
            </div>
        @endif
        @if($vendor->address)
            <div class="info-row">
                <div class="info-label">Address:</div>
                <div class="info-value">{{ $vendor->address }}</div>
            </div>
        @endif
        @if($vendor->categories->count() > 0)
            <div class="info-row">
                <div class="info-label">Categories:</div>
                <div class="info-value">{{ $vendor->categories->pluck('name')->join(', ') }}</div>
            </div>
        @endif
    </div>

    <div class="summary">
        <div class="summary-item">
            <span class="summary-label">Total Expenses:</span>
            <span class="summary-value">Rs.{{ number_format($totalExpenses, 2) }}</span>
        </div>
    </div>

    @if($vendor->statements->isEmpty())
        <p style="text-align: center; color: #999; margin-top: 30px;">No transactions found for this vendor.</p>
    @else
        <h2>Account Statements</h2>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Narration</th>
                    <th>Chq/Ref</th>
                    <th class="amount">Amount</th>
                    <th>Flat/Owner</th>
                    <th>Category</th>
                </tr>
            </thead>
            <tbody>
                @foreach($vendor->statements as $statement)
                    <tr>
                        <td>{{ $statement->date ? $statement->date->format('d/m/Y') : '-' }}</td>
                        <td>{{ $statement->narration ?? '-' }}</td>
                        <td>{{ $statement->chq_ref_no ?? '-' }}</td>
                        <td class="amount">{{ $statement->withdrawal_amt ? 'RS ' . number_format($statement->withdrawal_amt, 2) : 'RS ' . number_format($statement->deposit_amt ?? 0, 2) }}</td>
                        <td>
                            @if($statement->flat)
                                {{ $statement->flat->flat_number }} - {{ $statement->flat->owner_name ?? 'No Owner' }}
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            @if($statement->categories->count() > 0)
                                {{ $statement->categories->pluck('name')->join(', ') }}
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</body>
</html>
