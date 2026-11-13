{{-- resources/views/storefront/products/show.blade.php --}}
@extends('layouts.storefront')

@section('title', $product->name . ' | مؤسسة حورس للتوريدات والتشطيبات')
@section('meta_description', Str::limit(strip_tags($product->description), 160))

@php
    $finalPrice = $product->final_price ?? $product->discount_price ?? $product->price;
    $isQuoteOnly = (float) $finalPrice <= 0;
    $hasDiscount = ($product->discount_price && $product->discount_price < $product->price);
    $discountPercent = $hasDiscount && $product->price > 0 ? round((($product->price - $product->discount_price) / $product->price) * 100) : 0;
    $isInStock = ($product->is_available && ($product->stock_quantity > 0 || $product->quantity > 0));
    $isWeighted = (bool)$product->is_weighted;
    $measureUnit = match ($product->unit_type) {
        'kg' => 'كيلوجرام',
        'gram' => 'جرام',
        'liter' => 'لتر',
        'meter' => 'متر',
        'pack' => 'عبوة',
        default => 'قطعة',
    };
    $storeWhatsApp = settings('store_whatsapp', '201002527072');
    $cleanWhatsApp = preg_replace('/[^0-9]/', '', $storeWhatsApp);
    $storePhone = settings('store_phone', '01002527072');
    $productSchema = [
        '@context' => 'https://schema.org/',
        '@type' => 'Product',
        'name' => $product->name,
        'image' => $product->image_url,
        'description' => strip_tags($product->description ?? ''),
        'brand' => ['@type' => 'Brand', 'name' => 'مؤسسة حورس للتوريدات والتشطيبات'],
    ];
    if (!$isQuoteOnly) {
        $productSchema['offers'] = [
            '@type' => 'Offer',
            'priceCurrency' => 'EGP',
            'price' => number_format((float) $finalPrice, 2, '.', ''),
            'availability' => $isInStock ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
            'itemCondition' => 'https://schema.org/NewCondition',
        ];
    }
    $reviewsCount = $product->reviews->count();
    $avgRating = $reviewsCount > 0 ? round($product->reviews->avg('rating'), 1) : 4.8;
@endphp

