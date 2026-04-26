<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    protected $fillable = [
        'name',
        'description',
        'contact_person',
        'phone',
        'email',
        'address',
        'vendor_type',
        'total_paid',
    ];

    protected $casts = [
        'total_paid' => 'decimal:2',
    ];

    /**
     * Get all account statements for this vendor
     */
    public function statements()
    {
        return $this->hasMany(AccountStatement::class);
    }

    /**
     * Get all categories for this vendor
     */
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_vendor');
    }

    /**
     * Get all import rules for this vendor
     */
    public function importRules()
    {
        return $this->hasMany(ImportRule::class);
    }

    /**
     * Attach multiple categories to vendor
     */
    public function addCategories($categoryIds)
    {
        if (!is_array($categoryIds)) {
            $categoryIds = [$categoryIds];
        }
        return $this->categories()->attach($categoryIds);
    }

    /**
     * Sync vendor categories (replace existing)
     */
    public function syncCategories($categoryIds)
    {
        if (!is_array($categoryIds)) {
            $categoryIds = [$categoryIds];
        }
        return $this->categories()->sync($categoryIds);
    }

    /**
     * Check if vendor has specific category
     */
    public function hasCategory($categoryId)
    {
        return $this->categories()->where('category_id', $categoryId)->exists();
    }

    /**
     * Get category names as comma-separated string
     */
    public function getCategoryNamesAttribute()
    {
        return $this->categories()->pluck('name')->implode(', ');
    }

    /**
     * Scope for filtering by category
     */
    public function scopeByCategory($query, $categoryId)
    {
        return $query->whereHas('categories', function ($q) use ($categoryId) {
            $q->where('category_id', $categoryId);
        });
    }

    /**
     * Scope for filtering by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('vendor_type', $type);
    }

    /**
     * Get total expenses for this vendor
     */
    public function getTotalExpensesAttribute()
    {
        return $this->statements()->whereNotNull('withdrawal_amt')->sum('withdrawal_amt');
    }
}
