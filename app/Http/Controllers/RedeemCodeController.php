<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RedeemCode;
use Illuminate\Support\Facades\Auth;

class RedeemCodeController extends Controller
{
    /**
     * Validate a redeem code
     */
    public function validateCode(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:50',
            'amount' => 'required|numeric|min:0'
        ]);

        $code = RedeemCode::where('code', strtoupper($request->code))->first();

        if (!$code) {
            return response()->json([
                'success' => false,
                'message' => 'Kode redeem tidak ditemukan'
            ], 404);
        }

        if (!$code->canBeUsedFor($request->amount)) {
            $message = 'Kode tidak dapat digunakan';
            
            if ($code->status !== 'active') {
                $message = 'Kode sudah tidak aktif';
            } elseif ($code->used_count >= $code->max_usage) {
                $message = 'Kode sudah habis digunakan';
            } elseif ($code->min_purchase && $request->amount < $code->min_purchase) {
                $message = 'Minimal pembelian Rp ' . number_format($code->min_purchase, 0, ',', '.');
            }

            return response()->json([
                'success' => false,
                'message' => $message
            ], 400);
        }

        $discount = $code->calculateDiscount($request->amount);

        return response()->json([
            'success' => true,
            'message' => 'Kode valid',
            'data' => [
                'code' => $code->code,
                'type' => $code->type,
                'value' => $code->value,
                'value_type' => $code->value_type,
                'discount_amount' => $discount,
                'description' => $code->description
            ]
        ]);
    }

    /**
     * Use a redeem code
     */
    public function useCode(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:50',
            'amount' => 'required|numeric|min:0'
        ]);

        $code = RedeemCode::where('code', strtoupper($request->code))->first();

        if (!$code) {
            return response()->json([
                'success' => false,
                'message' => 'Kode redeem tidak ditemukan'
            ], 404);
        }

        if (!$code->canBeUsedFor($request->amount)) {
            $message = 'Kode tidak dapat digunakan';
            
            if ($code->status !== 'active') {
                $message = 'Kode sudah tidak aktif';
            } elseif ($code->used_count >= $code->max_usage) {
                $message = 'Kode sudah habis digunakan';
            } elseif ($code->min_purchase && $request->amount < $code->min_purchase) {
                $message = 'Minimal pembelian Rp ' . number_format($code->min_purchase, 0, ',', '.');
            }

            return response()->json([
                'success' => false,
                'message' => $message
            ], 400);
        }

        // Increment usage count
        $code->incrementUsage();

        $discount = $code->calculateDiscount($request->amount);

        return response()->json([
            'success' => true,
            'message' => 'Kode berhasil digunakan',
            'data' => [
                'code' => $code->code,
                'type' => $code->type,
                'value' => $code->value,
                'value_type' => $code->value_type,
                'discount_amount' => $discount,
                'description' => $code->description
            ]
        ]);
    }

    /**
     * Get available redeem codes for user
     */
    public function getAvailableCodes()
    {
        $codes = RedeemCode::valid()->get();

        return response()->json([
            'success' => true,
            'data' => $codes->map(function ($code) {
                return [
                    'code' => $code->code,
                    'type' => $code->type,
                    'value' => $code->value,
                    'value_type' => $code->value_type,
                    'min_purchase' => $code->min_purchase,
                    'description' => $code->description,
                    'valid_until' => $code->valid_until->format('d/m/Y')
                ];
            })
        ]);
    }
}
