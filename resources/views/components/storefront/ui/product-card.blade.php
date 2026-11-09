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
@endphp

<div class="group relative bg-white rounded-2xl border border-gray-100 hover:border-red-400 shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col h-full overflow-hidden hover:-translate-y-1"
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

    <!-- Badges (Discount & Weighted & Stock) -->
    <div class="absolute top-2.5 right-2.5 z-10 flex flex-col gap-1.5 items-start">
        @if($hasDiscount && $discountPercent > 0)
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-black bg-gradient-to-r from-red-600 to-orange-500 text-white shadow-md">
                خصم {{ $discountPercent }}%
            </span>
        @endif
        @if($isWeighted)
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-black bg-amber-400 text-gray-950 shadow-xs">
                بوحدة قياس
            </span>
        @endif
        @if($isQuoteOnly)
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-primary-50 text-primary-900 border border-primary-100">السعر عند الاستفسار</span>
        @elseif(!$isInStock)
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-gray-900/85 text-white backdrop-blur-xs">
                نفد مؤقتاً
            </span>
        @elseif($product->featured)
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-black bg-amber-500 text-white shadow-xs">
                مميز
            </span>
        @endif
    </div>

    <!-- Wishlist Button -->
    <button type="button"
            onclick="window.toggleWishlist({{ $product->id }}, this)"
            class="absolute top-2.5 left-2.5 z-10 w-8 h-8 rounded-full bg-white/90 backdrop-blur-xs text-gray-500 hover:text-red-600 hover:bg-white shadow-sm flex items-center justify-center transition-all duration-200 cursor-pointer"
            title="إضافة للمفضلة">
        <svg class="w-4 h-4 transition-transform active:scale-125" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
        </svg>
    </button>

    <!-- Product Image with Link -->
    <a href="{{ route('product.details', $product->id) }}" class="relative block pt-[80%] overflow-hidden bg-gradient-to-b from-orange-50/30 to-red-50/20">
        @if($product->image_url)
            <img src="{{ $product->image_url }}"
                 alt="{{ $product->name }}"
                 loading="lazy"
                 class="absolute inset-0 w-full h-full object-contain p-3 group-hover:scale-108 transition-transform duration-500 ease-out" />
        @else
            <img src="{{ asset('assets/site/img/horus-product-placeholder.svg') }}"
                 alt="{{ $product->name }}"
                 loading="lazy"
                 class="absolute inset-0 w-full h-full object-cover transition-transform duration-500 ease-out" />
        @endif
    </a>

    <!-- Card Content -->
    <div class="p-3.5 sm:p-4 flex flex-col flex-grow justify-between gap-3">
        <div>
            <!-- Category Tag -->
            @if($product->category)
                <a href="{{ route('category.product', $product->category->id) }}"
                   class="text-[11px] font-bold text-orange-700 hover:text-red-700 transition-colors inline-block mb-1">
                    {{ $product->category->name }}
                </a>
            @endif

            <!-- Product Name -->
            <h3 class="font-bold text-xs sm:text-sm text-gray-900 group-hover:text-red-600 transition-colors line-clamp-2 leading-snug">
                <a href="{{ route('product.details', $product->id) }}">
                    {{ $product->name }}
                </a>
            </h3>
        </div>

        <div>
            <!-- Pricing Row -->
            @if($isQuoteOnly)
                <div class="mb-3 text-sm font-black text-primary-800">اسألنا عن السعر والتوافر</div>
            @else
            <div class="flex items-baseline justify-between mb-3">
                <div class="flex items-baseline gap-2">
                    <span class="text-base sm:text-lg font-black text-red-600 tabular-nums">
                        {{ format_currency($finalPrice) }}
                    </span>
                    @if($hasDiscount)
                        <span class="text-xs text-gray-400 line-through tabular-nums">
                            {{ format_currency($product->price) }}
                        </span>
                    @endif
                </div>
                @if($isWeighted)
                    <span class="text-[10px] text-gray-500 font-bold bg-gray-100 px-1.5 py-0.5 rounded">لكل كجم</span>
                @endif
            </div>
            @endif

            <!-- Add to Cart Action Button -->
            @if($isQuoteOnly)
                <a href="https://wa.me/{{ $storeWhatsApp }}?text={{ urlencode('مرحباً حورس، أريد الاستفسار عن السعر والتوافر: ' . $product->name) }}" target="_blank" rel="noopener noreferrer"
                   class="w-full py-2.5 px-3 rounded-xl bg-primary-800 hover:bg-primary-900 text-white text-xs font-black transition-all shadow-md flex items-center justify-center gap-1.5">
                    استفسر عبر واتساب
                </a>
