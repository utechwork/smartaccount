<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Flat Details - {{ $flat->flat_number }}</title>
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
        .flat-info {
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
    </style>
</head>
<body>
    <div class="header">
        <h1>Flat Details - {{ $flat->flat_number }}</h1>
        <div class="date">Generated on {{ now()->format('d/m/Y H:i:s') }}</div>
    </div>

    <div class="flat-info">
        <div class="info-row">
            <div class="info-label">Flat Number:</div>
            <div class="info-value">{{ $flat->flat_number }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Owner Name:</div>
            <div class="info-value">{{ $flat->owner_name ?? 'Not specified' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Floor:</div>
            <div class="info-value">{{ $flat->floor ?? 'Not specified' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Area:</div>
            <div class="info-value">{{ $flat->area ? $flat->area . ' Sq Ft' : 'Not specified' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Status:</div>
            <div class="info-value">{{ $flat->status ?? 'Active' }}</div>
        </div>
    </div>

    @if($flat->accountStatements->isEmpty())
        <p style="text-align: center; color: #999;">No transactions found for this flat.</p>
    @else
        <h2>Account Statements</h2>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Narration</th>
                    <th>Chq/Ref</th>
                    <th class="amount">Withdrawal</th>
                    <th class="amount">Deposit</th>
                    <th>Vendor</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalWithdrawal = 0;
                    $totalDeposit = 0;
                @endphp
                @foreach($flat->accountStatements as $statement)
                    @php
                        $totalWithdrawal += $statement->withdrawal_amt ?? 0;
                        $totalDeposit += $statement->deposit_amt ?? 0;
                    @endphp
                    <tr>
                        <td>{{ $statement->date ? $statement->date->format('d/m/Y') : '-' }}</td>
                        <td>{{ $statement->narration ?? '-' }}</td>
                        <td>{{ $statement->chq_ref_no ?? '-' }}</td>
                        <td class="amount">{{ $statement->withdrawal_amt ? 'Rs.' . number_format($statement->withdrawal_amt, 2) : '-' }}</td>
                        <td class="amount">{{ $statement->deposit_amt ? 'Rs.' . number_format($statement->deposit_amt, 2) : '-' }}</td>
                        <td>
                            @if($statement->vendor)
                                {{ $statement->vendor->name }}
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div style="margin-top: 20px; padding: 15px; background-color: #f0f9ff; border-left: 4px solid #2563eb;">
            <div style="display: inline-block; margin-right: 30px;">
                <span style="font-weight: bold; color: #1e40af;">Total Withdrawals:</span>
                <span style="color: #2563eb; font-size: 16px; font-weight: bold;">Rs.{{ number_format($totalWithdrawal, 2) }}</span>
            </div>
            <div style="display: inline-block;">
                <span style="font-weight: bold; color: #1e40af;">Total Deposits:</span>
                <span style="color: #2563eb; font-size: 16px; font-weight: bold;">Rs.{{ number_format($totalDeposit, 2) }}</span>
            </div>
        </div>
    @endif
</body>
</html>
