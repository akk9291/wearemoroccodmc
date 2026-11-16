{{-- Ordered content blocks from a "sections" JSON column: html | cta | script. Blocks with hidden=true are kept but not shown. --}}
@foreach($sections ?? [] as $b)
@continue(! empty($b['hidden']))
@if($b['type'] === 'html')
{!! \App\Support\Content::render($b['html']) !!}
@elseif($b['type'] === 'cta')
@include('blocks.cta', ['b' => $b])
@elseif($b['type'] === 'script')
@push('scripts')
@include('scripts.'.$b['name'], $b)
@endpush
@endif
@endforeach
