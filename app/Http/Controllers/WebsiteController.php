<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ContentPage;

class WebsiteController extends Controller
{
    public function index()
    {
        return view('website.index');
    }

    public function contentPage($content_page_id, $slug)
    {
        $content_page = ContentPage::find($content_page_id);
        
        return view('website.content_page', compact('content_page'));
    }

    public function welcome()
    {
        return view('website.welcome');
    }
}
