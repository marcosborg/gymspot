<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\StoreAboutRequest;
use App\Http\Requests\UpdateAboutRequest;
use App\Http\Resources\Admin\AboutResource;
use App\Models\About;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AboutApiController extends Controller
{
    use MediaUploadingTrait;

    public function index()
    {
        return new AboutResource(About::all());
    }

}
