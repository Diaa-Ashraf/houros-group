@extends('layouts.storefront')

@section('title', 'عن مؤسسة حورس للتوريدات والتشطيبات')
@section('content')
<div class="min-h-screen bg-[#f7f8f5] py-8" dir="rtl">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 space-y-8">
        <nav class="text-xs text-gray-500"><a href="{{ route('site.home') }}" class="hover:text-primary-700">الرئيسية</a> / <b class="text-gray-800">من نحن</b></nav>
        <section class="relative overflow-hidden rounded-2xl bg-[#073f40] text-white p-8 sm:p-14">
            <span class="text-xs tracking-widest text-[#82d9c7]">مؤسسة حورس للتوريدات والتشطيبات</span>
            <h1 class="max-w-3xl mt-5 text-3xl sm:text-5xl leading-tight font-black">كل اللي تحتاجه لتشطيب عصري، <span class="text-[#82d9c7]">في مكان واحد.</span></h1>
            <p class="max-w-2xl mt-5 text-sm leading-8 text-white/75">نوفر خامات الديكور والتشطيب، دهانات SCIB، وأدوات ومستلزمات البناء. نساعدك تختار الخامة المناسبة من أول التأسيس لحد اللمسة الديكورية الأخيرة.</p>
            <div class="mt-7 flex flex-wrap gap-3"><a href="{{ route('site.products') }}" class="px-5 py-3 rounded bg-white text-[#073f40] text-xs font-bold">تصفح المنتجات</a><a href="{{ route('contact.index') }}" class="px-5 py-3 rounded border border-white/30 text-xs font-bold">تواصل معنا</a></div>
            <span class="absolute -left-10 -bottom-20 text-[190px] font-serif text-white/[.04]">ح</span>
        </section>
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-3">
            @foreach(['دهانات SCIB ومنتجات سكيب','أنظمة الجبسون بورد والأسقف المعلقة','كرانيش الفيوتك وبديل الخشب','أدوات ومستلزمات البناء والتشطيب'] as $item)
                <div class="bg-white border border-gray-200 rounded-xl p-5"><span class="text-primary-700 font-black">✦</span><p class="mt-3 text-sm font-bold text-gray-800">{{ $item }}</p></div>
            @endforeach
        </div>
        <section class="grid md:grid-cols-2 gap-8 items-center bg-white rounded-2xl border border-gray-200 p-7 sm:p-10">
            <div><span class="text-[10px] tracking-widest text-primary-700 font-bold">خبرة في مجال التشطيب</span><h2 class="mt-2 text-2xl font-black text-gray-900">نشتغل على التفاصيل اللي بتفرق.</h2></div>
            <p class="text-sm leading-8 text-gray-600">مع خبرة أكثر من 10 سنوات، بنقدم خامات واختيارات متنوعة تناسب المهندسين والمقاولين وكل شخص بيجهز بيته. هدفنا نوفر جودة موثوقة وخدمة واضحة من وقت السؤال لحد اختيار المنتجات.</p>
        </section>
        <section class="bg-[#eaf5f2] rounded-2xl p-7 sm:p-9 flex flex-col md:flex-row justify-between gap-5 items-start md:items-center">
            <div><h2 class="text-xl font-black text-[#073f40]">زورونا في حدائق الأهرام</h2><p class="mt-2 text-sm text-gray-600">٢٢ع حدائق الأهرام، بوابة حورس</p></div>
            <a href="tel:{{ settings('store_phone', '01002527072') }}" class="text-lg font-black text-primary-800" dir="ltr">{{ settings('store_phone', '01002527072') }}</a>
        </section>
    </div>
</div>
@endsection
