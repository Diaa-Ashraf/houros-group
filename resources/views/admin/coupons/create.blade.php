@extends('layouts.admin')

@section('title', 'إضافة كوبون خصم جديد')

@section('content')
<div class="space-y-6 max-w-4xl mx-auto">
    <!-- Header -->
    <div class="bg-white border border-[#EADBCC] rounded-3xl p-6 sm:p-8 shadow-xs flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2 mb-1.5">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black bg-[#073F40] text-[#E2BF52]">
                    <span>🏷️</span>
                    <span>كوبونات وقسائم التوفير</span>
                </span>
            </div>
            <h1 class="text-2xl font-black text-gray-900">إضافة كوبون خصم جديد</h1>
            <p class="text-xs text-gray-500 mt-1">أنشئ رمز ترويجي لتطبيق تخفيضات مباشرة على مشتريات العملاء بالمتجر أو لمنتج محدد.</p>
        </div>
        <a href="{{ route('admin.coupons.index') }}" class="px-4 py-2 rounded-xl border border-gray-200 bg-gray-50 hover:bg-gray-100 text-xs font-bold text-gray-700 transition-colors">
            ← العودة للكوبونات
        </a>
    </div>

    @if($errors->any())
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs space-y-1">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Form -->
    <form action="{{ route('admin.coupons.store') }}" method="POST" class="bg-white border border-[#EADBCC] rounded-3xl p-6 sm:p-8 shadow-xs space-y-6">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Coupon Code -->
            <div>
                <label for="code" class="block text-xs font-bold text-gray-700 mb-1.5">رمز الكوبون (الكود) <span class="text-red-500">*</span></label>
                <input type="text" name="code" id="code" value="{{ old('code') }}" placeholder="مثال: HORUS20 أو SCIB15" required class="w-full uppercase font-mono bg-gray-50 border border-gray-200 rounded-xl px-3.5 py-2.5 text-xs text-gray-900 outline-none focus:border-[#073F40]">
            </div>

            <!-- Title -->
            <div>
                <label for="title" class="block text-xs font-bold text-gray-700 mb-1.5">عنوان الكوبون الترويجي</label>
                <input type="text" name="title" id="title" value="{{ old('title') }}" placeholder="مثال: خصم 20% على دهانات سكيب" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3.5 py-2.5 text-xs text-gray-900 outline-none focus:border-[#073F40]">
            </div>

            <!-- Description -->
            <div class="md:col-span-2">
                <label for="description" class="block text-xs font-bold text-gray-700 mb-1.5">وصف الكوبون وشروط الاستخدام</label>
                <input type="text" name="description" id="description" value="{{ old('description') }}" placeholder="مثال: وفر حتى 50 ج.م عند الشراء بقيمة 150 ج.م فأكثر" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3.5 py-2.5 text-xs text-gray-900 outline-none focus:border-[#073F40]">
            </div>

            <!-- Type -->
            <div>
                <label for="type" class="block text-xs font-bold text-gray-700 mb-1.5">نوع الخصم <span class="text-red-500">*</span></label>
                <select name="type" id="type" required class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3.5 py-2.5 text-xs text-gray-900 outline-none focus:border-[#073F40]">
                    <option value="percentage" {{ old('type') === 'percentage' ? 'selected' : '' }}>نسبة مئوية (%)</option>
                    <option value="fixed" {{ old('type') === 'fixed' ? 'selected' : '' }}>مبلغ مالي ثابت (ج.م)</option>
                </select>
            </div>

            <!-- Value -->
            <div>
                <label for="value" class="block text-xs font-bold text-gray-700 mb-1.5">قيمة الخصم <span class="text-red-500">*</span></label>
                <input type="number" step="0.01" name="value" id="value" value="{{ old('value') }}" placeholder="مثال: 10 أو 50" required class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3.5 py-2.5 text-xs text-gray-900 outline-none focus:border-[#073F40]">
            </div>

            <!-- Target Specific Product (Optional) -->
            <div>
                <label for="product_id" class="block text-xs font-bold text-gray-700 mb-1.5">تخصيص لمنتج محدد (اختياري)</label>
                <select name="product_id" id="product_id" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3.5 py-2.5 text-xs text-gray-900 outline-none focus:border-[#073F40]">
                    <option value="">-- شامل جميع المنتجات --</option>
                    @foreach($products as $prod)
                        <option value="{{ $prod->id }}" {{ old('product_id') == $prod->id ? 'selected' : '' }}>
                            {{ $prod->name }} ({{ number_format($prod->price, 2) }} ج.م)
                        </option>
                    @endforeach
                </select>
                <span class="text-[10px] text-gray-400 mt-0.5 block">إذا اخترت منتجاً، سيتم تطبيق الخصم فقط عند شراء هذا المنتج.</span>
            </div>

            <!-- Target Specific Category (Optional) -->
            <div>
                <label for="category_id" class="block text-xs font-bold text-gray-700 mb-1.5">تخصيص لقسم محدد (اختياري)</label>
                <select name="category_id" id="category_id" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3.5 py-2.5 text-xs text-gray-900 outline-none focus:border-[#073F40]">
                    <option value="">-- شامل جميع الأقسام --</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Min Order Amount -->
            <div>
                <label for="min_order_amount" class="block text-xs font-bold text-gray-700 mb-1.5">الحد الأدنى للطلب (اختياري)</label>
                <input type="number" step="0.01" name="min_order_amount" id="min_order_amount" value="{{ old('min_order_amount') }}" placeholder="مثال: 150" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3.5 py-2.5 text-xs text-gray-900 outline-none focus:border-[#073F40]">
            </div>

            <!-- Max Discount Amount (For percentage) -->
            <div>
                <label for="max_discount_amount" class="block text-xs font-bold text-gray-700 mb-1.5">أقصى حد لقيمة الخصم (للنسب المئوية)</label>
                <input type="number" step="0.01" name="max_discount_amount" id="max_discount_amount" value="{{ old('max_discount_amount') }}" placeholder="مثال: 50" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3.5 py-2.5 text-xs text-gray-900 outline-none focus:border-[#073F40]">
            </div>

            <!-- Usage Limit -->
            <div>
                <label for="usage_limit" class="block text-xs font-bold text-gray-700 mb-1.5">العدد الأقصى للاستخدام (فارغ = غير محدود)</label>
                <input type="number" name="usage_limit" id="usage_limit" value="{{ old('usage_limit') }}" placeholder="مثال: 500" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3.5 py-2.5 text-xs text-gray-900 outline-none focus:border-[#073F40]">
            </div>

            <!-- Starts At -->
            <div>
                <label for="starts_at" class="block text-xs font-bold text-gray-700 mb-1.5">تاريخ بدء السريان</label>
                <input type="datetime-local" name="starts_at" id="starts_at" value="{{ old('starts_at') }}" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3.5 py-2.5 text-xs text-gray-900 outline-none focus:border-[#073F40]">
            </div>

            <!-- Expires At -->
            <div>
                <label for="expires_at" class="block text-xs font-bold text-gray-700 mb-1.5">تاريخ انتهاء الكوبون</label>
                <input type="datetime-local" name="expires_at" id="expires_at" value="{{ old('expires_at') }}" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3.5 py-2.5 text-xs text-gray-900 outline-none focus:border-[#073F40]">
            </div>
        </div>

        <!-- Is Active -->
        <div class="flex items-center gap-2 pt-2">
            <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }} class="rounded border-gray-300 text-[#073F40] focus:ring-[#073F40]">
            <label for="is_active" class="text-xs font-bold text-gray-800">تفعيل الكوبون فوراً للاستخدام بالمتجر</label>
        </div>

        <!-- Submit Button -->
        <div class="pt-4 border-t border-[#EADBCC] flex items-center justify-end gap-3">
            <a href="{{ route('admin.coupons.index') }}" class="px-5 py-2.5 rounded-xl bg-gray-100 text-gray-700 text-xs font-bold hover:bg-gray-200">إلغاء</a>
            <button type="submit" class="px-7 py-3 rounded-2xl bg-[#073F40] hover:bg-[#0B5759] text-white text-xs font-black shadow-md cursor-pointer transition-all">
                حفظ ونشر الكوبون
            </button>
        </div>
    </form>
</div>
@endsection
