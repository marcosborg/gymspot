<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\RentAndPassTrait;
use App\Models\RentedSlot;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Client;
use App\Http\Controllers\Traits\IfthenPaymentsTrait;
use App\Models\PackPurchase;
use Illuminate\Support\Facades\Notification;
use App\Notifications\MulbancoReference;
use App\Models\User;
use App\Notifications\RentedSlotNotification;
use App\Models\PromoCodeItem;
use App\Models\PromoCodeUsage;
use App\Models\Pack;
use Illuminate\Support\Facades\DB;

class PaymentsController extends Controller
{
    use IfthenPaymentsTrait;
    use RentAndPassTrait;

    public function callbackMultibanco(Request $request)
    {

        if ($request->key !== env('ANTI_PHISHING_KEY')) {
            return response()->json([
                'error' => 'Chave anti-phishing inválida.',
            ], 403); // 403 Forbidden
        }
        $payment = Payment::where('request', $request->requestId)->first();

        if ($payment->paid == false) {
            $payment->paid = true;
            $payment->save();

            $cart = json_decode($payment->cart, true);

            $promo_code_usage = PromoCodeUsage::where('payment_id', $payment->id)->first();

            if ($promo_code_usage) {
                $promo_code_usage->used = 1;
                $promo_code_usage->save();
                $promo_code_item = PromoCodeItem::find($promo_code_usage->promo_code_item_id);
                $promo_code_item->qty_remain = $promo_code_item->qty_remain - 1;
                $promo_code_item->save();
            }

            if (isset($cart['price'])) {
                return $this->newPackPurchase($payment, $cart);
            } else {
                return $this->groupAdjacentSlots($cart, $payment->client_id);
            }
        }
    }

    public function callbackMbway(Request $request)
    {

        if ($request->key !== env('ANTI_PHISHING_KEY')) {
            return response()->json([
                'error' => 'Chave anti-phishing inválida.',
            ], 403); // 403 Forbidden
        }

        $payment = Payment::where('request', $request->requestId)->first();

        $cart = json_decode($payment->cart, true);

        if ($payment->paid == false) {
            $payment->paid = true;
            $payment->save();

            $promo_code_usage = PromoCodeUsage::where('payment_id', $payment->id)->first();

            if ($promo_code_usage) {
                $promo_code_usage->used = 1;
                $promo_code_usage->save();
                $promo_code_item = PromoCodeItem::find($promo_code_usage->promo_code_item_id);
                $promo_code_item->qty_remain = $promo_code_item->qty_remain - 1;
                $promo_code_item->save();
            }

            if (isset($cart['price'])) {
                return $this->newPackPurchase($payment, $cart);
            } else {
                return $this->groupAdjacentSlots($cart, $payment->client_id);
            }
        }
    }

    public function mbway(Request $request)
    {

        $user_id = $request->user()->id;
        $client = Client::where('user_id', $user_id)->first();
        $client_id = $client->id;
        $cart = $request->cart;
        $amount = $request->amount;
        $celphone = $request->celphone;

        $payment = new Payment;
        $payment->client_id = $client_id;
        $payment->method = 'mbway';
        $payment->cart = $cart;
        $payment->amount = $amount;
        $payment->save();

        $payment_mbway = $this->paymentMbway($payment->id, $amount, $celphone);

        $payment->request = $payment_mbway->RequestId;
        $payment->save();

        if ($request->promoCode['code'] !== '' && $request->promoCode['validPromoCode'] !== false) {
            $promo_code_item = PromoCodeItem::where('code', $request->promoCode['code'])->first();
            $promo_code_usage = new PromoCodeUsage;
            $promo_code_usage->promo_code_item_id = $promo_code_item->id;
            $promo_code_usage->client_id = $client_id;
            $promo_code_usage->payment_id = $payment->id;
            $promo_code_usage->value = $amount;
            $promo_code_usage->save();
        }

        return $payment_mbway;
    }

    public function checkMbwayStatus($requestId)
    {
        $mbway_status = $this->mbwayStatus($requestId);

        if ($mbway_status['Status'] == '000') {
            //SUCCESS
            $payment = Payment::where('request', $requestId)->first();
            $payment->paid = true;
            $payment->save();

            //AGRUPAR E GRAVAR
            $cart = json_decode($payment->cart, true);
            $this->groupAdjacentSlots($cart, $payment->client_id);
            return $mbway_status;
        } else {
            return $mbway_status;
        }
    }

