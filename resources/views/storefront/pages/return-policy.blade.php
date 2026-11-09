{{-- resources/views/storefront/pages/return-policy.blade.php --}}
@extends('layouts.storefront')

@section('title', 'سياسة الاستبدال والاسترجاع | ' . settings('site_name', 'مؤسسة حورس للتوريدات والتشطيبات'))

@section('content')
@php
    $customReturn = settings('return_policy_content');
@endphp

<div class="bg-gray-50/50 min-h-screen py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

        <!-- Breadcrumbs -->
        <nav class="flex items-center gap-2 text-xs font-medium text-charcoal-muted" aria-label="Breadcrumb">
            <a href="{{ route('site.home') }}" class="hover:text-primary-800 transition-colors">الرئيسية</a>
            <span>/</span>
            <span class="text-charcoal-heading font-bold">سياسة الاستبدال والاسترجاع</span>
        </nav>

        <!-- Main Card -->
        <div class="bg-white rounded-3xl border border-cream p-6 sm:p-10 shadow-sm space-y-8">
            <div class="border-b border-gray-100 pb-5 space-y-2">
                <span class="inline-block px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-800 border border-amber-100">
                    سياسة خدمة العملاء
                </span>
                <h1 class="text-2xl sm:text-3xl font-black text-charcoal-heading">سياسة الاستبدال والاسترجاع</h1>
                <p class="text-xs text-charcoal-muted">لضمان معالجة واضحة وعادلة لكل طلب</p>
            </div>

            @if(!empty($customReturn))
            <div class="text-xs sm:text-sm text-charcoal leading-relaxed whitespace-pre-line space-y-4">
                {{ $customReturn }}
            </div>
            @else
            <div class="text-xs sm:text-sm text-charcoal leading-relaxed space-y-6">
                <section class="space-y-2">
                    <h2 class="text-base font-bold text-primary-800">1. مراجعة الطلب عند الاستلام</h2>
                    <p class="text-charcoal-muted">
                        يرجى مراجعة المنتجات عند الاستلام. إذا وصل منتج تالفاً أو غير مطابق للطلب، تواصل معنا مع رقم الطلب وصورة توضح المشكلة حتى نراجعها ونرتب الحل المناسب.
                    </p>
                </section>

                <section class="space-y-2">
                    <h2 class="text-base font-bold text-primary-800">2. الاستبدال والاسترجاع</h2>
                    <p class="text-charcoal-muted">
                        تختلف حالة كل منتج بحسب نوعه وحالته عند التسليم. تواصل معنا قبل إعادة أي منتج لنوضح لك خطوات الاستبدال أو الاسترجاع المناسبة.
                    </p>
                </section>

                <section class="space-y-2">
                    <h2 class="text-base font-bold text-primary-800">3. المنتجات التي تم توريدها أو تركيبها</h2>
                    <p class="text-charcoal-muted">
                        المنتجات التي تم قصها أو تجهيزها حسب الطلب أو تركيبها قد تتطلب مراجعة خاصة. يرجى التحقق من المقاسات والمواصفات قبل اعتماد الطلب، والتواصل معنا فوراً عند وجود اختلاف.
                    </p>
                </section>

                <section class="space-y-2">
                    <h2 class="text-base font-bold text-primary-800">4. آلية استرداد الأموال</h2>
                    <div class="text-charcoal-muted space-y-1">
                        <p>يتم الاتفاق مع العميل على طريقة إعادة المبلغ بعد مراجعة الطلب وحالة المنتجات.</p>
                    </div>
                </section>

                <section class="space-y-2">
                    <h2 class="text-base font-bold text-primary-800">5. كيف تقدم طلب استرجاع أو استبدال؟</h2>
                    <p class="text-charcoal-muted">
                        تواصل معنا عبر واتساب أو الاتصال الهاتفي (<span dir="ltr">{{ settings('store_phone', '01002527072') }}</span>) أو من خلال صفحة <a href="{{ route('contact.index') }}" class="text-primary-800 font-bold underline">اتصل بنا</a> مع رقم الطلب وتفاصيل المشكلة.
                    </p>
                </section>
            </div>
            @endif
        </div>

    </div>
</div>
@endsection
