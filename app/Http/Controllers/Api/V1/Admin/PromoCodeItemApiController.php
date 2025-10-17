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
        $code  = (string) $request->input('code', '');
        // total da encomenda (ex.: total do carrinho após eventuais sales, antes do desconto)
        $value = (float)  $request->input('value', 0);

        $promoCode = PromoCodeItem::where('code', $code)
            ->whereDate('start_date', '<=', Carbon::today())
            ->whereDate('end_date', '>=', Carbon::today())
            ->where('status', 1)
            ->first();

        if (!$promoCode) {
            return response()->json([
                'success' => false,
                'message' => 'Código inválido ou fora do prazo de validade.',
            ]);
        }

        // Limite de utilizações
        if (!is_null($promoCode->qty_remain) && (int)$promoCode->qty_remain <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Este código promocional já foi utilizado o número máximo de vezes.',
            ]);
        }

        // ✅ Verificação do valor mínimo da encomenda
        // Só aplica se o campo existir e for > 0
        $min = (float) $promoCode->min_value;
        if ($min > 0 && $value < $min) {
            return response()->json([
                'success' => false,
                'message' => 'O valor mínimo para usar este código é de ' . number_format($min, 2, ',', '.') . ' €.',
                'description' => $promoCode->description,
                'data' => [
                    'type'        => $promoCode->type,
                    'value'       => $promoCode->amount,
                    'name'        => $promoCode->name,
                    'code'        => $promoCode->code,
                    'valid_until' => $promoCode->end_date, // já formatado pelo accessor
                    'min_value'   => $promoCode->min_value,
                    'promo'       => $promoCode->promo,
                    'pack_id'     => $promoCode->pack_id,
                ],
            ]);
        }

        // Se passou em tudo, é válido
        return response()->json([
            'success' => true,
            'message' => 'Código válido!',
            'description' => $promoCode->description,
            'data' => [
                'type'        => $promoCode->type,
                'value'       => $promoCode->amount,
                'name'        => $promoCode->name,
                'code'        => $promoCode->code,
                'valid_until' => $promoCode->end_date,
                'min_value'   => $promoCode->min_value,
                'promo'       => $promoCode->promo,
                'pack_id'     => $promoCode->pack_id,
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
