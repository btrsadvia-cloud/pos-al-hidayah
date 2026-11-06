<?php

namespace Tests\Feature;

use App\Models\QrisTransaction;
use App\Models\User;
use App\Services\BuatQrisService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class BuatQrisTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config([
            'buatqris.base_url'       => 'https://api.buatqris.site',
            'buatqris.account_id'     => 'test_acc_123',
            'buatqris.secret_token'   => 'sk_live_testtoken1234567890',
            'buatqris.signing_secret' => 'super_secret_webhook_key',
            'buatqris.test_mode'      => false,
        ]);
    }

    public function test_webhook_rejects_invalid_signature(): void
    {
        $payload = [
            'event'          => 'payment.success',
            'transaction_id' => 'TRX-12345',
            'status'         => 'success',
            'amount'         => 50000,
            'total_amount'   => 50035,
        ];

        $rawBody = json_encode($payload);

        $response = $this->call(
            'POST',
            '/api/buatqris/webhook',
            [],
            [],
            [],
            [
                'CONTENT_TYPE'           => 'application/json',
                'HTTP_X_BUATQRIS_EVENT'  => 'payment.success',
                'HTTP_X_BUATQRIS_SIGNATURE' => 'sha256=invalid_signature_hash',
            ],
            $rawBody
        );

        $response->assertStatus(401);
    }

    public function test_webhook_accepts_valid_signature_and_marks_paid(): void
    {
        $trx = QrisTransaction::create([
            'transaction_id' => '100099887766',
            'amount'         => 25000,
            'total_amount'   => 25037,
            'status'         => 'pending',
            'fee_by'         => 'user',
        ]);

        $payload = [
            'event'          => 'payment.success',
            'transaction_id' => '100099887766',
            'status'         => 'success',
            'amount'         => 25000,
            'total_amount'   => 25037,
            'paid_at'        => now()->toIso8601String(),
        ];

        $rawBody = json_encode($payload);
        $signature = 'sha256=' . hash_hmac('sha256', $rawBody, 'super_secret_webhook_key');

        $response = $this->call(
            'POST',
            '/api/buatqris/webhook',
            [],
            [],
            [],
            [
                'CONTENT_TYPE'               => 'application/json',
                'HTTP_X_BUATQRIS_EVENT'      => 'payment.success',
                'HTTP_X_BUATQRIS_DELIVERY'   => '100099887766',
                'HTTP_X_BUATQRIS_SIGNATURE'  => $signature,
            ],
            $rawBody
        );

        $response->assertStatus(200);

        $trx->refresh();
        $this->assertEquals('success', $trx->status);
        $this->assertNotNull($trx->paid_at);
    }

    public function test_webhook_is_idempotent(): void
    {
        $trx = QrisTransaction::create([
            'transaction_id' => '100099887767',
            'amount'         => 10000,
            'total_amount'   => 10015,
            'status'         => 'success',
            'paid_at'        => now(),
            'fee_by'         => 'user',
        ]);

        $payload = [
            'event'          => 'payment.success',
            'transaction_id' => '100099887767',
            'status'         => 'success',
            'amount'         => 10000,
            'total_amount'   => 10015,
        ];

        $rawBody = json_encode($payload);
        $signature = 'sha256=' . hash_hmac('sha256', $rawBody, 'super_secret_webhook_key');

        $response = $this->call(
            'POST',
            '/api/buatqris/webhook',
            [],
            [],
            [],
            [
                'CONTENT_TYPE'               => 'application/json',
                'HTTP_X_BUATQRIS_EVENT'      => 'payment.success',
                'HTTP_X_BUATQRIS_DELIVERY'   => '100099887767',
                'HTTP_X_BUATQRIS_SIGNATURE'  => $signature,
            ],
            $rawBody
        );

        $response->assertStatus(200);
        $response->assertJson(['status' => 'ok']);
    }

    public function test_buatqris_service_create_qris(): void
    {
        Http::fake([
            'https://api.buatqris.site*' => Http::response([
                'success' => true,
                'data'    => [
                    'transaction_id' => '100043729581',
                    'amount'         => 25000,
                    'total_amount'   => 25037,
                    'amount_uniq'    => 37,
                    'admin_fee'      => 500,
                    'credit_amount'  => 24500,
                    'fee_by'         => 'user',
                    'qris_method'    => 'qris_two',
                    'qr_url'         => 'https://app.buatqris.site/poto/qris/100043729581.png',
                    'qris_image'     => 'data:image/png;base64,iVBORw0KG...',
                    'payment_url'    => 'https://app.buatqris.site/trx/100043729581',
                    'expired_at'     => now()->addMinutes(15)->toIso8601String(),
                    'status'         => 'pending',
                ],
            ], 200),
        ]);

        $service = app(BuatQrisService::class);
        $result = $service->createQris(25000, 'Order #INV-1024');

        $this->assertTrue($result['success']);
        $this->assertDatabaseHas('qris_transactions', [
            'transaction_id' => '100043729581',
            'amount'         => 25000,
            'total_amount'   => 25037,
            'amount_uniq'    => 37,
            'status'         => 'pending',
        ]);
    }

    public function test_buatqris_service_check_status(): void
    {
        QrisTransaction::create([
            'transaction_id' => '100088889999',
            'amount'         => 30000,
            'total_amount'   => 30045,
            'status'         => 'pending',
            'fee_by'         => 'user',
        ]);

        Http::fake([
            'https://api.buatqris.site*' => Http::response([
                'success' => true,
                'data'    => [
                    'transaction_id' => '100088889999',
                    'status'         => 'success',
                    'amount'         => 30000,
                    'total_amount'   => 30045,
                    'updated_at'     => now()->toIso8601String(),
                ],
            ], 200),
        ]);

        $service = app(BuatQrisService::class);
        $res = $service->checkStatus('100088889999');

        $this->assertTrue($res['success']);
        $this->assertEquals('success', $res['status']);

        $this->assertDatabaseHas('qris_transactions', [
            'transaction_id' => '100088889999',
            'status'         => 'success',
        ]);
    }

    public function test_admin_can_access_settings(): void
    {
        $admin = User::create([
            'name'      => 'Admin Test',
            'username'  => 'admin_test',
            'password'  => Hash::make('password'),
            'level'     => 1,
            'is_active' => true,
        ]);

        $resAdmin = $this->actingAs($admin)->get('/settings/buatqris');
        $resAdmin->assertStatus(200);
    }

    public function test_cashier_cannot_access_settings(): void
    {
        $cashier = User::create([
            'name'      => 'Kasir Test',
            'username'  => 'kasir_test',
            'password'  => Hash::make('password'),
            'level'     => 2,
            'is_active' => true,
        ]);

        $resCashier = $this->actingAs($cashier)->get('/settings/buatqris');
        $resCashier->assertStatus(403);
    }
}
