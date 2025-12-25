<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockAllotmentCondition extends Model
{
    protected $fillable = [
        'district_id',
        'land_extent_from',
        'land_extent_to',
        'number_of_bags',
        'at_a_time_how_many',
        'interval_time_days',
        'is_active',
    ];

    protected $casts = [
        'land_extent_from' => 'decimal:2',
        'land_extent_to' => 'decimal:2',
        'number_of_bags' => 'integer',
        'at_a_time_how_many' => 'integer',
        'interval_time_days' => 'integer',
        'is_active' => 'boolean',
    ];

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    /**
     * Get stock allocation for a given land extent in a district
     */
    public static function getStockAllocationForDistrict(int $districtId, float $totalLand): ?array
    {
        $condition = self::where('district_id', $districtId)
            ->where('is_active', true)
            ->where('land_extent_from', '<=', $totalLand)
            ->where('land_extent_to', '>=', $totalLand)
            ->first();

        if ($condition) {
            return [
                'total_bags' => $condition->number_of_bags,
                'at_a_time' => $condition->at_a_time_how_many,
                'interval_days' => $condition->interval_time_days,
            ];
        }

        return null;
    }
}
