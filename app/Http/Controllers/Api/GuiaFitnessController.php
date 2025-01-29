<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\OpenaiApi;
use Illuminate\Http\Request;

class GuiaFitnessController extends Controller
{
    use OpenaiApi;

    public function startConversation(Request $request)
    {
        return $this->createThreadAndRun($request->message);
    }

    public function sendMessage(Request $request)
    {

        $thread_id = $request->thread_id;
        $message = $request->message;

        return $this->createMessage($thread_id, $message);
    }
}
