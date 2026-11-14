{{-- Hidden fields every website form carries: CSRF token, the page it was sent from, and a honeypot people never see. --}}
@csrf
<input type="hidden" name="source_page" value="/{{ ltrim(request()->path(), '/') }}">
<div aria-hidden="true" style="position:absolute;left:-10000px;top:auto;width:1px;height:1px;overflow:hidden"><label>Leave this field empty<input type="text" name="{{ \App\Http\Requests\Forms\WebsiteFormRequest::HONEYPOT }}" value="" tabindex="-1" autocomplete="off"></label></div>
