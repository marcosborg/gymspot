<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\IfthenPaymentsTrait;
use App\Http\Controllers\Traits\RentAndPassTrait;
use App\Models\Client;
use App\Models\Pack;
use App\Models\PackPurchase;
use App\Models\Payment;
use App\Models\PromoCodeItem;
use App\Models\PromoCodeUsage;
use App\Models\RentedSlot;
use App\Models\Spot;
use App\Models\User;
use App\Notifications\MulbancoReference;
use App\Notifications\RentedSlotNotification;
use App\Support\LockDateTime;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class PaymentsController extends Controller
{
    use IfthenPaymentsTrait;
    use RentAndPassTrait;

    public function validateCartSlots(Request $request)
    {
        $cart = $this->normalizeCartFromRequest($request);
        if (!$cart || !is_array($cart) || empty($cart)) {
            return response()->json(['error' => 'Carrinho inválido ou vazio'], 422);
        }

        if ($this->isPackCart($cart)) {
            return response()->json(['success' => true]);
        }

        $ruleViolationResponse = $this->buildCartRuleViolationResponse($cart);
        if ($ruleViolationResponse) {
            return $ruleViolationResponse;
        }

        return DB::transaction(function () use ($cart) {
            $this->lockSpotRowsForCart($cart);
            $conflicts = $this->findCartSlotConflicts($cart);

            if (!empty($conflicts)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pelo menos uma das slots já não está disponível.',
                    'conflicts' => $conflicts,
                ], 409);
            }

            return response()->json(['success' => true]);
        });
    }

    public function callbackMultibanco(Request $request)
    {
        if ($request->key !== env('ANTI_PHISHING_KEY')) {
            return response()->json([
                'error' => 'Chave anti-phishing inválida.',
            ], 403);
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

            if ($this->isPackCart($cart)) {
                return $this->newPackPurchase($payment, $cart);
            }

            return $this->reserveCartSlots($cart, $payment->client_id);
        }
    }

    public function callbackMbway(Request $request)
    {
        if ($request->key !== env('ANTI_PHISHING_KEY')) {
            return response()->json([
                'error' => 'Chave anti-phishing inválida.',
            ], 403);
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

            if ($this->isPackCart($cart)) {
                return $this->newPackPurchase($payment, $cart);
            }

            return $this->reserveCartSlots($cart, $payment->client_id);
        }
    }

    public function mbway(Request $request)
    {
        $user_id = $request->user()->id;
        $client = Client::where('user_id', $user_id)->first();
        $client_id = $client->id;
        $cart = $this->normalizeCartFromRequest($request);
        $celphone = $request->celphone;

        if (!$cart || !is_array($cart)) {
            return response()->json(['error' => 'Carrinho inválido.'], 422);
        }

        $promo_code_item = null;
        if ($this->isPackCart($cart)) {
            $pack_id = $request->input('pack_id');
            if (!$pack_id && isset($cart['id'])) {
                $pack_id = $cart['id'];
            }
            if (!$pack_id) {
                return response()->json(['error' => 'pack_id é obrigatório.'], 422);
            }
            $pack = Pack::find($pack_id);
            if (!$pack) {
                return response()->json(['error' => 'Pack inválido.'], 404);
            }
            $base_amount = (float) $pack->price;
            $promo_code_item = $this->resolvePromoCodeForPack($request, $pack, $base_amount);
            $amount = $this->calculateFinalAmount($base_amount, $promo_code_item);
            $cart = $this->normalizePackCart($pack, $cart);
        } else {
            $amount = (float) $request->input('amount');
            if ($amount <= 0) {
                return response()->json(['error' => 'amount é obrigatório.'], 422);
            }
        }

        if (!$this->isPackCart($cart)) {
            $ruleViolationResponse = $this->buildCartRuleViolationResponse($cart);
            if ($ruleViolationResponse) {
                return $ruleViolationResponse;
            }

            $conflictResponse = $this->buildSlotConflictResponse($cart);
            if ($conflictResponse) {
                return $conflictResponse;
            }
        }

        $payment = new Payment;
        $payment->client_id = $client_id;
        $payment->method = 'mbway';
        $payment->cart = $this->encodeCart($cart);
        $payment->amount = $amount;
        $payment->save();

        $payment_mbway = $this->paymentMbway($payment->id, $amount, $celphone);

        $payment->request = $payment_mbway->RequestId;
        $payment->save();

        if ($promo_code_item) {
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
            $payment = Payment::where('request', $requestId)->first();
            $payment->paid = true;
            $payment->save();

            $cart = json_decode($payment->cart, true);

            if ($this->isPackCart($cart)) {
                return $this->newPackPurchase($payment, $cart);
            }

            return $this->reserveCartSlots($cart, $payment->client_id);
        }

        return $mbway_status;
    }

    private function reserveCartSlots(array $cart, int $client_id)
    {
        return DB::transaction(function () use ($cart, $client_id) {
            $ruleViolationResponse = $this->buildCartRuleViolationResponse($cart);
            if ($ruleViolationResponse) {
                return $ruleViolationResponse;
            }

            $this->lockSpotRowsForCart($cart);
            $conflicts = $this->findCartSlotConflicts($cart);

            if (!empty($conflicts)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pelo menos uma das slots já não está disponível.',
                    'conflicts' => $conflicts,
                ], 409);
            }

            return $this->groupAdjacentSlots($cart, $client_id);
        });
    }

    private function groupAdjacentSlots(array $slots, $client_id)
    {
        $groupedSlots = [];
        $currentGroup = [];

        foreach ($slots as $slot) {
            if (empty($currentGroup)) {
                $currentGroup[] = $slot;
            } else {
                $lastSlotEnd = end($currentGroup)['end'];

                if ($slot['start'] === $lastSlotEnd) {
                    $currentGroup[] = $slot;
                } else {
                    $groupedSlots[] = [
                        'start_date_time' => $currentGroup[0]['timestamp'],
                        'end_date_time' => $this->calculateEndDateTime(end($currentGroup)['timestamp']),
                        'client_id' => $client_id,
                        'spot_id' => $currentGroup[0]['spot']['id'],
                    ];
                    $currentGroup = [$slot];
                }
            }
        }

        if (!empty($currentGroup)) {
            $groupedSlots[] = [
                'start_date_time' => $currentGroup[0]['timestamp'],
                'end_date_time' => $this->calculateEndDateTime(end($currentGroup)['timestamp']),
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

            $this->syncRentedSlotKeycode($rented_slot);

            $rented_slot->load('client');
            User::find(15)->notify(new RentedSlotNotification($rented_slot));
        }

        return $groupedSlots;
    }

    private function calculateEndDateTime($timestamp)
    {
        return LockDateTime::addMinutes($timestamp, 30);
    }

    public function multibanco(Request $request)
    {
        $user_id = $request->user()->id;
        $client = Client::where('user_id', $user_id)->first();
        $client_id = $client->id;
        $cart = $this->normalizeCartFromRequest($request);

        if (!$cart || !is_array($cart)) {
            return response()->json(['error' => 'Carrinho inválido.'], 422);
        }

        $promo_code_item = null;
        if ($this->isPackCart($cart)) {
            $pack_id = $request->input('pack_id');
            if (!$pack_id && isset($cart['id'])) {
                $pack_id = $cart['id'];
            }
            if (!$pack_id) {
                return response()->json(['error' => 'pack_id é obrigatório.'], 422);
            }
            $pack = Pack::find($pack_id);
            if (!$pack) {
                return response()->json(['error' => 'Pack inválido.'], 404);
            }
            $base_amount = (float) $pack->price;
            $promo_code_item = $this->resolvePromoCodeForPack($request, $pack, $base_amount);
            $amount = $this->calculateFinalAmount($base_amount, $promo_code_item);
            $cart = $this->normalizePackCart($pack, $cart);
        } else {
            $amount = (float) $request->input('amount');
            if ($amount <= 0) {
                return response()->json(['error' => 'amount é obrigatório.'], 422);
            }
        }

        if (!$this->isPackCart($cart)) {
            $ruleViolationResponse = $this->buildCartRuleViolationResponse($cart);
            if ($ruleViolationResponse) {
                return $ruleViolationResponse;
            }

            $conflictResponse = $this->buildSlotConflictResponse($cart);
            if ($conflictResponse) {
                return $conflictResponse;
            }
        }

        $payment = new Payment;
        $payment->client_id = $client_id;
        $payment->method = 'multibanco';
        $payment->cart = $this->encodeCart($cart);
        $payment->amount = $amount;
        $payment->save();

        $payment_multibanco = $this->paymentMultibanco($payment->id, $amount);
        $payment->request = $payment_multibanco['RequestId'];
        $payment->save();

        if ($promo_code_item) {
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
        $client = Client::where('user_id', $user_id)->firstOrFail();
        $client_id = $client->id;

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

        $ruleViolationResponse = $this->buildCartRuleViolationResponse($cart);
        if ($ruleViolationResponse) {
            return $ruleViolationResponse;
        }

        return DB::transaction(function () use ($client_id, $slotDays, $cart) {
            $packs = PackPurchase::where('client_id', $client_id)
                ->where('available', '>', 0)
                ->orderBy('created_at')
                ->lockForUpdate()
                ->get(['id', 'available', 'limit_date', 'created_at']);

            if ($packs->isEmpty()) {
                return response()->json(['error' => 'Não existem packs disponíveis'], 400);
            }

            $usable = [];
            foreach ($packs as $p) {
                $raw = $p->getRawOriginal('limit_date');
                try {
                    $expiryYmd = $this->rawLimitYmd($raw);
                } catch (\Throwable $e) {
                    $expiryYmd = null;
                }
                if (is_null($expiryYmd)) {
                    continue;
                }
                $usable[] = [
                    'id' => $p->id,
                    'available' => (int) $p->available,
                    'expiry' => $expiryYmd,
                    'created' => $p->created_at,
                ];
            }

            if (empty($usable)) {
                return response()->json(['error' => 'Sem packs com validade válida'], 400);
            }

            usort($usable, function ($a, $b) {
                if ($a['expiry'] === $b['expiry']) {
                    return $a['created'] <=> $b['created'];
                }
                return strcmp($a['expiry'], $b['expiry']);
            });

            $remainingById = [];
            foreach ($usable as $u) {
                $remainingById[$u['id']] = $u['available'];
            }

            foreach ($slotDays as $sDay) {
                $allocated = false;

                for ($i = 0; $i < count($usable); $i++) {
                    if ($usable[$i]['expiry'] >= $sDay && $remainingById[$usable[$i]['id']] > 0) {
                        $remainingById[$usable[$i]['id']]--;
                        $allocated = true;
                        break;
                    }
                }

                if (!$allocated) {
                    $latestValid = end($usable)['expiry'] ?? null;
                    return response()->json([
                        'error' => 'Não há packs válidos para pelo menos uma das datas',
                        'details' => [
                            'slot_date' => $sDay,
                            'latest_valid' => $latestValid,
                        ],
                    ], 400);
                }
            }

            $byId = $packs->keyBy('id');
            foreach ($remainingById as $id => $remain) {
                $start = $byId[$id]->available;
                $used = $start - $remain;
                if ($used > 0) {
                    $byId[$id]->available = $remain;
                    $byId[$id]->save();
                }
            }

            $this->lockSpotRowsForCart($cart);
            $conflicts = $this->findCartSlotConflicts($cart);
            if (!empty($conflicts)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pelo menos uma das slots já não está disponível.',
                    'conflicts' => $conflicts,
                ], 409);
            }

            return $this->groupAdjacentSlots($cart, $client_id);
        });
    }

    private function buildSlotConflictResponse(array $cart)
    {
        return DB::transaction(function () use ($cart) {
            $this->lockSpotRowsForCart($cart);
            $conflicts = $this->findCartSlotConflicts($cart);

            if (empty($conflicts)) {
                return null;
            }

            return response()->json([
                'success' => false,
                'message' => 'Pelo menos uma das slots já não está disponível.',
                'conflicts' => $conflicts,
            ], 409);
        });
    }

    private function buildCartRuleViolationResponse(array $cart)
    {
        $violation = $this->validateCartSelectionRules($cart);

        if (!$violation) {
            return null;
        }

        return response()->json([
            'success' => false,
            'error' => $violation['message'],
            'message' => $violation['message'],
            'rule' => $violation['rule'],
            'details' => $violation['details'] ?? null,
        ], 422);
    }

    private function lockSpotRowsForCart(array $cart): void
    {
        $spotIds = collect($cart)
            ->map(fn ($slot) => $slot['spot']['id'] ?? null)
            ->filter()
            ->unique()
            ->sort()
            ->values();

        if ($spotIds->isEmpty()) {
            return;
        }

        Spot::whereIn('id', $spotIds)->lockForUpdate()->get(['id']);
    }

    private function findCartSlotConflicts(array $cart): array
    {
        $conflicts = [];
        $now = Carbon::now(config('app.timezone'));

        foreach ($cart as $slot) {
            $spotId = $slot['spot']['id'] ?? null;
            $timestamp = $slot['timestamp'] ?? null;

            if (!$spotId || !$timestamp || !isset($slot['end'])) {
                continue;
            }

            $start = Carbon::parse($timestamp, config('app.timezone'));
            $end = $this->buildSlotEndDateTime($slot);

            if ($end->lessThanOrEqualTo($now)) {
                $conflicts[] = [
                    'spot_id' => $spotId,
                    'spot_name' => $slot['spot']['name'] ?? null,
                    'timestamp' => $timestamp,
                    'start' => $slot['start'] ?? $start->format('H:i'),
                    'end' => $slot['end'],
                    'reason' => 'expired',
                    'expired_at' => $end->format('Y-m-d H:i:s'),
                ];

                continue;
            }

            $existing = RentedSlot::where('spot_id', $spotId)
                ->where(function ($query) use ($start, $end) {
                    $query->where('start_date_time', '<', $end->format('Y-m-d H:i:s'))
                        ->where('end_date_time', '>', $start->format('Y-m-d H:i:s'));
                })
                ->first(['id', 'start_date_time', 'end_date_time']);

            if ($existing) {
                $conflicts[] = [
                    'spot_id' => $spotId,
                    'spot_name' => $slot['spot']['name'] ?? null,
                    'timestamp' => $timestamp,
                    'start' => $slot['start'] ?? $start->format('H:i'),
                    'end' => $slot['end'],
                    'reason' => 'occupied',
                    'rented_slot_id' => $existing->id,
                    'occupied_from' => $existing->start_date_time,
                    'occupied_until' => $existing->end_date_time,
                ];
            }
        }

        return $conflicts;
    }

    private function buildSlotEndDateTime(array $slot): Carbon
    {
        $start = Carbon::parse($slot['timestamp'], config('app.timezone'));
        $end = $slot['end'] ?? null;

        if (!$end || !is_string($end)) {
            return $start->copy()->addMinutes(30);
        }

        [$hours, $minutes] = array_map('intval', explode(':', $end));

        return $start->copy()->setTime($hours, $minutes, 0);
    }

    private function validateCartSelectionRules(array $cart): ?array
    {
        $slotsByDay = [];

        foreach ($cart as $slot) {
            if (!is_array($slot) || !isset($slot['timestamp'], $slot['start'], $slot['end'])) {
                continue;
            }

            $ymd = $this->ymdFromTimestamp($slot['timestamp']);
            $slotsByDay[$ymd][] = $slot;
        }

        foreach ($slotsByDay as $ymd => $daySlots) {
            $violation = $this->validateDaySelectionRules($ymd, $daySlots);
            if ($violation) {
                return $violation;
            }
        }

        return null;
    }

    private function validateDaySelectionRules(string $ymd, array $slots): ?array
    {
        $blocks = $this->buildBlocksForDay($slots);

        foreach ($blocks as $index => $block) {
            if ($block['slotsCount'] > 6) {
                return [
                    'rule' => 'max_consecutive_slots',
                    'message' => 'Só pode reservar até 3 horas seguidas por dia (equivalente a 6 slots). Poderá voltar a reservar após 4 horas da sua última sessão.',
                    'details' => [
                        'date' => $ymd,
                        'block_index' => $index,
                        'slots_count' => $block['slotsCount'],
                    ],
                ];
            }
        }

        for ($i = 0; $i < count($blocks) - 1; $i++) {
            $gap = $blocks[$i + 1]['startMin'] - $blocks[$i]['endMin'];

            if ($gap < 240) {
                return [
                    'rule' => 'minimum_gap_between_sessions',
                    'message' => 'Só pode reservar até 3 horas seguidas por dia (equivalente a 6 slots). Poderá voltar a reservar após 4 horas da sua última sessão.',
                    'details' => [
                        'date' => $ymd,
                        'block_index' => $i,
                        'gap_minutes' => $gap,
                    ],
                ];
            }
        }

        return null;
    }

    private function buildBlocksForDay(array $slots): array
    {
        $items = collect($slots)
            ->map(function ($slot) {
                return [
                    'startMin' => $this->toMinutes($slot['start']),
                    'endMin' => $this->toMinutes($slot['end']),
                ];
            })
            ->sortBy('startMin')
            ->values()
            ->all();

        $blocks = [];

        foreach ($items as $item) {
            $lastIndex = count($blocks) - 1;

            if ($lastIndex < 0) {
                $blocks[] = [
                    'startMin' => $item['startMin'],
                    'endMin' => $item['endMin'],
                    'slotsCount' => 1,
                ];
                continue;
            }

            if ($item['startMin'] === $blocks[$lastIndex]['endMin']) {
                $blocks[$lastIndex]['endMin'] = $item['endMin'];
                $blocks[$lastIndex]['slotsCount']++;
                continue;
            }

            $blocks[] = [
                'startMin' => $item['startMin'],
                'endMin' => $item['endMin'],
                'slotsCount' => 1,
            ];
        }

        return $blocks;
    }

    private function toMinutes(string $hhmm): int
    {
        [$hours, $minutes] = array_map('intval', explode(':', $hhmm));

        return ($hours * 60) + $minutes;
    }

    private function ymdFromTimestamp(string $ts): string
    {
        return Carbon::parse($ts, config('app.timezone'))->toDateString();
    }

    private function rawLimitYmd(?string $raw): ?string
    {
        if ($raw === null || $raw === '') {
            return null;
        }

        return Carbon::parse($raw)->toDateString();
    }

    private function newPackPurchase($payment, array $cart)
    {
        $pack_id = $cart['pack_id'] ?? ($cart['id'] ?? null);
        if (!$pack_id) {
            return;
        }
        $pack = Pack::find($pack_id);
        if (!$pack) {
            return;
        }
        $quantity = $cart['quantity'] ?? $pack->quantity ?? 1;

        $pack_purchase = new PackPurchase;
        $pack_purchase->client_id = $payment->client_id;
        $pack_purchase->pack_id = $pack->id;
        $pack_purchase->quantity = $quantity;
        $pack_purchase->available = $quantity;
        $pack_purchase->limit_date = Carbon::now()->addDays($pack->vality_days)->format('Y-m-d');
        $pack_purchase->save();
    }

    private function isPackCart($cart): bool
    {
        if (!is_array($cart)) {
            return false;
        }
        if (isset($cart['pack_id']) || (isset($cart['type']) && $cart['type'] === 'pack')) {
            return true;
        }
        if (isset($cart['id']) && isset($cart['quantity']) && !isset($cart[0])) {
            return true;
        }
        return false;
    }

    private function normalizeCartFromRequest(Request $request): ?array
    {
        $cart = $request->input('cart');
        if (is_string($cart)) {
            $decoded = json_decode($cart, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $decoded;
            }
            return null;
        }
        if (is_array($cart)) {
            return $cart;
        }
        return null;
    }

    private function normalizePackCart(Pack $pack, ?array $cart): array
    {
        $quantity = $cart['quantity'] ?? $pack->quantity ?? 1;
        return [
            'type' => 'pack',
            'pack_id' => $pack->id,
            'id' => $pack->id,
            'quantity' => $quantity,
            'price' => (float) $pack->price,
        ];
    }

    private function encodeCart($cart): string
    {
        if (is_string($cart)) {
            return $cart;
        }
        return json_encode($cart);
    }

    private function resolvePromoCodeForPack(Request $request, Pack $pack, float $base_amount): ?PromoCodeItem
    {
        $code = trim((string) $request->input('promo_code', ''));
        if ($code === '') {
            $code = trim((string) $request->input('promoCode.code', ''));
        }
        if ($code === '') {
            return null;
        }

        $promo_code_item = PromoCodeItem::where('code', $code)
            ->whereDate('start_date', '<=', Carbon::today())
            ->whereDate('end_date', '>=', Carbon::today())
            ->where('status', 1)
            ->where('promo', 'packs')
            ->first();

        if (!$promo_code_item) {
            return null;
        }

        if (!is_null($promo_code_item->qty_remain) && (int) $promo_code_item->qty_remain <= 0) {
            return null;
        }

        if (!is_null($promo_code_item->pack_id) && (int) $promo_code_item->pack_id !== (int) $pack->id) {
            return null;
        }

        $min_value = (float) $promo_code_item->min_value;
        if ($min_value > 0 && $base_amount < $min_value) {
            return null;
        }

        return $promo_code_item;
    }

    private function calculateFinalAmount(float $base_amount, ?PromoCodeItem $promo_code_item): float
    {
        $discount = 0.0;
        if ($promo_code_item) {
            if ($promo_code_item->type === 'percent') {
                $discount = $base_amount * ((float) $promo_code_item->amount / 100);
            } else {
                $discount = (float) $promo_code_item->amount;
            }
        }
        $final = $base_amount - $discount;
        if ($final < 0) {
            $final = 0;
        }
        return round($final, 2);
    }
}