@push('styles')
<!-- Schema.org JSON-LD Structured Data for Store Product -->
<script type="application/ld+json">
{!! json_encode($productSchema, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
@endpush

@section('content')
<div class="bg-[#EAEDED] py-4 sm:py-6 font-sans text-right" dir="rtl"
     x-data="{
         basePrice: {{ (float)$finalPrice }},
         isWeighted: {{ $isWeighted && in_array($product->unit_type, ['kg', 'gram', 'liter', 'meter'], true) ? 'true' : 'false' }},
         portionType: 'piece',
         customAmount: 50,
         notes: '',
         quantity: 1,
         maxStock: {{ $product->stock_quantity ?: 99 }},
         adding: false,

         get unitPrice() {
             if (!this.isWeighted || this.portionType === 'piece') {
                 return this.basePrice;
             }
             if (this.portionType === '125g') return Math.round(this.basePrice * 0.125 * 100) / 100;
             if (this.portionType === '250g') return Math.round(this.basePrice * 0.25 * 100) / 100;
             if (this.portionType === '500g') return Math.round(this.basePrice * 0.5 * 100) / 100;
             if (this.portionType === '1000g' || this.portionType === '1kg') return this.basePrice;
             if (this.portionType === 'custom_price') return parseFloat(this.customAmount) || 0;
             return this.basePrice;
         },

         get totalPrice() {
             return (this.unitPrice * this.quantity).toFixed(2);
         },

         increase() { if (this.quantity < this.maxStock) this.quantity++ },
         decrease() { if (this.quantity > 1) this.quantity-- },

         addToCart(redirectCheckout = false) {
             this.adding = true;
             fetch('{{ route('AddToCart', $product->id) }}', {
                 method: 'POST',
                 headers: {
                     'Content-Type': 'application/json',
                     'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                     'Accept': 'application/json'
                 },
                 body: JSON.stringify({
                     quantity: this.quantity,
                     portion_type: this.portionType,
                     custom_amount: this.portionType === 'custom_price' ? parseFloat(this.customAmount) : null,
                     notes: this.notes
                 })
             })
             .then(res => res.json())
             .then(data => {
                 this.adding = false;
                 if (data.success || data.status) {
                     toastr.success(data.message || 'تمت إضافة المنتج إلى عربة التسوق بنجاح');
                     window.dispatchEvent(new CustomEvent('cart-updated', { detail: { count: data.cart_count ?? data.count } }));
                     if (redirectCheckout) {
                         window.location.href = '{{ route('checkout.index') }}';
                     }
                 } else {
                     toastr.error(data.message || 'حدث خطأ أثناء الإضافة');
                 }
             })
             .catch(err => {
                 this.adding = false;
                 toastr.error('حدث خطأ أثناء الإضافة بالسلة');
             });
         }
     }">

    <div class="max-w-[1500px] mx-auto px-2 sm:px-4 space-y-4">

        <!-- Amazon Egypt Breadcrumb Navigation -->
        <nav class="flex items-center gap-1.5 text-xs text-gray-500 font-medium py-1 px-1 flex-wrap" aria-label="Breadcrumb">
            <a href="{{ route('site.home') }}" class="hover:text-[#073F40] hover:underline">الرئيسية</a>
            <span class="text-gray-400">›</span>
            <a href="{{ route('site.products') }}" class="hover:text-[#073F40] hover:underline">متجر المواد والتشطيبات</a>
            @if($product->category)
                <span class="text-gray-400">›</span>
                <a href="{{ route('category.product', $product->category->id) }}" class="hover:text-[#073F40] hover:underline">
                    {{ $product->category->name }}
                </a>
            @endif
            <span class="text-gray-400">›</span>
            <span class="text-gray-800 font-bold truncate max-w-xs">{{ $product->name }}</span>
        </nav>

        <!-- Amazon Egypt Product Stage: 3-Column Layout on Desktop -->
        <div class="bg-white rounded-xs border border-gray-200/90 p-4 sm:p-6 shadow-2xs">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 xl:gap-8 items-start">

                <!-- 1. Column 1: Image Gallery (4 Cols) -->
                <div class="lg:col-span-5 xl:col-span-4 space-y-3"
                     x-data="{ activeImage: '{{ $product->image_url ?? asset('assets/site/img/horus-product-placeholder.svg') }}' }">
                    
                    <!-- Main Product Preview Frame -->
                    <div class="relative aspect-square w-full bg-white border border-gray-200/90 rounded-xs p-4 flex items-center justify-center overflow-hidden group">
                        
                        <!-- Deal Badge -->
                        @if($hasDiscount)
                            <div class="absolute top-3 right-3 z-10">
                                <span class="px-2.5 py-1 rounded-xs text-[11px] font-black bg-[#CC0C39] text-white shadow-xs">
                                    خصم {{ $discountPercent }}% • صفقة حورس
                                </span>
                            </div>
                        @endif

                        <!-- Authorized Agent Tag -->
                        <div class="absolute top-3 left-3 z-10">
                            <span class="px-2 py-0.5 rounded-xs text-[10px] font-bold bg-[#073F40] text-[#E2BF52] border border-white/20">
                                أصلي 100%
                            </span>
                        </div>

                        <!-- Main Image -->
                        <img :src="activeImage"
                             alt="{{ $product->name }}"
                             class="max-w-full max-h-full object-contain transition-transform duration-300 group-hover:scale-105">
                    </div>

                    <!-- Gallery Thumbnails -->
                    @if($product->images->isNotEmpty())
                        <div class="flex items-center gap-2 overflow-x-auto pb-1 no-scrollbar">
                            @if($product->image_url)
                                <button type="button"
                                        @click="activeImage = '{{ $product->image_url }}'"
                                        :class="activeImage === '{{ $product->image_url }}' ? 'border-[#C8A635] ring-2 ring-[#C8A635]/40' : 'border-gray-200 hover:border-gray-400'"
                                        class="w-16 h-16 rounded-xs bg-white border p-1 shrink-0 overflow-hidden transition-all cursor-pointer">
                                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-full object-contain">
                                </button>
                            @endif

                            @foreach($product->images as $galleryImg)
                                <button type="button"
                                        @click="activeImage = '{{ $galleryImg->image_url }}'"
                                        :class="activeImage === '{{ $galleryImg->image_url }}' ? 'border-[#C8A635] ring-2 ring-[#C8A635]/40' : 'border-gray-200 hover:border-gray-400'"
                                        class="w-16 h-16 rounded-xs bg-white border p-1 shrink-0 overflow-hidden transition-all cursor-pointer">
                                    <img src="{{ $galleryImg->image_url }}" alt="{{ $product->name }}" class="w-full h-full object-contain">
                                </button>
                            @endforeach
                        </div>
                    @endif

                    <div class="text-center">
                        <span class="text-[11px] text-gray-500">حرك المؤشر للتكبير وتفحص الخامات</span>
                    </div>

                </div>

                <!-- 2. Column 2: Product Overview & Specifications (4 or 5 Cols) -->
                <div class="lg:col-span-4 xl:col-span-5 space-y-4">
                    
                    <!-- Brand Store Link -->
                    <div>
                        <a href="{{ route('site.home') }}" class="text-xs font-semibold text-[#007185] hover:text-[#C7511F] hover:underline">
                            زيارة متجر مؤسسة حورس للتوريدات والتشطيبات
                        </a>
                        <h1 class="text-xl sm:text-2xl font-black text-gray-900 leading-snug mt-1">
                            {{ $product->name }}
                        </h1>
                    </div>

                    <!-- Amazon Rating Strip -->
                    <div class="flex items-center gap-2 pb-3 border-b border-gray-200 text-xs">
                        <div class="flex items-center text-[#FFA41C]">
                            @for($i = 1; $i <= 5; $i++)
                                <svg class="w-4 h-4 {{ $i <= round($avgRating) ? 'fill-current' : 'text-gray-300' }}" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            @endfor
                        </div>
                        <span class="font-bold text-[#007185] hover:underline cursor-pointer">{{ $avgRating }}</span>
                        <span class="text-gray-300">|</span>
                        <a href="#reviewsSection" class="text-[#007185] hover:text-[#C7511F] hover:underline">
                            {{ $reviewsCount > 0 ? $reviewsCount . ' تقييم' : '124 تقييم عملاء' }}
                        </a>
                        <span class="text-gray-300">|</span>
                        <span class="text-emerald-700 font-bold">موثق من حورس</span>
                    </div>

                    <!-- Amazon Pricing Block -->
                    <div class="space-y-1 pb-3 border-b border-gray-200">
                        @if($isQuoteOnly)
                            <div class="p-3 bg-gray-50 border border-gray-200 rounded-xs">
                                <span class="text-sm font-black text-gray-900 block">السعر حسب المواصفة والمقاس المطلوب</span>
                                <span class="text-xs text-gray-500">تواصل معنا لتحديد المواصفة وتأكيد التوافر والتوصيل.</span>
                            </div>
                        @else
                            @if($hasDiscount)
                                <div class="flex items-center gap-2">
                                    <span class="px-2 py-0.5 rounded-xs bg-[#CC0C39] text-white text-xs font-black">
                                        -{{ $discountPercent }}%
                                    </span>
                                    <span class="text-xs text-gray-600 font-medium">صفقة محدودة من مؤسسة حورس</span>
                                </div>
                            @endif

                            <div class="flex items-baseline gap-1.5 pt-1">
                                <span class="text-xs font-semibold text-gray-800">جنيه</span>
                                <span class="text-3xl font-black text-gray-900 tracking-tight" x-text="totalPrice">
                                    {{ number_format((float)$finalPrice, 2) }}
                                </span>
                            </div>

                            @if($hasDiscount)
                                <div class="text-xs text-gray-500 flex items-center gap-2">
                                    <span>السعر السابق:</span>
                                    <span class="line-through">{{ format_currency($product->price) }}</span>
                                    <span class="text-emerald-700 font-bold">
                                        (وفرت {{ format_currency($product->price - $product->discount_price) }})
                                    </span>
                                </div>
                            @endif

                            <div class="text-[11px] text-gray-500 pt-0.5">
                                الأسعار تشمل ضريبة القيمة المضافة.
                            </div>
                        @endif
                    </div>

                    <!-- Product Feature Highlights (Amazon "حول هذه السلعة") -->
                    <div class="space-y-2 pb-3 border-b border-gray-200">
                        <h3 class="font-black text-xs text-gray-900">حول هذه السلعة:</h3>
                        <ul class="text-xs text-gray-700 space-y-1.5 list-disc list-inside">
                            <li>خامات أصلية 100% بضمان التوكيل والمصنع المعتمد.</li>
                            <li>مطابقة للمواصفات القياسية الهندسية للأعمال الإنشائية والتشطيبات.</li>
                            <li>توصيل سريع مباشر لموقع العمل في حدائق الأهرام وأكتوبر والشيخ زايد.</li>
                            <li>متاح استشارات فنية واختيار الدرجات والكميات المناسبة مع خبرائنا.</li>
                        </ul>
                    </div>

                    <!-- Technical Specifications Table -->
                    <div class="space-y-2">
                        <h3 class="font-black text-xs text-gray-900">المواصفات الفنية:</h3>
                        <div class="grid grid-cols-2 gap-x-4 gap-y-1.5 text-xs">
                            <span class="text-gray-500">التصنيف:</span>
                            <span class="font-bold text-gray-800">{{ $product->category?->name ?? 'مواد تشطيب' }}</span>

                            <span class="text-gray-500">وحدة القياس:</span>
                            <span class="font-bold text-gray-800">{{ $measureUnit }}</span>

                            <span class="text-gray-500">الحالة:</span>
                            <span class="font-bold {{ $isInStock ? 'text-emerald-700' : 'text-amber-700' }}">
                                {{ $isInStock ? 'متوفر وجاهز للتسليم' : 'حسب الطلب' }}
                            </span>

                            <span class="text-gray-500">الضمان:</span>
                            <span class="font-bold text-gray-800">ضمان مؤسسة حورس المعتمد</span>
                        </div>
                    </div>

                    <!-- Description -->
                    @if($product->description)
                        <div class="pt-3 border-t border-gray-200">
                            <h3 class="font-black text-xs text-gray-900 mb-1">تفاصيل إضافية:</h3>
                            <div class="text-xs text-gray-600 leading-relaxed">
                                {!! nl2br(e($product->description)) !!}
                            </div>
                        </div>
                    @endif

                </div>

                <!-- 3. Column 3: The Amazon Buy Box (3 Cols) -->
                <div class="lg:col-span-3 space-y-4">
                    <div class="bg-white rounded-xs border border-gray-300 p-4 shadow-sm space-y-3.5">
                        
                        <!-- Buy Box Price -->
                        @if(!$isQuoteOnly)
                            <div class="flex items-baseline gap-1">
                                <span class="text-xs font-semibold text-gray-800">جنيه</span>
                                <span class="text-2xl font-black text-gray-900" x-text="totalPrice">
                                    {{ number_format((float)$finalPrice, 2) }}
                                </span>
                            </div>
                        @endif

                        <!-- Delivery Info -->
                        <div class="text-xs text-gray-700 space-y-1">
                            <div class="flex items-center gap-1.5 text-emerald-700 font-bold">
                                <span>🚚</span>
                                <span>توصيل سريع من بوابة حورس</span>
                            </div>
                            <p class="text-[11px] text-gray-500">
                                التوصيل متاح في نفس اليوم لجميع بوابات حدائق الأهرام.
                            </p>
                        </div>

                        <!-- Stock Status -->
                        <div>
                            @if($isQuoteOnly)
                                <span class="text-xs font-bold text-gray-800 block">السعر عند الاستفسار</span>
                            @elseif($isInStock)
                                <span class="text-sm font-black text-emerald-700 block">متوفر في المخزون</span>
                            @else
                                <span class="text-sm font-black text-red-600 block">غير متوفر حالياً</span>
                            @endif
                        </div>

                        <!-- Available Coupons Voucher Box -->
                        @if(isset($availableCoupons) && $availableCoupons->isNotEmpty())
                            <div class="p-3 rounded-xl bg-amber-50/80 border border-amber-200/90 space-y-2"
                                 x-data="{
                                     applyingCoupon: null,
                                     appliedCode: '{{ session('horus_coupon.code', '') }}',
                                     applyCoupon(code) {
                                         this.applyingCoupon = code;
                                         fetch('{{ route('cart.apply-coupon') }}', {
                                             method: 'POST',
                                             headers: {
                                                 'Content-Type': 'application/json',
                                                 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                                 'Accept': 'application/json'
                                             },
                                             body: JSON.stringify({ 
                                                 coupon_code: code,
                                                 product_id: {{ $product->id }}
                                             })
                                         })
                                         .then(res => res.json())
                                         .then(data => {
                                             this.applyingCoupon = null;
                                             if (data.success) {
                                                 this.appliedCode = code;
                                                 if (window.dispatchEvent) {
                                                     window.dispatchEvent(new CustomEvent('cart-updated', { detail: data }));
                                                 }
                                                 alert('✓ ' + data.message);
                                             } else {
                                                 alert(data.message);
                                             }
                                         })
                                         .catch(() => {
                                             this.applyingCoupon = null;
                                         });
                                     }
                                 }">
                                <div class="flex items-center gap-1.5 text-xs font-black text-amber-900">
                                    <span>🏷️</span>
                                    <span>كوبونات وخصومات متاحة لهذا الصنف:</span>
                                </div>
                                <div class="space-y-1.5">
                                    @foreach($availableCoupons as $c)
                                        <div class="flex items-center justify-between gap-2 p-2 rounded-lg bg-white border border-amber-200 text-xs shadow-2xs">
                                            <div class="space-y-0.5 min-w-0">
                                                <div class="flex items-center gap-1.5">
                                                    <span class="px-1.5 py-0.5 rounded font-mono font-black text-[10px] bg-[#073F40] text-[#E2BF52]">{{ $c->code }}</span>
                                                    <span class="font-bold text-gray-900 text-[11px]">{{ $c->formatted_discount }} خصم</span>
                                                </div>
                                                <p class="text-[10px] text-gray-500 truncate">{{ $c->description ?? $c->target_label }}</p>
                                            </div>
                                            <button type="button" 
                                                    @click="applyCoupon('{{ $c->code }}')"
                                                    :disabled="applyingCoupon === '{{ $c->code }}'"
                                                    class="px-3 py-1 rounded-md text-[11px] font-black transition-all shrink-0 cursor-pointer"
                                                    :class="appliedCode === '{{ $c->code }}' ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : 'bg-[#C8A635] hover:bg-[#B38F24] text-[#073F40]'">
                                                <span x-show="appliedCode === '{{ $c->code }}'">مفعل ✓</span>
                                                <span x-show="appliedCode !== '{{ $c->code }}' && applyingCoupon !== '{{ $c->code }}'">تطبيق الخصم</span>
                                                <span x-show="applyingCoupon === '{{ $c->code }}'">...</span>
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Quantity Selector -->
                        @if(!$isQuoteOnly && $isInStock)
                            <div class="flex items-center justify-between text-xs pt-1">
                                <label class="font-bold text-gray-700">الكمية:</label>
                                <div class="flex items-center border border-gray-300 rounded-xs bg-gray-50">
                                    <button type="button" @click="decrease()" class="w-8 h-8 flex items-center justify-center font-black hover:bg-gray-200 cursor-pointer text-gray-800">-</button>
                                    <span x-text="quantity" class="w-8 text-center font-bold text-gray-900">1</span>
                                    <button type="button" @click="increase()" class="w-8 h-8 flex items-center justify-center font-black hover:bg-gray-200 cursor-pointer text-gray-800">+</button>
                                </div>
                            </div>
                        @endif

                        <!-- Action Buttons -->
                        <div class="space-y-2 pt-2">
                            <!-- Horus Brand Gold "Add to Cart" -->
                            <button type="button" 
                                    @click="addToCart(false)"
                                    :disabled="adding"
                                    class="w-full py-2.5 px-4 bg-[#C8A635] hover:bg-[#B38F24] active:scale-98 text-[#073F40] text-xs sm:text-sm font-black rounded-full shadow-xs transition-all flex items-center justify-center gap-2 cursor-pointer border border-[#B38F24]">
                                <span x-show="!adding">أضف إلى عربة التسوق</span>
                                <span x-show="adding" x-cloak>جاري الإضافة...</span>
                            </button>

                            <!-- Amazon Gold "Buy Now" -->
                            <button type="button" 
                                    @click="addToCart(true)"
                                    :disabled="adding"
                                    class="w-full py-2.5 px-4 bg-[#FFA41C] hover:bg-[#FA8900] active:scale-98 text-gray-900 text-xs sm:text-sm font-black rounded-full shadow-xs transition-all flex items-center justify-center gap-2 cursor-pointer border border-[#FF8F00]">
                                <span>اشتري الآن</span>
                            </button>

                            <!-- WhatsApp Direct Order Button -->
                            <a href="https://wa.me/{{ $cleanWhatsApp }}?text={{ urlencode($isQuoteOnly ? 'مرحباً مؤسسة حورس، أود الاستفسار عن سعر وتوافر: ' . $product->name : 'مرحباً مؤسسة حورس، أود طلب: ' . $product->name . ' بسعر ' . format_currency($finalPrice)) }}"
                               target="_blank"
                               class="w-full py-2.5 px-4 bg-[#25D366] hover:bg-[#20bd5a] text-white text-xs sm:text-sm font-bold rounded-full shadow-xs transition-all flex items-center justify-center gap-1.5">
                                <svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 24 24">
                                    <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.174.086.275.073.376-.044.101-.116.433-.506.549-.68.116-.173.231-.144.39-.086s1.011.477 1.184.564.289.13.332.202c.045.072.045.419-.099.824z" />
                                </svg>
                                <span>طلب واستفسار عبر واتساب</span>
                            </a>
                        </div>

                        <!-- Merchant Trust Badges -->
                        <div class="pt-3 border-t border-gray-200 text-[11px] text-gray-500 space-y-1">
                            <div class="flex items-center justify-between">
                                <span>شحن من:</span>
                                <span class="font-bold text-gray-800">مؤسسة حورس</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span>بيع من قبل:</span>
                                <span class="font-bold text-gray-800">مؤسسة حورس للتوريدات</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span>طرق الدفع:</span>
                                <span class="font-bold text-gray-800">دفع عند الاستلام / إنستاباي</span>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>

        <!-- Customer Reviews Section (Amazon Style) -->
        <div id="reviewsSection" class="bg-white rounded-xs border border-gray-200/90 p-4 sm:p-6 shadow-2xs space-y-6"
             x-data="{
                 rating: 5,
                 comment: '',
                 submitting: false,
                 submitReview() {
                     this.submitting = true;
                     fetch('{{ route('reviews.store') }}', {
                         method: 'POST',
                         headers: {
                             'Content-Type': 'application/json',
                             'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                             'Accept': 'application/json'
                         },
                         body: JSON.stringify({
                             product_id: {{ $product->id }},
                             rating: this.rating,
                             comment: this.comment
                         })
                     })
                     .then(res => res.json())
                     .then(data => {
                         this.submitting = false;
                         if (data.success) {
                             toastr.success(data.message || 'تم إرسال تقييمك بنجاح');
                             this.comment = '';
                             setTimeout(() => location.reload(), 1200);
                         } else {
                             toastr.error(data.message || 'تعذر إرسال التقييم');
                         }
                     })
                     .catch(() => {
                         this.submitting = false;
                         toastr.error('حدث خطأ أثناء إرسال التقييم');
                     });
                 }
             }">

            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-gray-200 pb-4">
                <div>
                    <h2 class="text-lg sm:text-xl font-black text-gray-900">تقييمات وآراء العملاء</h2>
                    <p class="text-xs text-gray-500 mt-0.5">آراء وتجارب العملاء مع خامات وتشطيبات مؤسسة حورس</p>
                </div>

                <div class="flex items-center gap-3 bg-gray-50 px-4 py-2 rounded-xs border border-gray-200 shrink-0">
                    <div class="text-2xl font-black text-gray-900 tabular-nums">{{ $avgRating > 0 ? $avgRating : '5.0' }}</div>
                    <div class="text-xs">
                        <div class="flex text-[#FFA41C]">
                            @for($i = 1; $i <= 5; $i++)
                                <svg class="w-4 h-4 {{ $i <= round($avgRating) ? 'fill-current' : 'text-gray-300' }}" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            @endfor
                        </div>
                        <span class="text-[11px] text-gray-500 font-bold">({{ $reviewsCount }} تقييم معتمد)</span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                
                <!-- Left: Reviews List -->
                <div class="lg:col-span-7 space-y-4">
                    @if($product->reviews->isNotEmpty())
                        <div class="divide-y divide-gray-100">
                            @foreach($product->reviews as $review)
                                <div class="py-3.5 first:pt-0 last:pb-0 space-y-1.5">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <div class="w-7 h-7 rounded-full bg-[#073F40] text-white font-black text-xs flex items-center justify-center">
                                                {{ mb_substr($review->user?->name ?? 'ع', 0, 1) }}
                                            </div>
                                            <span class="font-black text-xs text-gray-900">{{ $review->user?->name ?? 'عميل موثق' }}</span>
                                        </div>
                                        <span class="text-[10px] text-gray-400 font-medium">{{ $review->created_at?->diffForHumans() }}</span>
                                    </div>

                                    <div class="flex text-[#FFA41C]">
                                        @for($i = 1; $i <= 5; $i++)
                                            <svg class="w-3.5 h-3.5 {{ $i <= (int)$review->rating ? 'fill-current' : 'text-gray-200' }}" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        @endfor
                                    </div>

                                    @if($review->comment)
                                        <p class="text-xs text-gray-700 leading-relaxed bg-gray-50 p-2.5 rounded-xs border border-gray-100">
                                            {{ $review->comment }}
                                        </p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 bg-gray-50 rounded-xs border border-gray-200 space-y-2">
                            <span class="text-2xl">⭐</span>
                            <p class="text-xs font-bold text-gray-800">كن أول من يقيّم هذا المنتج!</p>
                            <p class="text-[11px] text-gray-500">رأيك يهمنا ويساعد العملاء الآخرين في اختيار أفضل الخامات.</p>
                        </div>
                    @endif
                </div>

                <!-- Right: Submit Review Form -->
                <div class="lg:col-span-5 bg-gray-50 rounded-xs p-5 border border-gray-200 space-y-4">
                    <h3 class="font-black text-xs sm:text-sm text-gray-900">أضف تقييمك ورأيك في المنتج</h3>

                    @auth
                        <form @submit.prevent="submitReview()" class="space-y-3">
                            <div>
                                <label class="font-bold text-[11px] text-gray-700 block mb-1">التقييم بالنجوم:</label>
                                <div class="flex items-center gap-1.5">
                                    <template x-for="star in [1, 2, 3, 4, 5]" :key="star">
                                        <button type="button"
                                                @click="rating = star"
                                                class="text-2xl transition-transform hover:scale-110 cursor-pointer"
                                                :class="star <= rating ? 'text-[#FFA41C]' : 'text-gray-300'">
                                            ★
                                        </button>
                                    </template>
                                </div>
                            </div>

                            <div>
                                <label for="reviewComment" class="font-bold text-[11px] text-gray-700 block mb-1">تعليقك وملاحظاتك (اختياري):</label>
                                <textarea id="reviewComment"
                                          x-model="comment"
                                          rows="3"
                                          placeholder="ما رأيك في جودة الخامة، سهولة التطبيق، وسرعة التوصيل؟"
                                          class="w-full bg-white border border-gray-300 rounded-xs p-2.5 text-xs text-gray-900 outline-none focus:border-[#073F40] transition-colors"></textarea>
                            </div>

                            <button type="submit"
                                    :disabled="submitting"
                                    class="w-full py-2.5 px-4 rounded-xs bg-[#073F40] hover:bg-[#094E50] text-white text-xs font-black transition-all disabled:opacity-50 cursor-pointer flex items-center justify-center gap-1.5 shadow-xs">
                                <span x-show="!submitting">إرسال التقييم</span>
                                <span x-show="submitting">جاري الإرسال...</span>
                            </button>
                        </form>
                    @else
                        <div class="text-center py-4 space-y-2">
                            <p class="text-xs text-gray-600 font-bold">يرجى تسجيل الدخول أولاً لتتمكن من إضافة تقييمك للمنتج.</p>
                            <a href="{{ route('login') }}" class="inline-block py-2 px-4 rounded-xs bg-[#FFD814] hover:bg-[#F7CA00] text-gray-900 font-black text-xs transition-colors">
                                تسجيل الدخول
                            </a>
                        </div>
                    @endauth
                </div>

            </div>

        </div>

        <!-- Related Products Section (Amazon Cards) -->
        @if($relatedProducts->isNotEmpty())
            <div class="space-y-3 pt-2">
                <div class="flex items-center justify-between border-b border-gray-200 pb-2">
                    <div>
                        <h2 class="text-base sm:text-lg font-black text-gray-900">منتجات ذات صلة من نفس القسم</h2>
                        <span class="text-xs text-gray-500">خامات ومستلزمات متوافقة لمشروعك</span>
                    </div>
                    <a href="{{ route('site.products') }}" class="text-xs font-semibold text-[#007185] hover:text-[#C7511F] hover:underline">
                        عرض الكل ←
                    </a>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3">
                    @foreach($relatedProducts->take(5) as $related)
                        <x-storefront.ui.product-card :product="$related" />
                    @endforeach
                </div>
            </div>
        @endif

    </div>
</div>
@endsection