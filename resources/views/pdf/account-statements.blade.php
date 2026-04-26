<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Account Statements Report</title>
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
        .summary {
            margin-top: 30px;
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
        <h1>Account Statements Report</h1>
        <div class="date">Generated on {{ now()->format('d/m/Y H:i:s') }}</div>
        <div style="margin-top: 10px; font-size: 12px; color: #666;">Total Statements: {{ count($statements) }}</div>
    </div>

    @php
        $totalWithdrawal = 0;
        $totalDeposit = 0;
        $statementsCount = count($statements);
    @endphp
    
    <p style="text-align: center; color: #333; margin-bottom: 10px;"><strong>Total Records: {{ $statementsCount }}</strong></p>
    
    @if($statementsCount == 0)
        <p style="text-align: center; color: #999;">No statements found.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Narration</th>
                    <th>Chq/Ref</th>
                    <th class="amount">Withdrawal</th>
                    <th class="amount">Deposit</th>
                    <th>Flat/Owner</th>
                    <th>Vendor</th>
                    <th>Category</th>
                </tr>
            </thead>
            <tbody>
                @for($i = 0; $i < count($statements); $i++)
                    @php
                        $statement = $statements[$i];
                        $totalWithdrawal += $statement->withdrawal_amt ?? 0;
                        $totalDeposit += $statement->deposit_amt ?? 0;
                    @endphp
                    @php
                        $totalWithdrawal += $statement->withdrawal_amt ?? 0;
                        $totalDeposit += $statement->deposit_amt ?? 0;
                    @endphp
                    <tr>
                        <td>{{ $statement->date ? $statement->date->format('d/m/Y') : '-' }}</td>
                        <td>{{ $statement->narration ?? '-' }}</td>
                        <td>{{ $statement->chq_ref_no ?? '-' }}</td>
                        <td class="amount">{{ $statement->withdrawal_amt ? 'RS ' . number_format($statement->withdrawal_amt, 2) : '-' }}</td>
                        <td class="amount">{{ $statement->deposit_amt ? 'RS ' . number_format($statement->deposit_amt, 2) : '-' }}</td>
                        <td>
                            @if($statement->flat)
                                {{ $statement->flat->flat_number }} - {{ $statement->flat->owner_name ?? 'No Owner' }}
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            @if($statement->vendor)
                                {{ $statement->vendor->name }}
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
                @endfor
            </tbody>
        </table>

        <div class="summary">
            <div class="summary-item">
                <span class="summary-label">Total Withdrawals:</span>
                <span class="summary-value">Rs.{{ number_format($totalWithdrawal, 2) }}</span>
            </div>
            <div class="summary-item">
                <span class="summary-label">Total Deposits:</span>
                <span class="summary-value">Rs.{{ number_format($totalDeposit, 2) }}</span>
            </div>
        </div>
    @endif
</body>
</html>
