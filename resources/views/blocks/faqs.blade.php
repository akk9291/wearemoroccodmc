{{-- group="...": faqs.group; open="1": first answer expanded --}}
@php($first = ($open ?? '') === '1')
@foreach(\App\Models\Faq::published()->where('group', $group)->ordered()->get() as $f)<div class="faq-item{{ $first && $loop->first ? ' open' : '' }} reveal" style="transition-delay:{{ $loop->index * 40 }}ms"><button type="button" class="faq-q" aria-expanded="{{ $first && $loop->first ? 'true' : 'false' }}">{!! $f->question !!}<span class="tog" aria-hidden="true">+</span></button><div class="faq-a"><div>{!! \App\Support\Content::urls($f->answer) !!}</div></div></div>@endforeach
