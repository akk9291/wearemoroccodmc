{{-- Newsletter sign-up. Footer (partials/newsletter) and <!--wam:newsletter-form id="" label="" button="" style=""--> in page content. --}}
@php($bag = $errors->getBag('newsletter'))
<form class="news{{ $bag->any() ? ' bad' : '' }}" data-simple data-form="newsletter" method="post" action="{{ route('forms.newsletter') }}" novalidate @if(!empty($style)) style="{{ $style }}" @endif>
  @include('partials.form-guard')
  <label class="lbl" for="{{ $id ?? 'news-email' }}">{{ $label ?? 'New houses and seasonal ideas, once a month' }}</label>
  <div class="row"><input class="field" id="{{ $id ?? 'news-email' }}" type="email" name="email" placeholder="you@studio.com" required autocomplete="email"><button class="btn btn-gold" type="submit">{{ $button ?? 'Join' }}</button></div>
  <p class="err" data-err="email">{{ $bag->first('email') ?: 'Please add a valid email.' }}</p>
  <p class="ok" data-done @if(session('form_sent') !== 'newsletter') hidden @endif>Thank you. You are on the list: one letter a month, nothing more.</p>
</form>
