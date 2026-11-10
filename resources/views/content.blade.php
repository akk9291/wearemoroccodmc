{{-- Generic template for pages, services, destinations, case studies and journal posts: hero + section blocks. --}}
@extends('layouts.app')

@section('title', $m->meta_title)

@section('meta')
@include('partials.seo', ['m' => $m])
@endsection

@section('content')
@if($m->hero_image)
@include('partials.hero', ['m' => $m])
@endif
@include('partials.sections', ['sections' => $m->sections])
@endsection
