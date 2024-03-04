@extends('layouts.website')
@section('title')
{{ $content_page->title }}
@endsection
@section('description')
{{ $content_page->excerpt }}
@endsection
@section('content')
<div id="cms_header"
    style="width: 100%; height: 30vh; background-image: url('/assets/website/images/header.jpg'); background-size: cover">
</div>
@if ($content_page->page_text)
<div class="container mt-5 mb-5 pt-5 pb-5">
    <h1>{{ $content_page->title }}</h1>
    @if ($content_page->excerpt)
    <div class="card mt-4 mb-4">
        <div class="card-body">
            <p>{{ $content_page->excerpt }}</p>
        </div>
    </div>
    @endif

    <div class="row">
        @if ($content_page->featured_image)
        <div class="col-md-6">
            <img src="{{ $content_page->featured_image ? $content_page->featured_image->getUrl() : '' }}"
                class="img-thumbnail">
        </div>
        @endif
        @if ($content_page->page_text)
        <div class="col">
            <div class="card text-justify">
                <div class="card-body">
                    {!! $content_page->page_text !!}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endif
@if ($content_page->slider)
<x-slider></x-slider>
@endif
@if ($content_page->steps)
<x-intro></x-intro>
@endif
@if ($content_page->about)
<x-about></x-about>
@endif
@if ($content_page->call)
<x-call></x-call>
@endif
@if ($content_page->services)
<x-services></x-services>
@endif
@if ($content_page->faqs)
<x-faqs></x-faqs>
@endif
@if ($content_page->gallery)
<x-gallery></x-gallery>
@endif
@if ($content_page->testimonial)
<x-testimonial></x-testimonial>
@endif
@if ($content_page->location)
<x-course></x-course>
@endif
@endsection
@section('styles')
@parent
<style>
    #cms_header::after {
        content: ' ';
        background: #000;
        display: inline-block;
        width: 100%;
        height: 100%;
        opacity: 0.7;
    }
</style>
@endsection