{{-- resources/views/storefront/pages/terms.blade.php --}}
@extends('layouts.storefront')

@section('title', 'الشروط والأحكام | ' . settings('site_name', 'مؤسسة حورس للتوريدات والتشطيبات'))

@section('content')
@php
    $customTerms = settings('terms_content');
@endphp

<div class="bg-gray-50/50 min-h-screen py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

        <!-- Breadcrumbs -->
        <nav class="flex items-center gap-2 text-xs font-medium text-charcoal-muted" aria-label="Breadcrumb">
            <a href="{{ route('site.home') }}" class="hover:text-primary-800 transition-colors">الرئيسية</a>
            <span>/</span>
            <span class="text-charcoal-heading font-bold">الشروط والأحكام</span>
        </nav>

        <!-- Main Card -->
        <div class="bg-white rounded-3xl border border-cream p-6 sm:p-10 shadow-sm space-y-8">
            <div class="border-b border-gray-100 pb-5 space-y-2">
                <span class="inline-block px-3 py-1 rounded-full text-xs font-bold bg-primary-50 text-primary-800 border border-primary-100">
                    وثيقة الاستخدام الرسمية
                </span>
                <h1 class="text-2xl sm:text-3xl font-black text-charcoal-heading">الشروط والأحكام</h1>
                <p class="text-xs text-charcoal-muted">آخر تحديث: {{ date('Y') }}</p>
            </div>

            @if(!empty($customTerms))
            <div class="text-xs sm:text-sm text-charcoal leading-relaxed whitespace-pre-line space-y-4">
                {{ $customTerms }}
            </div>
            @else
            <div class="text-xs sm:text-sm text-charcoal leading-relaxed space-y-6">
                <section class="space-y-2">
                    <h2 class="text-base font-bold text-primary-800">1. مقدمة وقبول الشروط</h2>
                    <p class="text-charcoal-muted">
                        مرحباً بكم في الموقع الإلكتروني لمؤسسة حورس للتوريدات والتشطيبات. باستخدامك للموقع أو إرسال طلب، فإنك توافق على الشروط الموضحة هنا.
                    </p>
                </section>

                <section class="space-y-2">
                    <h2 class="text-base font-bold text-primary-800">2. المنتجات والأسعار والمخزون</h2>
                    <p class="text-charcoal-muted">
                        بعض الأصناف معروضة للاستفسار عن السعر أو التوافر. الأسعار الظاهرة، إن وجدت، بالجنيه المصري؛ وسنتواصل معك لتأكيد السعر والمخزون قبل اعتماد الطلب.
                    </p>
                </section>

                <section class="space-y-2">
                    <h2 class="text-base font-bold text-primary-800">3. الاستلام والتوصيل</h2>
                    <p class="text-charcoal-muted">
                        عنوان المؤسسة هو ٢٢ع حدائق الأهرام، بوابة حورس. تواصل معنا لتأكيد توفر التوصيل لمنطقتك ورسومه قبل اعتماد الطلب.
                    </p>
                </section>

                <section class="space-y-2">
                    <h2 class="text-base font-bold text-primary-800">4. طرق الدفع</h2>
                    <p class="text-charcoal-muted">
                        @if(str_contains((string) settings('vodafone_cash_instructions'), 'تجريبي') || str_contains((string) settings('instapay_instructions'), 'تجريبي'))
                            أرقام فودافون كاش وإنستاباي الحالية تجريبية للعرض فقط. لا تحوّل أي مبالغ قبل تأكيد بيانات الدفع الرسمية هاتفياً مع المؤسسة. تخضع التحويلات الإلكترونية للمراجعة قبل تأكيد الطلب.
                        @else
                            تتوفر وسائل الدفع المفعّلة في صفحة إتمام الطلب، وتخضع التحويلات الإلكترونية للمراجعة قبل تأكيد الطلب. استخدم البيانات الحالية المعروضة أثناء إتمام الطلب.
                        @endif
                    </p>
                </section>

                <section class="space-y-2">
                    <h2 class="text-base font-bold text-primary-800">5. الحسابات وأمان البيانات</h2>
                    <p class="text-charcoal-muted">
                        أنت مسؤول تماماً عن الحفاظ على سرية بيانات حسابك وكلمة المرور الخاصة بك. يرجى إبلاغنا فوراً في حال الاشتباه في أي استخدام غير مصرح به لحسابك.
                    </p>
                </section>

                <section class="space-y-2">
                    <h2 class="text-base font-bold text-primary-800">6. التواصل والشكاوى</h2>
                    <p class="text-charcoal-muted">
                        يسعدنا استقبال استفساراتكم عبر صفحة <a href="{{ route('contact.index') }}" class="text-primary-800 font-bold underline">اتصل بنا</a> أو على الرقم (<span dir="ltr">{{ settings('store_phone', '01002527072') }}</span>) أو الواتساب الموضح في الموقع.
                    </p>
                </section>
            </div>
            @endif
        </div>

    </div>
</div>
@endsection
