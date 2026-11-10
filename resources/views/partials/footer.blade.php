<footer class="site-footer">
  <div class="container f-top">
    <div>
      <a class="brand" href="{{ route('home') }}" aria-label="We Are Morocco, home"><svg viewBox="0 0 32 32" aria-hidden="true" class="star"><path fill="currentColor" d="M16 2.5 18.4 12.2 28.5 14.5 18.4 16.8 16 26.5 13.6 16.8 3.5 14.5 13.6 12.2Z"/></svg><span class="word">weare<i>morocco</i></span></a>
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
