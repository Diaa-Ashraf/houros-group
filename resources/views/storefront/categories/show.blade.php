{{-- resources/views/storefront/categories/show.blade.php --}}
@extends('layouts.storefront')

@section('title', $category->name . ' | مؤسسة حورس شبيه أمازون')

@section('content')
<div class="bg-[#EAEDED] py-3 sm:py-4 min-h-screen font-sans text-right" dir="rtl"
     x-data="{
         mobileFiltersOpen: false,
         sort: '{{ request('sort', 'latest') }}'
     }">
    <div class="max-w-[1500px] mx-auto px-2 sm:px-4 space-y-3">

        <!-- Amazon Breadcrumb & Department Banner -->
        <div class="bg-white rounded-sm border border-gray-200/80 p-3 sm:p-4 shadow-2xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <nav class="flex items-center gap-1.5 text-xs text-gray-500 mb-1">
                    <a href="{{ route('site.home') }}" class="hover:text-[#C8A635]">الرئيسية</a>
                    <span>›</span>
                    <a href="{{ route('categories.index') }}" class="hover:text-[#C8A635]">الأقسام</a>
                    <span>›</span>
                    <span class="text-gray-900 font-bold">{{ $category->name }}</span>
                </nav>
                <h1 class="text-xl sm:text-2xl font-black text-gray-900 leading-tight">
                    {{ $category->name }}
                </h1>
                @if($category->description)
                    <p class="text-xs text-gray-600 mt-0.5">{{ $category->description }}</p>
                @endif
            </div>

            <div class="text-xs text-gray-600 shrink-0">
                <span>النتائج: <strong class="text-gray-900 font-black">{{ $products->total() }}</strong> صنف</span>
            </div>
        </div>

        <!-- Master 2 Column Layout (Sidebar Filters + Products Grid) -->
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-3 items-start">

            <!-- Amazon Desktop Sidebar Filters -->
            <aside class="hidden lg:block lg:col-span-1 bg-white border border-gray-200/80 rounded-sm p-4 text-xs space-y-5 sticky top-28">
                
                <!-- Quick Category Navigation -->
                <div class="space-y-2">
                    <h3 class="font-black text-sm text-gray-900">الأقسام الأخرى</h3>
                    <div class="space-y-1 max-h-56 overflow-y-auto">
                        <a href="{{ route('site.products') }}" class="block py-1 text-gray-700 hover:text-[#073F40]">
                            ‹ جميع الأقسام والمنتجات
                        </a>
                        <a href="{{ route('categories.index') }}" class="block py-1 text-gray-700 hover:text-[#073F40]">
                            ‹ دليل الأقسام المصور
                        </a>
                    </div>
                </div>

                <!-- Price Range Filter -->
                <div class="pt-3 border-t border-gray-200 space-y-2">
                    <h3 class="font-black text-sm text-gray-900">السعر (جنيه مصري)</h3>
                    <form action="{{ route('category.product', $category->id) }}" method="GET" class="space-y-2">
                        @foreach(request()->except(['min_price', 'max_price', 'page']) as $k => $v)
                            <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                        @endforeach
                        <div class="flex items-center gap-1.5">
                            <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="من" class="w-16 px-2 py-1 bg-white border border-gray-300 rounded text-xs outline-none">
                            <span class="text-gray-400">-</span>
                            <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="إلى" class="w-16 px-2 py-1 bg-white border border-gray-300 rounded text-xs outline-none">
                            <button type="submit" class="px-2.5 py-1 bg-[#F0F2F2] hover:bg-[#E3E6E6] border border-[#D5D9D9] rounded text-xs font-bold text-gray-800 shadow-2xs">انتقل</button>
                        </div>
                    </form>
                </div>

                <!-- Special Offers & Availability -->
                <div class="pt-3 border-t border-gray-200 space-y-2">
                    <h3 class="font-black text-sm text-gray-900">خيارات التصفية</h3>
                    <div class="space-y-1.5 text-gray-700">
                        <a href="{{ route('category.product', array_merge(['id' => $category->id], request()->query(), ['deals_only' => request('deals_only') ? null : 1])) }}"
                           class="flex items-center gap-2 hover:text-[#CC0C39]">
                            <input type="checkbox" {{ request('deals_only') ? 'checked' : '' }} class="rounded text-[#CC0C39] pointer-events-none">
                            <span>عروض وتخفيضات القسم فقط 🔥</span>
                        </a>
                        <a href="{{ route('category.product', array_merge(['id' => $category->id], request()->query(), ['in_stock' => request('in_stock') ? null : 1])) }}"
                           class="flex items-center gap-2 hover:text-emerald-700">
                            <input type="checkbox" {{ request('in_stock') ? 'checked' : '' }} class="rounded text-emerald-600 pointer-events-none">
                            <span>المتوفر بالمخزون فقط ✔️</span>
                        </a>
                    </div>
                </div>

                @if(request()->hasAny(['deals_only', 'in_stock', 'min_price', 'max_price']))
                    <div class="pt-3 border-t border-gray-200">
                        <a href="{{ route('category.product', $category->id) }}" class="block text-center py-1.5 text-xs font-bold text-red-600 hover:underline">
                            مسح جميع الفلاتر
                        </a>
                    </div>
                @endif
            </aside>

            <!-- Products Grid Main Area -->
            <main class="lg:col-span-4 space-y-3">
                
                @if($products->isNotEmpty())
                    <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-2.5 sm:gap-3">
                        @foreach($products as $product)
                            <x-storefront.ui.product-card :product="$product" />
                        @endforeach
                    </div>

                    @if($products->hasPages())
                        <div class="pt-6 flex justify-center">
                            {{ $products->links() }}
                        </div>
                    @endif
                @else
                    <div class="bg-white rounded-sm border border-gray-200 p-12 text-center space-y-3">
                        <div class="text-4xl">📦</div>
                        <h4 class="font-black text-base text-gray-900">لا توجد منتجات مسجلة في هذا القسم حالياً</h4>
                        <p class="text-xs text-gray-500 max-w-md mx-auto">
                            يمكنك تصفح باقي الأقسام أو التواصل معنا للاستفسار عن المنتجات والأسعار.
                        </p>
                        <div class="pt-2">
                            <a href="{{ route('site.products') }}" class="inline-block px-5 py-2 bg-[#FFD814] hover:bg-[#F7CA00] text-gray-900 font-bold text-xs rounded shadow-xs">
                                تصفح كل منتجات حورس
                            </a>
                        </div>
                    </div>
                @endif

            </main>

        </div>

    </div>
</div>
@endsection
