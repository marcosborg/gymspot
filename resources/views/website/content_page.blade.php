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
<div class="container mt-5 mb-5 pt-5 pb-5">
    <h1>{{ $content_page->title }}</h1>
    <div class="card mt-4 mb-4">
        <div class="card-body">
            <p>{{ $content_page->excerpt }}</p>
        </div>
    </div>
    <div class="row">
        @if ($content_page->featured_image)
        <div class="col-md-6">
            <img src="{{ $content_page->featured_image ? $content_page->featured_image->getUrl() : '' }}"
                class="img-thumbnail">
        </div>
        @endif
        <div class="col">
            <div class="card text-justify">
                <div class="card-body">
                    {!! $content_page->page_text !!}
                </div>
            </div>
        </div>
    </div>
</div>
<x-call></x-call>
<x-services></x-services>
<x-gallery></x-gallery>
<x-testimonial></x-testimonial>
<x-course></x-course>
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