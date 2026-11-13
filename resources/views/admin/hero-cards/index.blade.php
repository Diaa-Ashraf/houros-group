@extends('layouts.admin')

@section('title', __('كروت وبطاقات الهيرو (Hero Cards)'))

@section('content')
<div class="space-y-6 text-right" dir="rtl">
 
    <!-- Top Header -->
    <div class="bg-white border border-[#EADBCC] rounded-2xl p-6 sm:p-8 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="text-xs font-bold text-[#C5A059] uppercase tracking-wider">Amazon-Style Hero Rack</span>
            </div>
            <h1 class="font-sans text-2xl sm:text-3xl font-bold text-[#18181B]">
                {{ __('كروت وبطاقات واجهة الهيرو') }}
            </h1>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">
                {{ __('إدارة البطاقات الترويجية الـ 5 التي تطفو أسفل بانر الهيرو في الصفحة الرئيسية على نمط أمازون مصر.') }}
            </p>
        </div>

        <a href="{{ route('admin.hero-cards.create') }}" class="px-5 py-3 rounded-2xl bg-[#073F40] hover:bg-[#0B5759] text-white text-xs font-black shadow-md transition-all flex items-center gap-2 shrink-0 cursor-pointer">
            <span class="text-base leading-none">+</span>
            <span>{{ __('إضافة كارت جديد') }}</span>
        </a>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center justify-between">
            <span>{{ session('success') }}</span>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900 font-black">✕</button>
        </div>
    @endif

    <!-- Cards Table -->
    <div class="bg-white border border-[#EADBCC] rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead class="bg-[#F8F6F2] text-gray-600 uppercase text-[11px] border-b border-[#EADBCC]">
                    <tr>
                        <th class="py-3.5 px-6 font-semibold" style="width: 70px;">{{ __('الترتيب') }}</th>
                        <th class="py-3.5 px-4 font-semibold" style="width: 100px;">{{ __('الصورة') }}</th>
                        <th class="py-3.5 px-6 font-semibold">{{ __('العنوان والنص الترويجي') }}</th>
                        <th class="py-3.5 px-6 font-semibold">{{ __('الرابط ونص التوجيه') }}</th>
                        <th class="py-3.5 px-4 font-semibold text-center">{{ __('الحالة') }}</th>
                        <th class="py-3.5 px-6 text-left font-semibold">{{ __('إجراءات') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#EADBCC]">
                    @forelse($cards as $card)
                        <tr class="hover:bg-[#F8F6F2]/50 transition-colors">
                            <td class="py-4 px-6 font-black text-gray-700">
                                <span class="w-8 h-8 rounded-lg bg-gray-100 border border-gray-200 flex items-center justify-center text-xs font-bold">
                                    {{ $card->order }}
                                </span>
                            </td>
                            <td class="py-4 px-4">
                                <div class="w-16 h-16 rounded-xl bg-gray-50 border border-gray-200 p-1 flex-shrink-0 flex items-center justify-center overflow-hidden">
                                    <img src="{{ $card->image_url }}" alt="{{ $card->title }}" class="w-full h-full object-cover rounded-lg">
                                </div>
                            </td>
                            <td class="py-4 px-6 space-y-1">
                                <div class="flex items-center gap-2">
                                    <span class="font-black text-[#18181B] text-sm">{{ $card->title }}</span>
                                    @if($card->badge_text)
                                        <span class="px-2 py-0.5 rounded bg-[#C8A635]/20 text-[#073F40] font-bold text-[10px]">
                                            {{ $card->badge_text }}
                                        </span>
                                    @endif
                                </div>
                                @if($card->subtitle)
                                    <span class="text-xs text-gray-500 block max-w-md">{{ $card->subtitle }}</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 space-y-1">
                                <span class="font-bold text-[#007185] text-xs block">{{ $card->link_text ?: 'تسوق الآن ←' }}</span>
                                <span class="text-[11px] text-gray-400 block truncate max-w-xs" dir="ltr">{{ $card->url ?: '-' }}</span>
                            </td>
                            <td class="py-4 px-4 text-center">
                                <form action="{{ route('admin.hero-cards.toggle', $card->id) }}" method="POST" class="inline-block">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" 
                                            class="px-3 py-1 rounded-full text-[11px] font-bold transition-all cursor-pointer {{ $card->is_active ? 'bg-emerald-100 text-emerald-800 hover:bg-emerald-200' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}"
                                            title="{{ __('انقر لتبديل حالة العرض') }}">
                                        {{ $card->is_active ? '✓ معروض بالواجهة' : '✕ معطل ومخفي' }}
                                    </button>
                                </form>
                            </td>
                            <td class="py-4 px-6 text-left">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.hero-cards.edit', $card->id) }}" 
                                       class="px-3 py-1.5 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 text-gray-800 font-bold text-xs transition-colors">
                                        {{ __('تعديل') }}
                                    </a>
                                    <form action="{{ route('admin.hero-cards.destroy', $card->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا الكارت من الواجهة؟')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-3 py-1.5 rounded-lg border border-red-200 bg-red-50 hover:bg-red-100 text-red-700 font-bold text-xs transition-colors cursor-pointer">
                                            {{ __('حذف') }}
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-gray-500">
                                <span class="text-3xl block mb-2">🃏</span>
                                <p class="text-sm font-bold">{{ __('لا توجد بطاقات هيرو مضافة حالياً.') }}</p>
                                <p class="text-xs text-gray-400 mt-1">{{ __('يمكنك إضافة أول كارت بالضغط على زر "إضافة كارت جديد" بالأعلى.') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

