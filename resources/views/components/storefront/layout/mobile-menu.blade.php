{{-- resources/views/components/storefront/layout/mobile-menu.blade.php --}}
@php
    $storePhone = settings('store_phone', '01002527072');
    $storeWhatsApp = settings('store_whatsapp', '201002527072');
    $siteLogo = asset('assets/site/img/horus-mark.png');
    $siteName = 'مؤسسة حورس للتوريدات والتشطيبات';
    $navCategories = \Illuminate\Support\Facades\Cache::remember('horus_mobile_nav_categories_v3', 3600, function () {
        return \App\Models\Category::select('id', 'name', 'slug')->whereIn('slug', [
            'scib-paints', 'gypsum-board', 'fyutec-cornices', 'wall-panels', 
            'construction-tools', 'finishing-materials', 'doors', 'decor'
        ])->get();
    });
@endphp

<div x-data="{ open: false }"
     @open-mobile-menu.window="open = true"
     @keydown.escape.window="open = false"
     class="relative z-50 lg:hidden"
     dir="rtl">

    <!-- Backdrop Overlay -->
    <div x-show="open"
         x-cloak
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="open = false"
         class="fixed inset-0 bg-black/60 backdrop-blur-xs z-50"></div>

    <!-- Amazon Style Side Drawer -->
    <div x-show="open"
         x-cloak
         x-transition:enter="transition ease-in-out duration-300 transform"
         x-transition:enter-start="translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transition ease-in-out duration-300 transform"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="translate-x-full"
         class="fixed inset-y-0 right-0 max-w-[320px] sm:max-w-xs w-full bg-white shadow-2xl flex flex-col z-50 overflow-hidden">

        <!-- Amazon Header: Hello, Sign In (#073F40) -->
        <div class="p-4 bg-[#073F40] text-white flex items-center justify-between shadow-sm">
            <a href="{{ auth()->check() ? route('account.profile') : route('login') }}" 
               @click="open = false" 
               class="flex items-center gap-3 min-w-0 group">
                <div class="w-10 h-10 rounded-full bg-white/15 text-white flex items-center justify-center font-bold text-sm shrink-0 border border-white/20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <div class="min-w-0">
                    <h3 class="font-black text-sm text-white truncate leading-tight">
                        @auth مرحباً، {{ auth()->user()->name }} @else مرحباً، تسجيل الدخول @endauth
                    </h3>
                    <p class="text-[11px] text-[#C8A635] font-bold mt-0.5">حسابك في مؤسسة حورس</p>
                </div>
            </a>
            <button @click="open = false" type="button" class="w-8 h-8 rounded-lg bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-colors shrink-0 cursor-pointer" aria-label="إغلاق">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Scrollable Navigation Area (Amazon Style Sections) -->
        <div class="flex-1 overflow-y-auto divide-y divide-gray-200 text-xs text-gray-800">

            <!-- Section 1: Core Navigation -->
            <div class="py-3 px-4 space-y-1">
                <div class="font-black text-sm text-gray-900 mb-2">التصفح السريع</div>
                <a href="{{ route('site.home') }}" @click="open = false" class="block py-2 px-2 hover:bg-gray-100 rounded font-semibold text-gray-800">
                    الرئيسية
                </a>
                <a href="{{ route('site.products') }}" @click="open = false" class="block py-2 px-2 hover:bg-gray-100 rounded font-semibold text-gray-800">
                    كل المنتجات والأسعار
                </a>
                <a href="{{ route('categories.index') }}" @click="open = false" class="block py-2 px-2 hover:bg-gray-100 rounded font-semibold text-gray-800">
                    دليل جميع الأقسام
                </a>
                <a href="{{ route('orders.track') }}" @click="open = false" class="block py-2 px-2 hover:bg-gray-100 rounded font-semibold text-gray-800">
                    تتبع حالة الشحنات والطلبات
                </a>
                <a href="{{ Route::has('branches.index') ? route('branches.index') : url('/branches') }}" @click="open = false" class="block py-2 px-2 hover:bg-gray-100 rounded font-semibold text-gray-800">
                    موقعنا (حدائق الأهرام - بوابة حورس)
                </a>
            </div>

            <!-- Section 2: Shop by Department (Amazon Style Categories) -->
            <div class="py-3 px-4 space-y-1">
                <div class="font-black text-sm text-gray-900 mb-2">تسوق حسب القسم</div>
                @foreach($navCategories as $cat)
                    <a href="{{ route('category.product', $cat->id) }}" 
                       @click="open = false" 
                       class="flex items-center justify-between py-2 px-2 hover:bg-gray-100 rounded font-medium text-gray-700">
                        <span>{{ $cat->name }}</span>
                        <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </a>
                @endforeach
            </div>

            <!-- Section 3: Help & Contact -->
            <div class="py-3 px-4 space-y-2">
                <div class="font-black text-sm text-gray-900 mb-2">المساعدة وخدمة العملاء</div>
                <a href="{{ route('contact') }}" @click="open = false" class="block py-1.5 px-2 hover:bg-gray-100 rounded font-medium text-gray-700">
                    تواصل معنا
                </a>
                <a href="{{ Route::has('about') ? route('about') : url('/about') }}" @click="open = false" class="block py-1.5 px-2 hover:bg-gray-100 rounded font-medium text-gray-700">
                    عن مؤسسة حورس
                </a>
            </div>

        </div>

        <!-- Bottom Actions (WhatsApp + Phone) -->
        <div class="p-3 bg-gray-50 border-t border-gray-200 space-y-2">
            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $storeWhatsApp) }}?text={{ urlencode('مرحباً مؤسسة حورس، أود طلب مواد تشطيب عبر واتساب.') }}"
               target="_blank"
               rel="noopener noreferrer"
               class="w-full flex items-center justify-center gap-2 bg-[#25D366] hover:bg-[#20bd5a] text-white font-black py-2.5 px-4 rounded-md text-xs transition-colors shadow-xs">
                <span>طلب أوردر فوري عبر واتساب</span>
            </a>
            <a href="tel:{{ $storePhone }}" class="w-full flex items-center justify-center gap-2 border border-gray-300 bg-white text-gray-900 hover:bg-gray-100 font-bold py-2 px-4 rounded-md text-xs transition-colors" dir="ltr">
                <span class="font-black">{{ $storePhone }}</span>
                <span>📞</span>
            </a>
        </div>

    </div>
</div>
