{{-- General enquiry form on /contact: <!--wam:contact-form--> in the page content. Works with and without JavaScript. --}}
@php($bag = $errors->getBag('contact'))
@php($old = fn ($key) => $bag->any() ? old($key) : '')
<form class="form-grid" id="contact-form" data-simple data-form="contact" method="post" action="{{ route('forms.contact') }}" novalidate style="margin-top:1.75rem">
      @include('partials.form-guard')
      <div class="form-grid two"><label class="lbl{{ $bag->has('name') ? ' bad' : '' }}">Name *<input class="field" name="name" value="{{ $old('name') }}" required autocomplete="name" placeholder="Your name"><span class="err">{{ $bag->first('name') ?: 'Please add your name.' }}</span></label>
      <label class="lbl{{ $bag->has('email') ? ' bad' : '' }}">Email *<input class="field" type="email" name="email" value="{{ $old('email') }}" required autocomplete="email" placeholder="you@studio.com"><span class="err">{{ $bag->first('email') ?: 'Please add a valid email address.' }}</span></label></div>
      <label class="lbl{{ $bag->has('message') ? ' bad' : '' }}">Message *<textarea class="field" name="message" required rows="5" placeholder="How can we help?">{{ $old('message') }}</textarea><span class="err">{{ $bag->first('message') ?: 'Please write a short message.' }}</span></label>
      <label class="check lbl{{ $bag->has('privacy') ? ' bad' : '' }}" style="text-transform:none;letter-spacing:0;font-size:.875rem"><input type="checkbox" name="privacy" value="1" required> I agree to the <a href="{{ route('privacy') }}" style="color:var(--gold-light)">privacy policy</a>.<span class="err">{{ $bag->first('privacy') ?: 'Please accept the privacy policy.' }}</span></label>
      <button class="btn btn-gold" type="submit"><span>Send message</span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg></button>
      <p class="notice" data-done @if(session('form_sent') !== 'contact') hidden @endif>Thank you. Your message is with the atelier in Marrakech; we reply within one business day.</p>
    </form>
