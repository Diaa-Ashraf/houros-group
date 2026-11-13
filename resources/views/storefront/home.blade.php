{{-- Horus Group storefront landing page --}}
@extends('layouts.storefront')

@section('title', 'مؤسسة حورس للتوريدات والتشطيبات | المركز الرئيسي حدائق الأهرام')
@section('meta_description', 'كل احتياجات التشطيب والديكور في مكان واحد. وكيل معتمد لدهانات SCIB، جبسون بورد، كرانيش فيوتك، بديل الخشب، أدوات ومستلزمات التشطيب. حدائق الأهرام، بوابة حورس.')

@section('content')
@php
    $horusPhone = settings('store_phone', '01002527072');
    $horusWhatsApp = preg_replace('/[^0-9]/', '', settings('store_whatsapp', '201002527072'));
@endphp

<div class="bg-[#EAEDED] font-sans text-right" dir="rtl">

    <!-- 1. Top Horizontal Sub-Category & Deals Ribbon (Amazon Egypt Style) -->
    <div class="bg-white border-b border-gray-200/90 py-2 px-2 sm:px-4 shadow-2xs">
        <div class="max-w-[1500px] mx-auto flex items-center justify-between gap-2">
            
            <!-- Scrollable Pills Strip -->
            <div class="flex items-center gap-1.5 sm:gap-2 overflow-x-auto no-scrollbar scroll-smooth py-0.5 text-xs font-semibold text-gray-800" id="amazonPillsTrack">
                <a href="{{ route('site.products', ['filter' => 'deals']) }}" 
                   class="shrink-0 px-3 py-1.5 rounded-full bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 font-black flex items-center gap-1.5 transition-colors">
                    <span class="w-2 h-2 rounded-full bg-red-600 animate-pulse"></span>
                    <span>صفقات حورس اليوم</span>
                </a>

                <a href="{{ route('category.product', 33) }}" 
                   class="shrink-0 px-3 py-1.5 rounded-full bg-gray-50 hover:bg-gray-100 text-gray-800 border border-gray-200/80 hover:border-[#073F40] transition-colors">
                    دهانات SCIB المعتمدة
                </a>

                <a href="{{ route('category.product', 34) }}" 
                   class="shrink-0 px-3 py-1.5 rounded-full bg-gray-50 hover:bg-gray-100 text-gray-800 border border-gray-200/80 hover:border-[#073F40] transition-colors">
                    جبسون بورد وأسقف معلقة
                </a>

                <a href="{{ route('category.product', 35) }}" 
                   class="shrink-0 px-3 py-1.5 rounded-full bg-gray-50 hover:bg-gray-100 text-gray-800 border border-gray-200/80 hover:border-[#073F40] transition-colors">
                    كرانيش فيوتك الأصلية
                </a>

                <a href="{{ route('category.product', 36) }}" 
                   class="shrink-0 px-3 py-1.5 rounded-full bg-gray-50 hover:bg-gray-100 text-gray-800 border border-gray-200/80 hover:border-[#073F40] transition-colors">
                    بديل الخشب والرخام
                </a>

                <a href="{{ route('category.product', 37) }}" 
                   class="shrink-0 px-3 py-1.5 rounded-full bg-gray-50 hover:bg-gray-100 text-gray-800 border border-gray-200/80 hover:border-[#073F40] transition-colors">
                    أدوات ومستلزمات البناء
                </a>

                <a href="{{ route('category.product', 38) }}" 
                   class="shrink-0 px-3 py-1.5 rounded-full bg-gray-50 hover:bg-gray-100 text-gray-800 border border-gray-200/80 hover:border-[#073F40] transition-colors">
                    مواد التأسيس والتشطيب
                </a>

                <a href="{{ route('categories.index') }}" 
                   class="shrink-0 px-3 py-1.5 rounded-full bg-gray-50 hover:bg-gray-100 text-gray-800 border border-gray-200/80 hover:border-[#073F40] transition-colors">
                    دليل جميع الأقسام
                </a>

                <a href="https://wa.me/{{ $horusWhatsApp }}?text={{ urlencode('مرحباً مؤسسة حورس، أود طلب تسعير وتوريد مباشر للمشروع.') }}" 
                   target="_blank" 
                   rel="noopener noreferrer"
                   class="shrink-0 px-3 py-1.5 rounded-full bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200 font-bold flex items-center gap-1 transition-colors">
                    <span>توريد مشروعات المقاولين</span>
                    <span class="text-xs">↗</span>
                </a>
            </div>

            <!-- Quick Shop Link -->
            <a href="{{ route('site.products') }}" 
               class="shrink-0 text-xs font-bold text-[#073F40] hover:text-[#C8A635] hover:underline hidden md:flex items-center gap-1 px-2">
                <span>تصفح الكل</span>
                <span>←</span>
            </a>

        </div>
    </div>

    <!-- 2. Amazon Full-Width Dynamic Hero Carousel -->
    <div class="relative bg-neutral-900 overflow-hidden select-none"
         x-data="{
             active: 0,
             total: 4,
             timer: null,
             next() { this.active = (this.active + 1) % this.total; },
             prev() { this.active = (this.active - 1 + this.total) % this.total; },
             start() { this.timer = setInterval(() => this.next(), 6000); },
             stop() { clearInterval(this.timer); }
         }"
         x-init="start()"
         @mouseenter="stop()"
         @mouseleave="start()">

        <!-- Slides Container -->
        <div class="relative w-full h-[360px] sm:h-[440px] md:h-[500px] lg:h-[540px]">

            <!-- Slide 1: دهانات SCIB العالمية وتلوين الكمبيوتر -->
            <div x-show="active === 0"
                 x-transition:enter="transition-opacity ease-out duration-700"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition-opacity ease-in duration-500"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="absolute inset-0 w-full h-full">
                
                <!-- Background Image -->
                <img src="{{ asset('assets/site/img/hero/horus-luxury-interior.jpg') }}" 
                     alt="دهانات SCIB مؤسسة حورس" 
                     class="w-full h-full object-cover object-center">

                <!-- Ambient Amazon Teal Overlay -->
                <div class="absolute inset-0 bg-gradient-to-r from-transparent via-[#073F40]/60 to-[#073F40]/95"></div>
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-black/20"></div>

                <!-- Slide Content -->
                <div class="absolute inset-0 max-w-[1500px] mx-auto px-4 sm:px-8 md:px-12 flex items-center">
                    <div class="max-w-2xl text-white space-y-3 sm:space-y-4 pt-2 sm:pt-4">
                        
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded bg-[#C8A635] text-[#073F40] font-black text-xs shadow-sm">
                            <span>⭐</span>
                            <span>الوكيل والمركز المعتمد لدهانات SCIB</span>
                        </div>

                        <h1 class="text-2xl sm:text-4xl md:text-5xl font-black leading-tight drop-shadow-md">
                            أحدث درجات وتشكيلات <br>
                            <span class="text-[#E2BF52]">دهانات SCIB وتلوين الكمبيوتر</span>
                        </h1>

                        <p class="text-xs sm:text-base text-gray-200 font-normal leading-relaxed max-w-xl drop-shadow-sm">
                            خلط وتلوين بالكمبيوتر فوري لأكثر من 10,000 درجة لون معتمدة • خامات عالية المقاومة وقابلة للغسيل • أسعار الوكيل المباشر وتوصيل فوري للموقع بحدائق الأهرام ومحيطها.
                        </p>

                        <div class="pt-2 flex flex-wrap items-center gap-3">
                            <a href="{{ route('category.product', 33) }}" 
                               class="px-6 py-3 bg-[#FFD814] hover:bg-[#F7CA00] active:scale-95 text-gray-900 font-black text-xs sm:text-sm rounded-sm shadow-md transition-all flex items-center gap-2">
                                <span>تسوق دهانات SCIB الآن</span>
                                <span>←</span>
                            </a>
                            <a href="https://wa.me/{{ $horusWhatsApp }}?text={{ urlencode('مرحباً، أود استشارة واختيار ألوان دهانات SCIB بالكمبيوتر.') }}" 
                               target="_blank" 
                               class="px-5 py-3 bg-white/20 hover:bg-white/30 backdrop-blur-sm text-white font-bold text-xs sm:text-sm rounded-sm border border-white/30 transition-all flex items-center gap-2">
                                <span>استفسار تلوين بالكمبيوتر</span>
                                <span>↗</span>
                            </a>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Slide 2: أنظمة الجبسون بورد والأسقف المعلقة -->
            <div x-show="active === 1"
                 x-cloak
                 x-transition:enter="transition-opacity ease-out duration-700"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition-opacity ease-in duration-500"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="absolute inset-0 w-full h-full">
                
                <img src="{{ asset('assets/site/img/hero/horus-gypsum-ceiling.jpg') }}" 
                     alt="جبسون بورد وأسقف معلقة" 
                     class="w-full h-full object-cover object-center">

                <div class="absolute inset-0 bg-gradient-to-r from-transparent via-[#073F40]/60 to-[#073F40]/95"></div>
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-black/20"></div>

                <div class="absolute inset-0 max-w-[1500px] mx-auto px-4 sm:px-8 md:px-12 flex items-center">
                    <div class="max-w-2xl text-white space-y-3 sm:space-y-4 pt-2 sm:pt-4">
                        
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded bg-[#0B5759] text-white border border-[#82d9c7]/40 font-bold text-xs shadow-sm">
                            <span>🏗️</span>
                            <span>أنظمة الجبسون بورد والأسقف المعلقة</span>
                        </div>

                        <h2 class="text-2xl sm:text-4xl md:text-5xl font-black leading-tight drop-shadow-md">
                            خامات كناوف ومستلزمات <br>
                            <span class="text-[#E2BF52]">الأسقف والديكورات الحديثة</span>
                        </h2>

                        <p class="text-xs sm:text-base text-gray-200 font-normal leading-relaxed max-w-xl drop-shadow-sm">
                            ألواح جبسون بورد مقاومة للرطوبة والحرائق • قطاعات الصاج والشاسيهات المعتمدة • معجون فواصل وشبك وفيشر وكافة مستلزمات الفنيين والمهندسين.
                        </p>

                        <div class="pt-2 flex flex-wrap items-center gap-3">
                            <a href="{{ route('category.product', 34) }}" 
                               class="px-6 py-3 bg-[#FFD814] hover:bg-[#F7CA00] active:scale-95 text-gray-900 font-black text-xs sm:text-sm rounded-sm shadow-md transition-all flex items-center gap-2">
                                <span>استكشف الجبسون بورد والملحقات</span>
                                <span>←</span>
                            </a>
                            <a href="{{ route('site.products') }}" 
                               class="px-5 py-3 bg-white/20 hover:bg-white/30 backdrop-blur-sm text-white font-bold text-xs sm:text-sm rounded-sm border border-white/30 transition-all">
                                <span>كل مستلزمات البناء</span>
                            </a>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Slide 3: كرانيش وبانوهات فيوتك الأصلية -->
            <div x-show="active === 2"
                 x-cloak
                 x-transition:enter="transition-opacity ease-out duration-700"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition-opacity ease-in duration-500"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="absolute inset-0 w-full h-full">
                
                <img src="{{ asset('assets/site/img/hero/horus-cornices-classic.jpg') }}" 
                     alt="كرانيش وبانوهات فيوتك" 
                     class="w-full h-full object-cover object-center">

                <div class="absolute inset-0 bg-gradient-to-r from-transparent via-[#073F40]/60 to-[#073F40]/95"></div>
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-black/20"></div>

                <div class="absolute inset-0 max-w-[1500px] mx-auto px-4 sm:px-8 md:px-12 flex items-center">
                    <div class="max-w-2xl text-white space-y-3 sm:space-y-4 pt-2 sm:pt-4">
                        
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded bg-[#C8A635] text-[#073F40] font-black text-xs shadow-sm">
                            <span>⚜️</span>
                            <span>الموزع المعتمد لكرانيش وبانوهات فيوتك الأصلية</span>
                        </div>

                        <h2 class="text-2xl sm:text-4xl md:text-5xl font-black leading-tight drop-shadow-md">
                            فخامة اللمسات المعمارية <br>
                            <span class="text-[#E2BF52]">كرانيش، صرر، وبانوهات جدارية</span>
                        </h2>

                        <p class="text-xs sm:text-base text-gray-200 font-normal leading-relaxed max-w-xl drop-shadow-sm">
                            خامات بولي يوريثان عالية الكثافة من فيوتك • مقاومة تامة للماء والرطوبة والحرارة • سهلة وسريعة التركيب بأدق التفاصيل والرسومات الكلاسيكية والمودرن.
                        </p>

                        <div class="pt-2 flex flex-wrap items-center gap-3">
                            <a href="{{ route('category.product', 35) }}" 
                               class="px-6 py-3 bg-[#FFD814] hover:bg-[#F7CA00] active:scale-95 text-gray-900 font-black text-xs sm:text-sm rounded-sm shadow-md transition-all flex items-center gap-2">
                                <span>تصفح تشكيلة كرانيش فيوتك</span>
                                <span>←</span>
                            </a>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Slide 4: توريد مشروعات المقاولين والشركات -->
            <div x-show="active === 3"
                 x-cloak
                 x-transition:enter="transition-opacity ease-out duration-700"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition-opacity ease-in duration-500"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="absolute inset-0 w-full h-full">
                
                <img src="{{ asset('assets/site/img/hero/horus-paints-decor.jpg') }}" 
                     alt="توريد مشروعات المقاولين مؤسسة حورس" 
                     class="w-full h-full object-cover object-center">

                <div class="absolute inset-0 bg-gradient-to-r from-transparent via-[#073F40]/60 to-[#073F40]/95"></div>
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-black/20"></div>

                <div class="absolute inset-0 max-w-[1500px] mx-auto px-4 sm:px-8 md:px-12 flex items-center">
                    <div class="max-w-2xl text-white space-y-3 sm:space-y-4 pt-2 sm:pt-4">
                        
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded bg-emerald-600 text-white font-black text-xs shadow-sm">
                            <span>💼</span>
                            <span>قسم توريدات الشركات والمقاولين والمهندسين</span>
                        </div>

                        <h2 class="text-2xl sm:text-4xl md:text-5xl font-black leading-tight drop-shadow-md">
                            أسعار جملة وتسهيلات توريد <br>
                            <span class="text-[#E2BF52]">مباشرة لموقع مشروعك الإنشائي</span>
                        </h2>

                        <p class="text-xs sm:text-base text-gray-200 font-normal leading-relaxed max-w-xl drop-shadow-sm">
                            توفير كامل لكافة كميات مواد التشطيب، المعاجين، البويات، وألواح الجبس، مع سيارات نقل مجهزة وسرعة تسليم لجميع مناطق حدائق الأهرام وأكتوبر والشيخ زايد.
                        </p>

                        <div class="pt-2 flex flex-wrap items-center gap-3">
                            <a href="https://wa.me/{{ $horusWhatsApp }}?text={{ urlencode('مرحباً مؤسسة حورس، أود طلب عرض أسعار جملة لمشروعي.') }}" 
                               target="_blank" 
                               class="px-6 py-3 bg-[#25D366] hover:bg-[#20bd5a] active:scale-95 text-white font-black text-xs sm:text-sm rounded-sm shadow-md transition-all flex items-center gap-2">
                                <span>طلب عرض أسعار عبر واتساب</span>
                                <span>↗</span>
                            </a>
                            <a href="tel:{{ $horusPhone }}" 
                               class="px-5 py-3 bg-white/20 hover:bg-white/30 backdrop-blur-sm text-white font-bold text-xs sm:text-sm rounded-sm border border-white/30 transition-all flex items-center gap-2" dir="ltr">
                                <span>{{ $horusPhone }}</span>
                                <span>📞</span>
                            </a>
                        </div>

                    </div>
                </div>
            </div>

        </div>

        <!-- Amazon Left/Right Floating Navigation Chevrons -->
        <button type="button" 
                @click="prev()" 
                class="absolute right-2 sm:right-4 top-1/2 -translate-y-1/2 z-20 w-10 sm:w-11 h-20 sm:h-28 bg-white/80 hover:bg-white text-gray-900 border border-gray-300 rounded-xs shadow-md flex items-center justify-center transition-all opacity-80 hover:opacity-100 cursor-pointer focus:outline-none"
                aria-label="السابق">
            <svg class="w-6 h-6 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
            </svg>
        </button>

        <button type="button" 
                @click="next()" 
                class="absolute left-2 sm:left-4 top-1/2 -translate-y-1/2 z-20 w-10 sm:w-11 h-20 sm:h-28 bg-white/80 hover:bg-white text-gray-900 border border-gray-300 rounded-xs shadow-md flex items-center justify-center transition-all opacity-80 hover:opacity-100 cursor-pointer focus:outline-none"
                aria-label="التالي">
            <svg class="w-6 h-6 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
        </button>

        <!-- Slide Indicators -->
        <div class="absolute bottom-40 sm:bottom-48 inset-x-0 z-20 flex items-center justify-center gap-2 pointer-events-none">
            <template x-for="i in total" :key="i">
                <button type="button" 
                        @click="active = i - 1" 
                        class="pointer-events-auto h-2 rounded-full transition-all"
                        :class="active === (i - 1) ? 'w-8 bg-[#FFD814]' : 'w-2 bg-white/60 hover:bg-white'">
                </button>
            </template>
        </div>

        <!-- The Signature Amazon Bottom Fade Gradient (Smoothly dissolves into #EAEDED) -->
        <div class="absolute bottom-0 inset-x-0 h-44 sm:h-56 bg-gradient-to-t from-[#EAEDED] via-[#EAEDED]/80 to-transparent pointer-events-none z-10"></div>

    </div>

    <!-- 3. The Iconic Amazon Multi-Card Row (Dynamic from Dashboard) -->
    <div class="relative z-20 max-w-[1500px] mx-auto px-2 sm:px-4 -mt-28 sm:-mt-36 md:-mt-44 mb-6">
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-2.5 sm:gap-3.5">
            @if(isset($heroCards) && $heroCards->isNotEmpty())
                @foreach($heroCards as $card)
                    <div class="bg-white rounded-xs border border-gray-200/90 p-3 sm:p-4 shadow-sm hover:shadow-md transition-all duration-200 flex flex-col justify-between group">
                        <div class="space-y-2">
                            <div class="flex items-center justify-between gap-1">
                                <h3 class="font-black text-xs sm:text-sm text-gray-900 group-hover:text-[#073F40] line-clamp-1">
                                    {{ $card->title }}
                                </h3>
                                @if($card->badge_text)
                                    <span class="px-1.5 py-0.5 rounded-xs text-[9px] font-bold bg-[#C8A635]/20 text-[#073F40]">
                                        {{ $card->badge_text }}
                                    </span>
                                @endif
                            </div>

                            <div class="aspect-square w-full rounded-xs overflow-hidden bg-gray-50 flex items-center justify-center p-1">
                                <img src="{{ $card->image_url }}" 
                                     alt="{{ $card->title }}" 
                                     loading="lazy" 
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            </div>

                            @if($card->subtitle)
                                <p class="text-[11px] text-gray-500 line-clamp-1">{{ $card->subtitle }}</p>
                            @endif
                        </div>

                        <div class="pt-2.5 border-t border-gray-100 mt-2">
                            <a href="{{ $card->url ?: route('site.products') }}" 
                               @if(str_starts_with($card->url ?? '', 'http') && !str_contains($card->url ?? '', request()->getHost())) target="_blank" rel="noopener noreferrer" @endif
                               class="text-xs font-semibold {{ str_contains($card->url ?? '', 'wa.me') ? 'text-emerald-700 hover:text-emerald-900' : 'text-[#007185] hover:text-[#C7511F]' }} hover:underline block">
                                {{ $card->link_text ?: 'تسوق الآن ←' }}
                            </a>
                        </div>
                    </div>
                @endforeach
            @else
                <!-- Amazon Card 1: دهانات SCIB -->
                <div class="bg-white rounded-xs border border-gray-200/90 p-3 sm:p-4 shadow-sm hover:shadow-md transition-all duration-200 flex flex-col justify-between group">
                    <div class="space-y-2">
                        <h3 class="font-black text-xs sm:text-sm text-gray-900 group-hover:text-[#073F40] line-clamp-1">دهانات SCIB العالمية</h3>
                        <div class="aspect-square w-full rounded-xs overflow-hidden bg-gray-50 flex items-center justify-center p-1">
                            <img src="{{ asset('assets/site/img/categories/scib-paints.jpg') }}" alt="دهانات SCIB" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        </div>
                        <p class="text-[11px] text-gray-500 line-clamp-1">خلط كمبيوتر فوري لأحدث درجات 2026</p>
                    </div>
                    <div class="pt-2.5 border-t border-gray-100 mt-2">
                        <a href="{{ route('category.product', 33) }}" class="text-xs font-semibold text-[#007185] hover:text-[#C7511F] hover:underline block">
                            تسوق دهانات SCIB ←
                        </a>
                    </div>
                </div>

                <!-- Amazon Card 2: جبسون بورد وأسقف معلقة -->
                <div class="bg-white rounded-xs border border-gray-200/90 p-3 sm:p-4 shadow-sm hover:shadow-md transition-all duration-200 flex flex-col justify-between group">
                    <div class="space-y-2">
                        <h3 class="font-black text-xs sm:text-sm text-gray-900 group-hover:text-[#073F40] line-clamp-1">جبسون بورد وأسقف</h3>
                        <div class="aspect-square w-full rounded-xs overflow-hidden bg-gray-50 flex items-center justify-center p-1">
                            <img src="{{ asset('assets/site/img/categories/gypsum-board.jpg') }}" alt="جبسون بورد" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        </div>
                        <p class="text-[11px] text-gray-500 line-clamp-1">خامات كناوف ومستلزمات التثبيت</p>
                    </div>
                    <div class="pt-2.5 border-t border-gray-100 mt-2">
                        <a href="{{ route('category.product', 34) }}" class="text-xs font-semibold text-[#007185] hover:text-[#C7511F] hover:underline block">
                            تسوق الجبسون بورد ←
                        </a>
                    </div>
                </div>

                <!-- Amazon Card 3: كرانيش فيوتك الأصلية -->
                <div class="bg-white rounded-xs border border-gray-200/90 p-3 sm:p-4 shadow-sm hover:shadow-md transition-all duration-200 flex flex-col justify-between group">
                    <div class="space-y-2">
                        <h3 class="font-black text-xs sm:text-sm text-gray-900 group-hover:text-[#073F40] line-clamp-1">كرانيش فيوتك الأصلية</h3>
                        <div class="aspect-square w-full rounded-xs overflow-hidden bg-gray-50 flex items-center justify-center p-1">
                            <img src="{{ asset('assets/site/img/categories/fyutec-cornices.jpg') }}" alt="كرانيش فيوتك" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        </div>
                        <p class="text-[11px] text-gray-500 line-clamp-1">خامات أصلية مقاومة للماء والحرارة</p>
                    </div>
                    <div class="pt-2.5 border-t border-gray-100 mt-2">
                        <a href="{{ route('category.product', 35) }}" class="text-xs font-semibold text-[#007185] hover:text-[#C7511F] hover:underline block">
                            استكشف الفيوتك ←
                        </a>
                    </div>
                </div>

                <!-- Amazon Card 4: بديل الخشب وورق الحائط -->
                <div class="bg-white rounded-xs border border-gray-200/90 p-3 sm:p-4 shadow-sm hover:shadow-md transition-all duration-200 flex flex-col justify-between group">
                    <div class="space-y-2">
                        <h3 class="font-black text-xs sm:text-sm text-gray-900 group-hover:text-[#073F40] line-clamp-1">بديل الخشب والرخام</h3>
                        <div class="aspect-square w-full rounded-xs overflow-hidden bg-gray-50 flex items-center justify-center p-1">
                            <img src="{{ asset('assets/site/img/categories/wall-panels.jpg') }}" alt="بديل الخشب" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        </div>
                        <p class="text-[11px] text-gray-500 line-clamp-1">ديكورات جدارية وحلول عصرية</p>
                    </div>
                    <div class="pt-2.5 border-t border-gray-100 mt-2">
                        <a href="{{ route('category.product', 36) }}" class="text-xs font-semibold text-[#007185] hover:text-[#C7511F] hover:underline block">
                            تصفح البدائل الديكورية ←
                        </a>
                    </div>
                </div>

                <!-- Amazon Card 5: توريد مشروعات المقاولات -->
                <div class="col-span-2 md:col-span-1 bg-white rounded-xs border border-gray-200/90 p-3 sm:p-4 shadow-sm hover:shadow-md transition-all duration-200 flex flex-col justify-between group">
                    <div class="space-y-2">
                        <h3 class="font-black text-xs sm:text-sm text-gray-900 group-hover:text-[#073F40] line-clamp-1">توريد مشروعات المقاولات</h3>
                        <div class="aspect-square w-full rounded-xs overflow-hidden bg-gray-50 flex items-center justify-center p-1">
                            <img src="{{ asset('assets/site/img/categories/construction-tools.jpg') }}" alt="توريد المقاولات" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        </div>
                        <p class="text-[11px] text-gray-500 line-clamp-1">أسعار خاصة للمهندسين والمقاولين</p>
                    </div>
                    <div class="pt-2.5 border-t border-gray-100 mt-2">
                        <a href="https://wa.me/{{ $horusWhatsApp }}?text={{ urlencode('مرحباً، أود الاستفسار عن عروض أسعار الجملة وتوريد المشروعات.') }}" target="_blank" class="text-xs font-bold text-emerald-700 hover:text-emerald-900 hover:underline block">
                            طلب تسعير جملة عبر واتساب ↗
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- 4. Amazon Deals of the Day (صفقات اليوم والعروض التوفيرية) -->
    @if(isset($deals) && $deals->isNotEmpty())
    <section class="max-w-[1500px] mx-auto px-2 sm:px-4 mb-6">
        <div class="bg-white rounded-xs border border-gray-200/90 p-4 sm:p-5 shadow-2xs space-y-4">
            
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-3 border-b border-gray-200">
                <div class="flex items-center gap-3">
                    <span class="px-2.5 py-1 rounded-xs bg-[#CC0C39] text-white text-xs font-black">
                        صفقات حورس اليوم
                    </span>
                    <h2 class="text-base sm:text-lg font-black text-gray-900">
                        عروض حصرية وخصومات حتى 25% على مواد التشطيب
                    </h2>
                </div>
                <a href="{{ route('site.products', ['filter' => 'deals']) }}" class="text-xs font-semibold text-[#007185] hover:text-[#C7511F] hover:underline">
                    شاهد جميع صفقات اليوم ←
                </a>
            </div>

            <!-- Deals Grid (Amazon Egypt Style with Yellow Plus Buttons) -->
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3">
                @foreach($deals->take(5) as $deal)
                    @if($deal->product)
                        <x-storefront.ui.product-card :product="$deal->product" />
                    @endif
                @endforeach
            </div>

        </div>
    </section>
    @endif



    <!-- 6. Categories Visual Grid Section -->
    @if(isset($categories) && $categories->isNotEmpty())
    <section class="max-w-[1500px] mx-auto px-2 sm:px-4 mb-8">
        <div class="bg-white rounded-xs border border-gray-200/80 p-4 sm:p-6 shadow-2xs space-y-4">
            
            <!-- Section Header (Amazon Section Style) -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-3 border-b border-gray-200">
                <div>
                    <h2 class="text-lg sm:text-xl font-black text-gray-900">
                        تسوق حسب القسم • أقسام مؤسسة حورس المعتمدة
                    </h2>
                    <p class="text-xs text-gray-500">دهانات SCIB، جبسون بورد، كرانيش فيوتك، بديل خشب، ومستلزمات البناء والديكور</p>
                </div>
                <a href="{{ route('categories.index') }}" class="text-xs font-semibold text-[#007185] hover:text-[#C7511F] hover:underline shrink-0">
                    استعراض دليل جميع الأقسام بالصور والأسعار ←
                </a>
            </div>

            <!-- Categories Visual Cards -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4">
                @foreach($categories as $category)
                    <a href="{{ route('category.product', $category->id) }}" 
                       class="group relative block rounded-xs overflow-hidden bg-white border border-gray-200/90 hover:border-[#073F40] shadow-2xs hover:shadow-md transition-all duration-300">
                        
                        @php
                            $catImage = $category->image_url;
                            if (empty($catImage)) {
                                $defaultPath = method_exists($category, 'getDefaultImageForCategory') ? $category->getDefaultImageForCategory() : null;
                                $catImage = asset($defaultPath ?: 'assets/site/img/categories/finishing-materials.jpg');
                            }
                        @endphp
                        <!-- Category Image Container -->
                        <div class="relative aspect-[4/3] overflow-hidden bg-gray-100">
                            <img src="{{ $catImage }}" 
                                 alt="{{ $category->name }}" 
                                 loading="lazy" 
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">

                            <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/30 to-transparent"></div>

                            <!-- Item Count Badge -->
                            <div class="absolute top-2.5 right-2.5">
                                <span class="px-2 py-0.5 rounded text-[10px] font-black bg-black/60 backdrop-blur-xs text-white border border-white/20">
                                    {{ $category->products_count ?? 0 }} صنف
                                </span>
                            </div>

                            <!-- Title on Image -->
                            <div class="absolute bottom-2.5 inset-x-2.5 text-right space-y-0.5">
                                <h3 class="font-bold text-sm text-white group-hover:text-[#E2BF52] transition-colors truncate">
                                    {{ $category->name }}
                                </h3>
                                <div class="text-[10px] text-gray-300 group-hover:text-white font-medium flex items-center gap-1">
                                    <span>تصفح المنتجات والأسعار</span>
                                    <span>←</span>
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

        </div>
    </section>
    @endif

    <!-- 7. Featured Products Carousel Section -->
    @if(isset($featuredProducts) && $featuredProducts->isNotEmpty())
    <div class="max-w-[1500px] mx-auto px-2 sm:px-4 mb-8">
        <div class="bg-white rounded-xs border border-gray-200/80 p-4 sm:p-6 shadow-2xs space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-gray-200">
                <div>
                    <h2 class="text-lg sm:text-xl font-black text-gray-900">منتجات مختارة من حورس</h2>
                    <p class="text-xs text-gray-500">خامات وأدوات موثوقة تساعدك تنجز شغلك بأفضل جودة وأسعار تنافسية</p>
                </div>
                <a href="{{ route('site.products') }}" class="text-xs font-semibold text-[#007185] hover:text-[#C7511F] hover:underline">
                    عرض جميع المنتجات ←
                </a>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-5 gap-3">
                @foreach($featuredProducts->take(5) as $product)
                    <x-storefront.ui.product-card :product="$product" />
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- 8. SCIB Paints Authorized Agent Band -->
    <section class="max-w-[1500px] mx-auto px-2 sm:px-4 mb-8">
        <div class="bg-gradient-to-r from-[#073F40] via-[#094E50] to-[#0B5759] text-white rounded-xs border border-[#062F31] p-6 sm:p-10 shadow-sm flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="space-y-2 max-w-2xl text-right">
                <span class="inline-block px-3 py-1 rounded bg-white/15 text-[#E2BF52] text-xs font-black">
                    الوكيل المعتمد لدهانات SCIB العالمية
                </span>
                <h2 class="text-2xl sm:text-3xl font-black leading-tight">
                    اختار ألوانك بثقة مع دهانات SCIB عند حورس
                </h2>
                <p class="text-xs sm:text-sm text-gray-200 leading-relaxed">
                    متاح جميع منتجات سكيب للدهانات الداخلية والخارجية والواجهات، مع ماكينة خلط الألوان بالكمبيوتر لاختيار أي درجة لون بدقة 100%.
                </p>
                <div class="pt-2">
                    <a href="https://wa.me/{{ $horusWhatsApp }}?text={{ urlencode('مرحباً، أود الاستفسار عن درجات وأسعار دهانات SCIB.') }}" 
                       target="_blank" 
                       class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#FFD814] hover:bg-[#F7CA00] text-gray-900 font-black text-xs rounded-xs shadow-md">
                        <span>اسأل عن درجات ألوان سكيب بالكمبيوتر</span>
                        <span>←</span>
                    </a>
                </div>
            </div>

            <div class="w-32 h-32 sm:w-40 sm:h-40 rounded-full bg-white/10 border-2 border-white/20 flex flex-col items-center justify-center shrink-0 text-center p-4">
                <span class="text-2xl sm:text-3xl font-black text-[#E2BF52] tracking-tighter">SCIB</span>
                <span class="text-[10px] text-gray-200 tracking-widest font-bold">PAINTS</span>
                <span class="w-8 h-1 bg-[#25D366] rounded mt-1"></span>
            </div>
        </div>
    </section>

    <!-- 9. Support & Direct Contact Band -->
    <section class="max-w-[1500px] mx-auto px-2 sm:px-4 mb-8">
        <div class="bg-white rounded-xs border border-gray-200/80 p-5 sm:p-8 shadow-2xs flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="text-right">
                <span class="text-xs font-bold text-[#073F40]">فريق خدمة العملاء جاهز لمساعدتك</span>
                <h3 class="text-lg sm:text-xl font-black text-gray-900 mt-1">
                    مشروعك يستاهل بداية مظبوطة وخامات تثق فيها
                </h3>
                <p class="text-xs text-gray-500 mt-0.5">كلمنا واسأل عن المنتجات والأسعار أو زورنا في فرع المعرض بحدائق الأهرام.</p>
            </div>
            <div class="flex items-center gap-3 shrink-0">
                <a href="tel:{{ $horusPhone }}" class="px-5 py-2.5 rounded-xs bg-gray-100 hover:bg-gray-200 text-gray-900 text-xs font-black border border-gray-300 flex items-center gap-2" dir="ltr">
                    <span>{{ $horusPhone }}</span>
                    <span>📞</span>
                </a>
                <a href="https://wa.me/{{ $horusWhatsApp }}?text={{ urlencode('مرحباً حورس، أود الاستفسار عن المنتجات والأسعار.') }}" target="_blank" class="px-5 py-2.5 rounded-xs bg-[#25D366] hover:bg-[#20bd5a] text-white text-xs font-black shadow-xs flex items-center gap-2">
                    <span>طلب فوري عبر واتساب</span>
                    <span>↗</span>
                </a>
            </div>
        </div>
    </section>

</div>
@endsection