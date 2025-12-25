<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    protected $fillable = [
        'document_number',
        'name',
        'phone',
        'district_id',
        'mandal_id',
        'total_land',
        'total_stock_allotted',
        'stock_availed',
        'document_photo',
        'created_by',
    ];

    protected $casts = [
        'total_land' => 'decimal:2',
        'total_stock_allotted' => 'integer',
        'stock_availed' => 'integer',
    ];

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public function mandal(): BelongsTo
    {
        return $this->belongsTo(Mandal::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function additionalBags(): HasMany
    {
        return $this->hasMany(CustomerAdditionalBag::class);
    }

    public function getBalanceStockAttribute(): int
    {
        return $this->total_stock_allotted - $this->stock_availed;
    }

    public function getDocumentPhotoUrlAttribute(): ?string
    {
        if ($this->document_photo) {
            return asset('storage/' . $this->document_photo);
        }
        return null;
    }

    /**
     * Calculate stock allocation based on land extent
     * 
     * First checks for district-specific conditions, if not found, uses default rules:
     * - Up to 1.00: 2 Qty
     * - 1.00 to 1.20: 3 Qty
     * - 1.21 to 2.00: 4 Qty
     * - 2.01 to 2.20: 5 Qty
     * - 2.21 to 3.00: 6 Qty
     * - 3.01 to 3.20: 7 Qty
     * - 3.21 to 4.00: 8 Qty
     * - 4.01 to 4.20: 9 Qty
     * - 4.21 to 5.00: 10 Qty
     * - 5.01 to 5.20: 11 Qty
     * - 5.21 to 6.00: 12 Qty
     * - 6.01 to 6.20: 13 Qty
     * - 6.21 to 7.00: 14 Qty
     * - Above 7.01: round to nearest number and multiply by 2
     * 
     * @param float $totalLand
     * @param int|null $districtId
     * @return int
     */
    public static function calculateStockAllocation(float $totalLand, ?int $districtId = null): int
    {
        // Check for district-specific conditions first
        if ($districtId) {
            $condition = StockAllotmentCondition::where('district_id', $districtId)
                ->where('is_active', true)
                ->where('land_extent_from', '<=', $totalLand)
                ->where('land_extent_to', '>=', $totalLand)
                ->first();
            
            if ($condition) {
                return $condition->number_of_bags;
            }
        }
        
        // Default rules if no condition found
        if ($totalLand <= 1.00) {
            return 2;
        } elseif ($totalLand <= 1.20) {
            return 3;
        } elseif ($totalLand <= 2.00) {
            return 4;
        } elseif ($totalLand <= 2.20) {
            return 5;
        } elseif ($totalLand <= 3.00) {
            return 6;
        } elseif ($totalLand <= 3.20) {
            return 7;
        } elseif ($totalLand <= 4.00) {
            return 8;
        } elseif ($totalLand <= 4.20) {
            return 9;
        } elseif ($totalLand <= 5.00) {
            return 10;
        } elseif ($totalLand <= 5.20) {
            return 11;
        } elseif ($totalLand <= 6.00) {
            return 12;
        } elseif ($totalLand <= 6.20) {
            return 13;
        } elseif ($totalLand <= 7.00) {
            return 14;
        } else {
            // For above 7.01, round to nearest number and multiply by 2
            $roundedNumber = round($totalLand);
            return $roundedNumber * 2;
        }
    }
}
