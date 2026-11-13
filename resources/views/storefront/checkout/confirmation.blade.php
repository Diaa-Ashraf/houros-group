{{-- resources/views/storefront/checkout/confirmation.blade.php --}}
@extends('layouts.storefront')

@section('title', 'تم تأكيد طلبك بنجاح | ' . $order->public_order_id)

@section('content')
<div class="py-12 bg-canvas-base min-h-screen">
    <div class="container mx-auto px-4 max-w-3xl space-y-8">
        
        <!-- Success Celebration Card -->
        <div class="bg-white rounded-3xl p-8 sm:p-12 border border-surface-border shadow-sm text-center space-y-5">
            <div class="w-20 h-20 rounded-full bg-emerald-50 text-emerald-600 border border-emerald-200 text-4xl flex items-center justify-center mx-auto shadow-inner">
                ✓
            </div>

            <div class="space-y-2">
                <span class="inline-block px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                    تم تسجيل الطلب وحجز المنتجات من المخزون بنجاح 🎉
                </span>
                <h1 class="text-2xl sm:text-3xl font-black text-charcoal-heading">
                    شكراً لك، تم استلام طلبك!
                </h1>
                <p class="text-xs sm:text-sm text-charcoal-muted max-w-md mx-auto leading-relaxed">
                    فريق مؤسسة حورس يراجع طلبك ويجهزه للتسليم فوراً.
                    @if($order->delivery_type === 'delivery')
                        <span class="block mt-1 font-bold text-charcoal-secondary">سيتم تأكيد تكلفة وموعد التوصيل معك قبل التحصيل.</span>
                    @endif
                </p>
            </div>

            <!-- Public Order ID Card with Copy Button -->
            <div class="bg-canvas-subtle p-4 rounded-2xl border border-surface-border inline-flex flex-col sm:flex-row items-center gap-3 sm:gap-6 mx-auto"
                 x-data="{ copied: false }">
                <div class="text-right">
                    <span class="text-[10px] text-charcoal-muted block font-bold">رقم الطلب المرجعي:</span>
                    <span class="text-lg font-mono font-black text-primary-800 tracking-wider">
                        {{ $order->public_order_id }}
                    </span>
                </div>

                <button type="button" 
                        @click="
                            navigator.clipboard.writeText('{{ $order->public_order_id }}');
                            copied = true;
                            setTimeout(() => copied = false, 2500);
                            toastr.success('تم نسخ رقم الطلب');
                        "
                        class="px-3.5 py-1.5 rounded-xl bg-white border border-surface-border hover:bg-canvas-base text-xs font-bold text-charcoal transition-colors shadow-2xs flex items-center gap-1.5 cursor-pointer">
                    <svg class="w-4 h-4 text-charcoal-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                    <span x-text="copied ? 'تم النسخ!' : 'نسخ الرقم'">نسخ الرقم</span>
                </button>
            </div>

            <!-- WhatsApp Order Alert -->
            @if($order->payment_method === 'whatsapp_checkout' || session('whatsapp_url'))
                <div class="p-4 rounded-2xl bg-emerald-50 border-2 border-emerald-300 text-emerald-900 text-xs text-right space-y-2 max-w-lg mx-auto shadow-xs">
                    <div class="font-black text-sm flex items-center gap-2 text-emerald-800">
                        <svg class="w-5 h-5 fill-[#25D366] shrink-0" viewBox="0 0 24 24">
                            <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.174.086.275.073.376-.044.101-.116.433-.506.549-.68.116-.173.231-.144.39-.086s1.011.477 1.184.564.289.13.332.202c.045.072.045.419-.099.824z" />
                        </svg>
                        <span>تم حجز المنتجات من المخزون بنجاح!</span>
                    </div>
                    <p class="text-[11px] leading-relaxed text-emerald-800">
                        تم تسجيل طلبك برقم رسمي وخصم المنتجات من المخزون. يرجى المتابعة عبر محادثة واتساب لتأكيد موعد الاستلام أو التوصيل.
                    </p>
                    @if(session('whatsapp_url'))
                        <div class="pt-1">
                            <a href="{{ session('whatsapp_url') }}" target="_blank" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-[#25D366] text-white font-black text-xs hover:bg-[#20bd5a] shadow-xs">
                                <span>فتح محادثة واتساب الآن ←</span>
                            </a>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Digital Payment Review Alert -->
            @if(in_array($order->payment_method, ['instapay', 'vodafone_cash'], true))
                <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 text-xs text-right space-y-1 max-w-lg mx-auto">
                    <div class="font-bold flex items-center gap-1.5">
                        <span>⏳</span>
                        <span>طلبك قيد المراجعة والتحقق من الدفع الإلكتروني</span>
                    </div>
                    <p class="text-[11px] leading-relaxed text-amber-800">
                        سيتواصل معك فريق حورس لتأكيد التحويل واستكمال تجهيز الطلب.
                    </p>
                </div>
            @endif
        </div>

        <!-- Order Information & Summary Breakdown -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-surface-border shadow-sm space-y-6">
            <h2 class="font-black text-base text-charcoal-heading border-b border-surface-border pb-3">
                تفاصيل الطلب والاستلام
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                <div class="p-3.5 rounded-2xl bg-canvas-subtle space-y-1 border border-surface-border">
                    <span class="text-[10px] text-charcoal-muted block font-bold">اسم العميل ورقم الهاتف:</span>
                    <span class="font-bold text-charcoal block">{{ $order->customer_name }}</span>
                    <span class="text-charcoal-muted font-mono" dir="ltr">{{ $order->phone }}</span>
                </div>

                <div class="p-3.5 rounded-2xl bg-canvas-subtle space-y-1 border border-surface-border">
                    <span class="text-[10px] text-charcoal-muted block font-bold">طريقة الاستلام:</span>
                    @if($order->delivery_type === 'pickup')
                        <span class="font-bold text-emerald-800 block">🏪 استلام من الفرع</span>
                        <span class="text-charcoal-muted">{{ $order->branch?->name ?: 'الفرع المحدد' }} ({{ $order->branch?->address }})</span>
                    @else
                        <span class="font-bold text-primary-800 block">🚚 توصيل للمنزل</span>
                        <span class="text-charcoal-muted">{{ $order->address }}</span>
                    @endif
                </div>

                <div class="p-3.5 rounded-2xl bg-canvas-subtle space-y-1 border border-surface-border">
                    <span class="text-[10px] text-charcoal-muted block font-bold">طريقة الدفع:</span>
                    <span class="font-bold text-charcoal block">
                        {{ $order->payment_method_label }}
                    </span>
                    @if($order->instapay_reference)
                        <span class="text-[10px] text-charcoal-muted">المرجع: {{ $order->instapay_reference }}</span>
                    @endif
                </div>

                <div class="p-3.5 rounded-2xl bg-canvas-subtle space-y-1 border border-surface-border">
                    <span class="text-[10px] text-charcoal-muted block font-bold">حالة الطلب:</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-primary-100 text-primary-800">
                        {{ $order->status_label ?? 'قيد المراجعة والتجهيز' }}
                    </span>
                </div>
            </div>

            <!-- Ordered Items List -->
            <div class="pt-4 border-t border-surface-border space-y-3">
                <h3 class="font-bold text-xs text-charcoal-heading">المنتجات المحجوزة:</h3>
                <div class="divide-y divide-gray-100">
                    @foreach($order->orderItems as $item)
                        <div class="py-2.5 flex items-center justify-between text-xs">
                            <div class="flex items-center gap-3">
                                <span class="w-6 h-6 rounded-lg bg-canvas-subtle text-[11px] font-bold flex items-center justify-center border border-surface-border">
                                    {{ $item->quantity }}x
                                </span>
                                <div>
                                    <span class="font-bold text-charcoal">{{ $item->product?->name ?: 'منتج بالطلب' }}</span>
                                    @if($item->portion_label)
                                        <span class="text-[10px] text-primary-700 bg-primary-50 px-1.5 py-0.5 rounded mr-1">({{ $item->portion_label }})</span>
                                    @endif
                                </div>
                            </div>
                            <span class="font-black text-primary-800 tabular-nums">
                                {{ number_format($item->total_price, 2) }} ج.م
                            </span>
                        </div>
                    @endforeach
                </div>

                <div class="pt-3 border-t border-surface-border flex items-center justify-between font-black text-sm text-charcoal-heading">
                    <span>الإجمالي الكلي:</span>
                    <span class="text-lg text-primary-800 tabular-nums">
                        {{ number_format($order->total_price, 2) }} ج.م
                    </span>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="pt-6 border-t border-surface-border flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ route('site.home') }}" 
                   class="w-full sm:w-auto py-3 px-6 rounded-2xl bg-primary-800 hover:bg-primary-900 text-white font-bold text-xs text-center transition-all shadow-md">
                    العودة للرئيسية والتسوق
                </a>

                @php
                    $whatsappMsg = urlencode("مرحباً مؤسسة حورس، أود متابعة طلبي المسجل برقم: {$order->public_order_id}");
                    $storeWhatsapp = preg_replace('/[^0-9]/', '', settings('store_whatsapp', '201002527072'));
                @endphp
                <a href="https://wa.me/{{ $storeWhatsapp }}?text={{ $whatsappMsg }}" 
                   target="_blank" 
                   rel="noopener noreferrer"
                   class="w-full sm:w-auto py-3 px-6 rounded-2xl bg-[#25D366] hover:bg-[#20bd5a] text-white font-bold text-xs text-center transition-all flex items-center justify-center gap-2 shadow-md shadow-emerald-500/20">
                    <svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 24 24">
                        <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.174.086.275.073.376-.044.101-.116.433-.506.549-.68.116-.173.231-.144.39-.086s1.011.477 1.184.564.289.13.332.202c.045.072.045.419-.099.824z" />
                    </svg>
                    <span>متابعة الطلب عبر واتساب</span>
                </a>
            </div>

        </div>

    </div>
</div>
@endsection
