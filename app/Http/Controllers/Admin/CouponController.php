<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\Category;
use App\Http\Requests\Admin\StoreCouponRequest;
use App\Http\Requests\Admin\UpdateCouponRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class CouponController extends Controller
{
    public function index(): View
    {
        $coupons = Coupon::select([
            'id',
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
            'starts_at',
            'expires_at',
            'is_active',
            'created_at',
        ])
        ->with(['product:id,name', 'category:id,name'])
        ->latest('id')
        ->paginate(15);

        return view('admin.coupons.index', compact('coupons'));
    }

    public function create(): View
    {
        $products = Product::select(['id', 'name', 'price'])->where('is_available', 1)->orderBy('name')->get();
        $categories = Category::select(['id', 'name'])->orderBy('name')->get();

        return view('admin.coupons.create', compact('products', 'categories'));
    }

    public function store(StoreCouponRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['code'] = strtoupper(trim($data['code']));
        $data['is_active'] = $request->boolean('is_active', true);

        Coupon::create($data);

        return redirect()->route('admin.coupons.index')->with('success', 'تم إنشاء كوبون الخصم بنجاح وتفعيله.');
    }

    public function edit(Coupon $coupon): View
    {
        $products = Product::select(['id', 'name', 'price'])->where('is_available', 1)->orderBy('name')->get();
        $categories = Category::select(['id', 'name'])->orderBy('name')->get();

        return view('admin.coupons.edit', compact('coupon', 'products', 'categories'));
    }

    public function update(UpdateCouponRequest $request, Coupon $coupon): RedirectResponse
    {
        $data = $request->validated();
        $data['code'] = strtoupper(trim($data['code']));
        $data['is_active'] = $request->boolean('is_active');

        $coupon->update($data);

        return redirect()->route('admin.coupons.index')->with('success', 'تم تحديث بيانات كوبون الخصم بنجاح.');
    }

    public function destroy(Coupon $coupon): RedirectResponse
    {
        $coupon->delete();

        return redirect()->route('admin.coupons.index')->with('success', 'تم حذف الكوبون بنجاح.');
    }
}
