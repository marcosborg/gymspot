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
        abort_if(Gate::denies('about_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new AboutResource(About::all());
    }

    public function store(StoreAboutRequest $request)
    {
        $about = About::create($request->all());

        if ($request->input('image', false)) {
            $about->addMedia(storage_path('tmp/uploads/' . basename($request->input('image'))))->toMediaCollection('image');
        }

        return (new AboutResource($about))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(About $about)
    {
        abort_if(Gate::denies('about_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new AboutResource($about);
    }

    public function update(UpdateAboutRequest $request, About $about)
    {
        $about->update($request->all());

        if ($request->input('image', false)) {
            if (! $about->image || $request->input('image') !== $about->image->file_name) {
                if ($about->image) {
                    $about->image->delete();
                }
                $about->addMedia(storage_path('tmp/uploads/' . basename($request->input('image'))))->toMediaCollection('image');
            }
        } elseif ($about->image) {
            $about->image->delete();
        }

        return (new AboutResource($about))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(About $about)
    {
        abort_if(Gate::denies('about_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $about->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
