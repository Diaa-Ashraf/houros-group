<?php

namespace App\Services;

use App\Models\Coupon;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class CouponService
{
    /**
     * Validate coupon code against an amount, user/phone, and optional cart items.
     */
    public function validateCoupon(string $code, float $subtotal, ?int $userId = null, ?string $phone = null, ?array $cartItems = null): array
    {
        $code = trim(strtoupper($code));
        
        $coupon = Coupon::with(['product', 'category'])->where('code', $code)->first();

        if (!$coupon) {
            return [
                'valid' => false,
                'message' => 'كود الكوبون غير صحيح، يرجى التأكد من الرمز وإعادة المحاولة.',
                'coupon' => null,
                'discount' => 0.00,
            ];
        }

        if (!$coupon->is_active) {
            return [
                'valid' => false,
                'message' => 'هذا الكوبون غير متاح أو متوقف مؤقتاً حالياً.',
                'coupon' => null,
                'discount' => 0.00,
            ];
        }

        $now = Carbon::now();

        if ($coupon->starts_at && $now->lt($coupon->starts_at)) {
            return [
                'valid' => false,
                'message' => 'هذا الكوبون سيبدأ سريانه قريباً في ' . $coupon->starts_at->format('Y-m-d') . '.',
                'coupon' => null,
                'discount' => 0.00,
            ];
        }

        if ($coupon->expires_at && $now->gt($coupon->expires_at)) {
            return [
                'valid' => false,
                'message' => 'عذراً، انتهت صلاحية استخدام هذا الكوبون.',
                'coupon' => null,
                'discount' => 0.00,
            ];
        }

        if ($coupon->usage_limit !== null && $coupon->used_count >= $coupon->usage_limit) {
            return [
                'valid' => false,
                'message' => 'عذراً، تم استنفاد الحد الأقصى المتاح لاستخدام هذا الكوبون.',
                'coupon' => null,
                'discount' => 0.00,
            ];
        }

        if ($coupon->min_order_amount !== null && $subtotal < (float) $coupon->min_order_amount) {
            return [
                'valid' => false,
                'message' => "الحد الأدنى لقيمة الطلب لتفعيل هذا الكوبون هو {$coupon->min_order_amount} ج.م (إجمالي سلتك الحالي: {$subtotal} ج.م).",
                'coupon' => null,
                'discount' => 0.00,
            ];
        }

        // Check if coupon is restricted to a specific product
        if ($coupon->product_id && !empty($cartItems)) {
            $hasTargetProduct = false;
            foreach ($cartItems as $item) {
                if ((int)($item['product_id'] ?? 0) === (int)$coupon->product_id) {
                    $hasTargetProduct = true;
                    break;
                }
            }
            if (!$hasTargetProduct) {
                $productName = $coupon->product?->name ?? 'منتج محدد';
                return [
                    'valid' => false,
                    'message' => "هذا الكوبون مخصص لمنتج ({$productName})، يرجى إضافة هذا الصنف أولاً إلى سلة المشتريات للاستفادة من الخصم.",
                    'coupon' => null,
                    'discount' => 0.00,
                ];
            }
        }

        // Check usage_per_user limit
        if ($coupon->usage_per_user !== null) {
            $userUsageQuery = \App\Models\CouponUsage::where('coupon_id', $coupon->id);
            if ($userId) {
                $userUsageQuery->where(function ($q) use ($userId, $phone) {
                    $q->where('user_id', $userId);
                    if ($phone) {
                        $q->orWhere('customer_phone', $phone);
                    }
                });
            } elseif ($phone) {
                $userUsageQuery->where('customer_phone', $phone);
            }

            if ($userId || $phone) {
                $existingCount = $userUsageQuery->count();
                if ($existingCount >= $coupon->usage_per_user) {
                    return [
                        'valid' => false,
                        'message' => "لقد استنفدت الحد الأقصى المسموح به لاستخدام هذا الكوبون ({$coupon->usage_per_user} مرة).",
                        'coupon' => null,
                        'discount' => 0.00,
                    ];
                }
            }
        }

        $discount = $coupon->calculateDiscount($subtotal, $cartItems);

        return [
            'valid' => true,
            'message' => "تم تفعيل كود الخصم ({$coupon->code}) بنجاح! وفرت " . number_format($discount, 2) . ' ج.م.',
            'coupon' => $coupon,
            'discount' => $discount,
        ];
    }

    /**
     * Get active and public coupons available for customers.
     */
    public function getAvailableCoupons(?float $subtotal = null): Collection
    {
        return Coupon::active()
            ->with(['product:id,name,image', 'category:id,name'])
            ->orderByRaw('CASE WHEN product_id IS NULL THEN 0 ELSE 1 END')
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();
    }

    /**
     * Get active coupons relevant to a specific product (or general).
     */
    public function getActiveCouponsForProduct(int $productId, ?int $categoryId = null): Collection
    {
        return Coupon::active()
            ->where(function ($q) use ($productId, $categoryId) {
                $q->where('product_id', $productId)
                  ->orWhereNull('product_id');
                if ($categoryId) {
                    $q->orWhere('category_id', $categoryId);
                }
            })
            ->orderByRaw('CASE WHEN product_id = ? THEN 0 WHEN category_id = ? THEN 1 ELSE 2 END', [$productId, $categoryId ?? 0])
            ->take(3)
            ->get();
    }

    /**
     * Increment usage count for a coupon and record detailed entry.
     */
    public function recordUsage(string $code, ?int $userId = null, ?int $orderId = null, ?string $phone = null, float $discountAmount = 0.0): bool
    {
        $coupon = Coupon::where('code', trim(strtoupper($code)))->first();
        if ($coupon) {
            $coupon->increment('used_count');

            \App\Models\CouponUsage::create([
                'coupon_id' => $coupon->id,
                'user_id' => $userId,
                'order_id' => $orderId,
                'customer_phone' => $phone,
                'discount_amount' => $discountAmount,
            ]);

            return true;
        }
        return false;
    }
}
