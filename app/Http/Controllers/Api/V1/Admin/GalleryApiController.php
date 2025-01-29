<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\StoreGalleryRequest;
use App\Http\Requests\UpdateGalleryRequest;
use App\Http\Resources\Admin\GalleryResource;
use App\Models\Gallery;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GalleryApiController extends Controller
{
    use MediaUploadingTrait;

    public function index()
    {
        return new GalleryResource(Gallery::all());
    }

}