@elseif($isInStock)
                <button type="button"
                        @click="handleAddClick()"
                        :disabled="adding"
                        class="w-full py-2.5 px-3 rounded-xl bg-gradient-to-r from-red-600 to-orange-500 hover:from-red-700 hover:to-orange-600 active:scale-[0.98] text-white text-xs font-black transition-all shadow-md hover:shadow-orange-500/25 flex items-center justify-center gap-1.5 cursor-pointer">
                    <svg x-show="!adding" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    <svg x-show="adding" x-cloak class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                    <span x-text="adding ? 'جاري الإضافة...' : (isWeighted ? 'تحديد الوزن والإضافة' : 'أضف للسلة')">
                        {{ $isWeighted ? 'تحديد الوزن والإضافة' : 'أضف للسلة' }}
                    </span>
                </button>
            @else
                <a href="https://wa.me/{{ $storeWhatsApp }}?text={{ urlencode('مرحباً حورس، أريد الاستفسار عن التوافر: ' . $product->name) }}" target="_blank" rel="noopener noreferrer"
                   class="w-full py-2.5 px-3 rounded-xl bg-gray-100 hover:bg-emerald-50 hover:text-emerald-800 text-gray-600 text-xs font-bold transition-colors text-center block">
                    اسأل عن التوافر عبر واتساب
                </a>
            @endif
        </div>
    </div>

    <!-- Quick Portion / Weight Modal for Weighted Products in Cards -->
    @if($isWeighted && !$isQuoteOnly)
        <div x-show="portionModalOpen"
             x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-xs"
             @click.self="portionModalOpen = false"
             @keydown.escape.window="portionModalOpen = false">

            <div class="bg-white rounded-3xl max-w-sm w-full p-5 sm:p-6 shadow-2xl border border-orange-200 space-y-4 text-right"
                 dir="rtl"
                 @click.stop>

                <div class="flex items-center justify-between border-b border-orange-100 pb-3">
                    <div>
                        <h4 class="font-black text-sm text-gray-900">{{ $product->name }}</h4>
                        <span class="text-[11px] text-gray-500 font-bold">سعر الوحدة: {{ format_currency($finalPrice) }}</span>
                    </div>
                    <button type="button" @click="portionModalOpen = false" class="p-1 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-600">
                        ✕
                    </button>
                </div>

                <div class="space-y-3 text-xs">
                    <label class="font-black text-gray-900 block">اختر الكمية المطلوبة:</label>

                    <div class="grid grid-cols-2 gap-2">
                        <button type="button"
                                @click="portionType = '125g'"
                                :class="portionType === '125g' ? 'border-red-600 bg-red-600 text-white font-black' : 'border-gray-200 bg-gray-50 hover:bg-orange-50 text-gray-800 font-bold'"
                                class="py-2 px-2.5 rounded-xl border-2 text-center transition-all cursor-pointer">
                                <span>ثمن الوحدة (12.5%)</span>
                            <span class="block text-[10px] opacity-85" x-text="'(' + (basePrice * 0.125).toFixed(1) + ' ج.م)'"></span>
                        </button>

                        <button type="button"
                                @click="portionType = '250g'"
                                :class="portionType === '250g' ? 'border-red-600 bg-red-600 text-white font-black' : 'border-gray-200 bg-gray-50 hover:bg-orange-50 text-gray-800 font-bold'"
                                class="py-2 px-2.5 rounded-xl border-2 text-center transition-all cursor-pointer">
                                <span>ربع الوحدة (25%)</span>
                            <span class="block text-[10px] opacity-85" x-text="'(' + (basePrice * 0.25).toFixed(1) + ' ج.م)'"></span>
                        </button>

                        <button type="button"
                                @click="portionType = '500g'"
                                :class="portionType === '500g' ? 'border-red-600 bg-red-600 text-white font-black' : 'border-gray-200 bg-gray-50 hover:bg-orange-50 text-gray-800 font-bold'"
                                class="py-2 px-2.5 rounded-xl border-2 text-center transition-all cursor-pointer">
                                <span>نصف الوحدة (50%)</span>
                            <span class="block text-[10px] opacity-85" x-text="'(' + (basePrice * 0.5).toFixed(1) + ' ج.م)'"></span>
                        </button>

                        <button type="button"
                                @click="portionType = '1000g'"
                                :class="portionType === '1000g' ? 'border-red-600 bg-red-600 text-white font-black' : 'border-gray-200 bg-gray-50 hover:bg-orange-50 text-gray-800 font-bold'"
                                class="py-2 px-2.5 rounded-xl border-2 text-center transition-all cursor-pointer">
                                <span>وحدة كاملة</span>
                            <span class="block text-[10px] opacity-85" x-text="'(' + basePrice.toFixed(1) + ' ج.م)'"></span>
                        </button>
                    </div>

                    <!-- Custom Price in modal -->
                    <div class="pt-1">
                        <button type="button"
                                @click="portionType = 'custom_price'"
                                :class="portionType === 'custom_price' ? 'border-orange-500 bg-orange-50 text-orange-950 font-black' : 'border-gray-200 bg-gray-50 text-gray-700 hover:bg-orange-50 font-bold'"
                                class="w-full py-2 px-3 rounded-xl border-2 flex items-center justify-between text-xs transition-all cursor-pointer">
                            <span>طلب بمبلغ مالي محدد</span>
                            <span class="text-[10px] font-bold text-orange-700">بـ 20 ج / 50 ج ↓</span>
                        </button>

                        <div x-show="portionType === 'custom_price'" x-cloak class="mt-2 p-2.5 rounded-xl bg-orange-50/80 border border-orange-200 flex items-center gap-2">
                            <input type="number"
                                   x-model.number="customAmount"
                                   placeholder="20"
                                   min="5"
                                   class="w-24 bg-white border border-orange-300 rounded-lg px-2.5 py-1 text-xs font-black text-gray-900 outline-none">
                            <span class="font-bold text-gray-700">ج.م</span>
                            <span class="text-[10px] text-gray-500 mr-auto" x-show="approxGrams > 0">
                                كمية تقريبية: <strong class="text-orange-800 font-black" x-text="approxGrams"></strong> {{ $product->unit_type ?? 'وحدة' }}
                            </span>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="pt-1">
                        <input type="text"
                               x-model="notes"
                               placeholder="ملاحظات التجهيز (مثال: اللون أو المقاس المطلوب)"
                               class="w-full bg-gray-50 border border-gray-200 focus:border-red-600 rounded-xl px-3 py-2 text-xs outline-none">
                    </div>
                </div>

                <!-- Modal Actions -->
                <div class="pt-2 border-t border-orange-100 flex items-center justify-between gap-3">
                    <div class="text-xs">
                        <span class="text-gray-500 block text-[10px]">إجمالي الصنف:</span>
                        <span class="font-black text-base text-red-600 tabular-nums" x-text="unitPrice.toFixed(2) + ' ج.م'"></span>
                    </div>

                    <button type="button"
                            @click="submitAddToCart(portionType, customAmount, notes)"
                            :disabled="adding"
                            class="py-2.5 px-5 rounded-xl bg-gradient-to-r from-red-600 to-orange-500 text-white font-black text-xs shadow-md hover:from-red-700 hover:to-orange-600 cursor-pointer">
                        <span x-text="adding ? 'جاري الإضافة...' : 'تأكيد وإضافة للسلة'">تأكيد وإضافة للسلة</span>
                    </button>
                </div>

            </div>
        </div>
    @endif
</div>
