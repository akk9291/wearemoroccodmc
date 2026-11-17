<footer class="site-footer">
  <div class="container f-top">
    <div>
      <a class="brand" href="{{ route('home') }}" aria-label="We Are Morocco, home"><svg viewBox="0 0 32 32" aria-hidden="true" class="star"><path fill="currentColor" d="M16 2.5 18.4 12.2 28.5 14.5 18.4 16.8 16 26.5 13.6 16.8 3.5 14.5 13.6 12.2Z"/></svg><span class="word">weare<i>morocco</i></span></a>
      <p class="f-blurb">Private journeys, events and ground handling across Morocco, designed and run from our atelier in Marrakech. Local roots. Global standards.</p>
      @include('partials.newsletter')
    </div>
    <div class="f-cols">
      <div class="f-col"><h4>Explore</h4><ul><li><a href="{{ route('itineraries.index') }}">Signature journeys</a></li><li><a href="{{ route('destinations.index') }}">Destinations</a></li><li><a href="{{ route('experiences') }}">Experiences</a></li><li><a href="{{ route('quote') }}">Design your stay</a></li><li><a href="{{ route('journal.index') }}">Journal</a></li></ul></div>
      <div class="f-col"><h4>Services</h4><ul><li><a href="{{ route('services.show', 'tailor-made') }}">Tailor-made journeys</a></li><li><a href="{{ route('services.show', 'incentives') }}">Incentives &amp; groups</a></li><li><a href="{{ route('services.show', 'events') }}">Events &amp; weddings</a></li><li><a href="{{ route('services.show', 'ground-handling') }}">For travel advisors</a></li><li><a href="{{ route('case-studies.index') }}">Case studies</a></li></ul></div>
      <div class="f-col"><h4>Atelier</h4><ul><li>Av. Hassan II, Koutoubia Center II, 1er Étage, Bureau N°5, Marrakech</li><li><a href="tel:+212653051512">+212 653 051 512</a></li><li><a href="mailto:info@wearemoroccodmc.com">info@wearemoroccodmc.com</a></li><li><a href="#">Instagram</a> · <a href="#">Facebook</a></li></ul></div>
    </div>
  </div>
  <div class="container legal"><span>© 2026 We Are Morocco Travel.</span><nav aria-label="Legal"><a href="{{ route('about') }}">About</a><a href="{{ route('contact') }}">Contact</a><a href="{{ route('privacy') }}">Privacy</a><a href="{{ route('terms') }}">Terms</a><a href="{{ route('cookies') }}">Cookies</a></nav><span>Private travel · Events · DMC</span></div>
</footer>
