{{-- resources/views/storefront/checkout.blade.php --}}
@extends('layouts.storefront')

@section('title', 'إتمام الطلب | مؤسسة حورس')

@section('content')
<div class="py-6 sm:py-10 bg-canvas-base min-h-screen"
     x-data="{
        deliveryType: '{{ old('delivery_type', $deliveryType) }}',
        paymentMethod: '{{ old('payment_method', $paymentMethods['enable_cod'] ? 'cash' : ($paymentMethods['enable_vodafone_cash'] ? 'vodafone_cash' : 'instapay')) }}',
        selectedBranch: '{{ old('branch_id', $branches->first()?->id ?? 1) }}',
        branchesData: {{ json_encode($branches->keyBy('id')) }},
        submitting: false,
        deliveryFee: {{ (float)$totals['delivery_fee'] }},
        subtotal: {{ (float)$totals['subtotal'] }},
        discountAmount: {{ (float)($totals['discount_amount'] ?? 0) }},
        couponCode: '{{ $totals['coupon_code'] ?? '' }}',
        couponInput: '',
        couponLoading: false,

        applyCoupon(codeToApply) {
            let code = (codeToApply || this.couponInput).trim();
            if (!code) {
                toastr.warning('يرجى إدخال أو اختيار كود الكوبون');
                return;
            }
            this.couponLoading = true;
            fetch('{{ route('cart.apply-coupon') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ coupon_code: code })
            })
            .then(res => res.json())
            .then(data => {
                this.couponLoading = false;
                if (data.success) {
                    this.couponCode = code;
                    this.discountAmount = parseFloat(data.discount) || 0;
                    this.couponInput = '';
                    toastr.success(data.message || 'تم تطبيق الكوبون بنجاح!');
                } else {
                    toastr.error(data.message || 'كوبون الخصم غير صالح');
                }
            })
            .catch(() => {
                this.couponLoading = false;
                toastr.error('حدث خطأ أثناء تطبيق الكوبون');
            });
        },

        removeCoupon() {
            this.couponLoading = true;
            fetch('{{ route('cart.remove-coupon') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                this.couponLoading = false;
                if (data.success) {
                    this.couponCode = '';
                    this.discountAmount = 0;
                    toastr.info('تم إلغاء تفعيل الكوبون');
                }
            })
            .catch(() => {
                this.couponLoading = false;
                toastr.error('حدث خطأ أثناء إلغاء الكوبون');
            });
        },

        get discountedSubtotal() {
            return Math.max(0, this.subtotal - this.discountAmount);
        },

        get grandTotal() {
            let fee = this.deliveryType === 'pickup' ? 0 : this.deliveryFee;
            return this.discountedSubtotal + fee;
        },

        submitCheckout(e) {
            if (this.submitting) return;
            this.submitting = true;

            const form = e.target;
            const formData = new FormData(form);

            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(async (res) => {
                const data = await res.json().catch(() => ({}));
                if (res.ok && data.success) {
                    window.dispatchEvent(new CustomEvent('cart-updated', { detail: { count: 0 } }));
                    toastr.success(data.message || 'تم تسجيل طلبك بنجاح!');
                    if (data.whatsapp_url) {
                        window.open(data.whatsapp_url, '_blank');
                    }
                    setTimeout(() => {
                        window.location.href = data.redirect_url;
                    }, 400);
                } else {
                    this.submitting = false;
                    if (data.errors) {
                        const allErrors = Object.values(data.errors).flat();
                        toastr.error(allErrors[0] || 'يرجى مراجعة وتصحيح البيانات');
                    } else {
                        toastr.error(data.message || 'حدث خطأ أثناء تسجيل الطلب، يرجى المحاولة مجدداً');
                    }
                }
            })
            .catch(() => {
                this.submitting = false;
                toastr.error('حدث خطأ في الاتصال، يرجى المحاولة مرة أخرى.');
            });
        }
     }">

    <div class="container mx-auto px-3 sm:px-4 md:px-6 max-w-7xl space-y-6">

        <!-- Breadcrumbs -->
        <nav class="flex items-center gap-2 text-xs font-bold text-charcoal-muted flex-wrap">
            <a href="{{ route('site.home') }}" class="hover:text-red-600 transition-colors">الرئيسية</a>
            <span>/</span>
            <a href="{{ route('cart') }}" class="hover:text-red-600 transition-colors">سلة المشتريات</a>
            <span>/</span>
            <span class="text-charcoal-heading font-black">إتمام الطلب والدفع</span>
        </nav>

        <!-- Page Header -->
        <div class="pb-3 border-b border-cream flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div>
                <h1 class="text-xl sm:text-2xl md:text-3xl font-black text-charcoal-heading">إتمام الطلب والدفع</h1>
                <p class="text-xs sm:text-sm text-charcoal-muted mt-1">مؤسسة حورس للتوريدات والتشطيبات</p>
            </div>
            <div class="flex items-center gap-2 text-xs font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 px-3 py-1.5 rounded-xl self-start sm:self-auto">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>فروعنا مفتوحة للتوصيل والاستلام حتى 2:00 صباحاً</span>
            </div>
        </div>

        <form action="{{ route('checkout.place') }}"
              method="POST"
              enctype="multipart/form-data"
              @submit.prevent="submitCheckout($event)"
              class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8 items-start">
            @csrf

            <!-- Form Left / Main Column (7 cols) -->
            <div class="lg:col-span-7 space-y-5">

                <!-- 1. Customer Information Card -->
                <div class="bg-white rounded-2xl sm:rounded-3xl p-4 sm:p-6 border border-cream shadow-xs space-y-4">
                    <div class="flex items-center gap-2.5 border-b border-cream pb-3">
                        <span class="w-7 h-7 rounded-xl bg-red-50 text-red-600 text-xs font-black flex items-center justify-center border border-red-200 shrink-0">1</span>
                        <div>
                            <h2 class="font-black text-sm sm:text-base text-charcoal-heading">بيانات العميل والتواصل</h2>
                            <p class="text-[11px] text-charcoal-muted">الاسم ورقم الهاتف لمتابعة طلبك وتوصيله</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 text-xs">
                        <!-- Full Name -->
                        <div>
                            <label for="name" class="font-bold text-charcoal-heading block mb-1">
                                الاسم بالكامل <span class="text-red-600">*</span>
                            </label>
                            <input type="text"
                                   id="name"
                                   name="name"
                                   value="{{ old('name', $user?->name) }}"
                                   placeholder="أحمد محمود علي"
                                   required
                                   class="w-full bg-canvas-base border border-cream focus:border-red-500 focus:ring-2 focus:ring-red-500/20 rounded-xl px-3.5 py-2.5 text-xs sm:text-sm text-charcoal outline-none transition-all">
                        </div>

                        <!-- Phone -->
                        <div>
                            <label for="phone" class="font-bold text-charcoal-heading block mb-1">
                                رقم الهاتف (واتساب) <span class="text-red-600">*</span>
                            </label>
                            <input type="tel"
                                   id="phone"
                                   name="phone"
                                   value="{{ old('phone', $user?->phone) }}"
                                   placeholder="01012345678 أو الرقم الدولي مع الكود"
                                   dir="ltr"
                                   required
                                   class="w-full bg-canvas-base border border-cream focus:border-[#073F40] focus:ring-2 focus:ring-[#073F40]/20 rounded-xl px-3.5 py-2.5 text-xs sm:text-sm text-charcoal text-right outline-none transition-all font-mono">
                            <span class="text-[10px] text-charcoal-muted mt-1 block">يقبل أرقام المحمول المصرية والأرقام الدولية مع كود الدولة</span>
                        </div>
                    </div>
                </div>

                <!-- 2. Branch Selection Card -->
                <div class="bg-white rounded-2xl sm:rounded-3xl p-4 sm:p-6 border border-cream shadow-xs space-y-4">
                    <div class="flex items-center justify-between border-b border-cream pb-3">
                        <div class="flex items-center gap-2.5">
                            <span class="w-7 h-7 rounded-xl bg-red-50 text-red-600 text-xs font-black flex items-center justify-center border border-red-200 shrink-0">2</span>
                            <div>
                                <h2 class="font-black text-sm sm:text-base text-charcoal-heading">موقع تجهيز الطلب</h2>
                                <p class="text-[11px] text-charcoal-muted">حدد الفرع الأقرب إليك للتجهيز أو التوصيل منه</p>
                            </div>
                        </div>
                        <span class="text-[10px] font-black px-2.5 py-1 rounded-lg bg-orange-100 text-orange-900 border border-orange-200">
                            موقع مؤسسة حورس
                        </span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                        @foreach($branches as $branch)
                        <label class="relative p-3.5 rounded-2xl border-2 cursor-pointer transition-all flex flex-col justify-between select-none group"
                               :class="selectedBranch == {{ $branch->id }}
                                    ? 'border-red-600 bg-red-50/70 shadow-sm ring-2 ring-red-500/20'
                                    : 'border-cream hover:border-orange-300 bg-white hover:bg-orange-50/30'">

                            <div class="flex items-start gap-2.5 mb-2">
                                <input type="radio"
                                       name="branch_id"
                                       value="{{ $branch->id }}"
                                       x-model="selectedBranch"
                                       class="text-red-600 focus:ring-red-500 mt-0.5 shrink-0">
                                <div class="min-w-0">
                                    <span class="font-black text-xs sm:text-sm text-charcoal-heading block group-hover:text-red-700 transition-colors">
                                        {{ $branch->name }}
                                    </span>
                                    <span class="text-[10px] text-charcoal-muted block leading-relaxed mt-0.5 line-clamp-2">
                                        📍 {{ $branch->address }}
                                    </span>
                                </div>
                            </div>

                            <div class="pt-2 border-t border-cream/70 flex items-center justify-between text-[10px]">
                                <span class="text-charcoal font-bold" dir="ltr">📞 {{ $branch->phone ?: '01234567890' }}</span>
                                <span class="font-black" :class="selectedBranch == {{ $branch->id }} ? 'text-red-700' : 'text-emerald-700'">
                                    <span x-show="selectedBranch == {{ $branch->id }}">✓ المختار</span>
                                    <span x-show="selectedBranch != {{ $branch->id }}">متاح</span>
                                </span>
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>

                <!-- 3. Delivery Method & Address Card -->
                <div class="bg-white rounded-2xl sm:rounded-3xl p-4 sm:p-6 border border-cream shadow-xs space-y-4">
                    <div class="flex items-center gap-2.5 border-b border-cream pb-3">
                        <span class="w-7 h-7 rounded-xl bg-[#073F40]/10 text-[#073F40] text-xs font-black flex items-center justify-center border border-[#073F40]/20 shrink-0">3</span>
                        <div>
                            <h2 class="font-black text-sm sm:text-base text-charcoal-heading">طريقة الاستلام وعنوان التوصيل</h2>
                            <p class="text-[11px] text-charcoal-muted">اختر توصيل لباب البيت أو الاستلام بنفسك من الفرع</p>
                        </div>
                    </div>

                    <!-- Delivery Type Radios -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                        <label class="relative flex items-center gap-3 p-3.5 rounded-2xl border-2 cursor-pointer transition-all select-none"
                               :class="deliveryType === 'delivery'
                                    ? 'border-[#073F40] bg-[#073F40]/5 shadow-sm ring-2 ring-[#073F40]/20'
                                    : 'border-cream hover:border-gray-300 bg-white'">
                            <input type="radio"
                                   name="delivery_type"
                                   value="delivery"
                                   x-model="deliveryType"
                                   class="text-[#073F40] focus:ring-[#073F40]">
                            <div class="flex-1">
                                <span class="font-black text-xs sm:text-sm text-charcoal-heading block">🛵 توصيل سريع للمنزل</span>
                                <span class="text-[10px] text-charcoal-muted block mt-0.5">يرجى التواصل لتأكيد توفر التوصيل لمنطقتك</span>
                            </div>
                        </label>

                        <label class="relative flex items-center gap-3 p-3.5 rounded-2xl border-2 cursor-pointer transition-all select-none"
                               :class="deliveryType === 'pickup'
                                    ? 'border-[#073F40] bg-[#073F40]/5 shadow-sm ring-2 ring-[#073F40]/20'
                                    : 'border-cream hover:border-gray-300 bg-white'">
                            <input type="radio"
                                   name="delivery_type"
                                   value="pickup"
                                   x-model="deliveryType"
                                   class="text-[#073F40] focus:ring-[#073F40]">
                            <div class="flex-1">
                                <span class="font-black text-xs sm:text-sm text-charcoal-heading block">🏪 استلام من الفرع المختار</span>
                                <span class="text-[10px] text-emerald-700 font-bold block mt-0.5">تجهيز فوري ومجاناً (0 ج.م رسوم)</span>
                            </div>
                        </label>
                    </div>

                    <!-- Home Delivery Address Field (Visible when deliveryType === 'delivery') -->
                    <div x-show="deliveryType === 'delivery'" x-transition class="space-y-2 text-xs pt-1">
                        <label for="address" class="font-bold text-charcoal-heading block">
                            عنوان التوصيل بالتفصيل <span class="text-red-600">*</span>
                        </label>

                        @if($user && !empty($user->addresses) && count($user->addresses) > 0)
                        <div class="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-none">
                            <span class="text-[10px] text-charcoal-muted shrink-0 font-bold">عناوينك المسجلة:</span>
                            @foreach($user->addresses as $savedAddr)
                            <button type="button"
                                    onclick="document.getElementById('address').value = {{ json_encode($savedAddr['address'] ?? '') }};"
                                    class="px-2.5 py-1 rounded-lg bg-canvas-base border border-cream hover:border-red-500 hover:text-red-700 text-[11px] font-bold text-charcoal shrink-0 transition-colors cursor-pointer">
                                {{ $savedAddr['title'] ?? 'عنوان' }}
                            </button>
                            @endforeach
                        </div>
                        @endif

                        <textarea id="address"
                                  name="address"
                                  rows="3"
                                  placeholder="اسم المنطقة، الشارع، رقم العمارة، رقم الشقة، علامة مميزة (مثال: بجوار مسجد...)"
                                  class="w-full bg-canvas-base border border-cream focus:border-red-500 focus:ring-2 focus:ring-red-500/20 rounded-xl p-3 text-xs sm:text-sm text-charcoal outline-none transition-all leading-relaxed">{{ old('address', $user?->address) }}</textarea>
                    </div>

                    <!-- Pickup Notice (Visible when deliveryType === 'pickup') -->
                    <div x-show="deliveryType === 'pickup'" x-cloak x-transition class="p-3 bg-emerald-50/70 border border-emerald-200 rounded-xl text-xs space-y-1">
                        <div class="font-black text-emerald-800 flex items-center gap-1.5">
                            <span>✓</span>
                            <span>سيتم تجهيز طلبك في <strong x-text="branchesData[selectedBranch]?.name || 'الفرع المختار'"></strong> للاستلام فور وصولك.</span>
                        </div>
                        <p class="text-[11px] text-emerald-700" x-text="'عنوان الفرع: ' + (branchesData[selectedBranch]?.address || '')"></p>
                    </div>
                </div>

                <!-- 4. Payment Method Card -->
                <div class="bg-white rounded-2xl sm:rounded-3xl p-4 sm:p-6 border border-cream shadow-xs space-y-4">
                    <div class="flex items-center gap-2.5 border-b border-cream pb-3">
                        <span class="w-7 h-7 rounded-full bg-[#073F40]/10 text-[#073F40] text-xs font-black flex items-center justify-center border border-[#073F40]/20 shrink-0">4</span>
                        <div>
                            <h2 class="font-black text-sm sm:text-base text-charcoal-heading">طريقة الدفع</h2>
                            <p class="text-[11px] text-charcoal-muted">اختر الدفع عند الاستلام أو إنستاباي أو فودافون كاش أو تأكيد واتساب</p>
                        </div>
                    </div>

                    <!-- Payment Radios -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 text-xs">
                        @if($paymentMethods['enable_cod'])
                        <label class="relative flex items-center gap-3 p-3.5 rounded-2xl border-2 cursor-pointer transition-all select-none"
                               :class="paymentMethod === 'cash'
                                    ? 'border-[#073F40] bg-[#073F40]/5 shadow-sm ring-2 ring-[#073F40]/20'
                                    : 'border-cream hover:border-gray-300 bg-white'">
                            <input type="radio"
                                   name="payment_method"
                                   value="cash"
                                   x-model="paymentMethod"
                                   class="text-[#073F40] focus:ring-[#073F40]">
                            <div class="flex-1">
                                <span class="font-black text-xs sm:text-sm text-charcoal-heading block">💵 الدفع عند الاستلام</span>
                                <span class="text-[10px] text-charcoal-muted block mt-0.5"
                                      x-text="deliveryType === 'pickup' ? 'تدفع في المعرض عند الاستلام' : 'تدفع للمندوب عند باب البيت'"></span>
                            </div>
                        </label>
                        @endif

                        @if($paymentMethods['enable_instapay'])
                        <label class="relative flex items-center gap-3 p-3.5 rounded-2xl border-2 cursor-pointer transition-all select-none"
                               :class="paymentMethod === 'instapay'
                                    ? 'border-[#C8A635] bg-[#C8A635]/10 shadow-sm ring-2 ring-[#C8A635]/30'
                                    : 'border-cream hover:border-gray-300 bg-white'">
                            <input type="radio"
                                   name="payment_method"
                                   value="instapay"
                                   x-model="paymentMethod"
                                   class="text-[#C8A635] focus:ring-[#C8A635]">
                            <div class="flex-1">
                                <span class="font-black text-xs sm:text-sm text-charcoal-heading block">📱 إنستاباي (InstaPay)</span>
                                <span class="text-[10px] text-charcoal-muted block mt-0.5">تحويل لحظي مباشر لحساب المؤسسة</span>
                            </div>
                        </label>
                        @endif

                        @if($paymentMethods['enable_vodafone_cash'])
                        <label class="relative flex items-center gap-3 p-3.5 rounded-2xl border-2 cursor-pointer transition-all select-none"
                               :class="paymentMethod === 'vodafone_cash' ? 'border-[#E60000] bg-red-50/70 shadow-sm ring-2 ring-red-500/20' : 'border-cream hover:border-red-300 bg-white'">
                            <input type="radio" name="payment_method" value="vodafone_cash" x-model="paymentMethod" class="text-red-600 focus:ring-red-500">
                            <div class="flex-1">
                                <span class="font-black text-xs sm:text-sm text-charcoal-heading block">📲 فودافون كاش</span>
                                <span class="text-[10px] text-charcoal-muted block mt-0.5">تحويل لمحفظة كاش وإرفاق الإيصال</span>
                            </div>
                        </label>
                        @endif

                        <!-- WhatsApp Checkout Option -->
                        <label class="relative flex items-center gap-3 p-3.5 rounded-2xl border-2 cursor-pointer transition-all select-none"
                               :class="paymentMethod === 'whatsapp_checkout' ? 'border-[#25D366] bg-emerald-50/70 shadow-sm ring-2 ring-emerald-500/20' : 'border-cream hover:border-emerald-300 bg-white'">
                            <input type="radio" name="payment_method" value="whatsapp_checkout" x-model="paymentMethod" class="text-emerald-600 focus:ring-emerald-500">
                            <div class="flex-1">
                                <span class="font-black text-xs sm:text-sm text-charcoal-heading flex items-center gap-1.5">
                                    <svg class="w-4 h-4 fill-[#25D366] shrink-0" viewBox="0 0 24 24">
                                        <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.174.086.275.073.376-.044.101-.116.433-.506.549-.68.116-.173.231-.144.39-.086s1.011.477 1.184.564.289.13.332.202c.045.072.045.419-.099.824z" />
                                    </svg>
                                    <span>متابعة عبر واتساب</span>
                                </span>
                                <span class="text-[10px] text-charcoal-muted block mt-0.5">حجز المخزون ومتابعة الطلب فوري</span>
                            </div>
                        </label>
                    </div>

                    <!-- Electronic payment details -->
                    <div x-show="paymentMethod === 'instapay' || paymentMethod === 'vodafone_cash'" x-cloak x-transition class="space-y-4 pt-3 border-t border-cream text-xs">

                        <!-- Account Details Box -->
                        <div class="p-4 rounded-2xl bg-gradient-to-br from-amber-50 to-orange-50 border-2 border-orange-200 space-y-2.5">
                            <div class="font-black text-amber-950 flex items-center justify-between flex-wrap gap-2">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                    <span x-text="paymentMethod === 'instapay' ? 'بيانات التحويل عبر إنستاباي:' : 'بيانات محفظة فودافون كاش:'"></span>
                                </div>
                                <span class="text-[10px] px-2.5 py-0.5 rounded-full bg-orange-200 text-orange-900 font-bold"
                                      x-text="branchesData[selectedBranch]?.name || 'الفرع المختار'"></span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs pt-1">
                                <div x-show="paymentMethod === 'vodafone_cash'" class="bg-white p-3 rounded-xl border border-orange-200 shadow-2xs">
                                    <span class="text-[10px] text-gray-500 block">رقم محفظة فودافون كاش:</span>
                                    @if(!empty($paymentDetails['vodafone_cash_number']))
                                        <span class="font-mono font-black text-red-600 text-xs sm:text-sm select-all block mt-0.5" dir="ltr">{{ $paymentDetails['vodafone_cash_number'] }}</span>
                                    @else
                                        <span class="font-bold text-gray-700 text-xs block mt-0.5">{{ settings('store_phone', '01002527072') }}</span>
                                    @endif
                                    @if(!empty($paymentDetails['vodafone_cash_number']) && (str_contains($paymentDetails['vodafone_cash_number'], '000000') || str_contains((string) $paymentDetails['vodafone_cash_instructions'], 'تجريبي')))
                                        <span class="mt-1 block rounded bg-amber-50 px-2 py-1 text-[9px] font-bold text-amber-800">بيانات تجريبية — أكّد الرقم الرسمي هاتفياً قبل التحويل.</span>
                                    @endif
                                </div>
                                <div x-show="paymentMethod === 'instapay'" class="bg-white p-3 rounded-xl border border-orange-200 shadow-2xs">
                                    <span class="text-[10px] text-gray-500 block">بيانات إنستاباي:</span>
                                    @if(!empty($paymentDetails['instapay_username']))
                                        <span class="font-mono font-black text-primary-800 text-xs sm:text-sm block mt-0.5" dir="ltr">{{ $paymentDetails['instapay_username'] }}</span>
                                    @elseif(!empty($paymentDetails['instapay_number']))
                                        <span class="font-mono font-black text-primary-800 text-xs sm:text-sm block mt-0.5" dir="ltr">{{ $paymentDetails['instapay_number'] }}</span>
                                    @else
                                        <span class="font-mono font-black text-primary-800 text-xs sm:text-sm block mt-0.5">تواصل معنا لتأكيد بيانات الحساب</span>
                                    @endif
                                    @if(!empty($paymentDetails['instapay_name']))
                                        <span class="mt-1 block text-[10px] font-bold text-gray-600">{{ $paymentDetails['instapay_name'] }}</span>
                                    @endif
                                    @if(str_contains((string) $paymentDetails['instapay_instructions'], 'تجريبي'))
                                        <span class="mt-1 block rounded bg-amber-50 px-2 py-1 text-[9px] font-bold text-amber-800">بيانات تجريبية — أكّد الحساب الرسمي هاتفياً قبل التحويل.</span>
                                    @endif
                                </div>
                                <div class="bg-white p-3 rounded-xl border border-orange-200 shadow-2xs">
                                    <span class="text-[10px] text-gray-500 block">رقم الهاتف للتواصل:</span>
                                    <span class="font-mono font-black text-emerald-700 text-xs sm:text-sm select-all block mt-0.5"
                                          dir="ltr"
                                          x-text="'01002527072'">
                                    </span>
                                </div>
                            </div>
                            <div class="text-[10px] text-amber-900 font-medium bg-white/80 p-2.5 rounded-xl border border-amber-200 leading-relaxed" x-show="paymentMethod === 'instapay'">
                                {{ $paymentDetails['instapay_instructions'] ?: 'حوّل قيمة الطلب، ثم أرفق صورة الإيصال لمراجعة التحويل من فريق حورس.' }}
                            </div>
                            <div class="text-[10px] text-amber-900 font-medium bg-white/80 p-2.5 rounded-xl border border-amber-200 leading-relaxed" x-show="paymentMethod === 'vodafone_cash'">
                                {{ $paymentDetails['vodafone_cash_instructions'] ?: 'حوّل قيمة الطلب إلى المحفظة ثم أرفق صورة الإيصال لمراجعة التحويل.' }}
                            </div>
                        </div>

                        <!-- Upload Receipt -->
                        <div>
                            <label for="payment_proof_image" class="font-bold text-charcoal-heading block mb-1">
                                صورة إيصال التحويل <span class="text-charcoal-muted font-normal">(اختياري للمراجعة)</span>
                            </label>
                            <input type="file"
                                   id="payment_proof_image"
                                   name="payment_proof_image"
                                   accept="image/*"
                                   class="w-full bg-canvas-base border border-cream rounded-xl p-2.5 text-xs text-charcoal file:ml-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-[#073F40] file:text-white hover:file:bg-[#0B5759] cursor-pointer">
                            <span class="text-[10px] text-charcoal-muted mt-1 block">ارفع لقطة شاشة لعملية التحويل الناجحة لتأكيد فوري للطلب.</span>
                        </div>

                        <!-- Sender Phone Number / Account -->
                        <div>
                            <label for="instapay_reference" class="font-bold text-charcoal-heading block mb-1">
                                <span x-text="paymentMethod === 'vodafone_cash' ? 'رقم المحفظة / الهاتف المحوّل منه (فودافون كاش):' : 'رقم الهاتف أو الحساب المحوّل منه (إنستاباي):'">
                                    الرقم المحوّل منه:
                                </span>
                                <span class="text-[#073F40] text-[11px] font-bold">(لتأكيد ومطابقة الإيداع)</span>
                            </label>
                            <input type="text"
                                   id="instapay_reference"
                                   name="instapay_reference"
                                   value="{{ old('instapay_reference') }}"
                                   :placeholder="paymentMethod === 'vodafone_cash' ? 'مثال: 01012345678 (الرقم اللي حولت منه)' : 'مثال: 01012345678 أو username@instapay'"
                                   class="w-full bg-canvas-base border border-cream focus:border-[#073F40] focus:ring-2 focus:ring-[#073F40]/20 rounded-xl px-3.5 py-2.5 text-xs sm:text-sm text-charcoal outline-none transition-all font-mono">
                            <span class="text-[10px] text-gray-500 mt-1 block">اكتب رقم الهاتف أو الحساب الذي قمت بالتحويل منه لنقوم بمطابقته فورياً والبدء في تجهيز طلبك.</span>
                        </div>

                    </div>
                </div>

                <!-- 5. Notes Card -->
                <div class="bg-white rounded-2xl sm:rounded-3xl p-4 sm:p-6 border border-cream shadow-xs space-y-2">
                    <label for="notes" class="font-bold text-xs sm:text-sm text-charcoal-heading block">
                        ملاحظات إضافية لتجهيز وتوصيل الطلب <span class="text-charcoal-muted font-normal">(اختياري)</span>
                    </label>
                    <textarea id="notes"
                              name="notes"
                              rows="2"
                              placeholder="أضف اللون أو المقاس أو أي تفاصيل تساعدنا في تجهيز طلبك..."
                              class="w-full bg-canvas-base border border-cream focus:border-red-500 focus:ring-2 focus:ring-red-500/20 rounded-xl p-3 text-xs sm:text-sm text-charcoal outline-none transition-all leading-relaxed">{{ old('notes') }}</textarea>
                </div>

            </div>

            <!-- Summary Right / Sidebar Column (5 cols) -->
            <div class="lg:col-span-5 space-y-5">

                <div class="bg-white rounded-2xl sm:rounded-3xl p-4 sm:p-6 border border-cream shadow-xs space-y-4 lg:sticky lg:top-24">
                    <div class="flex items-center justify-between border-b border-cream pb-3">
                        <h3 class="font-black text-sm sm:text-base text-charcoal-heading">
                            ملخص الطلب
                        </h3>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-black bg-red-50 text-red-600 border border-red-200">
                            {{ count($items) }} أصناف
                        </span>
                    </div>

                    <!-- Compact Items List -->
                    <div class="max-h-64 overflow-y-auto divide-y divide-cream/60 pl-1 admin-scrollbar">
                        @foreach($items as $item)
                        <div class="py-2.5 first:pt-0 last:pb-0 flex items-center justify-between gap-3 text-xs">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <div class="w-11 h-11 rounded-xl bg-canvas-base border border-cream p-1 shrink-0 flex items-center justify-center overflow-hidden">
                                    @if($item['image'])
                                        <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" class="w-full h-full object-contain">
                                    @else
                                        <span class="text-lg">🛍️</span>
                                    @endif
                                </div>
                                <div class="min-w-0 space-y-0.5">
                                    <div class="font-black text-charcoal-heading truncate">{{ $item['name'] }}</div>
                                    @if(!empty($item['portion_label']))
                                        <div class="text-[10px] text-orange-800 font-bold">{{ $item['portion_label'] }}</div>
                                    @endif
                                    <div class="text-[10px] text-charcoal-muted">الكمية: <strong class="text-charcoal">{{ $item['quantity'] }}</strong></div>
                                </div>
                            </div>
                            <div class="font-black text-red-600 text-xs sm:text-sm tabular-nums shrink-0">
                                {{ number_format($item['price_usd'] * $item['quantity'], 2) }} ج.م
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- Interactive Coupon Box -->
                    <div class="pt-3 border-t border-cream space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-charcoal-heading flex items-center gap-1.5">
                                <span>🏷️</span>
                                <span>كوبون أو كود خصم</span>
                            </span>
                            <span class="text-[10px] text-accent-700 bg-accent-50 px-2 py-0.5 rounded-full font-bold">توفير فوري</span>
                        </div>

                        <!-- If Coupon Applied -->
                        <div x-show="couponCode && discountAmount > 0" class="flex items-center justify-between p-2.5 rounded-xl bg-emerald-50 border border-emerald-200">
                            <div class="flex items-center gap-2">
                                <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-800 flex items-center justify-center text-xs font-black">✓</span>
                                <div>
                                    <div class="text-xs font-black text-emerald-900 flex items-center gap-1">
                                        <span>كود:</span>
                                        <span class="font-mono tracking-wider uppercase" x-text="couponCode"></span>
                                    </div>
                                    <div class="text-[10px] text-emerald-700 font-bold">
                                        خصم: <span x-text="discountAmount.toFixed(2) + ' ج.م'"></span>
                                    </div>
                                </div>
                            </div>
                            <button type="button" 
                                    @click="removeCoupon()" 
                                    :disabled="couponLoading"
                                    class="text-xs text-red-600 hover:text-red-800 font-bold p-1 cursor-pointer"
                                    title="إلغاء الخصم">
                                ✕ إلغاء
                            </button>
                        </div>

                        <!-- If No Coupon Applied -->
                        <div x-show="!couponCode || discountAmount <= 0" class="space-y-2">
                            <div class="flex items-center gap-2">
                                <input type="text" 
                                       x-model="couponInput" 
                                       placeholder="أدخل كود الخصم..." 
                                       class="flex-1 uppercase font-black text-xs bg-canvas-subtle border border-surface-border rounded-xl px-3 py-2 focus:outline-none focus:border-primary-600 transition-colors"
                                       @keydown.enter.prevent="applyCoupon()">
                                <button type="button" 
                                        @click="applyCoupon()"
                                        :disabled="couponLoading"
                                        class="px-4 py-2 bg-[#073F40] hover:bg-[#0B5759] text-white text-xs font-black rounded-xl transition-all disabled:opacity-50 cursor-pointer shrink-0">
                                    <span x-show="!couponLoading">تطبيق</span>
                                    <span x-show="couponLoading">...</span>
                                </button>
                            </div>

                            @if(isset($availableCoupons) && $availableCoupons->isNotEmpty())
                                <div class="space-y-1.5 pt-1">
                                    <span class="text-[10px] text-gray-500 font-bold block">كوبونات متاحة:</span>
                                    <div class="flex flex-wrap gap-1.5">
                                        @foreach($availableCoupons as $av)
                                            <button type="button" 
                                                    @click="applyCoupon('{{ $av->code }}')"
                                                    :disabled="couponLoading"
                                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-amber-50 hover:bg-amber-100 border border-amber-200 text-xs text-amber-900 transition-all cursor-pointer">
                                                <span class="font-mono font-black text-[10px] text-[#073F40]">{{ $av->code }}</span>
                                                <span class="text-[10px] text-amber-700">({{ $av->formatted_discount }})</span>
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Financial Breakdown -->
                    <div class="space-y-2.5 text-xs pt-3 border-t border-cream">
                        <div class="flex items-center justify-between text-charcoal-muted">
                            <span class="font-bold">المجموع الفرعي</span>
                            <span class="font-bold text-charcoal tabular-nums">{{ $totals['formatted_subtotal'] }}</span>
                        </div>

                        <div x-show="discountAmount > 0" class="flex items-center justify-between text-emerald-700 bg-emerald-50 px-2.5 py-1.5 rounded-xl border border-emerald-200 text-xs font-bold">
                            <span>خصم الكوبون (<span x-text="couponCode"></span>)</span>
                            <span class="tabular-nums">- <span x-text="discountAmount.toFixed(2) + ' ج.م'"></span></span>
                        </div>

                        <div class="flex items-center justify-between text-charcoal-muted">
                            <span class="font-bold">رسوم التوصيل</span>
                            <span class="font-black tabular-nums"
                                  :class="deliveryType === 'pickup' ? 'text-emerald-600' : 'text-charcoal'"
                                  x-text="deliveryType === 'pickup' ? 'مجاناً (استلام من الفرع)' : '{{ $totals['formatted_delivery_fee'] }}'">
                            </span>
                        </div>

                        <div class="border-t border-cream pt-3 flex items-center justify-between text-sm sm:text-base font-black text-charcoal-heading">
                            <span>الإجمالي النهائي:</span>
                            <span class="text-lg sm:text-xl text-red-600 tabular-nums"
                                  x-text="grandTotal.toFixed(2) + ' ج.م'">
                            </span>
                        </div>
                        @if((float) settings('delivery_fee', 0) <= 0)
                            <p class="text-[10px] text-charcoal-muted leading-relaxed">تكلفة التوصيل غير مضافة للإجمالي، ويؤكدها الفرع معك قبل التحصيل.</p>
                        @endif
                    </div>

                    <!-- Place Order Button in Brand Gold -->
                    <div class="pt-2">
                        <button type="submit"
                                :disabled="submitting"
                                class="w-full py-3.5 px-4 rounded-full bg-[#C8A635] hover:bg-[#B38F24] active:scale-[0.98] text-[#073F40] font-black text-sm text-center shadow-md border border-[#B38F24] transition-all flex items-center justify-center gap-2 cursor-pointer disabled:opacity-60 disabled:cursor-not-allowed">
                            <svg x-show="submitting" x-cloak class="w-5 h-5 animate-spin text-[#073F40]" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>
                            <span x-text="submitting ? 'جاري تأكيد وتسجيل طلبك...' : 'تأكيد وحفظ الطلب الآن 🛍️'">تأكيد وحفظ الطلب الآن 🛍️</span>
                        </button>
                    </div>

                    <div class="text-center text-[10px] text-charcoal-muted pt-1 leading-relaxed">
                        بالضغط على "تأكيد الطلب"، يتم إرسال طلبك مباشرة لقسم التجهيز في الفرع ومتابعته معك عبر واتساب.
                    </div>
                </div>

            </div>
        </form>

    </div>
</div>
@endsection
