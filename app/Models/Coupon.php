<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'title',
        'description',
        'type',
        'value',
        'product_id',
        'category_id',
        'min_order_amount',
        'max_discount_amount',
        'usage_limit',
        'used_count',
        'usage_per_user',
        'starts_at',
        'expires_at',
        'is_active',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'product_id' => 'integer',
        'category_id' => 'integer',
        'min_order_amount' => 'decimal:2',
        'max_discount_amount' => 'decimal:2',
        'usage_limit' => 'integer',
        'used_count' => 'integer',
        'usage_per_user' => 'integer',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    protected $appends = ['formatted_discount', 'target_label'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        $now = Carbon::now();
        return $query->where('is_active', true)
            ->where(function ($q) use ($now) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>=', $now);
            })
            ->where(function ($q) {
                $q->whereNull('usage_limit')->orWhereRaw('used_count < usage_limit');
            });
    }

    public function getFormattedDiscountAttribute(): string
    {
        if ($this->type === 'percentage') {
            return (float)$this->value . '%';
        }
        return number_format((float)$this->value, 2) . ' ج.م';
    }

    public function getTargetLabelAttribute(): string
    {
        if ($this->product_id && $this->product) {
            return 'مخصص لمنتج: ' . $this->product->name;
        }
        if ($this->category_id && $this->category) {
            return 'مخصص لقسم: ' . $this->category->name;
        }
        return 'شامل جميع المنتجات';
    }

    public function isValidForAmount(float $amount): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $now = Carbon::now();
        if ($this->starts_at && $now->lt($this->starts_at)) {
            return false;
        }

        if ($this->expires_at && $now->gt($this->expires_at)) {
            return false;
        }

        if ($this->usage_limit !== null && $this->used_count >= $this->usage_limit) {
            return false;
        }

        if ($this->min_order_amount !== null && $amount < (float) $this->min_order_amount) {
            return false;
        }

        return true;
    }

    public function calculateDiscount(float $subtotal, ?array $cartItems = null): float
    {
        if (!$this->isValidForAmount($subtotal)) {
            return 0.00;
        }

        $applicableSubtotal = $subtotal;

        // If coupon is targeted to a specific product
        if ($this->product_id && !empty($cartItems)) {
            $productSubtotal = 0.0;
            foreach ($cartItems as $item) {
                if ((int)($item['product_id'] ?? 0) === (int)$this->product_id) {
                    $productSubtotal += (float)($item['total'] ?? 0);
                }
            }
            if ($productSubtotal <= 0) {
                return 0.00;
            }
            $applicableSubtotal = $productSubtotal;
        }

        if ($this->type === 'percentage') {
            $discount = ($applicableSubtotal * (float) $this->value) / 100;
            if ($this->max_discount_amount !== null) {
                $discount = min($discount, (float) $this->max_discount_amount);
            }
            return round(min($discount, $applicableSubtotal), 2);
        }

        return round(min((float) $this->value, $applicableSubtotal), 2);
    }

    public function usages(): HasMany
    {
        return $this->hasMany(CouponUsage::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'coupon_code', 'code');
    }
}
