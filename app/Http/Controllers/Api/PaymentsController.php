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
use Illuminate\Support\Facades\Notification;
use App\Notifications\MulbancoReference;

class PaymentsController extends Controller
{
    use IfthenPaymentsTrait;
    use RentAndPassTrait;

    public function calbackMultibanco(Request $request)
    {

        if ($request->key !== env('ANTI_PHISHING_KEY')) {
            return response()->json([
                'error' => 'Chave anti-phishing inválida.',
            ], 403); // 403 Forbidden
        }
        $payment = Payment::where('request', $request->requestId)->first();
        $payment->paid = true;
        $payment->save();

        //AGRUPAR E GRAVAR
        $cart = json_decode($payment->cart, true);

        return $this->groupAdjacentSlots($cart, $payment->client_id);

    }

    public function calbackMbway(Request $request)
    {

        if ($request->key !== env('ANTI_PHISHING_KEY')) {
            return response()->json([
                'error' => 'Chave anti-phishing inválida.',
            ], 403); // 403 Forbidden
        }
        $payment = Payment::where('request', $request->requestId)->first();
        $payment->paid = true;
        $payment->save();

        //AGRUPAR E GRAVAR
        $cart = json_decode($payment->cart, true);

        return $this->groupAdjacentSlots($cart, $payment->client_id);

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
            $start_date_time = Carbon::parse($rented_slot->start_date_time)->timestamp;
            $end_date_time = Carbon::parse($rented_slot->end_date_time)->timestamp;

            $this->sendKeycode($rented_slot->keypass, $rented_slot->id, $start_date_time, $end_date_time);
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

        Notification::route('mail', $request->user()->email)
            ->notify(new MulbancoReference($payment_multibanco));

        return $payment_multibanco;

    }

}
