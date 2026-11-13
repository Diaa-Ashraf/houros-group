{{-- resources/views/components/storefront/layout/header.blade.php --}}
@php
    $cartCount = 0;
    try {
        $cartCount = app(\App\Services\CartService::class)->getCount();
    } catch (\Throwable $e) {
        $cartCount = session('cart') ? count(session('cart')) : 0;
    }

    $storePhone = settings('store_phone', '01002527072');
    $storeWhatsApp = preg_replace('/[^0-9]/', '', settings('store_whatsapp', '201002527072'));
    $categoriesList = \Illuminate\Support\Facades\Cache::remember('horus_amazon_nav_categories_v1', 3600, function () {
        return \App\Models\Category::select('id', 'name', 'slug')->whereIn('slug', [
            'scib-paints', 'gypsum-board', 'fyutec-cornices', 'wall-panels', 
            'construction-tools', 'finishing-materials', 'doors', 'decor'
        ])->get();
    });
@endphp

<header class="sticky top-0 z-40 w-full font-sans select-none text-white shadow-md" dir="rtl"
        x-data="{ 
            selectedCategory: 'all',
            searchQuery: '{{ request('q', request('search')) }}',
            searchResults: [],
            searchLoading: false,
            searchOpen: false,
            userDropdown: false,
            locationModal: false,
            fetchResults() {
                if (!this.searchQuery || this.searchQuery.trim().length < 1) {
                    this.searchResults = [];
                    this.searchOpen = false;
                    return;
                }
                this.searchLoading = true;
                let url = '{{ route('search.autocomplete') }}?q=' + encodeURIComponent(this.searchQuery);
                if (this.selectedCategory !== 'all') {
                    url += '&category_id=' + encodeURIComponent(this.selectedCategory);
                }
                fetch(url)
                    .then(res => res.json())
                    .then(data => {
                        this.searchResults = data.results || [];
                        this.searchOpen = this.searchResults.length > 0;
                        this.searchLoading = false;
                    })
                    .catch(() => {
                        this.searchLoading = false;
                    });
            }
        }"
        @click.outside="searchOpen = false; userDropdown = false">

    <!-- Amazon Tier 1: Main Header Bar (Deep Teal Brand #073F40) -->
    <div class="bg-[#073F40] px-2 sm:px-4 py-2 sm:py-2.5">
        <div class="container mx-auto flex flex-wrap sm:flex-nowrap items-center justify-between gap-2 md:gap-4">

            <!-- 1. Brand Logo (Amazon Style) -->
            <a href="{{ route('site.home') }}" 
               class="flex items-center gap-2 p-1.5 border border-transparent hover:border-white rounded-sm transition-colors group shrink-0">
                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-lg bg-white/10 p-1 flex items-center justify-center border border-white/20 shrink-0">
                    <img src="{{ asset('assets/site/img/horus-mark.png') }}" alt="حورس" class="w-full h-full object-contain">
                </div>
                <div class="flex flex-col text-right leading-none">
                    <div class="flex items-baseline gap-1">
                        <span class="text-base sm:text-xl font-black text-white tracking-tight">حورس</span>
                        <span class="text-xs sm:text-sm font-bold text-[#C8A635]">.eg</span>
                    </div>
                    <span class="text-[9px] text-gray-300 font-semibold tracking-wider">للتشطيبات والديكور</span>
                </div>
            </a>

            <!-- 2. Delivery Location (Amazon Style) -->
            <a href="{{ Route::has('branches.index') ? route('branches.index') : url('/branches') }}" 
               class="hidden md:flex items-center gap-1.5 p-1.5 border border-transparent hover:border-white rounded-sm transition-colors shrink-0 text-right cursor-pointer">
                <svg class="w-5 h-5 text-gray-200 shrink-0 mt-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <div class="flex flex-col leading-tight">
                    <span class="text-[11px] text-gray-300 font-normal">التوصيل إلى</span>
                    <span class="text-xs text-white font-bold whitespace-nowrap">حدائق الأهرام • الجيزة</span>
                </div>
            </a>

            <!-- 3. The Iconic Amazon Omnibar Search -->
            <div class="w-full order-last sm:order-none sm:w-auto sm:flex-1 max-w-3xl sm:mx-2 relative pt-1 sm:pt-0">
                <form action="{{ route('search.index') }}" method="GET" class="w-full flex rounded-md overflow-hidden shadow-sm focus-within:ring-2 focus-within:ring-[#C8A635]">
                    
                    <!-- Category Select Dropdown (Right in RTL) -->
                    <div class="hidden sm:flex relative bg-[#f3f3f3] hover:bg-[#dadada] border-l border-gray-300 transition-colors shrink-0">
                        <select name="category_id" 
                                x-model="selectedCategory"
                                class="h-10 text-xs font-semibold text-gray-800 bg-transparent pr-3 pl-6 outline-none cursor-pointer appearance-none">
                            <option value="all">الكل</option>
                            @foreach($categoriesList as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        <svg class="w-3 h-3 text-gray-600 absolute left-2 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>

                    <!-- Search Input Box -->
                    <input type="text"
                           name="q"
                           x-model="searchQuery"
                           @input.debounce.250ms="fetchResults()"
                           @focus="if(searchResults.length > 0) searchOpen = true"
                           placeholder="ابحث في دهانات SCIB، جبسون بورد، كرانيش فيوتك، بديل خشب..."
                           autocomplete="off"
                           class="w-full h-10 px-3.5 bg-white text-gray-900 text-sm placeholder-gray-500 font-medium outline-none">

                    <!-- Amazon Search Submit Button (Warm Gold #C8A635) -->
                    <button type="submit" 
                            class="w-12 sm:w-14 h-10 bg-[#C8A635] hover:bg-[#d8b43f] active:bg-[#b5942d] text-[#073F40] flex items-center justify-center transition-colors shrink-0 cursor-pointer"
                            aria-label="بحث">
                        <svg class="w-5 h-5 font-bold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </button>
                </form>

                <!-- Amazon Live Autocomplete Dropdown -->
                <div x-show="searchOpen"
                     x-cloak
                     class="absolute top-full right-0 left-0 mt-1 bg-white rounded-b-md shadow-2xl border border-gray-200 z-50 divide-y divide-gray-100 overflow-hidden text-right">
                    <template x-for="item in searchResults" :key="item.id">
                        <a :href="item.url" class="flex items-center gap-3 p-3 hover:bg-gray-50 transition-colors">
                            <img :src="item.image_url" :alt="item.name" class="w-10 h-10 object-contain rounded bg-gray-50 p-1 border border-gray-200 shrink-0">
                            <div class="flex-1 min-w-0">
                                <div class="text-xs font-bold text-gray-900 truncate" x-text="item.name"></div>
                                <div class="text-[10px] text-gray-500" x-text="item.category"></div>
                            </div>
                            <div class="text-xs font-black text-[#073F40] tabular-nums shrink-0" x-text="item.price"></div>
                        </a>
                    </template>
                    <div class="p-2.5 bg-gray-50 text-center">
                        <button type="submit" class="text-xs font-bold text-[#073F40] hover:text-[#C8A635] transition-colors">
                            عرض جميع النتائج لـ "<span x-text="searchQuery"></span>" ←
                        </button>
                    </div>
                </div>
            </div>

            <!-- 4. Direct WhatsApp (Fast Support) -->
            <a href="https://wa.me/{{ $storeWhatsApp }}?text={{ urlencode('مرحباً مؤسسة حورس، أود الاستفسار عن مواد التشطيب والأسعار.') }}" 
               target="_blank" 
               rel="noopener noreferrer" 
               class="hidden xl:flex items-center gap-1.5 p-1.5 border border-transparent hover:border-white rounded-sm transition-colors shrink-0 text-right">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                <div class="flex flex-col leading-tight">
                    <span class="text-[11px] text-gray-300 font-normal">خدمة العملاء</span>
                    <span class="text-xs text-emerald-400 font-bold whitespace-nowrap">طلب عبر واتساب</span>
                </div>
            </a>

            <!-- 5. Account & Lists (Amazon Style) -->
            <div class="relative hidden sm:block shrink-0" @mouseenter="userDropdown = true" @mouseleave="userDropdown = false">
                <a href="{{ auth()->check() ? route('account.profile') : route('login') }}" 
                   class="flex items-center gap-1 p-1.5 border border-transparent hover:border-white rounded-sm transition-colors text-right cursor-pointer">
                    <div class="flex flex-col leading-tight">
                        <span class="text-[11px] text-gray-300 font-normal truncate max-w-[110px]">
                            @auth مرحباً، {{ auth()->user()->name }} @else مرحباً، تسجيل الدخول @endauth
                        </span>
                        <div class="flex items-center gap-1">
                            <span class="text-xs text-white font-bold whitespace-nowrap">الحساب والقوائم</span>
                            <svg class="w-3 h-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>
                </a>

                <!-- Amazon Account Hover Menu -->
                <div x-show="userDropdown"
                     x-cloak
                     class="absolute left-0 mt-1 w-56 bg-white rounded-md shadow-2xl border border-gray-200 py-3 z-50 text-gray-800 text-xs text-right">
                    @auth
                        <div class="px-4 pb-2 mb-2 border-b border-gray-100">
                            <div class="font-bold text-gray-900 truncate">{{ auth()->user()->name }}</div>
                            <div class="text-[10px] text-gray-500 truncate">{{ auth()->user()->phone }}</div>
                        </div>
                        <a href="{{ route('account.profile') }}" class="block px-4 py-2 hover:bg-gray-100 hover:text-[#073F40]">الملف الشخصي</a>
                        <a href="{{ route('account.orders') }}" class="block px-4 py-2 hover:bg-gray-100 hover:text-[#073F40]">طلباتي ومشترياتي</a>
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 hover:bg-primary-50 text-[#073F40] font-bold">لوحة التحكم والإدارة</a>
                        @endif
                        <div class="pt-2 mt-2 border-t border-gray-100">
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full text-right px-4 py-1.5 text-red-600 hover:bg-red-50 font-bold">تسجيل الخروج</button>
                            </form>
                        </div>
                    @else
                        <div class="px-4 pb-3 mb-2 border-b border-gray-100 text-center">
                            <a href="{{ route('login') }}" class="block w-full py-2 bg-[#C8A635] hover:bg-[#d8b43f] text-[#073F40] font-black rounded-md shadow-xs mb-1">
                                تسجيل الدخول
                            </a>
                            <span class="text-[10px] text-gray-500">عميل جديد؟ <a href="{{ route('login') }}" class="text-[#073F40] font-bold underline">ابدأ من هنا</a></span>
                        </div>
                        <a href="{{ route('orders.track') }}" class="block px-4 py-2 hover:bg-gray-100">تتبع الطلبات والشحنات</a>
                        <a href="{{ route('site.products') }}" class="block px-4 py-2 hover:bg-gray-100">كتالوج المنتجات</a>
                    @endauth
                </div>
            </div>

            <!-- 6. Orders (Amazon Returns & Orders) -->
            <a href="{{ route('orders.track') }}" 
               class="hidden lg:flex flex-col p-1.5 border border-transparent hover:border-white rounded-sm transition-colors text-right shrink-0 leading-tight">
                <span class="text-[11px] text-gray-300 font-normal">تتبع</span>
                <span class="text-xs text-white font-bold whitespace-nowrap">الطلبات والشحنات</span>
            </a>

            <!-- 7. Amazon Cart Icon with Basket Counter -->
            <button type="button"
                    @click.prevent="$dispatch('open-cart-drawer')"
                    class="flex items-center gap-1.5 p-1.5 px-2 border border-transparent hover:border-white rounded-sm transition-colors cursor-pointer shrink-0 group">
                <div class="relative flex items-center">
                    <!-- Amazon Cart Graphic -->
                    <svg class="w-8 h-8 text-white group-hover:text-[#C8A635] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <!-- Amazon Floating Badge in Basket -->
                    <span id="cart-badge-count" 
                          class="absolute -top-1 right-2.5 text-xs font-black text-[#C8A635] tabular-nums">
                        {{ $cartCount }}
                    </span>
                </div>
                <span class="hidden sm:inline text-xs text-white font-black mt-2">العربة</span>
            </button>

        </div>
    </div>

    <!-- Amazon Tier 2: Sub-Navigation Bar (Contrasting Dark Teal #0B5759) -->
    <div class="bg-[#0B5759] border-t border-[#09484a] px-2 sm:px-4 text-xs font-semibold">
        <div class="container mx-auto flex items-center justify-between">
            
            <!-- Horizontal Sub-Nav Links -->
            <div class="flex items-center overflow-x-auto no-scrollbar py-1 gap-1">
                
                <!-- ☰ All (Amazon Hamburger) -->
                <button type="button" 
                        @click="$dispatch('open-mobile-menu')"
                        class="flex items-center gap-1.5 px-2.5 py-1.5 border border-transparent hover:border-white rounded-sm transition-colors shrink-0 font-bold text-white cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <span>الكل</span>
                </button>

                <!-- Core Category Links (Amazon Style) -->
                <a href="{{ route('categories.index') }}" 
                   class="px-2.5 py-1.5 border border-transparent hover:border-white rounded-sm transition-colors shrink-0 text-white font-bold whitespace-nowrap {{ request()->routeIs('categories.index') ? 'border-white bg-white/10' : '' }}">
                    جميع الأقسام
                </a>

                <a href="{{ route('category.product', 33) }}" 
                   class="px-2.5 py-1.5 border border-transparent hover:border-white rounded-sm transition-colors shrink-0 text-white whitespace-nowrap">
                    دهانات SCIB
                </a>

                <a href="{{ route('category.product', 34) }}" 
                   class="px-2.5 py-1.5 border border-transparent hover:border-white rounded-sm transition-colors shrink-0 text-white whitespace-nowrap">
                    جبسون بورد
                </a>

                <a href="{{ route('category.product', 35) }}" 
                   class="px-2.5 py-1.5 border border-transparent hover:border-white rounded-sm transition-colors shrink-0 text-white whitespace-nowrap">
                    كرانيش فيوتك
                </a>

                <a href="{{ route('category.product', 36) }}" 
                   class="px-2.5 py-1.5 border border-transparent hover:border-white rounded-sm transition-colors shrink-0 text-white whitespace-nowrap">
                    بديل الخشب والرخام
                </a>

                <a href="{{ route('category.product', 37) }}" 
                   class="px-2.5 py-1.5 border border-transparent hover:border-white rounded-sm transition-colors shrink-0 text-white whitespace-nowrap">
                    أدوات ومستلزمات البناء
                </a>

                <a href="{{ route('category.product', 38) }}" 
                   class="px-2.5 py-1.5 border border-transparent hover:border-white rounded-sm transition-colors shrink-0 text-white whitespace-nowrap">
                    مواد التشطيب
                </a>

                <a href="{{ route('site.products') }}" 
                   class="px-2.5 py-1.5 border border-transparent hover:border-white rounded-sm transition-colors shrink-0 text-white whitespace-nowrap">
                    كل المنتجات
                </a>

                <a href="{{ Route::has('branches.index') ? route('branches.index') : url('/branches') }}" 
                   class="px-2.5 py-1.5 border border-transparent hover:border-white rounded-sm transition-colors shrink-0 text-white whitespace-nowrap">
                    موقعنا (حدائق الأهرام)
                </a>

                <a href="{{ route('contact') }}" 
                   class="px-2.5 py-1.5 border border-transparent hover:border-white rounded-sm transition-colors shrink-0 text-white whitespace-nowrap">
                    تواصل معنا
                </a>
            </div>

            <!-- Amazon Right/Left Promo Strip Message -->
            <div class="hidden xl:flex items-center gap-2 py-1 px-2 border border-transparent hover:border-white rounded-sm shrink-0">
                <span class="text-[#C8A635] font-black text-xs">وكيل معتمد SCIB</span>
                <span class="text-white/40">|</span>
                <span class="text-gray-200 text-[11px] font-medium">توريد مباشر لشركات المقاولات والمهندسين</span>
            </div>

        </div>
    </div>

</header>
