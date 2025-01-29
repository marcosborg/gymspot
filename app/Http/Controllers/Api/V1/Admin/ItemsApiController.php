<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\StoreItemRequest;
use App\Http\Requests\UpdateItemRequest;
use App\Http\Resources\Admin\ItemResource;
use App\Models\Item;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ItemsApiController extends Controller
{
    use MediaUploadingTrait;

    public function index()
    {
        abort_if(Gate::denies('item_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new ItemResource(Item::all());
    }

    public function store(StoreItemRequest $request)
    {
        $item = Item::create($request->all());

        if ($request->input('icon', false)) {
            $item->addMedia(storage_path('tmp/uploads/' . basename($request->input('icon'))))->toMediaCollection('icon');
        }

        return (new ItemResource($item))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Item $item)
    {
        abort_if(Gate::denies('item_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new ItemResource($item);
    }

    public function update(UpdateItemRequest $request, Item $item)
    {
        $item->update($request->all());

        if ($request->input('icon', false)) {
            if (! $item->icon || $request->input('icon') !== $item->icon->file_name) {
                if ($item->icon) {
                    $item->icon->delete();
                }
                $item->addMedia(storage_path('tmp/uploads/' . basename($request->input('icon'))))->toMediaCollection('icon');
            }
        } elseif ($item->icon) {
            $item->icon->delete();
        }

        return (new ItemResource($item))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(Item $item)
    {
        abort_if(Gate::denies('item_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $item->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
