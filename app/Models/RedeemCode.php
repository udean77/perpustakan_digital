<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class RedeemCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'type',
        'value',
        'value_type',
        'max_usage',
        'used_count',
        'min_purchase',
        'valid_from',
        'valid_until',
        'status',
        'description'
    ];

    protected $casts = [
        'valid_from' => 'date',
        'valid_until' => 'date',
        'value' => 'decimal:2',
        'min_purchase' => 'decimal:2'
    ];

    /**
     * Check if the redeem code is valid
     */
    public function isValid()
    {
        $now = Carbon::now();
        return $this->status === 'active' &&
               $this->used_count < $this->max_usage &&
               $now->between($this->valid_from, $this->valid_until);
    }

    /**
     * Check if the redeem code can be used for the given amount
     */
    public function canBeUsedFor($amount)
    {
        if (!$this->isValid()) {
            return false;
        }

        if ($this->min_purchase && $amount < $this->min_purchase) {
            return false;
        }

        return true;
    }

    /**
     * Calculate discount amount
     */
    public function calculateDiscount($subtotal, $shippingCost = 0)
    {
        if ($this->type === 'free_shipping') {
            return $shippingCost;
        }
        
        if ($this->value_type === 'percentage') {
            return ($subtotal * $this->value) / 100;
        }

        return $this->value;
    }

    /**
     * Increment usage count
     */
    public function incrementUsage()
    {
        $this->increment('used_count');
        
        if ($this->used_count >= $this->max_usage) {
            $this->update(['status' => 'inactive']);
        }
    }

    /**
     * Generate a unique code
     */
    public static function generateCode($length = 8)
    {
        do {
            $code = strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, $length));
        } while (self::where('code', $code)->exists());

        return $code;
    }

    /**
     * Scope for active codes
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for valid codes
     */
    public function scopeValid($query)
    {
        $now = Carbon::now();
        return $query->where('status', 'active')
                    ->where('valid_from', '<=', $now)
                    ->where('valid_until', '>=', $now)
                    ->whereRaw('used_count < max_usage');
    }
}
