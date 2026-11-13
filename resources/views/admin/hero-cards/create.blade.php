@extends('layouts.admin')

@section('title', __('إضافة كارت هيرو جديد'))

@section('content')
<div class="max-w-4xl mx-auto space-y-6 text-right" dir="rtl">
    
    <!-- Header -->
    <div class="bg-white border border-[#EADBCC] rounded-2xl p-6 sm:p-8 shadow-sm flex items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('admin.hero-cards.index') }}" class="text-xs font-bold text-[#C5A059] hover:underline">
                    ← العودة لقائمة كروت الهيرو
                </a>
            </div>
            <h1 class="font-sans text-2xl font-bold text-[#18181B]">
                {{ __('إضافة كارت واجهة جديد (Hero Card)') }}
            </h1>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">
                {{ __('سيظهر هذا الكارت في صف بطاقات أمازون التي تطفو أسفل بانر الهيرو بالصفحة الرئيسية.') }}
            </p>
        </div>
    </div>

    @if ($errors->any())
        <div class="p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 text-xs font-bold space-y-1">
            <span class="block text-sm">يرجى تصحيح الأخطاء التالية:</span>
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Form -->
    <form action="{{ route('admin.hero-cards.store') }}" method="POST" enctype="multipart/form-data" class="bg-white border border-[#EADBCC] rounded-2xl p-6 sm:p-8 shadow-sm space-y-6">
        @csrf

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">

            <!-- Title -->
            <div class="sm:col-span-2 space-y-1.5">
                <label for="title" class="font-bold text-xs text-gray-800 block">
                    {{ __('عنوان الكارت الرئيسي') }} <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       id="title" 
                       name="title" 
                       value="{{ old('title') }}" 
                       required 
                       placeholder="مثال: دهانات SCIB العالمية"
                       class="w-full bg-gray-50 border border-gray-200 focus:border-[#073F40] focus:bg-white rounded-xl px-4 py-2.5 text-xs text-gray-900 outline-none transition-all">
            </div>

            <!-- Subtitle -->
            <div class="sm:col-span-2 space-y-1.5">
                <label for="subtitle" class="font-bold text-xs text-gray-800 block">
                    {{ __('الوصف الفرعي القصير') }}
                </label>
                <input type="text" 
                       id="subtitle" 
                       name="subtitle" 
                       value="{{ old('subtitle') }}" 
                       placeholder="مثال: خلط كمبيوتر فوري لأحدث درجات 2026"
                       class="w-full bg-gray-50 border border-gray-200 focus:border-[#073F40] focus:bg-white rounded-xl px-4 py-2.5 text-xs text-gray-900 outline-none transition-all">
            </div>

            <!-- Image Upload -->
            <div class="sm:col-span-2 space-y-1.5" x-data="{ preview: null }">
                <label for="image" class="font-bold text-xs text-gray-800 block">
                    {{ __('صورة الكارت') }} <span class="text-gray-400 font-normal">(يفضل أبعاد مربعة 1:1 أو 4:3)</span>
                </label>
                <input type="file" 
                       id="image" 
                       name="image" 
                       accept="image/*"
                       @change="preview = URL.createObjectURL($event.target.files[0])"
                       class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-xs text-gray-700 file:mr-4 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-[#073F40] file:text-white cursor-pointer">
                
                <div x-show="preview" x-cloak class="mt-2 w-28 h-28 rounded-xl border border-gray-300 p-1 overflow-hidden bg-gray-50">
                    <img :src="preview" class="w-full h-full object-cover rounded-lg">
                </div>
            </div>

            <!-- Link URL -->
            <div class="space-y-1.5">
                <label for="url" class="font-bold text-xs text-gray-800 block">
                    {{ __('رابط التوجيه (URL)') }}
                </label>
                <input type="text" 
                       id="url" 
                       name="url" 
                       value="{{ old('url') }}" 
                       placeholder="مثال: /product/category/33 أو رابط واتساب"
                       class="w-full bg-gray-50 border border-gray-200 focus:border-[#073F40] focus:bg-white rounded-xl px-4 py-2.5 text-xs text-gray-900 outline-none transition-all text-left" dir="ltr">
            </div>

            <!-- Link Text -->
            <div class="space-y-1.5">
                <label for="link_text" class="font-bold text-xs text-gray-800 block">
                    {{ __('نص الرابط السفلي') }}
                </label>
                <input type="text" 
                       id="link_text" 
                       name="link_text" 
                       value="{{ old('link_text', 'تسوق الآن ←') }}" 
                       placeholder="مثال: تسوق دهانات SCIB ←"
                       class="w-full bg-gray-50 border border-gray-200 focus:border-[#073F40] focus:bg-white rounded-xl px-4 py-2.5 text-xs text-gray-900 outline-none transition-all">
            </div>

            <!-- Badge Text (Optional) -->
            <div class="space-y-1.5">
                <label for="badge_text" class="font-bold text-xs text-gray-800 block">
                    {{ __('شارة أو علامة صغيرة (اختياري)') }}
                </label>
                <input type="text" 
                       id="badge_text" 
                       name="badge_text" 
                       value="{{ old('badge_text') }}" 
                       placeholder="مثال: خصم 20% أو وكيل معتمد"
                       class="w-full bg-gray-50 border border-gray-200 focus:border-[#073F40] focus:bg-white rounded-xl px-4 py-2.5 text-xs text-gray-900 outline-none transition-all">
            </div>

            <!-- Display Order -->
            <div class="space-y-1.5">
                <label for="order" class="font-bold text-xs text-gray-800 block">
                    {{ __('ترتيب الظهور في الصف') }}
                </label>
                <input type="number" 
                       id="order" 
                       name="order" 
                       value="{{ old('order', $nextOrder) }}" 
                       min="0"
                       class="w-full bg-gray-50 border border-gray-200 focus:border-[#073F40] focus:bg-white rounded-xl px-4 py-2.5 text-xs text-gray-900 outline-none transition-all">
            </div>

            <!-- Is Active -->
            <div class="sm:col-span-2 pt-2">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" 
                           name="is_active" 
                           value="1" 
                           {{ old('is_active', true) ? 'checked' : '' }}
                           class="w-4 h-4 rounded text-[#073F40] border-gray-300 focus:ring-[#073F40]">
                    <span class="font-bold text-xs text-gray-900">{{ __('تفعيل وعرض الكارت في الصفحة الرئيسية مباشرة') }}</span>
                </label>
            </div>

        </div>

        <div class="pt-4 border-t border-gray-100 flex items-center justify-end gap-3">
            <a href="{{ route('admin.hero-cards.index') }}" class="px-5 py-2.5 rounded-xl border border-gray-300 text-gray-700 font-bold text-xs hover:bg-gray-50 transition-colors">
                {{ __('إلغاء') }}
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-[#073F40] hover:bg-[#0B5759] text-white font-black text-xs shadow-md transition-all cursor-pointer">
                {{ __('حفظ الكارت') }}
            </button>
        </div>

    </form>

</div>
@endsection

