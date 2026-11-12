<?php

namespace App\Services;

use App\Models\QrisTransaction;
use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BuatQrisService
{
    protected string $baseUrl;
    protected string $accountId;
    protected string $secretToken;
    protected string $signingSecret;
    protected string $feeBy;
    protected ?string $method;
    protected bool $testMode;
    protected ?string $callbackUrl;

    public function __construct()
    {
        $this->baseUrl = config('buatqris.base_url', 'https://api.buatqris.site');
        $this->accountId = (string) config('buatqris.account_id', '');
        $this->secretToken = (string) config('buatqris.secret_token', '');
        $this->signingSecret = (string) config('buatqris.signing_secret', '');
        $this->feeBy = (string) config('buatqris.fee_by', 'user');
        $this->method = config('buatqris.method') ?: null;
        $this->testMode = (bool) config('buatqris.test_mode', false);
        $this->callbackUrl = config('buatqris.callback_url') ?: null;
    }

    /**
     * Build HTTP client with SSL CA bundle support and IPv4 forcing for Windows environments.
     */
    protected function newHttpClient()
    {
        $caBundle = storage_path('cacert.pem');
        $options = [
            'connect_timeout'  => 8,
            'force_ip_resolve' => 'v4',
        ];
        if (file_exists($caBundle)) {
            $options['verify'] = $caBundle;
        }
        return Http::asForm()->timeout(15)->withOptions($options);
    }

    /**
     * Check if credentials are configured.
     */
    public function isConfigured(): bool
    {
        return !empty($this->accountId) && !empty($this->secretToken);
    }

    /**
     * Create dynamic QRIS invoice via BuatQris API.
     *
     * @param int $amount Amount in Rupiah
     * @param string $description Description (max 100 chars)
     * @param array $options Optional overrides (callback_url, fee_by, qris_method, user_id, invoice_number)
     * @return array [success => bool, data => ?QrisTransaction, message => ?string]
     */
    public function createQris(int $amount, string $description = 'Pembayaran Toko Al-Hidayah', array $options = []): array
    {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'message' => 'Kredensial BuatQris belum dikonfigurasi. Silakan atur BUATQRIS_ACCOUNT_ID dan BUATQRIS_SECRET_TOKEN di file .env atau pengaturan.',
            ];
        }

        if ($amount < 1000) {
            return [
                'success' => false,
                'message' => 'Nominal pembayaran QRIS minimal Rp 1.000.',
            ];
        }

        $params = [
            'action'       => 'api_create_qris',
            'account_id'   => $this->accountId,
            'secret_token' => $this->secretToken,
            'amount'       => $amount,
            'description'  => mb_substr($description, 0, 100),
            'fee_by'       => $options['fee_by'] ?? $this->feeBy,
            'app_name'     => 'POS Toko Al-Hidayah',
            'app_version'  => '1.0.0',
            'app_url'      => config('app.url', 'http://127.0.0.1:8000'),
        ];

        $method = $options['qris_method'] ?? $this->method;
        if (!empty($method)) {
            $params['qris_method'] = $method;
        }

        $cb = $options['callback_url'] ?? $this->callbackUrl;
        if (!empty($cb)) {
            $params['callback_url'] = $cb;
        }

        $isTest = isset($options['test']) ? (bool) $options['test'] : $this->testMode;
        if ($isTest) {
            $params['test'] = '1';
        }

        try {
            $response = $this->newHttpClient()
                ->post($this->baseUrl, $params);

            $body = $response->json();

            if (!$response->successful() || empty($body['success'])) {
                $errorMsg = $body['message'] ?? 'Gagal membuat QRIS (Status HTTP: ' . $response->status() . ')';
                Log::warning('BuatQris createQris failed', ['params' => $params, 'response' => $body]);
                return [
                    'success' => false,
                    'message' => $errorMsg,
                ];
            }

            $data = $body['data'];

            // QRIS valid for 5 minutes per user requirement
            $expiredAt = Carbon::now()->addMinutes(5);

            $trx = QrisTransaction::create([
                'transaction_id' => (string) $data['transaction_id'],
                'invoice_number' => $options['invoice_number'] ?? null,
                'amount'         => (int) ($data['amount'] ?? $amount),
                'total_amount'   => (int) ($data['total_amount'] ?? $amount),
                'amount_uniq'    => (int) ($data['amount_uniq'] ?? 0),
                'admin_fee'      => (int) ($data['admin_fee'] ?? 0),
                'credit_amount'  => (int) ($data['credit_amount'] ?? 0),
                'fee_by'         => (string) ($data['fee_by'] ?? $this->feeBy),
                'qris_method'    => $data['qris_method'] ?? null,
                'qr_url'         => $data['qr_url'] ?? null,
                'qris_image'     => $data['qris_image'] ?? null,
                'payment_url'    => $data['payment_url'] ?? null,
                'status'         => 'pending',
                'is_test'        => $isTest,
                'expired_at'     => $expiredAt,
                'raw_response'   => $data,
                'created_by'     => $options['user_id'] ?? auth()->id(),
            ]);

            return [
                'success' => true,
                'data'    => $trx,
            ];
        } catch (\Exception $e) {
            Log::error('BuatQris createQris exception: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'success' => false,
                'message' => 'Koneksi ke gateway BuatQris gagal: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Check transaction status via BuatQris API.
     * Respects rate limit (max 1 check per 20 seconds).
     *
     * @param string $transactionId
     * @return array [success => bool, status => string, data => ?QrisTransaction, message => ?string, retry_after => ?int]
     */
    public function checkStatus(string $transactionId): array
    {
        $trx = QrisTransaction::where('transaction_id', $transactionId)->first();

        // If already success, return immediately without consuming rate limit
        if ($trx && $trx->isSuccess()) {
            return [
                'success' => true,
                'status'  => 'success',
                'data'    => $trx,
                'message' => 'Pembayaran telah lunas.',
            ];
        }

        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'status'  => $trx ? $trx->status : 'error',
                'message' => 'Kredensial BuatQris belum dikonfigurasi.',
            ];
        }

        try {
            $response = $this->newHttpClient()
                ->post($this->baseUrl, [
                    'action'         => 'api_check_status',
                    'account_id'     => $this->accountId,
                    'secret_token'   => $this->secretToken,
                    'transaction_id' => $transactionId,
                ]);

            $body = $response->json();

            // Handle Rate Limiting (429)
            if ($response->status() === 429 || ($body['error'] ?? null) === 'rate_limited') {
                $retryAfter = (int) ($body['retry_after'] ?? $response->header('Retry-After', 15));
                return [
                    'success'     => false,
                    'status'      => $trx ? $trx->status : 'pending',
                    'rate_limited'=> true,
                    'retry_after' => $retryAfter,
                    'message'     => $body['message'] ?? "Terlalu sering memeriksa status. Harap tunggu {$retryAfter} detik.",
                    'data'        => $trx,
                ];
            }

            if (!$response->successful() || empty($body['success'])) {
                return [
                    'success' => false,
                    'status'  => $trx ? $trx->status : 'unknown',
                    'message' => $body['message'] ?? 'Gagal memeriksa status pembayaran.',
                    'data'    => $trx,
                ];
            }

            $data = $body['data'];
            $newStatus = strtolower((string) ($data['status'] ?? 'pending'));

            if ($trx) {
                $trx->status = $newStatus;
                if (!empty($data['total_amount'])) {
                    $trx->total_amount = (int) $data['total_amount'];
                }
                if ($newStatus === 'success' && empty($trx->paid_at)) {
                    $trx->paid_at = Carbon::now();
                }
                $trx->save();
            }

            return [
                'success' => true,
                'status'  => $newStatus,
                'data'    => $trx,
                'message' => match ($newStatus) {
                    'success' => 'Pembayaran berhasil dikonfirmasi lunas!',
                    'expired' => 'Waktu pembayaran QRIS telah kedaluwarsa.',
                    'failed'  => 'Pembayaran QRIS dibatalkan atau gagal.',
                    default   => 'Menunggu pembayaran dari pelanggan...',
                },
            ];
        } catch (\Exception $e) {
            Log::error('BuatQris checkStatus exception: ' . $e->getMessage());
            return [
                'success' => false,
                'status'  => $trx ? $trx->status : 'error',
                'message' => 'Gagal memeriksa status: ' . $e->getMessage(),
                'data'    => $trx,
            ];
        }
    }

    /**
     * Verify HMAC-SHA256 webhook signature.
     *
     * @param string $rawBody
     * @param string|null $signatureHeader
     * @return bool
     */
    public function verifyWebhookSignature(string $rawBody, ?string $signatureHeader): bool
    {
        if (empty($this->signingSecret)) {
            // If signing secret is not configured yet, log warning and reject for security
            Log::warning('BuatQris webhook rejected: BUATQRIS_SIGNING_SECRET is not configured.');
            return false;
        }

        if (empty($signatureHeader)) {
            return false;
        }

        $calculated = 'sha256=' . hash_hmac('sha256', $rawBody, $this->signingSecret);

        return hash_equals($calculated, $signatureHeader);
    }

    /**
     * Process incoming webhook notification.
     * Idempotent: checks transaction status and avoids duplicate actions.
     *
     * @param array $payload
     * @return array [success => bool, message => string]
     */
    public function handleWebhook(array $payload): array
    {
        $event = $payload['event'] ?? '';
        $transactionId = $payload['transaction_id'] ?? null;

        if (empty($transactionId)) {
            return ['success' => false, 'message' => 'Missing transaction_id'];
        }

        $trx = QrisTransaction::where('transaction_id', (string) $transactionId)->first();
        if (!$trx) {
            Log::warning("BuatQris webhook: transaction {$transactionId} not found in database.");
            return ['success' => false, 'message' => 'Transaction not found'];
        }

        // Idempotency: if already processed as success, acknowledge immediately
        if ($trx->isSuccess()) {
            return ['success' => true, 'message' => 'Already processed'];
        }

        $status = strtolower($payload['status'] ?? '');
        if ($event === 'payment.success' || $status === 'success') {
            $trx->status = 'success';
            $trx->paid_at = !empty($payload['paid_at']) ? Carbon::parse($payload['paid_at']) : Carbon::now();
            if (!empty($payload['total_amount'])) {
                $trx->total_amount = (int) $payload['total_amount'];
            }
            $trx->save();

            Log::info("BuatQris webhook: Payment for {$transactionId} is SUCCESS.");
            return ['success' => true, 'message' => 'Payment marked as success'];
        }

        if ($event === 'payment.expired' || $status === 'expired') {
            $trx->status = 'expired';
            $trx->save();
            return ['success' => true, 'message' => 'Payment marked as expired'];
        }

        if ($event === 'payment.failed' || $status === 'failed') {
            $trx->status = 'failed';
            $trx->save();
            return ['success' => true, 'message' => 'Payment marked as failed'];
        }

        return ['success' => true, 'message' => 'Event noted'];
    }
}

