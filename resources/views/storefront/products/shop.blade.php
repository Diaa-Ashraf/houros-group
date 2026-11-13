{{-- resources/views/storefront/products/shop.blade.php --}}
@extends('layouts.storefront')

@section('title', 'تسوق منتجات وخامات حورس للتوريدات والتشطيبات')

@section('content')
@php
    $activeFiltersCount = 0;
    if (request('category_id')) $activeFiltersCount++;
    if (request('deals_only')) $activeFiltersCount++;
    if (request('in_stock')) $activeFiltersCount++;
    if (request('min_price') || request('max_price')) $activeFiltersCount++;
    if (request('search')) $activeFiltersCount++;
@endphp

<div class="bg-[#EAEDED] py-3 sm:py-4 min-h-screen font-sans text-right" dir="rtl"
     x-data="{
         mobileFiltersOpen: false,
         sort: '{{ request('sort', 'latest') }}',
         activeFiltersCount: {{ $activeFiltersCount }}
     }">
    <div class="max-w-[1500px] mx-auto px-2 sm:px-4 space-y-3">

        <!-- Amazon Top Category Filter Pills Carousel -->
        <div class="relative bg-white rounded-xs border border-gray-200/80 p-2 sm:p-2.5 shadow-2xs">
            <div class="flex items-center gap-2 overflow-x-auto no-scrollbar scroll-smooth py-1" id="amazonPillsTrack">
                <a href="{{ route('site.products', array_merge(request()->except(['category_id', 'page']))) }}"
                   class="px-3.5 py-1.5 rounded-full text-xs font-bold shrink-0 transition-colors border {{ !request('category_id') ? 'bg-[#073F40] text-white border-[#073F40]' : 'bg-white text-gray-700 hover:bg-gray-100 border-gray-300' }}">
                    الكل
                </a>

                <a href="{{ route('site.products', array_merge(request()->query(), ['deals_only' => request('deals_only') ? null : 1])) }}"
                   class="px-3.5 py-1.5 rounded-full text-xs font-bold shrink-0 transition-colors border flex items-center gap-1 {{ request('deals_only') ? 'bg-[#CC0C39] text-white border-[#CC0C39]' : 'bg-white text-gray-700 hover:bg-gray-100 border-gray-300' }}">
                    <span>صفقات وعروض اليوم</span>
                    <span class="text-[10px]">🔥</span>
                </a>

                @foreach($categories as $cat)
                    <a href="{{ route('site.products', array_merge(request()->except(['category_id', 'page']), ['category_id' => $cat->id])) }}"
                       class="px-3.5 py-1.5 rounded-full text-xs font-medium shrink-0 transition-colors border flex items-center gap-1.5 {{ request('category_id') == $cat->id ? 'bg-[#073F40] text-white border-[#073F40] font-bold' : 'bg-white text-gray-700 hover:bg-gray-100 border-gray-300' }}">
                        <span>{{ $cat->name }}</span>
                        @if($cat->products_count > 0)
                            <span class="text-[10px] {{ request('category_id') == $cat->id ? 'text-amber-300' : 'text-gray-400' }}">({{ $cat->products_count }})</span>
                        @endif
                    </a>
                @endforeach

                <a href="{{ route('site.products', array_merge(request()->query(), ['in_stock' => request('in_stock') ? null : 1])) }}"
                   class="px-3.5 py-1.5 rounded-full text-xs font-medium shrink-0 transition-colors border {{ request('in_stock') ? 'bg-emerald-700 text-white border-emerald-700 font-bold' : 'bg-white text-gray-700 hover:bg-gray-100 border-gray-300' }}">
                    المتوفر بالمخزون فقط
                </a>
            </div>
        </div>

        <!-- Sub-header: Results Count, Mobile Filter Button, Sort Dropdown -->
        <div class="bg-white rounded-xs border border-gray-200/80 px-3 sm:px-4 py-2.5 shadow-2xs flex flex-wrap items-center justify-between gap-2.5 text-xs">
            <div class="flex items-center gap-2">
                <!-- Prominent Mobile Filter Trigger Button -->
                <button type="button" 
                        @click="mobileFiltersOpen = true"
                        class="lg:hidden inline-flex items-center gap-1.5 px-3 py-1.5 bg-[#073F40] text-white font-bold rounded-md shadow-xs active:scale-95 transition-all cursor-pointer">
                    <svg class="w-4 h-4 text-[#E2BF52]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    <span>تصفية وفلترة</span>
                    @if($activeFiltersCount > 0)
                        <span class="w-4 h-4 rounded-full bg-[#CC0C39] text-white text-[10px] font-black flex items-center justify-center">
                            {{ $activeFiltersCount }}
                        </span>
                    @endif
                </button>

                <span class="text-gray-700 text-[11px] sm:text-xs">
                    النتائج: <strong class="text-gray-900 font-bold">{{ $products->total() }}</strong> منتج
                    @if(request('search'))
                        لـ "<strong class="text-[#073F40]">{{ request('search') }}</strong>"
                    @endif
                </span>
            </div>

            <!-- Active Filter Reset Pill on Mobile -->
            @if($activeFiltersCount > 0)
                <div class="lg:hidden flex items-center">
                    <a href="{{ route('site.products') }}" class="text-[11px] text-red-600 font-bold hover:underline flex items-center gap-1">
                        <span>مسح الفلاتر</span>
                        <span>✕</span>
                    </a>
                </div>
            @endif

            <!-- Amazon Sort Selector -->
            <div class="flex items-center gap-1.5">
                <label for="amazonSortSelect" class="text-gray-500 font-medium text-[11px] hidden sm:inline">الترتيب:</label>
                <select id="amazonSortSelect"
                        name="sort"
                        onchange="window.location.href = this.value"
                        class="bg-[#F0F2F2] hover:bg-[#E3E6E6] border border-[#D5D9D9] text-gray-900 text-xs font-medium rounded-md px-2 py-1.5 outline-none cursor-pointer shadow-2xs">
                    <option value="{{ route('site.products', array_merge(request()->query(), ['sort' => 'latest'])) }}" {{ request('sort', 'latest') == 'latest' ? 'selected' : '' }}>الأحدث أولاً</option>
                    <option value="{{ route('site.products', array_merge(request()->query(), ['sort' => 'price_low'])) }}" {{ request('sort') == 'price_low' ? 'selected' : '' }}>السعر: الأقل للأعلى</option>
                    <option value="{{ route('site.products', array_merge(request()->query(), ['sort' => 'price_high'])) }}" {{ request('sort') == 'price_high' ? 'selected' : '' }}>السعر: الأعلى للأقل</option>
                    <option value="{{ route('site.products', array_merge(request()->query(), ['sort' => 'name'])) }}" {{ request('sort') == 'name' ? 'selected' : '' }}>أبجدياً (أ - ي)</option>
                </select>
            </div>
        </div>

        <!-- Master 2 Column Layout (Sidebar Filters + Products Grid) -->
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-3 items-start">

            <!-- Amazon Desktop Sidebar Filters (Right Side in RTL) -->
            <aside class="hidden lg:block lg:col-span-1 bg-white border border-gray-200/80 rounded-xs p-4 text-xs space-y-5 sticky top-28 shadow-2xs">
                
                <!-- Department / Categories -->
                <div class="space-y-2">
                    <h3 class="font-black text-sm text-gray-900 flex items-center justify-between">
                        <span>القسم</span>
                        @if(request('category_id'))
                            <a href="{{ route('site.products', request()->except(['category_id', 'page'])) }}" class="text-[10px] text-red-600 font-bold hover:underline">إلغاء</a>
                        @endif
                    </h3>
                    <div class="space-y-1 max-h-64 overflow-y-auto pr-1">
                        <a href="{{ route('site.products', request()->except(['category_id', 'page'])) }}" 
                           class="flex items-center justify-between py-1 hover:text-[#C8A635] {{ !request('category_id') ? 'font-black text-[#073F40]' : 'text-gray-700' }}">
                            <span>الكل (جميع الأقسام)</span>
                            <span class="text-[10px] text-gray-400">({{ $products->total() }})</span>
                        </a>

                        @foreach($categories as $cat)
                            <a href="{{ route('site.products', array_merge(request()->except(['category_id', 'page']), ['category_id' => $cat->id])) }}" 
                               class="flex items-center justify-between py-1 hover:text-[#C8A635] {{ request('category_id') == $cat->id ? 'font-black text-[#073F40]' : 'text-gray-700' }}">
                                <span>{{ $cat->name }}</span>
                                <span class="text-[10px] text-gray-400">({{ $cat->products_count }})</span>
                            </a>
                        @endforeach
                    </div>
                </div>

                <!-- Price Range (Amazon Price Filter) -->
                <div class="pt-3 border-t border-gray-200 space-y-2">
                    <h3 class="font-black text-sm text-gray-900">السعر (جنيه مصري)</h3>
                    <form action="{{ route('site.products') }}" method="GET" class="space-y-2">
                        @foreach(request()->except(['min_price', 'max_price', 'page']) as $k => $v)
                            <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                        @endforeach
                        <div class="flex items-center gap-1.5">
                            <input type="number" 
                                   name="min_price" 
                                   value="{{ request('min_price') }}" 
                                   placeholder="من" 
                                   class="w-16 px-2 py-1 bg-white border border-gray-300 rounded text-xs outline-none focus:border-[#073F40]">
                            <span class="text-gray-400">-</span>
                            <input type="number" 
                                   name="max_price" 
                                   value="{{ request('max_price') }}" 
                                   placeholder="إلى" 
                                   class="w-16 px-2 py-1 bg-white border border-gray-300 rounded text-xs outline-none focus:border-[#073F40]">
                            <button type="submit" 
                                    class="px-2.5 py-1 bg-[#F0F2F2] hover:bg-[#E3E6E6] border border-[#D5D9D9] rounded text-xs font-bold text-gray-800 shadow-2xs cursor-pointer">
                                تطبيق
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Brands (Amazon Checkbox Style) -->
                <div class="pt-3 border-t border-gray-200 space-y-2">
                    <h3 class="font-black text-sm text-gray-900">العلامات التجارية المعتمدة</h3>
                    <div class="space-y-1.5 text-gray-700">
                        <label class="flex items-center gap-2 cursor-pointer hover:text-[#073F40]">
                            <input type="checkbox" checked disabled class="rounded text-[#073F40] focus:ring-[#C8A635]">
                            <span>دهانات SCIB (سكيب)</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer hover:text-[#073F40]">
                            <input type="checkbox" checked disabled class="rounded text-[#073F40] focus:ring-[#C8A635]">
                            <span>فيوتك Fyutec الأصلي</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer hover:text-[#073F40]">
                            <input type="checkbox" checked disabled class="rounded text-[#073F40] focus:ring-[#C8A635]">
                            <span>كناوف للجبسون بورد</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer hover:text-[#073F40]">
                            <input type="checkbox" checked disabled class="rounded text-[#073F40] focus:ring-[#C8A635]">
                            <span>كيمابود ومستلزمات العزل</span>
                        </label>
                    </div>
                </div>

                <!-- Deals & Discounts (Amazon Deals) -->
                <div class="pt-3 border-t border-gray-200 space-y-2">
                    <h3 class="font-black text-sm text-gray-900">العروض والخصومات</h3>
                    <div class="space-y-1.5">
                        <a href="{{ route('site.products', array_merge(request()->query(), ['deals_only' => request('deals_only') ? null : 1])) }}"
                           class="flex items-center gap-2 text-gray-700 hover:text-[#CC0C39]">
                            <input type="checkbox" {{ request('deals_only') ? 'checked' : '' }} class="rounded text-[#CC0C39] pointer-events-none">
                            <span>صفقات حورس الحصرية 🔥</span>
                        </a>
                        <a href="{{ route('site.products', array_merge(request()->query(), ['in_stock' => request('in_stock') ? null : 1])) }}"
                           class="flex items-center gap-2 text-gray-700 hover:text-emerald-700">
                            <input type="checkbox" {{ request('in_stock') ? 'checked' : '' }} class="rounded text-emerald-600 pointer-events-none">
                            <span>المتوفر بالمخزون فقط ✓</span>
                        </a>
                    </div>
                </div>

                <!-- Reset Filters -->
                @if($activeFiltersCount > 0)
                    <div class="pt-3 border-t border-gray-200">
                        <a href="{{ route('site.products') }}" class="block text-center py-2 px-3 bg-gray-100 hover:bg-gray-200 text-xs font-bold text-red-600 rounded transition-colors">
                            مسح جميع الفلاتر ({{ $activeFiltersCount }})
                        </a>
                    </div>
                @endif

            </aside>

            <!-- Product Cards Grid Area -->
            <main class="lg:col-span-4 space-y-4">
                
                @if($products->isNotEmpty())
                    <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-2.5 sm:gap-3">
                        @foreach($products as $product)
                            <x-storefront.ui.product-card :product="$product" />
                        @endforeach
                    </div>

                    <!-- Pagination Bar -->
                    @if($products->hasPages())
                        <div class="pt-6 flex justify-center">
                            {{ $products->links() }}
                        </div>
                    @endif
                @else
                    <!-- Empty State -->
                    <div class="bg-white rounded-xs border border-gray-200 p-8 sm:p-12 text-center space-y-3">
                        <div class="text-4xl">🔍</div>
                        <h4 class="font-black text-base text-gray-900">لم يتم العثور على سلع تطابق اختياراتك</h4>
                        <p class="text-xs text-gray-500 max-w-md mx-auto">
                            تأكد من شروط البحث أو قم بمسح الفلاتر لاستعراض جميع الخامات والمنتجات المتاحة بمؤسسة حورس.
                        </p>
                        <div class="pt-2">
                            <a href="{{ route('site.products') }}" class="inline-block px-5 py-2.5 bg-[#C8A635] hover:bg-[#B38F24] text-[#073F40] font-black text-xs rounded shadow-xs">
                                استعراض جميع المنتجات
                            </a>
                        </div>
                    </div>
                @endif

            </main>

        </div>

    </div>

    <!-- Floating Quick Filter Button on Mobile (Bottom Left) -->
    <div class="fixed bottom-20 left-4 z-40 lg:hidden">
        <button type="button" 
                @click="mobileFiltersOpen = true"
                class="px-4 py-2.5 rounded-full bg-[#073F40] hover:bg-[#0B5759] text-white text-xs font-black shadow-xl flex items-center gap-2 border border-[#E2BF52] cursor-pointer active:scale-95 transition-transform">
            <svg class="w-4 h-4 text-[#E2BF52]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
            </svg>
            <span>فلترة المنتجات</span>
            @if($activeFiltersCount > 0)
                <span class="w-4 h-4 rounded-full bg-[#CC0C39] text-white text-[10px] font-black flex items-center justify-center">
                    {{ $activeFiltersCount }}
                </span>
            @endif
        </button>
    </div>

    <!-- Complete Amazon Mobile Filters Drawer (Full Screen / Slide Over) -->
    <div x-show="mobileFiltersOpen"
         x-cloak
         class="fixed inset-0 z-[99999] lg:hidden overflow-hidden"
         dir="rtl">
        
        <!-- Backdrop with Blur -->
        <div class="fixed inset-0 bg-black/60 backdrop-blur-xs transition-opacity" 
             @click="mobileFiltersOpen = false"></div>

        <!-- Slide Panel -->
        <div class="fixed inset-y-0 right-0 max-w-sm w-full bg-white shadow-2xl flex flex-col z-[100000] overflow-hidden">
            
            <!-- Drawer Header -->
            <div class="p-4 bg-[#073F40] text-white flex items-center justify-between border-b border-[#0B5759]">
                <div class="flex items-center gap-2">
                    <span class="text-base text-[#E2BF52]">⚡</span>
                    <h3 class="font-black text-sm text-white">تصفية وفلترة المنتجات</h3>
                    @if($activeFiltersCount > 0)
                        <span class="px-2 py-0.5 rounded-full bg-[#CC0C39] text-white text-[10px] font-bold">
                            {{ $activeFiltersCount }} نشط
                        </span>
                    @endif
                </div>
                <button type="button" 
                        @click="mobileFiltersOpen = false" 
                        class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center font-bold text-sm cursor-pointer">
                    ✕
                </button>
            </div>
            
            <!-- Drawer Body (Scrollable Form) -->
            <form action="{{ route('site.products') }}" method="GET" class="flex-1 overflow-y-auto p-4 space-y-5 text-xs divide-y divide-gray-100">
                @if(request('search'))
                    <input type="hidden" name="search" value="{{ request('search') }}">
                @endif
                @if(request('sort'))
                    <input type="hidden" name="sort" value="{{ request('sort') }}">
                @endif

                <!-- 1. Categories Selection -->
                <div class="space-y-2 pt-1">
                    <h4 class="font-black text-sm text-gray-900 flex items-center justify-between">
                        <span>أقسام وخامات حورس</span>
                        <span class="text-[10px] text-gray-500 font-normal">اختر القسم</span>
                    </h4>
                    
                    <div class="space-y-1 max-h-56 overflow-y-auto pr-1">
                        <label class="flex items-center justify-between p-2 rounded-lg bg-gray-50 hover:bg-gray-100 cursor-pointer">
                            <div class="flex items-center gap-2">
                                <input type="radio" name="category_id" value="" {{ !request('category_id') ? 'checked' : '' }} class="text-[#073F40] focus:ring-[#C8A635]">
                                <span class="font-bold text-gray-800">جميع الأقسام</span>
                            </div>
                            <span class="text-[10px] text-gray-400">({{ $products->total() }})</span>
                        </label>

                        @foreach($categories as $cat)
                            <label class="flex items-center justify-between p-2 rounded-lg bg-gray-50 hover:bg-gray-100 cursor-pointer">
                                <div class="flex items-center gap-2">
                                    <input type="radio" name="category_id" value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'checked' : '' }} class="text-[#073F40] focus:ring-[#C8A635]">
                                    <span class="font-medium text-gray-900">{{ $cat->name }}</span>
                                </div>
                                <span class="text-[10px] text-gray-400">({{ $cat->products_count }})</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- 2. Price Range (Min / Max) -->
                <div class="space-y-2 pt-4">
                    <h4 class="font-black text-sm text-gray-900">نطاق السعر (جنيه مصري)</h4>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="text-[10px] text-gray-500 block mb-1">من (أقل سعر):</label>
                            <input type="number" 
                                   name="min_price" 
                                   value="{{ request('min_price') }}" 
                                   placeholder="0" 
                                   class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-xs outline-none focus:border-[#073F40] font-bold">
                        </div>
                        <div>
                            <label class="text-[10px] text-gray-500 block mb-1">إلى (أعلى سعر):</label>
                            <input type="number" 
                                   name="max_price" 
                                   value="{{ request('max_price') }}" 
                                   placeholder="10000" 
                                   class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-xs outline-none focus:border-[#073F40] font-bold">
                        </div>
                    </div>
                </div>

                <!-- 3. Deals & Stock Toggles -->
                <div class="space-y-2 pt-4">
                    <h4 class="font-black text-sm text-gray-900">حالة العرض والتوافر</h4>
                    <div class="space-y-2">
                        <label class="flex items-center justify-between p-2.5 rounded-xl border border-red-200 bg-red-50/50 cursor-pointer">
                            <div class="flex items-center gap-2">
                                <input type="checkbox" name="deals_only" value="1" {{ request('deals_only') ? 'checked' : '' }} class="rounded text-[#CC0C39] focus:ring-red-500">
                                <span class="font-bold text-[#CC0C39]">صفقات وتخفيضات اليوم فقط</span>
                            </div>
                            <span class="text-xs">🔥</span>
                        </label>

                        <label class="flex items-center justify-between p-2.5 rounded-xl border border-emerald-200 bg-emerald-50/50 cursor-pointer">
                            <div class="flex items-center gap-2">
                                <input type="checkbox" name="in_stock" value="1" {{ request('in_stock') ? 'checked' : '' }} class="rounded text-emerald-600 focus:ring-emerald-500">
                                <span class="font-bold text-emerald-800">المتوفر في المخزون فقط</span>
                            </div>
                            <span class="text-xs">✓</span>
                        </label>
                    </div>
                </div>

                <!-- 4. Brands -->
                <div class="space-y-2 pt-4">
                    <h4 class="font-black text-sm text-gray-900">العلامات التجارية المعتمدة</h4>
                    <div class="space-y-1.5 text-gray-700">
                        <div class="flex items-center gap-2 py-1">
                            <span class="w-2 h-2 rounded-full bg-[#073F40]"></span>
                            <span>دهانات SCIB (سكيب) المعتمدة</span>
                        </div>
                        <div class="flex items-center gap-2 py-1">
                            <span class="w-2 h-2 rounded-full bg-[#073F40]"></span>
                            <span>فيوتك Fyutec الأصلي بضمان المصنع</span>
                        </div>
                        <div class="flex items-center gap-2 py-1">
                            <span class="w-2 h-2 rounded-full bg-[#073F40]"></span>
                            <span>كناوف لألواح الجبسون بورد</span>
                        </div>
                        <div class="flex items-center gap-2 py-1">
                            <span class="w-2 h-2 rounded-full bg-[#073F40]"></span>
                            <span>كيمابود وخامات العزل والتأسيس</span>
                        </div>
                    </div>
                </div>

                <!-- Submit & Reset Actions Inside Drawer -->
                <div class="pt-5 space-y-2 sticky bottom-0 bg-white py-3 border-t border-gray-200">
                    <button type="submit" 
                            class="w-full py-3 px-4 bg-[#C8A635] hover:bg-[#B38F24] active:scale-98 text-[#073F40] font-black text-sm rounded-xl shadow-md transition-all flex items-center justify-center gap-2 cursor-pointer border border-[#B38F24]">
                        <span>تطبيق الفلاتر وعرض النتائج</span>
                        <span>←</span>
                    </button>

                    @if($activeFiltersCount > 0)
                        <a href="{{ route('site.products') }}" 
                           class="w-full block py-2 text-center text-xs text-red-600 font-bold hover:underline">
                            مسح جميع الفلاتر (إعادة ضبط)
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
