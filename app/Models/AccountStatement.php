<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountStatement extends Model
{
    protected $table = 'account_statements';

    protected $fillable = [
        'date',
        'narration',
        'chq_ref_no',
        'value_date',
        'withdrawal_amt',
        'deposit_amt',
        'closing_balance',
        'flat_id',
        'vendor_id',
        'expense_details',
        'remark',
    ];

    protected $casts = [
        'date' => 'date',
        'value_date' => 'date',
        'withdrawal_amt' => 'decimal:2',
        'deposit_amt' => 'decimal:2',
        'closing_balance' => 'decimal:2',
    ];

    /**
     * Get the flat associated with this statement
     */
    public function flat()
    {
        return $this->belongsTo(Flat::class);
    }

    /**
     * Get the vendor associated with this statement
     */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * Get all categories for this account statement
     */
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_account_statement');
    }
}
