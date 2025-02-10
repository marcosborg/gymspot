<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\StorePackRequest;
use App\Http\Requests\UpdatePackRequest;
use App\Http\Resources\Admin\PackResource;
use App\Models\Pack;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PacksApiController extends Controller
{
    use MediaUploadingTrait;

    public function index()
    {
        abort_if(Gate::denies('pack_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new PackResource(Pack::with(['spot'])->get());
    }

    public function store(StorePackRequest $request)
    {
        $pack = Pack::create($request->all());

        if ($request->input('image', false)) {
            $pack->addMedia(storage_path('tmp/uploads/' . basename($request->input('image'))))->toMediaCollection('image');
        }

        return (new PackResource($pack))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Pack $pack)
    {
        abort_if(Gate::denies('pack_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new PackResource($pack->load(['spot']));
    }

    public function update(UpdatePackRequest $request, Pack $pack)
    {
        $pack->update($request->all());

        if ($request->input('image', false)) {
            if (! $pack->image || $request->input('image') !== $pack->image->file_name) {
                if ($pack->image) {
                    $pack->image->delete();
                }
                $pack->addMedia(storage_path('tmp/uploads/' . basename($request->input('image'))))->toMediaCollection('image');
            }
        } elseif ($pack->image) {
            $pack->image->delete();
        }

        return (new PackResource($pack))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(Pack $pack)
    {
        abort_if(Gate::denies('pack_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $pack->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
