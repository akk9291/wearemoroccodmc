@php($stories = \App\Models\Testimonial::published()->where('placement', $placement ?? 'guests')->ordered()->get()->map(fn ($t) => [
    'img' => $t->image,
    'name' => $t->name,
    'role' => $t->role,
    'trip' => $t->trip,
    'quote' => $t->quote,
]))
<script>window.WAM_STORIES={!! json_encode($stories, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!};</script>
