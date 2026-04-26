<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PettyCash extends Model
{
    protected $fillable = [
        'date',
        'amount',
        'availability',
        'expense_type',
        'expense_paid',
        'payment_method',
        'cleared_by',
        'vendor_name',
        'expense_details',
        'remark',
        'expense_category',
        'is_withdrawal',
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
        'expense_paid' => 'decimal:2',
        'is_withdrawal' => 'boolean',
    ];

    /**
     * Get formatted date
     */
    public function getFormattedDateAttribute()
    {
        return $this->date?->format('d-m-Y');
    }

    /**
     * Get balance color based on availability
     */
    public function getAvailabilityColorAttribute()
    {
        if ($this->availability === 'Yes') {
            return 'bg-green-50 text-green-700';
        } elseif ($this->availability === 'No') {
            return 'bg-red-50 text-red-700';
        }
        return 'bg-gray-50 text-gray-700';
    }

    /**
     * Get category color
     */
    public function getCategoryColorAttribute()
    {
        return match($this->expense_category) {
            'Maintenance' => 'bg-blue-50 text-blue-700',
            'Miscellaneous' => 'bg-purple-50 text-purple-700',
            default => 'bg-gray-50 text-gray-700',
        };
    }
}
