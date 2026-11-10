@php($it = \App\Models\Itinerary::published()->signature()->ordered()->first())
@if($it)
      <div class="jdetail reveal-right" role="tabpanel">
        <div class="pic img-zoom"><img id="j-img" src="{{ $it->card_image }}" alt="{{ $it->title }}" loading="lazy"><span class="route" id="j-route">{!! $it->card_route !!}</span></div>
        <div class="body"><p id="j-sum">{!! $it->summary !!}</p><ul class="dots g-2 grid" id="j-inc">@foreach($it->highlights ?? [] as $h)<li>{!! $h !!}</li>@endforeach</ul>
          <div><a class="btn btn-gold btn-sm" id="j-cta" href="{{ route('itineraries.show', $it) }}"><span>See the day-by-day</span>{!! \App\Support\Content::icon('arrow') !!}</a></div></div>
      </div>
@endif