    private function groupAdjacentSlots(array $slots, $client_id)
    {
        $groupedSlots = [];
        $currentGroup = [];

        foreach ($slots as $slot) {
            if (empty($currentGroup)) {
                // Iniciar um novo grupo
                $currentGroup[] = $slot;
            } else {
                // Obter o horário de término do último slot no grupo atual
                $lastSlotEnd = end($currentGroup)['end'];

                // Verificar se o slot atual começa imediatamente após o último slot do grupo atual
                if ($slot['start'] === $lastSlotEnd) {
                    // Se sim, adicionar ao grupo atual
                    $currentGroup[] = $slot;
                } else {
                    // Caso contrário, finalizar o grupo atual e iniciar um novo
                    $groupedSlots[] = [
                        'start_date_time' => $currentGroup[0]['timestamp'], // Ajustado para "start_date_time"
                        'end_date_time' => $this->calculateEndDateTime(end($currentGroup)['timestamp']),  // Agora calcula o horário correto de término
                        'client_id' => $client_id,
                        'spot_id' => $currentGroup[0]['spot']['id'],
                    ];
                    $currentGroup = [$slot];
                }
            }
        }

        // Adicionar o último grupo se existir
        if (!empty($currentGroup)) {
            $groupedSlots[] = [
                'start_date_time' => $currentGroup[0]['timestamp'],  // Ajustado para "start_date_time"
                'end_date_time' => $this->calculateEndDateTime(end($currentGroup)['timestamp']),   // Agora calcula o horário correto de término
                'client_id' => $client_id,
                'spot_id' => $currentGroup[0]['spot']['id'],
            ];
        }

        foreach ($groupedSlots as $slot) {
            $rented_slot = new RentedSlot;
            $rented_slot->client_id = $slot['client_id'];
            $rented_slot->spot_id = $slot['spot_id'];
            $rented_slot->start_date_time = $slot['start_date_time'];
            $rented_slot->end_date_time = $slot['end_date_time'];
            $rented_slot->keypass = mt_rand(100000, 999999);
            $rented_slot->save();
            // CRIAR PASS
            $start_date_time = Carbon::parse($rented_slot->start_date_time)->subHour()->timestamp;
            $end_date_time = Carbon::parse($rented_slot->end_date_time)->subHour()->timestamp;
            $this->sendKeycode($rented_slot->keypass, $rented_slot->id, $start_date_time, $end_date_time);

            $rented_slot->load('client');
            User::find(15)->notify(new RentedSlotNotification($rented_slot));
        }

        return $groupedSlots;
    }

    private function calculateEndDateTime($timestamp)
    {
        // Adicionar 30 minutos ao timestamp para calcular o horário de término
        $dateTime = new \DateTime($timestamp);
        $dateTime->modify('+30 minutes');
        return $dateTime->format('Y-m-d H:i:s');
    }

    public function multibanco(Request $request)
    {

        $user_id = $request->user()->id;
        $client = Client::where('user_id', $user_id)->first();
        $client_id = $client->id;
        $cart = $request->cart;
        $amount = $request->amount;

        $payment = new Payment;
        $payment->client_id = $client_id;
        $payment->method = 'multibanco';
        $payment->cart = $cart;
        $payment->amount = $amount;
        $payment->save();

        $payment_multibanco = $this->paymentMultibanco($payment->id, $amount);
        $payment->request = $payment_multibanco['RequestId'];
        $payment->save();

        if ($request->promoCode['code'] !== '' && $request->promoCode['validPromoCode'] !== false) {
            $promo_code_item = PromoCodeItem::where('code', $request->promoCode['code'])->first();
            $promo_code_usage = new PromoCodeUsage;
            $promo_code_usage->promo_code_item_id = $promo_code_item->id;
            $promo_code_usage->client_id = $client_id;
            $promo_code_usage->payment_id = $payment->id;
            $promo_code_usage->value = $amount;
            $promo_code_usage->save();
        }

        Notification::route('mail', $request->user()->email)
            ->notify(new MulbancoReference($payment_multibanco));

        return $payment_multibanco;
    }

