<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController
{
    public function conekta(Request $request): JsonResponse
    {
        Log::info('Webhook Conekta recibido', ['event' => $request->input('type')]);

        return response()->json(['received' => true]);
    }

    public function stripe(Request $request): JsonResponse
    {
        Log::info('Webhook Stripe recibido', ['event' => $request->input('type')]);

        return response()->json(['received' => true]);
    }
}
