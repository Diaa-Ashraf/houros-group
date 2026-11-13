@extends('layouts.admin')

@section('title', 'كوبونات الخصم وقسائم التوفير')

@section('content')
<div class="space-y-6">
    <!-- Top Header -->
    <div class="bg-white border border-[#EADBCC] rounded-3xl p-6 sm:p-8 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1.5">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black bg-[#073F40] text-[#E2BF52]">
                    <span>🏷️</span>
                    <span>كوبونات وقسائم التوفير</span>
                </span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-gray-900">
                كوبونات الخصم وقسائم الشراء
            </h1>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">
                إدارة أكواد الخصم الترويجية، تحديد نسب الخصم المئوية أو المبالغ الثابتة، وتخصيص الكوبونات لمنتجات أو أقسام معينة.
            </p>
        </div>

        <a href="{{ route('admin.coupons.create') }}" class="px-5 py-3 rounded-2xl bg-[#073F40] hover:bg-[#0B5759] text-white text-xs font-black shadow-md transition-all flex items-center gap-2 shrink-0 cursor-pointer">
            <span>+</span>
            <span>إضافة كوبون جديد</span>
        </a>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center gap-2">
            <span>✓</span>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Coupons Table -->
    <div class="bg-white border border-[#EADBCC] rounded-3xl shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-start text-xs">
                <thead class="bg-gray-50/80 text-gray-600 uppercase text-[11px] border-b border-gray-200">
                    <tr>
                        <th class="py-3.5 px-6 text-start font-bold">الكود والعنوان</th>
                        <th class="py-3.5 px-4 text-start font-bold">التخصيص</th>
                        <th class="py-3.5 px-4 text-start font-bold">النوع والقيمة</th>
                        <th class="py-3.5 px-4 text-start font-bold">الحد الأدنى</th>
                        <th class="py-3.5 px-4 text-start font-bold">مرات الاستخدام</th>
                        <th class="py-3.5 px-4 text-start font-bold">الصلاحية</th>
                        <th class="py-3.5 px-4 text-start font-bold">الحالة</th>
                        <th class="py-3.5 px-6 text-end font-bold">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($coupons as $coupon)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="py-4 px-6 font-bold text-gray-900">
                                <div class="space-y-1">
                                    <span class="inline-block bg-[#073F40] text-[#E2BF52] px-2.5 py-1 rounded-lg font-mono text-xs font-black border border-[#0B5759]">
                                        {{ $coupon->code }}
                                    </span>
                                    @if($coupon->title)
                                        <p class="text-xs font-bold text-gray-800">{{ $coupon->title }}</p>
                                    @endif
                                    @if($coupon->description)
                                        <p class="text-[11px] text-gray-500 max-w-xs truncate">{{ $coupon->description }}</p>
                                    @endif
                                </div>
                            </td>
                            <td class="py-4 px-4">
                                @if($coupon->product)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-blue-50 border border-blue-200 text-blue-800 text-[11px] font-bold">
                                        <span>🎨</span>
                                        <span class="max-w-[120px] truncate">{{ $coupon->product->name }}</span>
                                    </span>
                                @elseif($coupon->category)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-purple-50 border border-purple-200 text-purple-800 text-[11px] font-bold">
                                        <span>📁</span>
                                        <span>{{ $coupon->category->name }}</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg bg-gray-100 text-gray-600 text-[11px] font-bold">
                                        <span>🌐</span>
                                        <span>عام لكامل المتجر</span>
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-4 font-black text-[#073F40] text-sm tabular-nums">
                                {{ $coupon->formatted_discount }}
                            </td>
                            <td class="py-4 px-4 font-bold text-gray-600 tabular-nums">
                                {{ $coupon->min_order_amount ? number_format($coupon->min_order_amount, 2) . ' ج.م' : 'بدون حد أدنى' }}
                            </td>
                            <td class="py-4 px-4 font-bold text-gray-700 tabular-nums">
                                {{ $coupon->used_count }} / {{ $coupon->usage_limit ?: '∞' }}
                            </td>
                            <td class="py-4 px-4 text-gray-500 text-[11px]">
                                @if($coupon->expires_at)
                                    <span>ينتهي: {{ $coupon->expires_at->format('Y-m-d') }}</span>
                                @else
                                    <span class="text-emerald-700 font-bold">دائم</span>
                                @endif
                            </td>
                            <td class="py-4 px-4">
                                @if($coupon->is_active)
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-800 border border-emerald-200">مفعل</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-gray-100 text-gray-700 border border-gray-200">معطل</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-end space-x-2 space-x-reverse">
                                <a href="{{ route('admin.coupons.edit', $coupon) }}" class="p-2 text-[#073F40] hover:text-[#0B5759] font-bold">تعديل</a>
                                <form action="{{ route('admin.coupons.destroy', $coupon) }}" method="POST" class="inline-block" onsubmit="return confirm('هل تريد بالتأكيد حذف هذا الكوبون؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-red-600 hover:text-red-800 font-bold cursor-pointer">حذف</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-12 text-gray-400">
                                لا توجد كوبونات خصم مسجلة حالياً.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($coupons->hasPages())
            <div class="p-4 border-t border-gray-100">
                {{ $coupons->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
