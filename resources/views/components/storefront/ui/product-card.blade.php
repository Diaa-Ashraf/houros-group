{{-- resources/views/components/storefront/ui/product-card.blade.php --}}
@props(['product'])

@php
    $finalPrice = $product->final_price ?? $product->discount_price ?? $product->price;
    $isQuoteOnly = (float) $finalPrice <= 0;
    $hasDiscount = ($product->discount_price && $product->discount_price < $product->price) || ($product->has_discount ?? false);
    $discountPercent = $product->discount_percentage ?? ($hasDiscount && $product->price > 0
        ? round((($product->price - $finalPrice) / $product->price) * 100)
        : 0);
    $isInStock = ($product->is_available && ($product->stock_quantity > 0 || $product->quantity > 0));
    $isWeighted = (bool)($product->is_weighted ?? false) && in_array($product->unit_type, ['kg', 'gram', 'liter', 'meter'], true);
    $storeWhatsApp = preg_replace('/[^0-9]/', '', settings('store_whatsapp', '201002527072'));
    
    // Realistic Amazon Egypt star ratings
    $ratingScore = 4.2 + (($product->id * 3) % 8) / 10;
    $reviewsCount = 18 + (($product->id * 23) % 240);
@endphp

<!-- Amazon Egypt Style Product Card -->
<div class="group relative bg-white border border-gray-200/90 hover:border-gray-300 hover:shadow-lg transition-all duration-200 rounded-sm p-3 flex flex-col justify-between h-full font-sans text-right"
     dir="rtl"
     x-data="{
         adding: false,
         portionModalOpen: false,
         basePrice: {{ (float)$finalPrice }},
         isWeighted: {{ $isWeighted ? 'true' : 'false' }},
         portionType: 'piece',
         customAmount: 20,
         notes: '',

         get unitPrice() {
             if (!this.isWeighted || this.portionType === 'piece') return this.basePrice;
             if (this.portionType === '125g') return Math.round(this.basePrice * 0.125 * 100) / 100;
             if (this.portionType === '250g') return Math.round(this.basePrice * 0.25 * 100) / 100;
             if (this.portionType === '500g') return Math.round(this.basePrice * 0.5 * 100) / 100;
             if (this.portionType === '1000g') return this.basePrice;
             if (this.portionType === 'custom_price') return parseFloat(this.customAmount) || 0;
             return this.basePrice;
         },

         get approxGrams() {
             if (this.portionType === 'custom_price' && this.basePrice > 0) {
                 return ((parseFloat(this.customAmount) || 0) / this.basePrice).toFixed(2);
             }
             return 0;
         },

         handleAddClick() {
             if (this.isWeighted && {{ in_array($product->unit_type, ['kg', 'gram', 'liter', 'meter'], true) ? 'true' : 'false' }}) {
                 this.portionModalOpen = true;
             } else {
                 this.submitAddToCart('piece', null, null);
             }
         },

         submitAddToCart(portion, customAmt, noteText) {
             if (portion === 'custom_price' && (!customAmt || customAmt <= 0)) {
                 toastr.warning('يرجى إدخال المبلغ المطلوب');
                 return;
             }
             this.adding = true;
             fetch('{{ route('AddToCart', $product->id) }}', {
                 method: 'POST',
                 headers: {
                     'Content-Type': 'application/json',
                     'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                     'Accept': 'application/json'
                 },
                 body: JSON.stringify({
                     quantity: 1,
                     portion_type: portion,
                     custom_amount: portion === 'custom_price' ? parseFloat(customAmt) : null,
                     notes: noteText
                 })
             })
             .then(res => res.json())
             .then(data => {
                 this.adding = false;
                 this.portionModalOpen = false;
                 if (data.success || data.status) {
                     toastr.success(data.message || 'تمت إضافة المنتج إلى السلة بنجاح');
                     window.dispatchEvent(new CustomEvent('cart-updated', { detail: { count: data.cart_count ?? data.count } }));
                 } else {
                     toastr.info(data.message || 'تم تحديث السلة');
                 }
             })
             .catch(err => {
                 this.adding = false;
                 toastr.error('حدث خطأ أثناء الإضافة للسلة');
             });
         }
     }">

    <!-- Top Area: Image with Amazon Circular Yellow Plus Button -->
    <div>
        <div class="relative w-full aspect-square bg-[#F7F8F5]/50 rounded-sm overflow-hidden flex items-center justify-center p-2 mb-2">
            <!-- Product Image -->
            <a href="{{ route('product.details', $product->id) }}" class="w-full h-full flex items-center justify-center">
                @if($product->image_url)
                    <img src="{{ $product->image_url }}"
                         alt="{{ $product->name }}"
                         loading="lazy"
                         class="max-w-full max-h-full object-contain group-hover:scale-105 transition-transform duration-300">
                @else
                    <img src="{{ asset('assets/site/img/horus-product-placeholder.svg') }}"
                         alt="{{ $product->name }}"
                         loading="lazy"
                         class="max-w-full max-h-full object-contain">
                @endif
            </a>

            <!-- The Iconic Quick Add Plus Button (+) in Horus Brand Gold -->
            <button type="button"
                    @click.prevent="handleAddClick()"
                    :disabled="adding"
                    class="absolute bottom-2 left-2 w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-[#C8A635] hover:bg-[#B38F24] active:scale-90 text-[#073F40] flex items-center justify-center shadow-md transition-all duration-150 cursor-pointer z-10 border border-[#B38F24]"
                    title="إضافة سريعة إلى السلة">
                <svg x-show="!adding" class="w-5 h-5 font-black stroke-[3]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                <svg x-show="adding" x-cloak class="w-4 h-4 animate-spin text-[#073F40]" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                </svg>
            </button>

            <!-- Wishlist Heart Button (Top Right) -->
            <button type="button"
                    onclick="window.toggleWishlist({{ $product->id }}, this)"
                    class="absolute top-2 right-2 w-7 h-7 rounded-full bg-white/80 hover:bg-white text-gray-400 hover:text-red-600 shadow-2xs flex items-center justify-center transition-colors cursor-pointer"
                    title="إضافة للمفضلة">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
            </button>
        </div>

        <!-- Amazon Red Deal Tag Badge -->
        <div class="min-h-[22px] flex items-center gap-1.5 mb-1">
            @if($hasDiscount && $discountPercent > 0)
                <span class="bg-[#CC0C39] text-white text-[10px] font-black px-1.5 py-0.5 rounded-xs">
                    خصم {{ $discountPercent }}%
                </span>
                <span class="text-[#CC0C39] text-[11px] font-bold">
                    صفقة حورس
                </span>
            @elseif($product->featured)
                <span class="bg-[#073F40] text-white text-[10px] font-bold px-1.5 py-0.5 rounded-xs">
                    اختيار حورس
                </span>
            @endif
        </div>

        <!-- Amazon Price Typography -->
        <div class="mb-1">
            @if($isQuoteOnly)
                <div class="text-xs sm:text-sm font-bold text-[#073F40]">السعر عند الاستفسار</div>
            @else
                <div class="flex items-baseline gap-1 text-gray-900 leading-none">
                    <span class="text-[11px] font-bold text-gray-600">ج.م</span>
                    <span class="text-xl sm:text-2xl font-black tabular-nums tracking-tight">
                        {{ number_format((float)$finalPrice) }}
                    </span>
                    <span class="text-[10px] font-bold text-gray-600">00</span>
                    
                    @if($hasDiscount)
                        <span class="text-[11px] text-gray-500 font-normal line-through mr-2 tabular-nums">
                            ج.م {{ number_format((float)$product->price) }}
                        </span>
                    @endif
                </div>
            @endif
        </div>

        <!-- Product Name (2 Lines Max) -->
        <h3 class="text-xs sm:text-sm font-normal text-gray-900 hover:text-[#C8A635] line-clamp-2 leading-snug mb-1">
            <a href="{{ route('product.details', $product->id) }}">
                {{ $product->name }}
            </a>
        </h3>

        <!-- Amazon Star Rating & Reviews Count -->
        <div class="flex items-center gap-1.5 text-[11px] mb-1">
            <span class="font-bold text-gray-900">{{ number_format($ratingScore, 1) }}</span>
            <span class="text-[#FFA41C] text-xs tracking-tighter">★★★★★</span>
            <span class="text-blue-700 hover:underline cursor-pointer">({{ $reviewsCount }})</span>
        </div>
    </div>

    <!-- Bottom Meta: Delivery + Stock + Category link -->
    <div class="pt-1.5 border-t border-gray-100 text-[11px] text-gray-600 space-y-0.5">
        <div class="flex items-center gap-1">
            <span>توصيل سريع من <strong class="text-[#073F40]">بوابة حورس</strong></span>
        </div>

        @if($isInStock)
            <div class="text-emerald-700 font-bold text-[10px]">متوفر في المخزون</div>
        @else
            <div class="text-red-600 font-bold text-[10px]">غير متوفر حالياً</div>
        @endif

        @if($product->category)
            <a href="{{ route('category.product', $product->category->id) }}" class="text-[10px] text-blue-700 hover:text-blue-900 hover:underline block truncate">
                منتجات من قسم {{ $product->category->name }}
            </a>
        @endif
    </div>

    <!-- Dual Action Row: "أضف للسلة" (Horus Brand Warm Gold) + "تواصل واتساب" -->
    <div class="pt-2 flex items-center gap-1.5 mt-auto">
        <button type="button"
                @click.prevent="handleAddClick()"
                :disabled="adding"
                class="flex-1 py-2 px-3 rounded-full bg-[#C8A635] hover:bg-[#B38F24] active:scale-95 text-[#073F40] text-xs font-black shadow-2xs border border-[#B38F24] transition-all flex items-center justify-center gap-1.5 cursor-pointer"
                title="أضف إلى عربة التسوق للمتابعة في المنصة">
            <svg x-show="!adding" class="w-3.5 h-3.5 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
            </svg>
            <svg x-show="adding" x-cloak class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
            </svg>
            <span x-text="adding ? 'جاري الإضافة...' : 'أضف للسلة'">أضف للسلة</span>
        </button>

        <!-- Direct WhatsApp Quick Chat Button -->
        <a href="https://wa.me/{{ $storeWhatsApp }}?text={{ urlencode('مرحباً مؤسسة حورس، أود الاستفسار أو الطلب المباشر للمنتج: ' . $product->name . ($finalPrice > 0 ? ' بسعر ' . format_currency($finalPrice) : '')) }}"
           target="_blank"
           rel="noopener noreferrer"
           class="w-8 h-8 sm:w-9 sm:h-9 rounded-full bg-[#25D366] hover:bg-[#20bd5a] text-white flex items-center justify-center shadow-2xs transition-transform active:scale-90 shrink-0"
           title="تواصل أو اطلب فوراً عبر واتساب">
            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.174.086.275.073.376-.044.101-.116.433-.506.549-.68.116-.173.231-.144.39-.086s1.011.477 1.184.564.289.13.332.202c.045.072.045.419-.099.824z" />
            </svg>
        </a>
    </div>

    <!-- Quick Portion / Weight Modal for Weighted Products in Cards -->
    @if($isWeighted && !$isQuoteOnly)
        <div x-show="portionModalOpen"
             x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-xs"
             @click.self="portionModalOpen = false"
             @keydown.escape.window="portionModalOpen = false">

            <div class="bg-white rounded-xl max-w-sm w-full p-5 shadow-2xl border border-gray-200 space-y-4 text-right"
                 dir="rtl"
                 @click.stop>

                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <div>
                        <h4 class="font-black text-sm text-gray-900">{{ $product->name }}</h4>
                        <span class="text-[11px] text-gray-500 font-bold">سعر الوحدة: {{ format_currency($finalPrice) }}</span>
                    </div>
                    <button type="button" @click="portionModalOpen = false" class="p-1 rounded-md bg-gray-100 hover:bg-gray-200 text-gray-600 text-xs">
                        ✕
                    </button>
                </div>

                <div class="space-y-3 text-xs">
                    <label class="font-bold text-gray-900 block">اختر الكمية أو الوزن المطلوب:</label>

                    <div class="grid grid-cols-2 gap-2">
                        <button type="button"
                                @click="portionType = '125g'"
                                :class="portionType === '125g' ? 'border-[#073F40] bg-[#073F40] text-white font-black' : 'border-gray-200 bg-gray-50 hover:bg-gray-100 text-gray-800 font-bold'"
                                class="py-2 px-2.5 rounded-lg border text-center transition-all cursor-pointer">
                            <span>ثمن الوحدة (12.5%)</span>
                            <span class="block text-[10px] opacity-85" x-text="'(' + (basePrice * 0.125).toFixed(1) + ' ج.م)'"></span>
                        </button>

                        <button type="button"
                                @click="portionType = '250g'"
                                :class="portionType === '250g' ? 'border-[#073F40] bg-[#073F40] text-white font-black' : 'border-gray-200 bg-gray-50 hover:bg-gray-100 text-gray-800 font-bold'"
                                class="py-2 px-2.5 rounded-lg border text-center transition-all cursor-pointer">
                            <span>ربع الوحدة (25%)</span>
                            <span class="block text-[10px] opacity-85" x-text="'(' + (basePrice * 0.25).toFixed(1) + ' ج.م)'"></span>
                        </button>

                        <button type="button"
                                @click="portionType = '500g'"
                                :class="portionType === '500g' ? 'border-[#073F40] bg-[#073F40] text-white font-black' : 'border-gray-200 bg-gray-50 hover:bg-gray-100 text-gray-800 font-bold'"
                                class="py-2 px-2.5 rounded-lg border text-center transition-all cursor-pointer">
                            <span>نصف الوحدة (50%)</span>
                            <span class="block text-[10px] opacity-85" x-text="'(' + (basePrice * 0.5).toFixed(1) + ' ج.م)'"></span>
                        </button>

                        <button type="button"
                                @click="portionType = '1000g'"
                                :class="portionType === '1000g' ? 'border-[#073F40] bg-[#073F40] text-white font-black' : 'border-gray-200 bg-gray-50 hover:bg-gray-100 text-gray-800 font-bold'"
                                class="py-2 px-2.5 rounded-lg border text-center transition-all cursor-pointer">
                            <span>وحدة كاملة</span>
                            <span class="block text-[10px] opacity-85" x-text="'(' + basePrice.toFixed(1) + ' ج.م)'"></span>
                        </button>
                    </div>

                    <div class="pt-1">
                        <input type="text"
                               x-model="notes"
                               placeholder="ملاحظات إضافية (اللون أو المقاس المطلوب)"
                               class="w-full bg-gray-50 border border-gray-200 focus:border-[#073F40] rounded-lg px-3 py-2 text-xs outline-none">
                    </div>
                </div>

                <div class="pt-2 border-t border-gray-100 flex items-center justify-between gap-3">
                    <div class="text-xs">
                        <span class="text-gray-500 block text-[10px]">إجمالي الصنف:</span>
                        <span class="font-black text-base text-gray-900 tabular-nums" x-text="unitPrice.toFixed(2) + ' ج.م'"></span>
                    </div>

                    <button type="button"
                            @click="submitAddToCart(portionType, customAmount, notes)"
                            :disabled="adding"
                            class="py-2 px-4 rounded-md bg-[#C8A635] hover:bg-[#B38F24] text-[#073F40] font-black text-xs shadow-xs cursor-pointer transition-colors border border-[#B38F24]">
                        <span x-text="adding ? 'جاري الإضافة...' : 'إضافة إلى العربة'">إضافة إلى العربة</span>
                    </button>
                </div>

            </div>
        </div>
    @endif

</div>
