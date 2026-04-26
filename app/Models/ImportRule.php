<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImportRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'match_text',
        'vendor_id',
        'priority',
        'active',
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_import_rule');
    }
}