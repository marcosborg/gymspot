<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePromoCodeUsageRequest;
use App\Http\Requests\UpdatePromoCodeUsageRequest;
use App\Http\Resources\Admin\PromoCodeUsageResource;
use App\Models\PromoCodeUsage;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PromoCodeUsageApiController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('promo_code_usage_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new PromoCodeUsageResource(PromoCodeUsage::with(['promo_code_item', 'client'])->get());
    }

    public function store(StorePromoCodeUsageRequest $request)
    {
        $promoCodeUsage = PromoCodeUsage::create($request->all());

        return (new PromoCodeUsageResource($promoCodeUsage))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(PromoCodeUsage $promoCodeUsage)
    {
        abort_if(Gate::denies('promo_code_usage_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new PromoCodeUsageResource($promoCodeUsage->load(['promo_code_item', 'client']));
    }

    public function update(UpdatePromoCodeUsageRequest $request, PromoCodeUsage $promoCodeUsage)
    {
        $promoCodeUsage->update($request->all());

        return (new PromoCodeUsageResource($promoCodeUsage))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(PromoCodeUsage $promoCodeUsage)
    {
        abort_if(Gate::denies('promo_code_usage_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $promoCodeUsage->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
