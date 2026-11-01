<div class="floating-contact fixed end-4 z-40 flex flex-col gap-2.5 md:hidden">
    <a href="{{ \App\Support\Site::phoneHref() }}"
       class="flex size-12 items-center justify-center rounded-full bg-navy text-white shadow-lg ring-2 ring-white/90 transition hover:scale-105 hover:bg-navy-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-navy"
       aria-label="تماس تلفنی با پلاستو ماشین" title="{{ \App\Support\Site::phoneDisplay() }}">
        <x-icon name="phone" class="size-5" />
    </a>

    <a href="{{ \App\Support\Site::whatsappUrl(null, 'سلام، برای خرید دستگاه تولید و دوخت نایلون راهنمایی می‌خواهم.') }}" target="_blank" rel="noopener"
       class="flex size-12 items-center justify-center rounded-full bg-whatsapp text-white shadow-lg ring-2 ring-white/90 transition hover:scale-105 hover:bg-whatsapp-dark focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-whatsapp"
       aria-label="مشاوره در واتساپ" title="مشاوره در واتساپ">
        <x-icon name="whatsapp" class="size-6" />
    </a>
</div>
