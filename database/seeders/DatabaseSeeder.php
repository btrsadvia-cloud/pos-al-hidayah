<?php

namespace Database\Seeders;

use App\Models\AuditLog;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\ProductPrice;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Users (Admin & Kasir)
        $admin = User::firstOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Irfan Arif (Owner)',
                'password' => Hash::make('password'),
                'level' => 1,
                'is_active' => true,
            ]
        );

        $kasir1 = User::firstOrCreate(
            ['username' => 'kasir1'],
            [
                'name' => 'Desi Asthasari',
                'password' => Hash::make('password'),
                'level' => 2,
                'is_active' => true,
            ]
        );

        // 2. Units
        $unitsData = ['Pcs', 'Lusin', 'Kodi', 'Meter', 'Stel'];
        $units = [];
        foreach ($unitsData as $unitName) {
            $units[$unitName] = Unit::firstOrCreate(['name' => $unitName]);
        }

        // 3. Categories
        $categoriesData = [
            'Seragam Sekolah',
            'Busana Muslimah',
            'Alat Sholat',
            'Haji dan Umroh',
        ];
        $categories = [];
        foreach ($categoriesData as $catName) {
            $categories[$catName] = Category::firstOrCreate(['name' => $catName]);
        }

        // 4. Suppliers
        $suppliers = [
            Supplier::firstOrCreate(
                ['name' => 'Konveksi Berkah Busana Muslim'],
                [
                    'contact' => '081311223344 (Bp. Hendra)',
                    'address' => 'Pusat Grosir Tanah Abang Blok A Lt. 2, Jakarta',
                    'bank_account' => 'BCA 8830123456 a.n Berkah Busana',
                ]
            ),
            Supplier::firstOrCreate(
                ['name' => 'Grosir Perlengkapan Haji Barokah'],
                [
                    'contact' => '081255667788 (Hj. Farida)',
                    'address' => 'Kawasan Ampel, Jl. Sasak No. 12 Surabaya',
                    'bank_account' => 'Mandiri 142001928374 a.n Farida Barokah',
                ]
            ),
            Supplier::firstOrCreate(
                ['name' => 'CV Pabrik Seragam Nusantara'],
                [
                    'contact' => '082199887766 (Bp. Agus)',
                    'address' => 'Kawasan Industri Tekstil Cimahi, Bandung',
                    'bank_account' => 'BSI 7109283746 a.n Seragam Nusantara',
                ]
            ),
        ];

        // 5. Customers
        $customersData = [
            ['name' => 'Ibu Hj. Aminah', 'gender' => 'P', 'phone' => '081234567890', 'address' => 'Jl. Jendral Sudirman No. 45, Metro Pusat'],
            ['name' => 'Bpk. Ir. H. Ahmad Fauzi', 'gender' => 'L', 'phone' => '082198765432', 'address' => 'Kedaton, Kota Bandar Lampung'],
            ['name' => 'Ibu Siti Rahmawati', 'gender' => 'P', 'phone' => '085712345678', 'address' => 'Jl. AH Nasution No. 88, Kec. Metro Barat'],
            ['name' => 'Bpk. Muhammad Ridwan', 'gender' => 'L', 'phone' => '081399887766', 'address' => 'Kel. Imopuro RT 02/01, Metro Pusat'],
            ['name' => 'Yayasan Pendidikan Al-Khoiriyah', 'gender' => 'L', 'phone' => '082211443322', 'address' => 'Jl. Ki Hajar Dewantara, Metro Timur'],
        ];
        foreach ($customersData as $c) {
            Customer::firstOrCreate(['phone' => $c['phone']], $c);
        }

        // 6. Products (17 sample products across categories)
        $productsSeed = [
            // Seragam Sekolah
            [
                'barcode' => '8991001001',
                'name' => 'Seragam SD Merah Putih Lengan Pendek',
                'category' => 'Seragam Sekolah',
                'unit' => 'Stel',
                'stock' => 25,
                'min_stock' => 5,
                'cost' => 30,
                'supplier' => $suppliers[2]->id,
                'prices' => [
                    ['tier' => 'retail', 'min_qty' => 1, 'price' => 45],
                    ['tier' => 'wholesale', 'min_qty' => 12, 'price' => 35],
                    ['tier' => 'super_wholesale', 'min_qty' => 36, 'price' => 25],
                ],
            ],
            [
                'barcode' => '8991001002',
                'name' => 'Seragam SMP Biru Putih Putra',
                'category' => 'Seragam Sekolah',
                'unit' => 'Stel',
                'stock' => 18,
                'min_stock' => 5,
                'cost' => 40,
                'supplier' => $suppliers[2]->id,
                'prices' => [
                    ['tier' => 'retail', 'min_qty' => 1, 'price' => 60],
                    ['tier' => 'wholesale', 'min_qty' => 12, 'price' => 50],
                    ['tier' => 'super_wholesale', 'min_qty' => 36, 'price' => 40],
                ],
            ],
            [
                'barcode' => '8991001003',
                'name' => 'Seragam SMA Putih Abu-Abu Putri',
                'category' => 'Seragam Sekolah',
                'unit' => 'Stel',
                'stock' => 3, // LOW STOCK
                'min_stock' => 5,
                'cost' => 55,
                'supplier' => $suppliers[2]->id,
                'prices' => [
                    ['tier' => 'retail', 'min_qty' => 1, 'price' => 85],
                    ['tier' => 'wholesale', 'min_qty' => 12, 'price' => 70],
                    ['tier' => 'super_wholesale', 'min_qty' => 36, 'price' => 55],
                ],
            ],
            [
                'barcode' => '8991001004',
                'name' => 'Seragam Pramuka Lengkap Penggalang',
                'category' => 'Seragam Sekolah',
                'unit' => 'Stel',
                'stock' => 14,
                'min_stock' => 5,
                'cost' => 65,
                'supplier' => $suppliers[2]->id,
                'prices' => [
                    ['tier' => 'retail', 'min_qty' => 1, 'price' => 100],
                    ['tier' => 'wholesale', 'min_qty' => 12, 'price' => 85],
                    ['tier' => 'super_wholesale', 'min_qty' => 36, 'price' => 70],
                ],
            ],
            [
                'barcode' => '8991001005',
                'name' => 'Dasi & Topi Bordir Sekolah',
                'category' => 'Seragam Sekolah',
                'unit' => 'Pcs',
                'stock' => 50,
                'min_stock' => 10,
                'cost' => 70,
                'supplier' => $suppliers[2]->id,
                'prices' => [
                    ['tier' => 'retail', 'min_qty' => 1, 'price' => 125],
                    ['tier' => 'wholesale', 'min_qty' => 12, 'price' => 110],
                    ['tier' => 'super_wholesale', 'min_qty' => 36, 'price' => 95],
                ],
            ],

            // Busana Muslimah
            [
                'barcode' => '8992002001',
                'name' => 'Gamis Rayon Viscose Polos Premium',
                'category' => 'Busana Muslimah',
                'unit' => 'Pcs',
                'stock' => 20,
                'min_stock' => 5,
                'cost' => 90,
                'supplier' => $suppliers[0]->id,
                'prices' => [
                    ['tier' => 'retail', 'min_qty' => 1, 'price' => 150],
                    ['tier' => 'wholesale', 'min_qty' => 12, 'price' => 130],
                    ['tier' => 'super_wholesale', 'min_qty' => 36, 'price' => 115],
                ],
            ],
            [
                'barcode' => '8992002002',
                'name' => 'Gamis Syar\'i Ceruty Babydoll 2 Layer',
                'category' => 'Busana Muslimah',
                'unit' => 'Stel',
                'stock' => 9,
                'min_stock' => 5,
                'cost' => 105,
                'supplier' => $suppliers[0]->id,
                'prices' => [
                    ['tier' => 'retail', 'min_qty' => 1, 'price' => 175],
                    ['tier' => 'wholesale', 'min_qty' => 12, 'price' => 150],
                    ['tier' => 'super_wholesale', 'min_qty' => 36, 'price' => 135],
                ],
            ],
            [
                'barcode' => '8992002003',
                'name' => 'Jilbab Bergo Maryam Diamond Crepe',
                'category' => 'Busana Muslimah',
                'unit' => 'Pcs',
                'stock' => 4, // LOW STOCK
                'min_stock' => 10,
                'cost' => 120,
                'supplier' => $suppliers[0]->id,
                'prices' => [
                    ['tier' => 'retail', 'min_qty' => 1, 'price' => 200],
                    ['tier' => 'wholesale', 'min_qty' => 12, 'price' => 180],
                    ['tier' => 'super_wholesale', 'min_qty' => 36, 'price' => 160],
                ],
            ],
            [
                'barcode' => '8992002004',
                'name' => 'Pashmina Ceruty Baby Doll Shawl',
                'category' => 'Busana Muslimah',
                'unit' => 'Pcs',
                'stock' => 35,
                'min_stock' => 10,
                'cost' => 18,
                'supplier' => $suppliers[0]->id,
                'prices' => [
                    ['tier' => 'retail', 'min_qty' => 1, 'price' => 30],
                    ['tier' => 'wholesale', 'min_qty' => 12, 'price' => 25],
                    ['tier' => 'super_wholesale', 'min_qty' => 36, 'price' => 18],
                ],
            ],
            [
                'barcode' => '8992002005',
                'name' => 'Tunik Muslimah Katun Bordir',
                'category' => 'Busana Muslimah',
                'unit' => 'Pcs',
                'stock' => 12,
                'min_stock' => 5,
                'cost' => 45,
                'supplier' => $suppliers[0]->id,
                'prices' => [
                    ['tier' => 'retail', 'min_qty' => 1, 'price' => 75],
                    ['tier' => 'wholesale', 'min_qty' => 12, 'price' => 60],
                    ['tier' => 'super_wholesale', 'min_qty' => 36, 'price' => 48],
                ],
            ],

            // Alat Sholat
            [
                'barcode' => '8993003001',
                'name' => 'Mukena Katun Mikro Renda Mewah',
                'category' => 'Alat Sholat',
                'unit' => 'Pcs',
                'stock' => 16,
                'min_stock' => 5,
                'cost' => 55,
                'supplier' => $suppliers[0]->id,
                'prices' => [
                    ['tier' => 'retail', 'min_qty' => 1, 'price' => 90],
                    ['tier' => 'wholesale', 'min_qty' => 12, 'price' => 75],
                    ['tier' => 'super_wholesale', 'min_qty' => 36, 'price' => 60],
                ],
            ],
            [
                'barcode' => '8993003002',
                'name' => 'Sajadah Turkey Tebal Chenille Bludru',
                'category' => 'Alat Sholat',
                'unit' => 'Pcs',
                'stock' => 2, // LOW STOCK
                'min_stock' => 5,
                'cost' => 65,
                'supplier' => $suppliers[1]->id,
                'prices' => [
                    ['tier' => 'retail', 'min_qty' => 1, 'price' => 110],
                    ['tier' => 'wholesale', 'min_qty' => 12, 'price' => 95],
                    ['tier' => 'super_wholesale', 'min_qty' => 36, 'price' => 80],
                ],
            ],
            [
                'barcode' => '8993003003',
                'name' => 'Sarung Tenun Wadimor Padang Premium',
                'category' => 'Alat Sholat',
                'unit' => 'Pcs',
                'stock' => 28,
                'min_stock' => 10,
                'cost' => 85,
                'supplier' => $suppliers[1]->id,
                'prices' => [
                    ['tier' => 'retail', 'min_qty' => 1, 'price' => 140],
                    ['tier' => 'wholesale', 'min_qty' => 12, 'price' => 120],
                    ['tier' => 'super_wholesale', 'min_qty' => 36, 'price' => 100],
                ],
            ],
            [
                'barcode' => '8993003004',
                'name' => 'Peci Songkok Hitam Nasional AC',
                'category' => 'Alat Sholat',
                'unit' => 'Pcs',
                'stock' => 30,
                'min_stock' => 10,
                'cost' => 95,
                'supplier' => $suppliers[1]->id,
                'prices' => [
                    ['tier' => 'retail', 'min_qty' => 1, 'price' => 160],
                    ['tier' => 'wholesale', 'min_qty' => 12, 'price' => 140],
                    ['tier' => 'super_wholesale', 'min_qty' => 36, 'price' => 120],
                ],
            ],

            // Haji dan Umroh
            [
                'barcode' => '8994004001',
                'name' => 'Kain Ihram Pria Dewasa Katun Jacquard',
                'category' => 'Haji dan Umroh',
                'unit' => 'Stel',
                'stock' => 15,
                'min_stock' => 5,
                'cost' => 110,
                'supplier' => $suppliers[1]->id,
                'prices' => [
                    ['tier' => 'retail', 'min_qty' => 1, 'price' => 180],
                    ['tier' => 'wholesale', 'min_qty' => 12, 'price' => 155],
                    ['tier' => 'super_wholesale', 'min_qty' => 36, 'price' => 135],
                ],
            ],
            [
                'barcode' => '8994004002',
                'name' => 'Set Gamis & Mukena Putih Umroh Paris',
                'category' => 'Haji dan Umroh',
                'unit' => 'Stel',
                'stock' => 10,
                'min_stock' => 5,
                'cost' => 120,
                'supplier' => $suppliers[1]->id,
                'prices' => [
                    ['tier' => 'retail', 'min_qty' => 1, 'price' => 195],
                    ['tier' => 'wholesale', 'min_qty' => 12, 'price' => 170],
                    ['tier' => 'super_wholesale', 'min_qty' => 36, 'price' => 150],
                ],
            ],
            [
                'barcode' => '8994004003',
                'name' => 'Sabuk Haji Gesper Tawaf Hijau',
                'category' => 'Haji dan Umroh',
                'unit' => 'Pcs',
                'stock' => 1, // LOW STOCK
                'min_stock' => 5,
                'cost' => 25,
                'supplier' => $suppliers[1]->id,
                'prices' => [
                    ['tier' => 'retail', 'min_qty' => 1, 'price' => 50],
                    ['tier' => 'wholesale', 'min_qty' => 12, 'price' => 40],
                    ['tier' => 'super_wholesale', 'min_qty' => 36, 'price' => 30],
                ],
            ],
        ];

        foreach ($productsSeed as $p) {
            $product = Product::firstOrCreate(
                ['barcode' => $p['barcode']],
                [
                    'name' => $p['name'],
                    'category_id' => $categories[$p['category']]->id,
                    'unit_id' => $units[$p['unit']]->id,
                    'stock' => $p['stock'],
                    'min_stock' => $p['min_stock'],
                ]
            );

            // Seed tiered prices
            foreach ($p['prices'] as $pr) {
                ProductPrice::firstOrCreate(
                    [
                        'product_id' => $product->id,
                        'tier' => $pr['tier'],
                    ],
                    [
                        'min_qty' => $pr['min_qty'],
                        'price' => $pr['price'],
                    ]
                );
            }

            // Record initial stock in movement
            if ($p['stock'] > 0) {
                StockMovement::firstOrCreate(
                    [
                        'product_id' => $product->id,
                        'reason' => 'Stok Awal Toko Al-Hidayah',
                    ],
                    [
                        'type' => 'in',
                        'quantity' => $p['stock'],
                        'unit_cost' => $p['cost'],
                        'supplier_id' => $p['supplier'],
                        'user_id' => $admin->id,
                        'movement_date' => now()->toDateString(),
                    ]
                );
            }
        }

        // Record audit log for database seed
        AuditLog::record(
            action: 'Inisialisasi Sistem & Seeder Data Toko Al-Hidayah',
            subject: $admin,
            newValues: ['status' => 'Data awal berhasil di-seed'],
            userId: $admin->id
        );
    }
}
