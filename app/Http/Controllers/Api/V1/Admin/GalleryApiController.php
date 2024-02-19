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
        abort_if(Gate::denies('gallery_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new GalleryResource(Gallery::all());
    }

    public function store(StoreGalleryRequest $request)
    {
        $gallery = Gallery::create($request->all());

        if ($request->input('image', false)) {
            $gallery->addMedia(storage_path('tmp/uploads/' . basename($request->input('image'))))->toMediaCollection('image');
        }

        return (new GalleryResource($gallery))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Gallery $gallery)
    {
        abort_if(Gate::denies('gallery_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new GalleryResource($gallery);
    }

    public function update(UpdateGalleryRequest $request, Gallery $gallery)
    {
        $gallery->update($request->all());

        if ($request->input('image', false)) {
            if (! $gallery->image || $request->input('image') !== $gallery->image->file_name) {
                if ($gallery->image) {
                    $gallery->image->delete();
                }
                $gallery->addMedia(storage_path('tmp/uploads/' . basename($request->input('image'))))->toMediaCollection('image');
            }
        } elseif ($gallery->image) {
            $gallery->image->delete();
        }

        return (new GalleryResource($gallery))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(Gallery $gallery)
    {
        abort_if(Gate::denies('gallery_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $gallery->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
