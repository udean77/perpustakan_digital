<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\RedeemCode;
use Carbon\Carbon;

class RedeemCodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $codes = [
            [
                'code' => 'DISKON10',
                'type' => 'discount',
                'value' => 10,
                'value_type' => 'percentage',
                'max_usage' => 50,
                'min_purchase' => 100000,
                'valid_from' => Carbon::now(),
                'valid_until' => Carbon::now()->addMonths(3),
                'description' => 'Diskon 10% untuk pembelian minimal Rp 100.000'
            ],
            [
                'code' => 'CASHBACK50K',
                'type' => 'cashback',
                'value' => 50000,
                'value_type' => 'fixed',
                'max_usage' => 20,
                'min_purchase' => 200000,
                'valid_from' => Carbon::now(),
                'valid_until' => Carbon::now()->addMonths(2),
                'description' => 'Cashback Rp 50.000 untuk pembelian minimal Rp 200.000'
            ],
            [
                'code' => 'DISKON25',
                'type' => 'discount',
                'value' => 25,
                'value_type' => 'percentage',
                'max_usage' => 10,
                'min_purchase' => 150000,
                'valid_from' => Carbon::now(),
                'valid_until' => Carbon::now()->addMonths(6),
                'description' => 'Diskon 25% untuk pembelian minimal Rp 150.000'
            ],
            [
                'code' => 'WELCOME20',
                'type' => 'discount',
                'value' => 20,
                'value_type' => 'percentage',
                'max_usage' => 200,
                'min_purchase' => null,
                'valid_from' => Carbon::now(),
                'valid_until' => Carbon::now()->addMonths(12),
                'description' => 'Diskon 20% untuk pelanggan baru'
            ]
        ];

        foreach ($codes as $codeData) {
            RedeemCode::create($codeData);
        }
    }
}
