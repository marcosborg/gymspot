<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePromoCodeItemRequest;
use App\Http\Requests\UpdatePromoCodeItemRequest;
use App\Http\Resources\Admin\PromoCodeItemResource;
use App\Models\PromoCodeItem;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;

class PromoCodeItemApiController extends Controller
{

    public function validatePromoCode(Request $request)
    {
        $code = $request->code;

        $promoCode = PromoCodeItem::where('code', $code)
            ->whereDate('start_date', '<=', Carbon::today())
            ->whereDate('end_date', '>=', Carbon::today())
            ->first();

        if (!$promoCode) {
            return response()->json([
                'success' => false,
                'message' => 'Código inválido ou fora do prazo de validade.',
            ]);
        }

        if ($promoCode->qty_remain !== null && $promoCode->qty_remain <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Este código promocional já foi utilizado o número máximo de vezes.',
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Código válido!',
            'description' => $promoCode->description,
            'data' => [
                'type'   => $promoCode->type,
                'value'  => $promoCode->amount,
                'name'   => $promoCode->name,
                'code'   => $promoCode->code,
                'valid_until' => $promoCode->end_date,
                'min_value' => $promoCode->min_value,
            ],
        ]);
    }

    public function index()
    {
        abort_if(Gate::denies('promo_code_item_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new PromoCodeItemResource(PromoCodeItem::with(['user'])->get());
    }

    public function store(StorePromoCodeItemRequest $request)
    {
        $promoCodeItem = PromoCodeItem::create($request->all());

        return (new PromoCodeItemResource($promoCodeItem))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(PromoCodeItem $promoCodeItem)
    {
        abort_if(Gate::denies('promo_code_item_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new PromoCodeItemResource($promoCodeItem->load(['user']));
    }

    public function update(UpdatePromoCodeItemRequest $request, PromoCodeItem $promoCodeItem)
    {
        $promoCodeItem->update($request->all());

        return (new PromoCodeItemResource($promoCodeItem))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(PromoCodeItem $promoCodeItem)
    {
        abort_if(Gate::denies('promo_code_item_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $promoCodeItem->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
