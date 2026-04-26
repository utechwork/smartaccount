<?php

namespace App\Models;

use Database\Factories\FlatFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Flat extends Model
{
    /** @use HasFactory<FlatFactory> */
    use HasFactory;

    protected $table = 'flats';

    protected $fillable = [
        'flat_number',
        'floor_number',
        'flat_type',
        'occupancy_type',
        'owner_name',
        'owner_email',
        'owner_phone',
        'maintenance_amount',
        'maintenance_status',
        'last_maintenance_date',
        'notes',
        'builder_paid_exception',
    ];

    protected $casts = [
        'floor_number' => 'integer',
        'maintenance_amount' => 'decimal:2',
        'last_maintenance_date' => 'date',
        'builder_paid_exception' => 'boolean',
    ];

    /**
     * Get all account statements for this flat
     */
    public function accountStatements()
    {
        return $this->hasMany(AccountStatement::class);
    }

    /**
     * Get maintenance amount based on flat type and occupancy
     */
    public function getCalculatedMaintenanceAttribute()
    {
        if ($this->builder_paid_exception) {
            return 0;
        }

        // 1 BHK maintenance rates
        if ($this->flat_type === '1BHK') {
            if ($this->occupancy_type === 'owner') {
                return 2500;
            } else { // tenant
                return 2750;
            }
        }

        // 2 BHK maintenance rates (default)
        if ($this->occupancy_type === 'owner') {
            return 2700;
        } else { // tenant
            return 2900; // Assuming tenant 2BHK is 2900
        }
    }

    /**
     * Update maintenance amount to match calculated rate
     */
    public function syncMaintenanceAmount()
    {
        $this->update(['maintenance_amount' => $this->getCalculatedMaintenanceAttribute()]);
    }

    /**
     * Scope: Get flats by floor
     */
    public function scopeByFloor($query, $floor)
    {
        return $query->where('floor_number', $floor);
    }

    /**
     * Scope: Get flats by maintenance status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('maintenance_status', $status);
    }

    /**
     * Scope: Get overdue flats
     */
    public function scopeOverdue($query)
    {
        return $query->where('maintenance_status', 'overdue');
    }

    /**
     * Scope: Get pending payments
     */
    public function scopePending($query)
    {
        return $query->where('maintenance_status', 'pending');
    }

    /**
     * Scope: Get flats with builder paid exception
     */
    public function scopeBuilderPaidException($query)
    {
        return $query->where('builder_paid_exception', true);
    }

    /**
     * Scope: Get 1BHK flats
     */
    public function scopeOneBhk($query)
    {
        return $query->where('flat_type', '1BHK');
    }

    /**
     * Scope: Get 2BHK flats
     */
    public function scopeTwoBhk($query)
    {
        return $query->where('flat_type', '2BHK');
    }

    /**
     * Scope: Get owner occupied flats
     */
    public function scopeOwnerOccupied($query)
    {
        return $query->where('occupancy_type', 'owner');
    }

    /**
     * Scope: Get tenant occupied flats
     */
    public function scopeTenantOccupied($query)
    {
        return $query->where('occupancy_type', 'tenant');
    }
}
