<div class="modal" id="inquiry" role="dialog" aria-modal="true" aria-labelledby="inq-title" hidden>
  <div class="scrim" data-close></div>
  <div class="panel">
    <button type="button" class="icon-btn close" data-close aria-label="Close"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M6 6l12 12M18 6 6 18"/></svg></button>
    <p class="eyebrow" style="color:rgba(228,204,154,.9)">Private atelier</p>
    <h2 class="h2" id="inq-title" style="margin-top:1rem">Begin a private conversation.</h2>
    <form id="inq-form" data-form="inquiry" method="post" action="{{ route('forms.inquiry') }}" data-quote-action="{{ route('forms.quote') }}" novalidate>
      @include('partials.form-guard')
      <input type="hidden" name="brief" value="">
      <p class="small" style="margin-top:.75rem;color:rgba(239,230,214,.7)">Tell us how you like to travel. We will send a considered first sketch — not a brochure.</p>
      <div class="form-grid two" style="margin-top:1.75rem">
        <label class="lbl">Name<input class="field" name="name" required autocomplete="name" placeholder="Your name"><span class="err">Please add your name.</span></label>
        <label class="lbl">Email<input class="field" type="email" name="email" required autocomplete="email" placeholder="you@studio.com"><span class="err">Please add a valid email.</span></label>
        <label class="lbl">Travel window<input class="field" name="dates" placeholder="Apr 2027 · flexible"></label>
        <label class="lbl">Travelers<input class="field" name="travelers" placeholder="2 adults"></label>
      </div>
      <label class="lbl" style="display:block;margin-top:1rem">Interest<select class="field" name="intent" id="inq-intent"><option value="imperial-morocco">Imperial Morocco — 8 days</option><option value="magical-morocco">Magical Morocco — 11 days</option><option value="grand-morocco">The Grand Morocco — 12 days</option><option value="custom">A custom stay I just designed</option><option value="event">Wedding, retreat, or gathering</option><option value="incentive">Incentive or group program</option><option value="advisor">I am a travel advisor</option></select></label>
      <label class="lbl" style="display:block;margin-top:1rem">How should this feel?<textarea class="field" name="note" id="inq-note" rows="3" placeholder="Pace, privacy, must-sees, hard nos…"></textarea></label>
      <div class="draft" id="inq-brief" hidden style="margin-top:1rem"><h4>Your draft — sent with this message</h4><pre style="margin:.75rem 0 0;white-space:pre-wrap;font:400 .8125rem/1.6 var(--font-sans);color:rgba(239,230,214,.8)"></pre></div>
      <button class="btn btn-gold btn-block" type="submit" style="margin-top:1.5rem"><span>Send to the atelier</span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg></button>
      <p class="small" style="margin-top:1rem;text-align:center;color:var(--stone)">We reply as people. No newsletter, no CRM sequence.</p>
    </form>
    <div id="inq-done" hidden>
      <p class="lead" style="color:rgba(239,230,214,.78)">A producer will reply within one business day — usually sooner. If your dates are close, write us at <a href="mailto:{{ $site['email'] }}" style="color:var(--gold-light)">{{ $site['email'] }}</a> or WhatsApp <a href="tel:{{ $site['phone'] }}" style="color:var(--gold-light)">{{ $site['phone_display'] }}</a>.</p>
      <button type="button" class="btn btn-ghost" data-close style="margin-top:1.75rem">Return to the page</button>
    </div>
  </div>
</div>
