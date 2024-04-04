<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\StoreLocationRequest;
use App\Http\Requests\UpdateLocationRequest;
use App\Http\Resources\Admin\LocationResource;
use App\Models\Location;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LocationApiController extends Controller
{
    use MediaUploadingTrait;

    public function index()
    {
        return new LocationResource(Location::all());
    }

    public function show(Location $location)
    {
        return new LocationResource($location->load('spots.country'));
    }

}
