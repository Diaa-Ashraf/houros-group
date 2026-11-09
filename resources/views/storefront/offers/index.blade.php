@extends('layouts.storefront')
@section('title', 'عروض مؤسسة حورس للتوريدات والتشطيبات')
@section('content')
@php
    $storePhone = settings('store_phone', '01002527072');
    $storeWhatsApp = preg_replace('/[^0-9]/', '', settings('store_whatsapp', '201002527072'));
@endphp
<div class="min-h-screen bg-[#f7f8f5] py-8" dir="rtl">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 space-y-8">
        <nav class="text-xs text-gray-500"><a href="{{ route('site.home') }}" class="hover:text-primary-700">الرئيسية</a> / <b class="text-gray-800">العروض</b></nav>
        <section class="rounded-2xl bg-[#073f40] text-white p-8 sm:p-12 relative overflow-hidden">
            <div class="relative z-10"><span class="text-xs text-[#82d9c7] tracking-widest">مؤسسة حورس للتوريدات والتشطيبات</span><h1 class="mt-3 text-3xl sm:text-4xl font-black">عروض وخيارات <span class="text-[#82d9c7]">حورس</span></h1><p class="mt-3 max-w-2xl text-sm leading-7 text-white/75">تابع المنتجات المتاحة والعروض الحالية. وللاستفسار عن أسعار الكميات، تواصل معنا مباشرة.</p><a class="inline-block mt-5 px-5 py-3 rounded bg-white text-[#073f40] text-xs font-bold" href="https://wa.me/{{ $storeWhatsApp }}">اسأل عن الأسعار</a></div>
            <span class="absolute -left-4 -bottom-20 text-[190px] font-serif text-white/[.04]">ح</span>
        </section>
        @if($discountedProducts->isNotEmpty())
            <section><div class="flex items-end justify-between mb-5"><div><span class="text-[10px] tracking-widest text-primary-700 font-bold">اختيارات خاصة</span><h2 class="mt-1 text-2xl font-black text-gray-900">منتجات عليها عروض</h2></div></div><div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">@foreach($discountedProducts as $product)<x-storefront.ui.product-card :product="$product" />@endforeach</div><div class="mt-6">{{ $discountedProducts->links() }}</div></section>
        @else
            <section class="bg-white border border-gray-200 rounded-2xl p-8 sm:p-12 text-center"><span class="text-4xl text-primary-700">✦</span><h2 class="mt-3 text-xl font-black text-gray-900">تابع العروض القادمة</h2><p class="mt-2 text-sm text-gray-500">تواصل معنا لمعرفة أحدث الأسعار والعروض المتاحة على خامات التشطيب.</p><a class="inline-block mt-5 px-5 py-3 rounded bg-primary-800 text-white text-xs font-bold" href="tel:{{ $storePhone }}">{{ $storePhone }}</a></section>
        @endif
    </div>
</div>
@endsection
