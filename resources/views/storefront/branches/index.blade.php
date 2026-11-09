@extends('layouts.storefront')
@section('title', 'موقع مؤسسة حورس | حدائق الأهرام')
@section('content')
<div class="min-h-screen bg-[#f7f8f5] py-10" dir="rtl">
    <div class="max-w-6xl mx-auto px-4 space-y-8">
        <nav class="text-xs text-gray-500"><a href="{{ route('site.home') }}" class="hover:text-primary-700">الرئيسية</a> / <b class="text-gray-800">موقعنا</b></nav>
        <section class="bg-[#073f40] text-white p-8 sm:p-12 rounded-2xl">
            <span class="text-xs text-[#82d9c7] tracking-widest">زورونا</span>
            <h1 class="mt-3 text-3xl sm:text-4xl font-black">مؤسسة حورس في حدائق الأهرام</h1>
            <p class="mt-4 max-w-2xl text-sm leading-7 text-white/75">يسعدنا استقبالكم لمعاينة الخامات والاستفسار عن المنتجات والأسعار.</p>
        </section>
        <div class="grid md:grid-cols-2 gap-5">
            @forelse($branches as $branch)
                <article class="bg-white rounded-2xl border border-gray-200 p-6 sm:p-8">
                    <span class="text-xs text-primary-700 font-bold">مؤسسة حورس</span>
                    <h2 class="mt-2 text-xl font-black text-gray-900">{{ $branch->name }}</h2>
                    <p class="mt-4 text-sm text-gray-600">{{ $branch->address }}</p>
                    @if($branch->phone)<a class="mt-3 inline-block font-bold text-primary-800" href="tel:{{ $branch->phone }}" dir="ltr">{{ $branch->phone }}</a>@endif
                    <div class="mt-6 flex flex-wrap gap-3">
                        @if($branch->google_maps_url)<a href="{{ $branch->google_maps_url }}" target="_blank" rel="noopener" class="px-4 py-2 rounded bg-[#eaf5f2] text-primary-800 text-xs font-bold">افتح الموقع على الخريطة</a>@endif
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $branch->whatsapp ?: '201002527072') }}" class="px-4 py-2 rounded bg-[#073f40] text-white text-xs font-bold">تواصل واتساب</a>
                    </div>
                </article>
            @empty
                <article class="bg-white rounded-2xl border border-gray-200 p-6 sm:p-8"><span class="text-xs text-primary-700 font-bold">مؤسسة حورس</span><h2 class="mt-2 text-xl font-black text-gray-900">حدائق الأهرام، بوابة حورس</h2><p class="mt-4 text-sm text-gray-600">٢٢ع حدائق الأهرام، بوابة حورس</p><a class="mt-3 inline-block font-bold text-primary-800" href="tel:01002527072" dir="ltr">010 0252 7072</a></article>
            @endforelse
        </div>
        <div class="bg-[#eaf5f2] rounded-xl p-5 text-sm text-gray-700">للاستفسار عن التوصيل خارج المنطقة، يرجى التواصل معنا للتأكد من التغطية.</div>
    </div>
</div>
@endsection
