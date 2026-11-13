<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Deal;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DealController extends Controller
{
    /**
     * Display a listing of deals.
     */
    public function index(): View
    {
        $deals = Deal::with(['product' => function ($query) {
            $query->select(['id', 'name', 'price', 'image', 'is_available']);
        }])
        ->ordered()
        ->paginate(15);

        return view('admin.deals.index', compact('deals'));
    }

    /**
     * Show form for creating new deal.
     */
    public function create(): View
    {
        $products = Product::select(['id', 'name', 'price'])
            ->where('is_available', 1)
            ->orderBy('name')
            ->get();

        return view('admin.deals.create', compact('products'));
    }

    /**
     * Store a new deal.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'discount_percent' => 'required|integer|min:1|max:99',
            'original_price' => 'required|numeric|min:0',
            'deal_price' => 'required|numeric|min:0',
            'badge_text' => 'nullable|string|max:100',
            'is_active' => 'nullable|boolean',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'sort_order' => 'nullable|integer',
        ], [
            'product_id.required' => 'يرجى اختيار المنتج المراد تطبيق الخصم عليه.',
            'product_id.exists' => 'المنتج المختار غير موجود في قاعدة البيانات.',
            'discount_percent.required' => 'يرجى إدخال نسبة الخصم المئوية.',
            'discount_percent.min' => 'نسبة الخصم يجب أن تكون 1% على الأقل.',
            'discount_percent.max' => 'نسبة الخصم لا يمكن أن تتجاوز 99%.',
            'original_price.required' => 'السعر الأصلي مطلوب.',
            'deal_price.required' => 'سعر العرض بعد الخصم مطلوب.',
            'ends_at.after_or_equal' => 'تاريخ انتهاء العرض يجب أن يكون بعد تاريخ البدء.',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->boolean('is_active', true);
        $data['badge_text'] = $request->badge_text ?: 'عرض حصري';

        Deal::create($data);

        return redirect()->route('admin.deals.index')->with('success', 'تم إنشاء ونشر التخفيض بنجاح وتطبيقه على المنتج.');
    }

    /**
     * Show form for editing deal.
     */
    public function edit(Deal $deal): View
    {
        $products = Product::select(['id', 'name', 'price'])
            ->where('is_available', 1)
            ->orderBy('name')
            ->get();

        return view('admin.deals.edit', compact('deal', 'products'));
    }

    /**
     * Update deal.
     */
    public function update(Request $request, Deal $deal): RedirectResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'discount_percent' => 'required|integer|min:1|max:99',
            'original_price' => 'required|numeric|min:0',
            'deal_price' => 'required|numeric|min:0',
            'badge_text' => 'nullable|string|max:100',
            'is_active' => 'nullable|boolean',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'sort_order' => 'nullable|integer',
        ], [
            'product_id.required' => 'يرجى اختيار المنتج المراد تطبيق الخصم عليه.',
            'discount_percent.required' => 'يرجى إدخال نسبة الخصم المئوية.',
            'original_price.required' => 'السعر الأصلي مطلوب.',
            'deal_price.required' => 'سعر العرض بعد الخصم مطلوب.',
            'ends_at.after_or_equal' => 'تاريخ انتهاء العرض يجب أن يكون بعد تاريخ البدء.',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->boolean('is_active', true);

        $deal->update($data);

        return redirect()->route('admin.deals.index')->with('success', 'تم تحديث بيانات العرض والتخفيض بنجاح.');
    }

    /**
     * Delete deal.
     */
    public function destroy(Deal $deal): RedirectResponse
    {
        $deal->delete();
        return redirect()->route('admin.deals.index')->with('success', 'تم حذف العرض بنجاح وإعادة السعر الأصلي للمنتج.');
    }
}
