<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\ProductPrice;
use App\Models\Sale;
use App\Models\Shift;
use App\Models\StockMovement;
use App\Models\Unit;
use App\Models\User;
use App\Services\PosService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class PosTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $cashier;
    protected Product $product;
    protected Category $category;
    protected Unit $unit;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Setup users
        $this->admin = User::create([
            'name'      => 'Pemilik Toko',
            'username'  => 'admin',
            'password'  => Hash::make('password'),
            'level'     => 1,
            'is_active' => true,
        ]);

        $this->cashier = User::create([
            'name'      => 'Kasir Toko',
            'username'  => 'kasir1',
            'password'  => Hash::make('password'),
            'level'     => 2,
            'is_active' => true,
        ]);

        // 2. Setup master data
        $this->category = Category::create(['name' => 'Busana Muslimah']);
        $this->unit = Unit::create(['name' => 'Pcs']);

        // 3. Setup test product with 3 price tiers:
        // Retail: 100,000 (min 1)
        // Wholesale: 85,000 (min 12)
        // Super Wholesale: 75,000 (min 36)
        $this->product = Product::create([
            'barcode'     => '8991234567890',
            'name'        => 'Gamis Katun Rayon',
            'category_id' => $this->category->id,
            'unit_id'     => $this->unit->id,
            'stock'       => 50,
            'min_stock'   => 5,
        ]);

        ProductPrice::create([
            'product_id' => $this->product->id,
            'tier'       => 'retail',
            'min_qty'    => 1,
            'price'      => 100000,
        ]);

        ProductPrice::create([
            'product_id' => $this->product->id,
            'tier'       => 'wholesale',
            'min_qty'    => 12,
            'price'      => 85000,
        ]);

        ProductPrice::create([
            'product_id' => $this->product->id,
            'tier'       => 'super_wholesale',
            'min_qty'    => 36,
            'price'      => 75000,
        ]);
    }

    /**
     * 1. Test login with valid credentials and redirect.
     */
    public function test_login_with_valid_credentials(): void
    {
        $response = $this->post('/login', [
            'username' => 'admin',
            'password' => 'password',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($this->admin);
    }

    /**
     * 2. Test cashier forbidden on admin-only routes (HTTP 403).
     */
    public function test_admin_route_forbidden_for_cashier(): void
    {
        $this->actingAs($this->cashier);

        // GET /users should return 403 Forbidden
        $response = $this->get('/users');
        $response->assertStatus(403);

        // GET /reports should return 403 Forbidden
        $responseReports = $this->get('/reports');
        $responseReports->assertStatus(403);

        // GET /products/create should return 403 Forbidden
        $responseProductCreate = $this->get('/products/create');
        $responseProductCreate->assertStatus(403);
    }

    /**
     * 3. Test tiered pricing calculations.
     */
    public function test_tiered_pricing_applied_at_correct_qty(): void
    {
        // 1 piece -> retail tier (Rp 100.000)
        $p1 = $this->product->getPriceForQty(1);
        $this->assertEquals(100000, $p1['price']);
        $this->assertEquals('retail', $p1['tier']);

        // 11 pieces -> still retail tier (Rp 100.000)
        $p11 = $this->product->getPriceForQty(11);
        $this->assertEquals(100000, $p11['price']);
        $this->assertEquals('retail', $p11['tier']);

        // 12 pieces -> wholesale tier (Rp 85.000)
        $p12 = $this->product->getPriceForQty(12);
        $this->assertEquals(85000, $p12['price']);
        $this->assertEquals('wholesale', $p12['tier']);

        // 35 pieces -> wholesale tier (Rp 85.000)
        $p35 = $this->product->getPriceForQty(35);
        $this->assertEquals(85000, $p35['price']);
        $this->assertEquals('wholesale', $p35['tier']);

        // 36 pieces -> super_wholesale tier (Rp 75.000)
        $p36 = $this->product->getPriceForQty(36);
        $this->assertEquals(75000, $p36['price']);
        $this->assertEquals('super_wholesale', $p36['tier']);
    }

    /**
     * 4. Test checkout successful transaction and stock reduction.
     */
    public function test_checkout_success(): void
    {
        // Cashier opens a shift first
        $shift = Shift::create([
            'user_id'      => $this->cashier->id,
            'opened_at'    => now(),
            'opening_cash' => 200000,
        ]);

        $posService = app(PosService::class);

        $payload = [
            'items' => [
                [
                    'product_id'    => $this->product->id,
                    'quantity'      => 2,
                    'item_discount' => 0,
                ],
            ],
            'customer_id'    => null,
            'discount'       => 0,
            'payment_method' => 'cash',
            'paid_amount'    => 200000,
        ];

        $sale = $posService->checkout($payload, $this->cashier, $shift);

        $this->assertInstanceOf(Sale::class, $sale);
        $this->assertEquals('completed', $sale->status);
        $this->assertEquals(200000, $sale->total); // 2 * 100.000
        $this->assertEquals(0, $sale->change_amount);

        // Verify stock deducted from 50 to 48
        $this->assertEquals(48, $this->product->fresh()->stock);

        // Verify stock_movement out created
        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $this->product->id,
            'type'       => 'out',
            'quantity'   => 2,
        ]);

        // Verify sale_items created
        $this->assertDatabaseHas('sale_items', [
            'sale_id'      => $sale->id,
            'product_id'   => $this->product->id,
            'quantity'     => 2,
            'unit_price'   => 100000,
            'product_name' => 'Gamis Katun Rayon',
        ]);
    }

    /**
     * 5. Test checkout rejected when requested qty exceeds stock.
     */
    public function test_checkout_fails_insufficient_stock(): void
    {
        $shift = Shift::create([
            'user_id'      => $this->cashier->id,
            'opened_at'    => now(),
            'opening_cash' => 200000,
        ]);

        $posService = app(PosService::class);

        $payload = [
            'items' => [
                [
                    'product_id'    => $this->product->id,
                    'quantity'      => 999, // Exceeds 50
                    'item_discount' => 0,
                ],
            ],
            'payment_method' => 'cash',
            'paid_amount'    => 10000000,
        ];

        $this->expectException(ValidationException::class);
        $posService->checkout($payload, $this->cashier, $shift);
    }

    /**
     * 6. Test cashier discount limit enforced (max 10%).
     */
    public function test_cashier_discount_limit_enforced(): void
    {
        $shift = Shift::create([
            'user_id'      => $this->cashier->id,
            'opened_at'    => now(),
            'opening_cash' => 200000,
        ]);

        $posService = app(PosService::class);

        // Subtotal = 100.000. 10% = 10.000.
        // Cashier gives 15.000 (> 10%) discount -> Must fail
        $payloadExcessiveDiscount = [
            'items' => [
                [
                    'product_id'    => $this->product->id,
                    'quantity'      => 1,
                    'item_discount' => 0,
                ],
            ],
            'discount'       => 15000, // 15% discount
            'payment_method' => 'cash',
            'paid_amount'    => 85000,
        ];

        $this->expectException(ValidationException::class);
        $posService->checkout($payloadExcessiveDiscount, $this->cashier, $shift);
    }

    /**
     * 7. Test sale cancellation restores stock.
     */
    public function test_cancel_sale_restores_stock(): void
    {
        $shift = Shift::create([
            'user_id'      => $this->cashier->id,
            'opened_at'    => now(),
            'opening_cash' => 200000,
        ]);

        $posService = app(PosService::class);

        $sale = $posService->checkout([
            'items' => [
                [
                    'product_id'    => $this->product->id,
                    'quantity'      => 5,
                    'item_discount' => 0,
                ],
            ],
            'payment_method' => 'cash',
            'paid_amount'    => 500000,
        ], $this->cashier, $shift);

        $this->assertEquals(45, $this->product->fresh()->stock);

        // Admin cancels the sale
        $posService->cancelSale($sale, $this->admin, 'Pelanggan keliru memilih model pakaian');

        // Stock must be restored to 50
        $this->assertEquals(50, $this->product->fresh()->stock);

        // Sale status changed to cancelled (not deleted)
        $this->assertEquals('cancelled', $sale->fresh()->status);

        // Stock movement IN recorded for restoration
        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $this->product->id,
            'type'       => 'in',
            'quantity'   => 5,
        ]);
    }

    /**
     * 8. Test shift difference calculation.
     */
    public function test_shift_difference_calculation(): void
    {
        $this->actingAs($this->cashier);

        // Open shift with Rp 200.000 opening cash
        $this->post('/shift/open', ['opening_cash' => 200000])
             ->assertRedirect('/shift');

        $shift = $this->cashier->currentShift();
        $this->assertNotNull($shift);
        $this->assertEquals(200000, $shift->opening_cash);

        // Make a cash sale of Rp 100.000 in this shift
        $posService = app(PosService::class);
        $posService->checkout([
            'items' => [
                ['product_id' => $this->product->id, 'quantity' => 1, 'item_discount' => 0],
            ],
            'payment_method' => 'cash',
            'paid_amount'    => 100000,
        ], $this->cashier, $shift);

        // Expected cash = 200.000 + 100.000 = 300.000
        // Physical counted cash = 295.000 (Selisih -5.000)
        $this->post('/shift/close', ['counted_cash' => 295000])
             ->assertRedirect('/shift');

        $closedShift = $shift->fresh();
        $this->assertNotNull($closedShift->closed_at);
        $this->assertEquals(300000, $closedShift->expected_cash);
        $this->assertEquals(295000, $closedShift->counted_cash);
        $this->assertEquals(-5000, $closedShift->difference);
    }

    /**
     * 9. Test consecutive invoice number uniqueness.
     */
    public function test_invoice_number_uniqueness(): void
    {
        $date = Carbon::create(2026, 3, 1, 10, 0, 0);

        $inv1 = Sale::generateInvoiceNumber($date);
        $this->assertEquals('INV-20260301-0001', $inv1);

        Sale::create([
            'invoice_number' => $inv1,
            'user_id'        => $this->admin->id,
            'subtotal'       => 100000,
            'discount'       => 0,
            'total'          => 100000,
            'paid_amount'    => 100000,
            'change_amount'  => 0,
            'payment_method' => 'cash',
            'status'         => 'completed',
            'sold_at'        => $date,
        ]);

        $inv2 = Sale::generateInvoiceNumber($date);
        $this->assertEquals('INV-20260301-0002', $inv2);
    }
}

