<footer class="site-footer">
  <div class="container f-top">
    <div>
      <a class="brand brand-logo brand-full" href="{{ route('home') }}" aria-label="We Are Morocco DMC, home"><picture><source type="image/webp" srcset="{{ asset('assets/img/logo/wearemorocco-gold.webp') }}"><img src="{{ asset('assets/img/logo/wearemorocco-gold.png') }}" alt="We Are Morocco DMC" width="800" height="168" loading="lazy" decoding="async"></picture></a>
      <p class="f-blurb">{{ $site['footer_blurb'] }}</p>
      @include('partials.newsletter')
    </div>
    <div class="f-cols">
      <div class="f-col"><h4>{{ $site['footer_columns'][0] }}</h4><ul>@foreach($menus['footer-explore'] as $item)<li><a href="{{ $item->href() }}">{{ $item->label }}</a></li>@endforeach</ul></div>
      <div class="f-col"><h4>{{ $site['footer_columns'][1] }}</h4><ul>@foreach($menus['footer-services'] as $item)<li><a href="{{ $item->href() }}">{{ $item->label }}</a></li>@endforeach</ul></div>
      <div class="f-col"><h4>{{ $site['footer_columns'][2] }}</h4><ul><li>{{ $site['address'] }}</li><li><a href="tel:{{ $site['phone'] }}">{{ $site['phone_display'] }}</a></li><li><a href="mailto:{{ $site['email'] }}">{{ $site['email'] }}</a></li><li>@foreach($site['socials'] as $social)@if(! $loop->first) · @endif<a href="{{ $social['url'] }}">{{ $social['label'] }}</a>@endforeach</li></ul></div>
    </div>
  </div>
  <div class="container legal"><span>{{ $site['copyright'] }}</span><nav aria-label="Legal">@foreach($menus['legal'] as $item)<a href="{{ $item->href() }}">{{ $item->label }}</a>@endforeach</nav><span>{{ $site['footer_tagline'] }}</span></div>
</footer>
