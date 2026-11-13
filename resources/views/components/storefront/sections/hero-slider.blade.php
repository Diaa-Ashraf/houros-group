{{-- resources/views/components/storefront/sections/hero-slider.blade.php --}}
@props(['banners' => collect()])

<section class="relative overflow-hidden bg-canvas-base py-3 sm:py-5">
    <div class="container mx-auto px-4">
        <div class="swiper hero-swiper rounded-3xl overflow-hidden shadow-xl border border-cream" style="height: 440px; max-height: 480px;">
            <div class="swiper-wrapper h-full">
                @forelse($banners as $banner)
                <div class="swiper-slide relative h-full w-full bg-gradient-to-r from-primary-950 via-primary-900 to-primary-800 flex items-center overflow-hidden" style="height: 100%;">
                    @if($banner->image_url)
                    <img src="{{ $banner->image_url }}" alt="{{ $banner->title }}" class="absolute inset-0 w-full h-full object-cover object-center pointer-events-none scale-100">
                    @endif
                    <div class="absolute inset-0 bg-gradient-to-l from-black/85 via-black/50 to-transparent"></div>
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-black/20"></div>
                    <div class="relative z-10 container mx-auto px-6 sm:px-12 py-8 max-w-2xl text-white space-y-3.5 text-right" dir="rtl">
                        <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-black bg-accent-500 text-charcoal shadow-lg">
                            <span class="w-2 h-2 rounded-full bg-primary-800 animate-pulse"></span>
                            <span>مؤسسة حورس للتوريدات والتشطيبات</span>
                        </span>
                        <h2 class="text-2xl sm:text-4xl md:text-5xl font-black leading-tight tracking-tight drop-shadow-md">
                            {{ $banner->title }}
                        </h2>
                        @if($banner->description)
                        <p class="text-xs sm:text-sm md:text-base text-white/95 leading-relaxed font-medium drop-shadow-sm line-clamp-2">
                            {{ $banner->description }}
                        </p>
                        @endif
                        <div class="pt-2 flex flex-wrap items-center gap-3">
                            <a href="{{ $banner->url ?: route('site.products') }}" class="px-7 py-3 rounded-2xl bg-accent-500 hover:bg-accent-400 text-charcoal font-black text-xs sm:text-sm transition-all shadow-lg hover:scale-105 active:scale-95 flex items-center gap-2 cursor-pointer">
                                <span>تصفح المنتجات</span>
                                <span class="rotate-180">←</span>
                            </a>
                            <a href="{{ route('categories.index') }}" class="px-7 py-3 rounded-2xl bg-white/20 hover:bg-white/30 backdrop-blur-md text-white border border-white/40 font-bold text-xs sm:text-sm transition-all shadow-sm cursor-pointer">
                                تصفح الأقسام
                            </a>
                        </div>
                    </div>
                </div>
                @empty
                <!-- Default Horus Group hero banner -->
                <div class="swiper-slide relative h-full w-full bg-gradient-to-r from-primary-950 via-primary-900 to-primary-800 flex items-center overflow-hidden" style="height: 100%;">
                    <div class="relative z-10 container mx-auto px-6 sm:px-12 py-8 max-w-2xl text-white space-y-3.5 text-right" dir="rtl">
                        <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-black bg-accent-500 text-charcoal shadow-md">
                            <span>توريدات ومواد تشطيب معتمدة 100%</span>
                        </span>
                        <h2 class="text-2xl sm:text-4xl md:text-5xl font-black leading-tight tracking-tight">
                            مؤسسة حورس للتوريدات والتشطيبات
                        </h2>
                        <p class="text-xs sm:text-sm md:text-base text-white/90 leading-relaxed font-medium">
                            دهانات SCIB، جبسون بورد، كرانيش فيوتك، وأدوات ومستلزمات التشطيب والديكور بأسعار الجملة والتوريد المباشر.
                        </p>
                        <div class="pt-3 flex flex-wrap items-center gap-3">
                            <a href="{{ route('site.products') }}" class="px-7 py-3 rounded-2xl bg-accent-500 hover:bg-accent-400 text-charcoal font-black text-xs sm:text-sm transition-all shadow-lg hover:scale-105 active:scale-95 flex items-center gap-2 cursor-pointer">
                                <span>تصفح المنتجات</span>
                                <span class="rotate-180">←</span>
                            </a>
                            <a href="{{ route('categories.index') }}" class="px-7 py-3 rounded-2xl bg-white/20 hover:bg-white/30 backdrop-blur-md text-white border border-white/40 font-bold text-xs sm:text-sm transition-all shadow-sm cursor-pointer">
                                تصفح الأقسام
                            </a>
                        </div>
                    </div>
                </div>
                @endforelse
            </div>

            <!-- Swiper Navigation Arrows -->
            <div class="swiper-button-next !text-white !w-11 !h-11 !rounded-2xl !bg-black/30 backdrop-blur-md hover:!bg-black/50 transition-all after:!text-sm after:!font-bold"></div>
            <div class="swiper-button-prev !text-white !w-11 !h-11 !rounded-2xl !bg-black/30 backdrop-blur-md hover:!bg-black/50 transition-all after:!text-sm after:!font-bold"></div>

            <!-- Swiper Pagination -->
            <div class="swiper-pagination !bottom-4"></div>
        </div>
    </div>
</section>
