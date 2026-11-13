{{-- resources/views/storefront/cart.blade.php --}}
@extends('layouts.storefront')

@section('title', 'سلة مشترياتك | مؤسسة حورس للتوريدات والتشطيبات')

@section('content')
<div class="py-6 sm:py-10 bg-gradient-to-b from-canvas-subtle via-canvas-base to-canvas-base min-h-screen" 
     x-data="{
         items: {{ json_encode($items) }},
         totals: {{ json_encode($totals) }},
         deliveryType: '{{ $deliveryType }}',
         updating: false,
         branches: {{ json_encode($branches) }},
         
         // WhatsApp Modal State
         whatsappModalOpen: false,
         waSubmitting: false,
         waCustomerName: '{{ auth()->user()?->name ?? '' }}',
         waCustomerPhone: '{{ auth()->user()?->phone ?? '' }}',
         waBranchId: '{{ $branches->first()?->id ?? 1 }}',
         waAddress: '{{ auth()->user()?->default_address ?? '' }}',
         waNotes: '',
         waErrors: {},

         updateQty(key, newQty) {
             if (newQty < 1) return;
             this.updating = true;
             fetch('{{ route('update.cart.quantity') }}', {
                 method: 'POST',
                 headers: {
                     'Content-Type': 'application/json',
                     'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                     'Accept': 'application/json'
                 },
                 body: JSON.stringify({ key: key, quantity: newQty, delivery_type: this.deliveryType })
             })
             .then(res => res.json())
             .then(data => {
                 this.updating = false;
                 if (data.success) {
                     const idx = this.items.findIndex(i => i.key === key);
                     if (idx !== -1) {
                         this.items[idx].quantity = newQty;
                         this.items[idx].total_usd = this.items[idx].price_usd * newQty;
                     }
                     this.fetchTotals();
                     window.dispatchEvent(new CustomEvent('cart-updated', { detail: { count: data.cart_count } }));
                     toastr.success(data.message || 'تم تحديث الكمية بنجاح');
                 } else {
                     toastr.error(data.message || 'تعذر تحديث الكمية');
                 }
             })
             .catch(() => {
                 this.updating = false;
                 toastr.error('حدث خطأ في الاتصال، يرجى المحاولة لاحقاً');
             });
         },

         removeItem(key) {
             window.horusConfirm('حذف المنتج من السلة', 'هل تريد بالتأكيد إزالة هذا الصنف من سلة المشتريات؟', () => {
                 this.updating = true;
                 fetch('{{ route('removefromcart') }}', {
                     method: 'POST',
                     headers: {
                         'Content-Type': 'application/json',
                         'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                         'Accept': 'application/json'
                     },
                     body: JSON.stringify({ key: key, delivery_type: this.deliveryType })
                 })
                 .then(res => res.json())
                 .then(data => {
                     this.updating = false;
                     this.items = this.items.filter(i => i.key !== key);
                     this.totals = data.totals;
                     window.dispatchEvent(new CustomEvent('cart-updated', { detail: { count: data.cart_count } }));
                     toastr.success(data.message || 'تم حذف الصنف من السلة');
                 })
                 .catch(() => {
                     this.updating = false;
                     toastr.error('حدث خطأ أثناء الحذف');
                 });
             }, null, { confirmText: 'نعم، احذف', cancelText: 'تراجع', icon: '🗑️' });
         },

         clearAll() {
             window.horusConfirm('تفريغ السلة بالكامل', 'هل أنت متأكد من رغبتك في تفريغ سلة المشتريات بالكامل؟', () => {
                 this.updating = true;
                 fetch('{{ route('clearcart') }}', {
                     method: 'POST',
                     headers: {
                         'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                         'Accept': 'application/json'
                     }
                 })
                 .then(res => res.json())
                 .then(data => {
                     this.updating = false;
                     this.items = [];
                     this.totals = data.totals;
                     window.dispatchEvent(new CustomEvent('cart-updated', { detail: { count: 0 } }));
                     toastr.info('تم تفريغ السلة بنجاح');
                 })
                 .catch(() => {
                     this.updating = false;
                     toastr.error('حدث خطأ أثناء تفريغ السلة');
                 });
             }, null, { confirmText: 'تفريغ السلة', cancelText: 'إلغاء', icon: '⚠️' });
         },

         fetchTotals() {
             fetch('{{ route('cart.payload') }}?delivery_type=' + this.deliveryType)
                 .then(res => res.json())
                 .then(data => {
                     this.totals = data.totals;
                 });
         },

         changeDeliveryType(type) {
             this.deliveryType = type;
             this.fetchTotals();
         },

         couponCodeInput: '',
         couponApplying: false,

         applyCoupon() {
             if (!this.couponCodeInput.trim()) {
                 toastr.warning('يرجى كتابة كود الكوبون أولاً');
                 return;
             }
             this.couponApplying = true;
             fetch('{{ route('cart.apply-coupon') }}', {
                 method: 'POST',
                 headers: {
                     'Content-Type': 'application/json',
                     'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                     'Accept': 'application/json'
                 },
                 body: JSON.stringify({ coupon_code: this.couponCodeInput.trim() })
             })
             .then(res => res.json())
             .then(data => {
                 this.couponApplying = false;
                 if (data.success) {
                     this.totals = data.totals;
                     this.couponCodeInput = '';
                     toastr.success(data.message || 'تم تفعيل كود الخصم بنجاح');
                 } else {
                     toastr.error(data.message || 'كوبون الخصم غير صالح');
                 }
             })
             .catch(() => {
                 this.couponApplying = false;
                 toastr.error('حدث خطأ أثناء تطبيق الكوبون');
             });
         },

         applySpecificCoupon(code) {
             this.couponCodeInput = code;
             this.applyCoupon();
         },

         removeCoupon() {
             this.couponApplying = true;
             fetch('{{ route('cart.remove-coupon') }}', {
                 method: 'POST',
                 headers: {
                     'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                     'Accept': 'application/json'
                 }
             })
             .then(res => res.json())
             .then(data => {
                 this.couponApplying = false;
                 if (data.success) {
                     this.totals = data.totals;
                     toastr.info(data.message || 'تم حذف الكوبون');
                 }
             })
             .catch(() => {
                 this.couponApplying = false;
                 toastr.error('حدث خطأ أثناء إزالة الكوبون');
             });
         },

         openWhatsAppModal() {
             if (this.items.length === 0) {
                 toastr.warning('سلة الشراء فارغة، أضف منتجات أولاً للمتابعة.');
                 return;
             }
             this.waErrors = {};
             this.whatsappModalOpen = true;
         },

         submitWhatsAppOrder() {
             this.waErrors = {};
             
             // Client-side quick check
             if (!this.waCustomerName.trim() || this.waCustomerName.trim().length < 3) {
                 this.waErrors.name = 'يرجى كتابة الاسم بالكامل (3 أحرف على الأقل).';
             }
             if (!this.waCustomerPhone.trim()) {
                 this.waErrors.phone = 'يرجى إدخال رقم الهاتف للتواصل عبر واتساب.';
             } else {
                 const cleanPhone = this.waCustomerPhone.replace(/[^0-9]/g, '');
                 if (!/^(01)[0-2,5]{1}[0-9]{8}$/.test(cleanPhone)) {
                     this.waErrors.phone = 'يرجى إدخال رقم هاتف مصري صحيح يبدأ بـ 01 (مثال: 01012345678).';
                 }
             }

             if (this.deliveryType === 'delivery' && (!this.waAddress.trim() || this.waAddress.trim().length < 5)) {
                 this.waErrors.address = 'يرجى كتابة عنوان التوصيل بالتفصيل (المنطقة، الشارع، رقم العقار).';
             }

             if (Object.keys(this.waErrors).length > 0) {
                 toastr.error('يرجى تصحيح الأخطاء الموضحة بالنموذج.');
                 return;
             }

             this.waSubmitting = true;

             fetch('{{ route('checkout.whatsapp') }}', {
                 method: 'POST',
                 headers: {
                     'Content-Type': 'application/json',
                     'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                     'Accept': 'application/json'
                 },
                 body: JSON.stringify({
                     name: this.waCustomerName.trim(),
                     phone: this.waCustomerPhone.trim(),
                     delivery_type: this.deliveryType,
                     branch_id: this.waBranchId,
                     address: this.deliveryType === 'delivery' ? this.waAddress.trim() : null,
                     notes: this.waNotes.trim()
                 })
             })
             .then(async (res) => {
                 const data = await res.json().catch(() => ({}));
                 if (res.ok && data.success) {
                     window.dispatchEvent(new CustomEvent('cart-updated', { detail: { count: 0 } }));
                     toastr.success(data.message || 'تم تسجيل طلبك وحجز المنتجات بنجاح!');
                     
                     // Open WhatsApp in new window/tab
                     if (data.whatsapp_url) {
                         window.open(data.whatsapp_url, '_blank');
                     }

                     // Redirect current window to confirmation/tracking page
                     setTimeout(() => {
                         window.location.href = data.redirect_url;
                     }, 500);
                 } else {
                     this.waSubmitting = false;
                     if (data.errors) {
                         this.waErrors = data.errors;
                         const firstErr = Object.values(data.errors).flat()[0];
                         toastr.error(firstErr || 'يرجى مراجعة البيانات المدخلة');
                     } else {
                         toastr.error(data.message || 'تعذر إتمام الطلب، تأكد من توفر المخزون');
                     }
                 }
             })
             .catch(() => {
                 this.waSubmitting = false;
                 toastr.error('حدث خطأ في الاتصال، يرجى المحاولة مرة أخرى.');
             });
         }
     }">
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        
        <!-- Breadcrumbs -->
        <nav class="flex items-center gap-2 text-xs font-bold text-charcoal-muted" aria-label="Breadcrumb">
            <a href="{{ route('site.home') }}" class="hover:text-primary-700 transition-colors">الرئيسية</a>
            <span>/</span>
            <span class="text-charcoal-heading font-black">سلة المشتريات</span>
        </nav>

        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-surface-border">
            <div class="space-y-1">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-primary-50 text-primary-800 border border-primary-200 flex items-center justify-center shadow-xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-black text-charcoal-heading flex items-center gap-2.5">
                            <span>سلة المشتريات</span>
                            <span class="text-xs font-black px-2.5 py-1 rounded-full bg-primary-100 text-primary-900 border border-primary-200" x-show="items.length > 0">
                                <span x-text="items.length"></span> أصناف
                            </span>
                        </h1>
                        <p class="text-xs text-charcoal-muted">خامات ومواد التشطيب والديكور المعتمدة من مؤسسة حورس</p>
                    </div>
                </div>
            </div>

            <template x-if="items.length > 0">
                <div class="flex items-center gap-3">
                    <button type="button" 
                            @click="clearAll()"
                            class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-black text-red-600 hover:text-red-700 hover:bg-red-50 border border-transparent hover:border-red-200 transition-all cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        <span>تفريغ السلة</span>
                    </button>
                </div>
            </template>
        </div>

        <!-- Cart Content -->
        <template x-if="items.length > 0">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                
                <!-- Items Table / List (8 cols) -->
                <div class="lg:col-span-8 bg-white rounded-3xl p-4 sm:p-6 border border-surface-border shadow-sm space-y-4">
                    <div class="divide-y divide-gray-100">
                        <template x-for="item in items" :key="item.key">
                            <div class="py-4 first:pt-0 last:pb-0 flex flex-col sm:flex-row items-center sm:items-start justify-between gap-4 transition-colors">
                                
                                <!-- Item Info -->
                                <div class="flex items-center gap-3.5 flex-1 min-w-0 w-full sm:w-auto">
                                    <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-canvas-subtle border border-surface-border p-1.5 flex items-center justify-center shrink-0 overflow-hidden shadow-2xs">
                                        <template x-if="item.image">
                                            <img :src="item.image" :alt="item.name" class="w-full h-full object-contain">
                                        </template>
                                        <template x-if="!item.image">
                                            <div class="w-8 h-8 rounded-xl bg-primary-100 text-primary-800 flex items-center justify-center">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                                </svg>
                                            </div>
                                        </template>
                                    </div>

                                    <div class="flex-1 min-w-0 space-y-1">
                                        <template x-if="item.type === 'bundle'">
                                            <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-black bg-accent-100 text-accent-700 border border-accent-300">
                                                عرض مجمع توفيري ✨
                                            </span>
                                        </template>

                                        <h3 class="font-black text-sm sm:text-base text-charcoal-heading truncate" x-text="item.name"></h3>

                                        <!-- Weight / Portion Badge -->
                                        <template x-if="item.portion_label">
                                            <div class="flex items-center gap-1.5">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[10px] font-black bg-primary-50 text-primary-800 border border-primary-200" x-text="item.portion_label"></span>
                                            </div>
                                        </template>

                                        <!-- Preparation / Notes -->
                                        <template x-if="item.notes">
                                            <div class="text-[11px] text-charcoal-secondary bg-canvas-subtle px-2.5 py-1 rounded-lg border border-surface-border inline-block">
                                                <span class="font-bold text-charcoal-heading">ملاحظات:</span> <span x-text="item.notes"></span>
                                            </div>
                                        </template>
                                        
                                        <div class="text-xs text-charcoal-muted">
                                            سعر الوحدة: 
                                            <span class="font-black text-primary-800 tabular-nums" x-text="item.formatted_price_usd"></span>
                                        </div>

                                        <!-- Limited Stock Alert -->
                                        <template x-if="item.is_limited_stock">
                                            <span class="inline-flex items-center gap-1 text-[11px] text-amber-800 bg-amber-50 px-2.5 py-0.5 rounded-lg border border-amber-200 font-bold">
                                                كمية محدودة في المخزون (الحد الأقصى: 1)
                                            </span>
                                        </template>
                                    </div>
                                </div>

                                <!-- Quantity Stepper & Line Total -->
                                <div class="flex items-center justify-between sm:justify-end gap-5 w-full sm:w-auto pt-2 sm:pt-0 border-t sm:border-t-0 border-gray-100">
                                    
                                    <!-- Stepper -->
                                    <div class="flex items-center border border-surface-border rounded-xl bg-canvas-subtle p-1 shadow-2xs">
                                        <button type="button" 
                                                @click="updateQty(item.key, item.quantity - 1)"
                                                :disabled="updating || item.quantity <= 1"
                                                class="w-7 h-7 flex items-center justify-center rounded-lg bg-white hover:bg-primary-50 text-charcoal-heading font-black disabled:opacity-40 transition-colors shadow-2xs cursor-pointer">
                                            -
                                        </button>
                                        
                                        <span class="w-9 text-center font-black text-xs text-charcoal-heading tabular-nums" x-text="item.quantity"></span>
                                        
                                        <button type="button" 
                                                @click="updateQty(item.key, item.quantity + 1)"
                                                :disabled="updating || (item.is_limited_stock && item.quantity >= 1) || (item.quantity >= item.available_stock)"
                                                class="w-7 h-7 flex items-center justify-center rounded-lg bg-white hover:bg-primary-50 text-charcoal-heading font-black disabled:opacity-40 transition-colors shadow-2xs cursor-pointer">
                                            +
                                        </button>
                                    </div>

                                    <!-- Line Total -->
                                    <div class="text-left min-w-[95px]">
                                        <span class="text-sm sm:text-base font-black text-primary-800 tabular-nums" 
                                              x-text="(item.price_usd * item.quantity).toFixed(2) + ' ج.م'"></span>
                                    </div>

                                    <!-- Delete Button -->
                                    <button type="button" 
                                            @click="removeItem(item.key)"
                                            class="p-2 text-charcoal-muted hover:text-red-600 rounded-xl hover:bg-red-50 transition-colors cursor-pointer"
                                            title="حذف الصنف">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>

                            </div>
                        </template>
                    </div>

                    <!-- Return to shopping button -->
                    <div class="pt-4 border-t border-gray-100 flex items-center justify-between">
                        <a href="{{ route('site.products') }}" class="text-xs font-bold text-primary-700 hover:text-primary-800 hover:underline inline-flex items-center gap-1.5">
                            <span>← متابعة التسوق وإضافة خامات أخرى</span>
                        </a>
                        <span class="text-[11px] text-charcoal-muted">توصيل واستلام من فروع حورس</span>
                    </div>
                </div>

                <!-- Order Summary Sidebar (4 cols) -->
                <div class="lg:col-span-4 space-y-5">
                    
                    <!-- Delivery Option Choice -->
                    <div class="bg-white rounded-3xl p-5 border border-surface-border shadow-sm space-y-3">
                        <h3 class="font-black text-xs text-charcoal-heading flex items-center justify-between">
                            <span>طريقة الاستلام المفضلة</span>
                            <span class="text-[10px] text-primary-700 font-bold">حسب اختيارك</span>
                        </h3>
                        <div class="grid grid-cols-2 gap-2 text-xs font-bold">
                            <button type="button" 
                                    @click="changeDeliveryType('delivery')"
                                    :class="deliveryType === 'delivery' ? 'bg-primary-800 text-white shadow-sm font-black border-primary-800' : 'bg-canvas-subtle text-charcoal hover:bg-primary-50 border border-surface-border'"
                                    class="py-2.5 px-3 rounded-2xl flex flex-col items-center gap-1.5 transition-all cursor-pointer">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                                <span>توصيل للمنزل</span>
                            </button>

                            <button type="button" 
                                    @click="changeDeliveryType('pickup')"
                                    :class="deliveryType === 'pickup' ? 'bg-primary-800 text-white shadow-sm font-black border-primary-800' : 'bg-canvas-subtle text-charcoal hover:bg-primary-50 border border-surface-border'"
                                    class="py-2.5 px-3 rounded-2xl flex flex-col items-center gap-1.5 transition-all cursor-pointer">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                                <span>استلام من الفرع</span>
                            </button>
                        </div>

                        <!-- Pickup Notice -->
                        <div x-show="deliveryType === 'pickup'" class="text-[11px] text-emerald-800 bg-emerald-50 p-2.5 rounded-xl border border-emerald-200 font-bold">
                            الاستلام متاح من فرع حدائق الأهرام - بوابة حورس.
                        </div>
                    </div>

                    <!-- Coupon Card -->
                    <div class="bg-white rounded-3xl p-5 border border-surface-border shadow-sm space-y-3">
                        <h3 class="font-black text-xs text-charcoal-heading flex items-center justify-between">
                            <span>كود الخصم (كوبون)</span>
                            <span class="text-[10px] text-accent-700 bg-accent-50 px-2 py-0.5 rounded-full font-bold">توفير إضافي</span>
                        </h3>

                        <!-- Coupon Form when NOT applied -->
                        <template x-if="!totals.has_coupon">
                            <div class="space-y-3">
                                <div class="flex items-center gap-2">
                                    <input type="text" 
                                           x-model="couponCodeInput" 
                                           placeholder="أدخل كود الخصم هنا..." 
                                           class="flex-1 uppercase font-black text-xs bg-canvas-subtle border border-surface-border rounded-xl px-3 py-2.5 focus:outline-none focus:border-primary-600 transition-colors"
                                           @keydown.enter.prevent="applyCoupon()">
                                    <button type="button" 
                                            @click="applyCoupon()"
                                            :disabled="couponApplying"
                                            class="px-4 py-2.5 bg-primary-800 hover:bg-primary-900 text-white text-xs font-black rounded-xl transition-all disabled:opacity-50 cursor-pointer shrink-0">
                                        <span x-show="!couponApplying">تطبيق</span>
                                        <span x-show="couponApplying">...</span>
                                    </button>
                                </div>

                                @if(isset($availableCoupons) && $availableCoupons->isNotEmpty())
                                    <div class="pt-2 border-t border-gray-100 space-y-2">
                                        <span class="text-[11px] font-bold text-gray-500 block">أو اختر كوبون متوفر للتطبيق بنقرة واحدة:</span>
                                        <div class="space-y-1.5">
                                            @foreach($availableCoupons as $avail)
                                                <div class="flex items-center justify-between gap-2 p-2 rounded-xl bg-amber-50/50 border border-amber-200/70 text-xs hover:border-[#C8A635] transition-all">
                                                    <div class="min-w-0">
                                                        <div class="flex items-center gap-1.5">
                                                            <span class="font-mono font-black px-1.5 py-0.5 rounded bg-[#073F40] text-[#E2BF52] text-[10px]">{{ $avail->code }}</span>
                                                            <span class="font-black text-[#073F40] text-xs">{{ $avail->formatted_discount }} خصم</span>
                                                        </div>
                                                        <p class="text-[10px] text-gray-600 truncate mt-0.5">{{ $avail->title ?? $avail->description }}</p>
                                                    </div>
                                                    <button type="button" 
                                                            @click="applySpecificCoupon('{{ $avail->code }}')"
                                                            :disabled="couponApplying"
                                                            class="px-3 py-1 rounded-lg bg-[#C8A635] hover:bg-[#B38F24] text-[#073F40] text-[11px] font-black shrink-0 transition-all cursor-pointer shadow-2xs">
                                                        تطبيق
                                                    </button>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </template>

                        <!-- Applied Coupon State -->
                        <template x-if="totals.has_coupon">
                            <div class="flex items-center justify-between p-3 rounded-2xl bg-emerald-50 border border-emerald-200">
                                <div class="flex items-center gap-2">
                                    <span class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-xs font-black">✓</span>
                                    <div>
                                        <div class="text-xs font-black text-emerald-900 flex items-center gap-1.5">
                                            <span>كود:</span>
                                            <span class="tracking-wider uppercase" x-text="totals.coupon_code"></span>
                                        </div>
                                        <div class="text-[10px] text-emerald-700 font-bold">
                                            خصم: <span x-text="totals.formatted_discount"></span>
                                        </div>
                                    </div>
                                </div>
                                <button type="button" 
                                        @click="removeCoupon()" 
                                        class="text-xs text-red-600 hover:text-red-800 font-bold p-1 cursor-pointer"
                                        title="إلغاء الكوبون">
                                    ✕
                                </button>
                            </div>
                        </template>
                    </div>

                    <!-- Financial Summary & TWO Checkout Paths -->
                    <div class="bg-white rounded-3xl p-6 border border-surface-border shadow-sm space-y-4">
                        <h3 class="font-black text-sm text-charcoal-heading border-b border-gray-100 pb-3 flex items-center justify-between">
                            <span>ملخص الحساب</span>
                            <span class="text-[11px] text-charcoal-muted font-normal">الأسعار تشمل الضريبة</span>
                        </h3>

                        <div class="space-y-2.5 text-xs">
                            <div class="flex items-center justify-between text-charcoal-secondary">
                                <span>المجموع الفرعي</span>
                                <span class="font-bold text-charcoal-heading tabular-nums" x-text="totals.formatted_subtotal"></span>
                            </div>

                            <!-- Discount Row -->
                            <template x-if="totals.has_coupon && totals.discount_amount > 0">
                                <div class="flex items-center justify-between text-emerald-700 font-bold bg-emerald-50 p-2 rounded-xl border border-emerald-100">
                                    <span>خصم الكوبون (<span x-text="totals.coupon_code"></span>)</span>
                                    <span class="tabular-nums">- <span x-text="totals.formatted_discount"></span></span>
                                </div>
                            </template>

                            <div class="flex items-center justify-between text-charcoal-secondary">
                                <span>رسوم التوصيل</span>
                                <span class="font-bold tabular-nums" 
                                      :class="totals.delivery_fee == 0 ? 'text-emerald-700' : 'text-charcoal-heading'"
                                      x-text="totals.formatted_delivery_fee"></span>
                            </div>

                            <div class="border-t border-gray-100 pt-3 flex items-center justify-between text-base font-black text-charcoal-heading">
                                <span>الإجمالي النهائي</span>
                                <span class="text-xl text-primary-800 tabular-nums" x-text="totals.formatted_total"></span>
                            </div>
                            @if((float) settings('delivery_fee', 0) <= 0)
                                <p class="text-[10px] text-charcoal-muted leading-relaxed">تكلفة التوصيل الفعلية يحددها الفرع معك بدقة حسب العنوان قبل التحصيل.</p>
                            @endif
                        </div>

                        <!-- TWO DISTINCT ORDER PATHS -->
                        <div class="pt-3 space-y-2.5">
                            
                            <!-- Path 1: System Online Checkout -->
                            <a :href="'{{ route('checkout.index') }}?delivery_type=' + deliveryType" 
                               class="w-full py-3.5 px-4 rounded-2xl bg-primary-800 hover:bg-primary-900 active:scale-[0.98] text-white font-black text-xs sm:text-sm text-center shadow-md transition-all flex items-center justify-center gap-2 group">
                                <svg class="w-4 h-4 text-accent-300 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>إتمام الطلب أونلاين عبر الموقع</span>
                                <span class="rotate-180 text-xs">←</span>
                            </a>

                            <!-- Path 2: Instant WhatsApp Order with Inventory Reservation -->
                            <button type="button" 
                                    @click="openWhatsAppModal()"
                                    class="w-full py-3.5 px-4 rounded-2xl bg-[#25D366] hover:bg-[#20bd5a] active:scale-[0.98] text-white font-black text-xs sm:text-sm text-center shadow-md shadow-emerald-500/20 transition-all flex items-center justify-center gap-2 cursor-pointer">
                                <svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 24 24">
                                    <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.174.086.275.073.376-.044.101-.116.433-.506.549-.68.116-.173.231-.144.39-.086s1.011.477 1.184.564.289.13.332.202c.045.072.045.419-.099.824z" />
                                </svg>
                                <span>طلب ومتابعة فورية عبر واتساب</span>
                                <span class="text-[10px] bg-white/20 px-1.5 py-0.5 rounded-full font-bold">حجز المخزون</span>
                            </button>

                        </div>

                        <div class="pt-2 flex flex-col gap-1 text-[11px] text-charcoal-muted text-center border-t border-gray-100">
                            <span class="flex items-center justify-center gap-1.5">
                                <span>🔒</span>
                                <span>تسجيل رسمي في النظام • خصم المخزون فورياً • دفع عند الاستلام</span>
                            </span>
                        </div>
                    </div>

                </div>

            </div>
        </template>

        <!-- Empty Cart State -->
        <template x-if="items.length === 0">
            <div class="bg-white rounded-3xl p-10 sm:p-16 border border-surface-border text-center max-w-lg mx-auto shadow-sm space-y-5 my-8">
                <div class="w-20 h-20 rounded-3xl bg-primary-50 text-primary-800 flex items-center justify-center mx-auto border border-primary-200 shadow-inner">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                </div>

                <div class="space-y-1.5">
                    <h2 class="text-xl sm:text-2xl font-black text-charcoal-heading">سلة المشتريات فارغة</h2>
                    <p class="text-xs sm:text-sm text-charcoal-muted leading-relaxed">
                        سلتك فارغة حالياً. تصفح أقسام التشطيب والدهانات والديكور وأضف ما تحتاجه لمشروعك.
                    </p>
                </div>

                <div class="pt-3">
                    <a href="{{ route('site.products') }}" 
                       class="inline-flex items-center gap-2 py-3 px-6 rounded-2xl bg-primary-800 hover:bg-primary-900 text-white font-black text-xs sm:text-sm transition-all hover:scale-105 shadow-md">
                        <span>تصفح المنتجات الآن</span>
                        <span class="rotate-180">←</span>
                    </a>
                </div>
            </div>
        </template>

    </div>

    <!-- WHATSAPP EXPRESS ORDER MODAL -->
    <div x-show="whatsappModalOpen" 
         x-cloak 
         class="fixed inset-0 z-[99999] bg-charcoal-heading/60 backdrop-blur-xs flex items-center justify-center p-4 overflow-y-auto"
         dir="rtl">
        <div @click.away="if (!waSubmitting) whatsappModalOpen = false"
             x-show="whatsappModalOpen"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-7 shadow-2xl border border-surface-border space-y-5 my-8">
            
            <!-- Modal Header -->
            <div class="flex items-start justify-between gap-3 pb-3 border-b border-gray-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-600 border border-emerald-200 flex items-center justify-center text-xl shrink-0">
                        <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                            <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.174.086.275.073.376-.044.101-.116.433-.506.549-.68.116-.173.231-.144.39-.086s1.011.477 1.184.564.289.13.332.202c.045.072.045.419-.099.824z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-black text-charcoal-heading">إتمام الطلب عبر واتساب</h3>
                        <p class="text-[11px] text-charcoal-muted">حجز فوري للمخزون ومتابعة سريعة مع خدمة العملاء</p>
                    </div>
                </div>
                <button type="button" 
                        @click="if (!waSubmitting) whatsappModalOpen = false" 
                        class="p-1.5 rounded-xl text-charcoal-muted hover:text-charcoal hover:bg-gray-100 transition-colors cursor-pointer">
                    ✕
                </button>
            </div>

            <!-- Notice Banner -->
            <div class="p-3 rounded-2xl bg-primary-50 border border-primary-200 text-xs text-primary-900 space-y-1">
                <div class="font-bold flex items-center gap-1.5">
                    <span>⚡</span>
                    <span>كيف يعمل هذا الخيار؟</span>
                </div>
                <p class="text-[11px] text-charcoal-secondary leading-relaxed">
                    يتم تسجيل الطلب في نظام مؤسسة حورس برقم رسمي وخصم المنتجات من المخزون فوراً لضمان عدم نفادها، ثم فتح محادثة واتساب بتفاصيل طلبك لمتابعة التوصيل.
                </p>
            </div>

            <!-- Form Inputs -->
            <form @submit.prevent="submitWhatsAppOrder()" class="space-y-4">
                
                <!-- Full Name -->
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-charcoal-heading">الاسم بالكامل <span class="text-red-600">*</span></label>
                    <input type="text" 
                           x-model="waCustomerName" 
                           placeholder="مثال: أحمد محمود"
                           class="w-full bg-canvas-subtle border rounded-xl px-3.5 py-2.5 text-xs text-charcoal-heading outline-none transition-all"
                           :class="waErrors.name ? 'border-red-500 focus:border-red-600' : 'border-surface-border focus:border-primary-600'">
                    <template x-if="waErrors.name">
                        <p class="text-[11px] text-red-600 font-bold" x-text="Array.isArray(waErrors.name) ? waErrors.name[0] : waErrors.name"></p>
                    </template>
                </div>

                <!-- Phone Number -->
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-charcoal-heading">رقم الهاتف (واتساب) <span class="text-red-600">*</span></label>
                    <input type="tel" 
                           x-model="waCustomerPhone" 
                           placeholder="01012345678 أو الرقم الدولي مع الكود"
                           class="w-full bg-canvas-subtle border rounded-xl px-3.5 py-2.5 text-xs text-charcoal-heading outline-none transition-all dir-ltr text-right font-mono"
                           :class="waErrors.phone ? 'border-red-500 focus:border-red-600' : 'border-surface-border focus:border-primary-600'">
                    <template x-if="waErrors.phone">
                        <p class="text-[11px] text-red-600 font-bold" x-text="Array.isArray(waErrors.phone) ? waErrors.phone[0] : waErrors.phone"></p>
                    </template>
                </div>

                <!-- Delivery Type Toggle -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-charcoal-heading">طريقة الاستلام</label>
                    <div class="grid grid-cols-2 gap-2 text-xs font-bold">
                        <button type="button"
                                @click="deliveryType = 'delivery'; fetchTotals();"
                                :class="deliveryType === 'delivery' ? 'bg-primary-800 text-white font-black' : 'bg-canvas-subtle text-charcoal border border-surface-border'"
                                class="py-2.5 px-3 rounded-xl transition-all cursor-pointer">
                            توصيل للمنزل
                        </button>
                        <button type="button"
                                @click="deliveryType = 'pickup'; fetchTotals();"
                                :class="deliveryType === 'pickup' ? 'bg-primary-800 text-white font-black' : 'bg-canvas-subtle text-charcoal border border-surface-border'"
                                class="py-2.5 px-3 rounded-xl transition-all cursor-pointer">
                            استلام من الفرع
                        </button>
                    </div>
                </div>

                <!-- If Delivery: Address -->
                <div x-show="deliveryType === 'delivery'" class="space-y-1">
                    <label class="block text-xs font-bold text-charcoal-heading">عنوان التوصيل بالتفصيل <span class="text-red-600">*</span></label>
                    <textarea x-model="waAddress" 
                              rows="2"
                              placeholder="المنطقة، الشارع، رقم العقار، الدور، الشقة..."
                              class="w-full bg-canvas-subtle border rounded-xl px-3.5 py-2 text-xs text-charcoal-heading outline-none transition-all resize-none"
                              :class="waErrors.address ? 'border-red-500 focus:border-red-600' : 'border-surface-border focus:border-primary-600'"></textarea>
                    <template x-if="waErrors.address">
                        <p class="text-[11px] text-red-600 font-bold" x-text="Array.isArray(waErrors.address) ? waErrors.address[0] : waErrors.address"></p>
                    </template>
                </div>

                <!-- If Pickup: Branch Selector -->
                <div x-show="deliveryType === 'pickup'" class="space-y-1">
                    <label class="block text-xs font-bold text-charcoal-heading">اختر فرع الاستلام</label>
                    <select x-model="waBranchId" 
                            class="w-full bg-canvas-subtle border border-surface-border focus:border-primary-600 rounded-xl px-3 py-2.5 text-xs text-charcoal-heading outline-none">
                        <template x-for="branch in branches" :key="branch.id">
                            <option :value="branch.id" x-text="branch.name + ' - ' + branch.address"></option>
                        </template>
                    </select>
                </div>

                <!-- Notes -->
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-charcoal-heading">ملاحظات إضافية (اختياري)</label>
                    <input type="text" 
                           x-model="waNotes" 
                           placeholder="أي تفاصيل تخص موعد التسليم أو درجات الألوان..."
                           class="w-full bg-canvas-subtle border border-surface-border focus:border-primary-600 rounded-xl px-3.5 py-2 text-xs text-charcoal-heading outline-none transition-all">
                </div>

                <!-- Summary Row -->
                <div class="p-3 rounded-2xl bg-canvas-subtle border border-surface-border flex items-center justify-between text-xs">
                    <span class="text-charcoal-secondary font-bold">إجمالي الطلب:</span>
                    <span class="font-black text-sm text-primary-800 tabular-nums" x-text="totals.formatted_total"></span>
                </div>

                <!-- Submit Button -->
                <div class="pt-2">
                    <button type="submit" 
                            :disabled="waSubmitting"
                            class="w-full py-3.5 px-4 rounded-2xl bg-[#25D366] hover:bg-[#20bd5a] text-white font-black text-sm text-center shadow-lg shadow-emerald-500/25 transition-all flex items-center justify-center gap-2 disabled:opacity-50 cursor-pointer">
                        <svg x-show="!waSubmitting" class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                            <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.174.086.275.073.376-.044.101-.116.433-.506.549-.68.116-.173.231-.144.39-.086s1.011.477 1.184.564.289.13.332.202c.045.072.045.419-.099.824z" />
                        </svg>
                        <svg x-show="waSubmitting" x-cloak class="w-5 h-5 animate-spin text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                        <span x-text="waSubmitting ? 'جاري حجز المنتجات وتسجيل الطلب...' : 'تأكيد وحجز المخزون والتحويل لواتساب'">
                            تأكيد وحجز المخزون والتحويل لواتساب
                        </span>
                    </button>
                </div>
            </form>

        </div>
    </div>

</div>
@endsection
