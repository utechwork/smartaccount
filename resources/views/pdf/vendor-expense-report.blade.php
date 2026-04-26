<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Vendor Expense Report</title>
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
        .summary {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
            background-color: #dbeafe;
            padding: 20px;
            border-radius: 5px;
            border-left: 4px solid #2563eb;
        }
        .summary-item {
            text-align: center;
        }
        .summary-label {
            font-weight: bold;
            color: #1e40af;
            font-size: 12px;
        }
        .summary-value {
            font-size: 24px;
            color: #1e40af;
            margin-top: 5px;
        }
        .vendor {
            margin-bottom: 20px;
            border: 1px solid #e5e7eb;
            padding: 15px;
            border-radius: 5px;
            page-break-inside: avoid;
        }
        .vendor-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 10px;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 10px;
        }
        .vendor-name {
            font-size: 16px;
            font-weight: bold;
            color: #1e40af;
        }
        .vendor-amount {
            font-size: 16px;
            font-weight: bold;
            color: #16a34a;
        }
        .vendor-percentage {
            font-size: 11px;
            color: #666;
        }
        .vendor-info {
            font-size: 12px;
            color: #666;
            margin-top: 8px;
        }
        .vendor-info-item {
            margin-bottom: 5px;
        }
        .label {
            font-weight: bold;
            color: #333;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Vendor Expense Report</h1>
        <div class="date">Generated on {{ now()->format('d/m/Y H:i:s') }}</div>
    </div>

    <div class="summary">
        <div class="summary-item">
            <div class="summary-label">Total Vendors</div>
            <div class="summary-value">{{ $vendors->count() }}</div>
        </div>
        <div class="summary-item">
            <div class="summary-label">Overall Total Expense</div>
            <div class="summary-value">Rs.{{ number_format($overallTotal, 2) }}</div>
        </div>
    </div>

    @if($vendors->isEmpty())
        <p style="text-align: center; color: #999;">No vendors found.</p>
    @else
        @foreach($vendors as $vendor)
            @php
                $percentage = $overallTotal > 0 ? ($vendor->total_expenses / $overallTotal) * 100 : 0;
            @endphp
            <div class="vendor">
                <div class="vendor-header">
                    <div>
                        <div class="vendor-name">{{ $vendor->name }}</div>
                        @if($vendor->categories->count() > 0)
                            <div class="vendor-info-item" style="margin-top: 5px;">
                                <strong style="color: #333; font-size: 11px;">Categories:</strong>
                                <span style="font-size: 11px;">{{ $vendor->categories->pluck('name')->join(', ') }}</span>
                            </div>
                        @endif
                    </div>
                    <div style="text-align: right;">
                        <div class="vendor-amount">Rs.{{ number_format($vendor->total_expenses, 2) }}</div>
                        <div class="vendor-percentage">{{ number_format($percentage, 1) }}% of total</div>
                    </div>
                </div>

                @if($vendor->email || $vendor->phone || $vendor->address)
                    <div class="vendor-info">
                        @if($vendor->email)
                            <div class="vendor-info-item">
                                <span class="label">Email:</span> {{ $vendor->email }}
                            </div>
                        @endif
                        @if($vendor->phone)
                            <div class="vendor-info-item">
                                <span class="label">Phone:</span> {{ $vendor->phone }}
                            </div>
                        @endif
                        @if($vendor->address)
                            <div class="vendor-info-item">
                                <span class="label">Address:</span> {{ $vendor->address }}
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        @endforeach
    @endif
</body>
</html>
