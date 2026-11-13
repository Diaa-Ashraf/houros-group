{{-- resources/views/storefront/categories/index.blade.php --}}
@extends('layouts.storefront')

@section('title', 'أقسام مؤسسة حورس للتوريدات والتشطيبات')

@section('content')
<div class="py-8 sm:py-12 bg-canvas-base min-h-screen">
    <div class="container mx-auto px-4 space-y-8">
        
        <!-- Breadcrumb -->
        <nav class="flex items-center gap-2 text-xs text-charcoal-muted">
            <a href="{{ route('site.home') }}" class="hover:text-primary-800 transition-colors">الرئيسية</a>
            <span>/</span>
            <span class="text-charcoal-heading font-bold">أقسام المتجر</span>
        </nav>

        <!-- Luxury Header Section (Deep Teal with Brand Gold Accents) -->
        <div class="relative overflow-hidden rounded-2xl sm:rounded-3xl bg-gradient-to-r from-[#073F40] via-[#0B5759] to-[#073F40] text-white p-6 sm:p-10 shadow-md border border-[#0B5759]">
            <!-- Decorative Ambient Glows -->
            <div class="absolute -left-20 -bottom-20 w-80 h-80 rounded-full bg-[#C8A635]/15 blur-3xl pointer-events-none"></div>
            <div class="absolute -right-20 -top-20 w-80 h-80 rounded-full bg-white/10 blur-2xl pointer-events-none"></div>

            <div class="relative z-10 max-w-2xl space-y-3">
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-black bg-[#C8A635] text-[#073F40] shadow-xs">
                    <span>⭐</span>
                    <span>تشكيلة متكاملة للتوريدات والتشطيبات والديكور</span>
                </span>
                <h1 class="text-2xl sm:text-3xl md:text-4xl font-black text-white tracking-tight drop-shadow-sm">
                    أقسام مؤسسة حورس
                </h1>
                <p class="text-xs sm:text-sm text-gray-100 leading-relaxed max-w-xl">
                    تصفح جميع أقسام دهانات SCIB المعتمدة، ألواح الجبسون بورد والأسقف المعلقة، كرانيش الفيوتك الأصلية، بدائل الخشب والرخام، ومستلزمات البناء ومواد العزل والتأسيس بأسعار الوكيل المباشر.
                </p>
            </div>
        </div>

        <!-- Categories Visual Luxury Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
            @forelse($categories as $category)
                <a href="{{ route('category.product', $category->id) }}" 
                   class="group relative block rounded-2xl sm:rounded-3xl overflow-hidden bg-gray-900 border border-surface-border hover:border-primary-500 shadow-sm hover:shadow-2xl transition-all duration-500 hover:-translate-y-1.5">
                    
                    <!-- Card Media Container -->
                    <div class="relative aspect-[4/3] sm:aspect-[16/11] overflow-hidden bg-gray-950">
                        @if($category->image_url)
                            <img src="{{ $category->image_url }}" 
                                 alt="{{ $category->name }}" 
                                 loading="lazy" 
                                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700 ease-out">
                        @else
                            <div class="w-full h-full bg-[#073F40] flex items-center justify-center text-white text-3xl font-black">
                                {{ mb_substr($category->name, 0, 1) }}
                            </div>
                        @endif

                        <!-- Cinematic Dark Gradient Overlay for optimal contrast -->
                        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-black/10 group-hover:from-black/95 transition-colors"></div>

                        <!-- Top Meta (Item Count Badge & Hover Arrow) -->
                        <div class="absolute top-3 right-3 left-3 flex items-center justify-between pointer-events-none">
                            <span class="inline-block px-2.5 py-1 rounded-full text-[10px] font-black bg-white/20 backdrop-blur-md text-white border border-white/20 shadow-xs">
                                {{ $category->products_count ?? 0 }} صنف
                            </span>
                            <span class="w-7 h-7 rounded-full bg-white/15 backdrop-blur-md text-accent-300 flex items-center justify-center text-xs opacity-0 group-hover:opacity-100 group-hover:scale-110 transition-all">
                                ←
                            </span>
                        </div>

                        <!-- Bottom Content (Category Title & Action Prompt) -->
                        <div class="absolute bottom-0 inset-x-0 p-3.5 sm:p-5 text-right space-y-1">
                            <h3 class="font-black text-sm sm:text-base text-white group-hover:text-accent-300 transition-colors line-clamp-1 leading-snug">
                                {{ $category->name }}
                            </h3>
                            <div class="flex items-center gap-1.5 text-[11px] text-gray-300 font-bold group-hover:text-white transition-colors">
                                <span>تصفح المنتجات والأسعار</span>
                                <span class="text-accent-400 text-xs">←</span>
                            </div>
                        </div>
                    </div>
                </a>
            @empty
                <div class="col-span-full py-20 text-center bg-white rounded-3xl border border-cream shadow-sm p-8 space-y-3">
                    <span class="text-5xl block">🎨</span>
                    <h3 class="font-bold text-lg text-charcoal-heading">لا توجد أقسام مسجلة حالياً</h3>
                    <p class="text-xs text-charcoal-muted">يمكنك زيارة المتجر الرئيسي لرؤية جميع المنتجات المتاحة.</p>
                    <div class="pt-2">
                        <a href="{{ route('site.products') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary-800 hover:bg-primary-900 text-white font-bold text-xs transition-colors">
                            <span>تصفح جميع المنتجات</span>
                            <span>←</span>
                        </a>
                    </div>
                </div>
            @endforelse
        </div>

        @if($categories->hasPages())
        <div class="pt-6 flex justify-center">
            {{ $categories->links() }}
        </div>
        @endif

    </div>
</div>
@endsection
