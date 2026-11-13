{{-- resources/views/storefront/products/shop.blade.php --}}
@extends('layouts.storefront')

@section('title', 'تسوق منتجات وخامات حورس | شبيه أمازون')

@section('content')
<div class="bg-[#EAEDED] py-3 sm:py-4 min-h-screen font-sans text-right" dir="rtl"
     x-data="{
         mobileFiltersOpen: false,
         sort: '{{ request('sort', 'latest') }}'
     }">
    <div class="max-w-[1500px] mx-auto px-2 sm:px-4 space-y-3">

        <!-- Amazon Top Category Filter Pills Carousel -->
        <div class="relative bg-white rounded-sm border border-gray-200/80 p-2 sm:p-2.5 shadow-2xs">
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

        <!-- Amazon Sub-header: Results Count & Sort Dropdown -->
        <div class="bg-white rounded-sm border border-gray-200/80 px-4 py-2.5 shadow-2xs flex flex-wrap items-center justify-between gap-3 text-xs">
            <div class="flex items-center gap-2">
                <button type="button" 
                        @click="mobileFiltersOpen = true"
                        class="lg:hidden flex items-center gap-1.5 px-3 py-1.5 bg-gray-100 border border-gray-300 rounded font-bold text-gray-800">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    <span>تصفية النتائج</span>
                </button>

                <span class="text-gray-700">
                    النتائج: <strong class="text-gray-900 font-bold">{{ $products->total() }}</strong> منتج متاح
                    @if(request('search'))
                        لـ "<strong class="text-[#073F40]">{{ request('search') }}</strong>"
                    @endif
                </span>
            </div>

            <!-- Amazon Sort Selector -->
            <div class="flex items-center gap-2">
                <label for="amazonSortSelect" class="text-gray-600 font-medium">الترتيب حسب:</label>
                <select id="amazonSortSelect"
                        name="sort"
                        onchange="window.location.href = this.value"
                        class="bg-[#F0F2F2] hover:bg-[#E3E6E6] border border-[#D5D9D9] text-gray-900 text-xs font-medium rounded-md px-2.5 py-1.5 outline-none cursor-pointer shadow-2xs">
                    <option value="{{ route('site.products', array_merge(request()->query(), ['sort' => 'latest'])) }}" {{ request('sort', 'latest') == 'latest' ? 'selected' : '' }}>الأحدث أولاً</option>
                    <option value="{{ route('site.products', array_merge(request()->query(), ['sort' => 'price_low'])) }}" {{ request('sort') == 'price_low' ? 'selected' : '' }}>السعر: من الأقل للأعلى</option>
                    <option value="{{ route('site.products', array_merge(request()->query(), ['sort' => 'price_high'])) }}" {{ request('sort') == 'price_high' ? 'selected' : '' }}>السعر: من الأعلى للأقل</option>
                    <option value="{{ route('site.products', array_merge(request()->query(), ['sort' => 'name'])) }}" {{ request('sort') == 'name' ? 'selected' : '' }}>أبجدياً (أ - ي)</option>
                </select>
            </div>
        </div>

        <!-- Master 2 Column Layout (Sidebar Filters + Products Grid) -->
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-3 items-start">

            <!-- Amazon Desktop Sidebar Filters (Right Side in RTL) -->
            <aside class="hidden lg:block lg:col-span-1 bg-white border border-gray-200/80 rounded-sm p-4 text-xs space-y-5 sticky top-28">
                
                <!-- Department / Categories (Amazon Radio Style) -->
                <div class="space-y-2">
                    <h3 class="font-black text-sm text-gray-900">القسم</h3>
                    <div class="space-y-1.5 max-h-60 overflow-y-auto pr-1">
                        <a href="{{ route('site.products', request()->except(['category_id', 'page'])) }}" 
                           class="flex items-center justify-between py-1 hover:text-[#C8A635] {{ !request('category_id') ? 'font-black text-[#073F40]' : 'text-gray-700' }}">
                            <span>الكل (جميع الأقسام)</span>
                            <span class="text-[10px] text-gray-400">({{ $products->total() }} صنف)</span>
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

                <!-- Customer Reviews (Amazon Stars) -->
                <div class="pt-3 border-t border-gray-200 space-y-2">
                    <h3 class="font-black text-sm text-gray-900">تقييمات العملاء</h3>
                    <div class="space-y-1">
                        <div class="flex items-center gap-1.5 text-gray-800 hover:text-[#C8A635] cursor-pointer">
                            <span class="text-[#FFA41C] text-sm">★★★★☆</span>
                            <span class="text-[11px] font-medium">فأكثر</span>
                        </div>
                        <div class="flex items-center gap-1.5 text-gray-800 hover:text-[#C8A635] cursor-pointer">
                            <span class="text-[#FFA41C] text-sm">★★★☆☆</span>
                            <span class="text-[11px] font-medium">فأكثر</span>
                        </div>
                    </div>
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

                <!-- Price Range (Amazon Price Filter) -->
                <div class="pt-3 border-t border-gray-200 space-y-2">
                    <h3 class="font-black text-sm text-gray-900">السعر (جنيه مصري)</h3>
                    <form action="{{ route('site.products') }}" method="GET" class="space-y-2">
                        @foreach(request()->except(['min_price', 'max_price', 'page']) as $k => $v)
                            <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                        @endforeach
                        <div class="flex items-center gap-2">
                            <input type="number" 
                                   name="min_price" 
                                   value="{{ request('min_price') }}" 
                                   placeholder="من" 
                                   class="w-16 px-2 py-1 bg-white border border-gray-300 rounded text-xs outline-none">
                            <span class="text-gray-400">-</span>
                            <input type="number" 
                                   name="max_price" 
                                   value="{{ request('max_price') }}" 
                                   placeholder="إلى" 
                                   class="w-16 px-2 py-1 bg-white border border-gray-300 rounded text-xs outline-none">
                            <button type="submit" 
                                    class="px-2.5 py-1 bg-[#F0F2F2] hover:bg-[#E3E6E6] border border-[#D5D9D9] rounded text-xs font-bold text-gray-800 shadow-2xs">
                                انتقل
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Deals & Discounts (Amazon Deals) -->
                <div class="pt-3 border-t border-gray-200 space-y-2">
                    <h3 class="font-black text-sm text-gray-900">العروض والخصومات</h3>
                    <div class="space-y-1.5">
                        <a href="{{ route('site.products', array_merge(request()->query(), ['deals_only' => request('deals_only') ? null : 1])) }}"
                           class="flex items-center gap-2 text-gray-700 hover:text-[#CC0C39]">
                            <input type="checkbox" {{ request('deals_only') ? 'checked' : '' }} class="rounded text-[#CC0C39] pointer-events-none">
                            <span>صفقات حورس الحصرية</span>
                        </a>
                        <a href="{{ route('site.products', array_merge(request()->query(), ['in_stock' => request('in_stock') ? null : 1])) }}"
                           class="flex items-center gap-2 text-gray-700 hover:text-emerald-700">
                            <input type="checkbox" {{ request('in_stock') ? 'checked' : '' }} class="rounded text-emerald-600 pointer-events-none">
                            <span>المتوفر بالمخزون فقط</span>
                        </a>
                    </div>
                </div>

                <!-- Reset Filters -->
                @if(request()->hasAny(['category_id', 'deals_only', 'in_stock', 'min_price', 'max_price', 'search']))
                    <div class="pt-3 border-t border-gray-200">
                        <a href="{{ route('site.products') }}" class="block text-center py-1.5 text-xs font-bold text-red-600 hover:underline">
                            مسح جميع الفلاتر
                        </a>
                    </div>
                @endif

            </aside>

            <!-- Amazon Product Cards Grid Area (4 Columns) -->
            <main class="lg:col-span-4 space-y-4">
                
                @if($products->isNotEmpty())
                    <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-2.5 sm:gap-3">
                        @foreach($products as $product)
                            <x-storefront.ui.product-card :product="$product" />
                        @endforeach
                    </div>

                    <!-- Amazon Pagination Bar -->
                    @if($products->hasPages())
                        <div class="pt-6 flex justify-center">
                            {{ $products->links() }}
                        </div>
                    @endif
                @else
                    <!-- Empty State -->
                    <div class="bg-white rounded-sm border border-gray-200 p-12 text-center space-y-3">
                        <div class="text-4xl">🔍</div>
                        <h4 class="font-black text-base text-gray-900">لم يتم العثور على سلع تطابق بحثك</h4>
                        <p class="text-xs text-gray-500 max-w-md mx-auto">
                            تأكد من كتابة الكلمات بشكل صحيح أو قم بمسح الفلاتر لاستعراض جميع الخامات المتاحة لدى مؤسسة حورس.
                        </p>
                        <div class="pt-2">
                            <a href="{{ route('site.products') }}" class="inline-block px-5 py-2 bg-[#FFD814] hover:bg-[#F7CA00] text-gray-900 font-bold text-xs rounded shadow-xs">
                                عرض جميع المنتجات
                            </a>
                        </div>
                    </div>
                @endif

            </main>

        </div>

    </div>

    <!-- Mobile Filters Drawer -->
    <div x-show="mobileFiltersOpen"
         x-cloak
         class="fixed inset-0 z-50 lg:hidden overflow-hidden"
         dir="rtl">
        <div class="fixed inset-0 bg-black/50" @click="mobileFiltersOpen = false"></div>
        <div class="fixed inset-y-0 right-0 max-w-xs w-full bg-white shadow-2xl flex flex-col z-50 p-4 space-y-4 overflow-y-auto">
            <div class="flex items-center justify-between pb-3 border-b border-gray-200">
                <h3 class="font-black text-sm text-gray-900">تصفية النتائج</h3>
                <button type="button" @click="mobileFiltersOpen = false" class="text-gray-500 text-sm font-bold">✕ إغلاق</button>
            </div>
            
            <!-- Mobile Category List -->
            <div class="space-y-1.5 text-xs">
                <h4 class="font-bold text-gray-900">الأقسام:</h4>
                <a href="{{ route('site.products') }}" class="block py-1 text-gray-700">جميع الأقسام</a>
                @foreach($categories as $cat)
                    <a href="{{ route('site.products', ['category_id' => $cat->id]) }}" class="block py-1 text-gray-700">
                        {{ $cat->name }} ({{ $cat->products_count }})
                    </a>
                @endforeach
            </div>

            <!-- Mobile Quick Filters -->
            <div class="pt-3 border-t border-gray-200 space-y-2 text-xs">
                <a href="{{ route('site.products', array_merge(request()->query(), ['deals_only' => 1])) }}" class="block py-1 text-[#CC0C39] font-bold">
                    صفقات حورس فقط 🔥
                </a>
                <a href="{{ route('site.products', array_merge(request()->query(), ['in_stock' => 1])) }}" class="block py-1 text-emerald-700 font-bold">
                    المتوفر بالمخزون فقط ✔️
                </a>
            </div>

            <div class="pt-4">
                <button type="button" @click="mobileFiltersOpen = false" class="w-full py-2 bg-[#FFD814] text-gray-900 font-bold text-xs rounded">
                    تطبيق والعودة للمنتجات
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
