<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name',
        'description',
        'type', // 'vendor' or 'account_statement'
        'color',
    ];

    /**
     * Get all vendors for this category
     */
    public function vendors()
    {
        return $this->belongsToMany(Vendor::class, 'category_vendor');
    }

    /**
     * Get all account statements for this category
     */
    public function accountStatements()
    {
        return $this->belongsToMany(AccountStatement::class, 'category_account_statement');
    }

    /**
     * Get all import rules for this category
     */
    public function importRules()
    {
        return $this->belongsToMany(ImportRule::class, 'category_import_rule');
    }

    /**
     * Scope to filter by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }
}
