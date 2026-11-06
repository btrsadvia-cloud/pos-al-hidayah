<?php

namespace App\Http\Controllers;

use App\Services\BuatQrisService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class BuatQrisWebhookController extends Controller
{
    /**
     * Handle incoming webhook callback from BuatQris.
     */
    public function handle(Request $request, BuatQrisService $service): Response
    {
        $rawBody = $request->getContent();
        $signature = $request->header('X-BuatQris-Signature');
        $event = $request->header('X-BuatQris-Event');
        $deliveryId = $request->header('X-BuatQris-Delivery');

        Log::info('BuatQris Webhook received', [
            'event'     => $event,
            'delivery'  => $deliveryId,
            'signature' => $signature ? 'present' : 'missing',
        ]);

        // Verify HMAC-SHA256 signature
        if (!$service->verifyWebhookSignature($rawBody, $signature)) {
            Log::warning('BuatQris Webhook signature verification failed');
            return response('Invalid Signature', 401);
        }

        $payload = json_decode($rawBody, true);
        if (!is_array($payload)) {
            return response('Invalid JSON body', 400);
        }

        $result = $service->handleWebhook($payload);

        // Always reply with HTTP 200 within 6s so BuatQris doesn't retry unnecessarily
        return response(['status' => 'ok', 'result' => $result['message']], 200)
            ->header('Content-Type', 'application/json');
    }
}

