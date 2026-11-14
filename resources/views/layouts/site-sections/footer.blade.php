<!-- Horus Group site footer -->
<footer class="bg-[#073f40] text-white border-t border-[#82d9c7]/30 pt-14 pb-10">
 <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

 <!-- Main Footer Grid -->
 <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 pb-10 border-b border-white/15">

 <!-- Col 1: Brand Essence -->
 <div class="space-y-3">
 @php
 $footerLogo = get_site_logo();
 @endphp
 <a href="{{ route('site.home') }}" class="flex items-center gap-2.5 group">
 @if($footerLogo)
 <img src="{{ $footerLogo }}" alt="{{ settings('site_name', 'مؤسسة حورس للتوريدات والتشطيبات') }}" class="h-9 max-w-[45px] object-contain drop-shadow-sm rounded-lg">
 @else
                    <span class="horus-brand-mark w-10 h-10 rounded-xl text-white flex items-center justify-center font-black text-xl">ح</span>
 @endif
 <div>
 <h3 class="font-royal text-lg font-bold tracking-wider text-white group-hover:text-[#82d9c7] transition-colors">
 {{ settings('site_name', 'مؤسسة حورس للتوريدات والتشطيبات') }}
 </h3>
 <span class="text-[9px] tracking-[0.2em] text-[#82d9c7] uppercase block font-bold">
 {{ settings('site_tagline', 'حلول متكاملة للتشطيب والديكور') }}
 </span>
 </div>
 </a>

 <p class="text-xs leading-relaxed text-white/75 font-medium">
 {{ settings('about_us_short', 'خامات الديكور والتشطيب ودهانات SCIB وأدوات البناء في مكان واحد.') }}
 </p>

 <!-- Social Icons -->
 @php
 $storePhone = preg_replace('/[^0-9]/', '', settings('store_whatsapp', '201002527072'));
 $fbLink = settings('facebook_link', '');
 $instaLink = settings('instagram_link', '');
 $xLink = settings('twitter_link', '');
 @endphp
 <div class="flex items-center gap-2 pt-1">
 <a href="https://wa.me/{{ $storePhone }}" target="_blank" class="w-8 h-8 rounded-full bg-white/10 text-[#25D366] hover:bg-[#25D366] hover:text-white transition-all shadow-xs flex items-center justify-center" title="WhatsApp">
 <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
 <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z" />
 </svg>
 </a>
 @if($fbLink)
 <a href="{{ $fbLink }}" target="_blank" class="w-8 h-8 rounded-full bg-[#18181B] text-white hover:bg-black transition-all shadow-xs flex items-center justify-center" title="Facebook">
 <span class="text-xs font-bold">f</span>
 </a>
 @endif
 @if($instaLink)
 <a href="{{ $instaLink }}" target="_blank" class="w-8 h-8 rounded-full bg-[#18181B] text-white hover:bg-black transition-all shadow-xs flex items-center justify-center" title="Instagram">
 <span class="text-xs font-bold"></span>
 </a>
 @endif
 @if($xLink)
 <a href="{{ $xLink }}" target="_blank" class="w-8 h-8 rounded-full bg-[#18181B] text-white hover:bg-black transition-all shadow-xs flex items-center justify-center" title="X">
 <span class="text-xs font-bold">𝕏</span>
 </a>
 @endif
 </div>
 </div>

 <!-- Col 2: Quick Links -->
 <div>
 <h4 class="text-xs font-black text-[#18181B] uppercase tracking-wider mb-4 flex items-center gap-1.5">
 <span class="w-1.5 h-1.5 rounded-full bg-[#18181B]"></span>
 {{ __('استكشف المتجر') }}
 </h4>
 <ul class="space-y-2 text-xs font-bold text-[#18181B]/90">
 <li><a href="{{ route('site.home') }}" class="hover:text-white transition-colors">{{ __('الرئيسية') }}</a></li>
 <li><a href="{{ route('product.shop') }}" class="hover:text-white transition-colors">{{ __('كافة المنتجات والتشكيلات') }}</a></li>
 <li><a href="{{ route('product.shop', ['bundles_only' => 1]) }}" class="hover:text-white transition-colors flex items-center justify-between"><span>{{ __('باقات موفّرة وعروض') }}</span> <span class="text-[9px] px-1.5 py-0.2 bg-[#18181B] text-[#C5A059] rounded font-black"></span></a></li>
 <li><a href="{{ route('product.shop', ['discount_only' => 1]) }}" class="hover:text-white transition-colors">{{ __('عروض وتخفيضات خاصة') }}</a></li>
 <li><a href="{{ route('wishlist.index') }}" class="hover:text-white transition-colors">{{ __('قائمة المفضلة') }}</a></li>
 <li><a href="{{ route('cart') }}" class="hover:text-white transition-colors">{{ __('سلة المشتريات') }}</a></li>
 <li><a href="{{ route('contact') }}" class="hover:text-white transition-colors">{{ __('خدمة العملاء والتواصل') }}</a></li>
 </ul>
 </div>

 <!-- Col 3: Contact & Syrian Delivery -->
 <div>
 <h4 class="text-xs font-black text-[#18181B] uppercase tracking-wider mb-4 flex items-center gap-1.5">
 <span class="w-1.5 h-1.5 rounded-full bg-[#18181B]"></span>
 {{ __('التواصل والتوصيل') }}
 </h4>
 <ul class="space-y-2 text-xs font-semibold text-white/80">
 <li class="flex items-center gap-2">
 <span class="text-[#18181B]"></span>
                    <a href="{{ settings('google_maps_url', 'https://maps.app.goo.gl/AL2gbPB3KYh7Y2VFA?g_st=iwb') }}" target="_blank" rel="noopener noreferrer" class="hover:underline flex items-center gap-1.5">
                        <span>{{ settings('address', '٢٢ع حدائق الأهرام، بوابة حورس') }}</span>
                        <span class="text-[10px] text-[#C5A059] font-black">📍 الخريطة</span>
                    </a>
 </li>
 <li class="flex items-center gap-2">
 <span class="text-[#18181B]"></span>
 <a href="https://wa.me/{{ $storePhone }}" dir="ltr" class="text-[#18181B] hover:text-white font-bold transition-colors">
                    {{ settings('store_phone', '01002527072') }}
 </a>
 </li>
 <li class="flex items-center gap-2">
 <span class="text-[#18181B]">⏰</span>
 <span>{{ settings('working_hours', __('خدمة العملاء: يومياً 10:00 ص - 11:00 م')) }}</span>
 </li>
 </ul>
 </div>


 </div>

 <!-- Copyright -->
 <div class="pt-6 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs font-bold text-[#18181B]/80">
 <p>
 © {{ date('Y') }} {{ settings('site_name', 'مؤسسة حورس للتوريدات والتشطيبات') }}. {{ __('جميع الحقوق محفوظة.') }}
 </p>
 <div class="text-[11px] text-[#18181B] font-black">
 {{ settings('site_tagline', 'حلول متكاملة للتشطيب والديكور') }}
 </div>
 </div>

 <!-- Developer Attribution Bar (STACY WAY) -->
 <div class="mt-5 pt-3 border-t border-white/10 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs">
    <a href="https://wa.me/201202325201?text={{ urlencode('مرحباً، أود الاستفسار عن خدمات تطوير وبرمجة المواقع والمتاجر الإلكترونية من شركة STACY WAY') }}"
       target="_blank"
       rel="noopener noreferrer"
       class="inline-flex items-center gap-2.5 px-3 py-1.5 rounded-full bg-black/40 hover:bg-black/60 border border-white/15 hover:border-[#E2BF52]/60 shadow-sm transition-all duration-200">
        <img src="{{ asset('assets/site/img/stacy-way-logo.jpg') }}" 
             alt="STACY WAY" 
             class="w-6 h-6 rounded-full object-cover ring-1 ring-[#E2BF52]/70">
        <span class="text-[11px] text-white/90 font-medium">
            تطوير وتصميم بواسطة: <strong class="text-[#E2BF52] font-black">STACY WAY</strong>
        </span>
        <span class="w-1.5 h-1.5 rounded-full bg-[#25D366] animate-pulse"></span>
    </a>

    <a href="https://wa.me/201202325201?text={{ urlencode('مرحباً، أود الاستفسار عن خدمات تطوير وبرمجة المواقع والمتاجر الإلكترونية من شركة STACY WAY') }}"
       target="_blank"
       rel="noopener noreferrer"
       class="inline-flex items-center gap-2 text-[11px] text-[#25D366] hover:text-[#20bd5a] font-bold transition-colors bg-black/30 px-3 py-1.5 rounded-full border border-white/10">
        <svg class="w-3.5 h-3.5 fill-current shrink-0" viewBox="0 0 24 24">
            <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.174.086.275.073.376-.044.101-.116.433-.506.549-.68.116-.173.231-.144.39-.086s1.011.477 1.184.564.289.13.332.202c.045.072.045.419-.099.824z" />
        </svg>
        <span>تواصل مع المطور:</span>
        <span dir="ltr" class="font-black text-white">01202325201</span>
    </a>
 </div>

 </div>
</footer>
