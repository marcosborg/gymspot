<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\StoreSpotRequest;
use App\Http\Requests\UpdateSpotRequest;
use App\Http\Resources\Admin\SpotResource;
use App\Models\Spot;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SpotApiController extends Controller
{
    use MediaUploadingTrait;

    public function index()
    {
        if (request()->query('limit')) {
            $limit = request()->query('limit');
            $spots = new SpotResource(Spot::with(['location', 'country'])->inRandomOrder()->limit($limit)->get());
        } else {
            $spots = new SpotResource(Spot::with(['location', 'country'])->inRandomOrder()->get());
        }

        return $spots;
    }

    public function store(StoreSpotRequest $request)
    {
        $spot = Spot::create($request->all());
        $spot->items()->sync($request->input('items', []));
        foreach ($request->input('photos', []) as $file) {
            $spot->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('photos');
        }

        return (new SpotResource($spot))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Spot $spot)
    {
        return new SpotResource($spot->load(['location', 'country', 'items']));
    }

    public function update(UpdateSpotRequest $request, Spot $spot)
    {
        $spot->update($request->all());
        $spot->items()->sync($request->input('items', []));
        if (count($spot->photos) > 0) {
            foreach ($spot->photos as $media) {
                if (!in_array($media->file_name, $request->input('photos', []))) {
                    $media->delete();
                }
            }
        }
        $media = $spot->photos->pluck('file_name')->toArray();
        foreach ($request->input('photos', []) as $file) {
            if (count($media) === 0 || !in_array($file, $media)) {
                $spot->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('photos');
            }
        }

        return (new SpotResource($spot))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(Spot $spot)
    {
        abort_if(Gate::denies('spot_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $spot->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