    public function payByBudget(Request $request)
    {
        $user_id = $request->user()->id;
        $client  = Client::where('user_id', $user_id)->firstOrFail();
        $client_id = $client->id;

        // --- 1) Carrinho: array de slots com "timestamp"
        $cart = $request->input('cart');
        if (is_string($cart)) {
            $cart = json_decode($cart, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json(['error' => 'Carrinho em JSON inválido'], 422);
            }
        }
        if (!is_array($cart) || empty($cart)) {
            return response()->json(['error' => 'Carrinho inválido ou vazio'], 422);
        }

        // Extrair dias dos slots (YYYY-MM-DD), ordenar asc (cronológico)
        $slotDays = [];
        foreach ($cart as $i => $slot) {
            if (!isset($slot['timestamp'])) {
                return response()->json(['error' => "Slot {$i} sem 'timestamp'"], 422);
            }
            try {
                $slotDays[] = $this->ymdFromTimestamp($slot['timestamp']);
            } catch (\Throwable $e) {
                return response()->json(['error' => "Timestamp inválido no slot {$i}"], 422);
            }
        }
        sort($slotDays, SORT_STRING);

        // --- 2) Transação + lock pessimista
        return DB::transaction(function () use ($client_id, $slotDays, $cart) {

            // Buscar packs do cliente com saldo, em FIFO de criação
            $packs = PackPurchase::where('client_id', $client_id)
                ->where('available', '>', 0)
                ->orderBy('created_at')      // FIFO base
                ->lockForUpdate()
                ->get(['id', 'available', 'limit_date', 'created_at']);

            if ($packs->isEmpty()) {
                return response()->json(['error' => 'Não existem packs disponíveis'], 400);
            }

            // Normalizar packs como na app: SÓ com limit_date válido (YYYY-MM-DD)
            $usable = [];
            foreach ($packs as $p) {
                // usar valor CRU da BD, ignorando accessor
                $raw = $p->getRawOriginal('limit_date');
                try {
                    $expiryYmd = $this->rawLimitYmd($raw);
                } catch (\Throwable $e) {
                    $expiryYmd = null; // inválido -> descartar
                }
                if (is_null($expiryYmd)) {
                    continue; // app também ignora packs sem limit_date válido
                }
                $usable[] = [
                    'id'        => $p->id,
                    'available' => (int)$p->available,
                    'expiry'    => $expiryYmd,           // YYYY-MM-DD
                    'created'   => $p->created_at,       // para estabilidade FIFO
                ];
            }

            if (empty($usable)) {
                return response()->json(['error' => 'Sem packs com validade válida'], 400);
            }

            // Ordenar packs por validade asc, depois FIFO de criação (replica o greedy da app)
            usort($usable, function ($a, $b) {
                if ($a['expiry'] === $b['expiry']) {
                    return $a['created'] <=> $b['created'];
                }
                return strcmp($a['expiry'], $b['expiry']);
            });

            // --- 3) Alocação gulosa: cada slot precisa de pack com expiry ≥ slotDay
            // Vamos consumindo 'available' em memória e depois persistimos o delta.
            $remainingById = [];
            foreach ($usable as $u) {
                $remainingById[$u['id']] = $u['available'];
            }

            foreach ($slotDays as $sDay) {
                $allocated = false;

                // procurar o primeiro pack com expiry >= sDay e saldo > 0
                for ($i = 0; $i < count($usable); $i++) {
                    if ($usable[$i]['expiry'] >= $sDay && $remainingById[$usable[$i]['id']] > 0) {
                        $remainingById[$usable[$i]['id']]--;
                        $allocated = true;
                        break;
                    }
                }

                if (!$allocated) {
                    // falha: não há pack válido para esta data
                    // opcional: devolver a maior validade disponível p/ mensagem clara
                    $latestValid = end($usable)['expiry'] ?? null;
                    return response()->json([
                        'error'   => 'Não há packs válidos para pelo menos uma das datas',
                        'details' => [
                            'slot_date'    => $sDay,
                            'latest_valid' => $latestValid,
                        ],
                    ], 400);
                }
            }

            // --- 4) Persistir diferenças (apenas onde consumimos)
            // Map rápido dos modelos carregados
            $byId = $packs->keyBy('id');
            foreach ($remainingById as $id => $remain) {
                $start = $byId[$id]->available;     // antes
                $used  = $start - $remain;          // consumido
                if ($used > 0) {
                    $byId[$id]->available = $remain;
                    $byId[$id]->save();
                }
            }

            // --- 5) Criar as reservas (mantém a tua lógica)
            return $this->groupAdjacentSlots($cart, $client_id);
        });
    }

    private function ymdFromTimestamp(string $ts): string
    {
        // "YYYY-MM-DD HH:MM:SS" | ISO → "YYYY-MM-DD"
        return Carbon::parse($ts)->toDateString();
    }

    private function rawLimitYmd(?string $raw): ?string
    {
        // limit_date cru da BD -> "YYYY-MM-DD" | null
        if ($raw === null || $raw === '') return null;
        return Carbon::parse($raw)->toDateString();
    }

    private function newPackPurchase($payment, array $cart)
    {

        $pack = Pack::find($cart['id']);

        $pack_purchase = new PackPurchase;
        $pack_purchase->client_id = $payment->client_id;
        $pack_purchase->pack_id = $cart['id'];
        $pack_purchase->quantity = $cart['quantity'];
        $pack_purchase->available = $cart['quantity'];
        $pack_purchase->limit_date = Carbon::now()->addDays($pack->vality_days)->format('Y-m-d');
        $pack_purchase->save();
    }
}
