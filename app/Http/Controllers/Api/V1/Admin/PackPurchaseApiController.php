<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePackPurchaseRequest;
use App\Http\Requests\UpdatePackPurchaseRequest;
use App\Http\Resources\Admin\PackPurchaseResource;
use App\Models\PackPurchase;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PackPurchaseApiController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('pack_purchase_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new PackPurchaseResource(PackPurchase::with(['user', 'pack'])->get());
    }

    public function store(StorePackPurchaseRequest $request)
    {
        $packPurchase = PackPurchase::create($request->all());

        return (new PackPurchaseResource($packPurchase))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(PackPurchase $packPurchase)
    {
        abort_if(Gate::denies('pack_purchase_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new PackPurchaseResource($packPurchase->load(['user', 'pack']));
    }

    public function update(UpdatePackPurchaseRequest $request, PackPurchase $packPurchase)
    {
        $packPurchase->update($request->all());

        return (new PackPurchaseResource($packPurchase))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(PackPurchase $packPurchase)
    {
        abort_if(Gate::denies('pack_purchase_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $packPurchase->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
