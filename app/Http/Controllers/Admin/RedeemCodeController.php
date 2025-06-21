<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RedeemCode;
use Carbon\Carbon;

class RedeemCodeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $redeemCodes = RedeemCode::orderBy('created_at', 'desc')->get();
        return view('admin.redeem_code.index', compact('redeemCodes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.redeem_code.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:discount,cashback,free_shipping',
            'value' => 'required|numeric|min:0',
            'value_type' => 'required|in:percentage,fixed',
            'max_usage' => 'required|integer|min:1',
            'min_purchase' => 'nullable|numeric|min:0',
            'valid_from' => 'required|date',
            'valid_until' => 'required|date|after:valid_from',
            'description' => 'nullable|string|max:500',
        ]);

        // Generate unique code if not provided
        $code = $request->input('code') ?: RedeemCode::generateCode();

        RedeemCode::create([
            'code' => strtoupper($code),
            'type' => $request->type,
            'value' => $request->value,
            'value_type' => $request->value_type,
            'max_usage' => $request->max_usage,
            'min_purchase' => $request->min_purchase,
            'valid_from' => $request->valid_from,
            'valid_until' => $request->valid_until,
            'description' => $request->description,
            'status' => 'active'
        ]);

        return redirect()->route('admin.redeem_code.index')
            ->with('success', 'Kode redeem berhasil dibuat!');
    }

    /**
     * Display the specified resource.
     */
    public function show(RedeemCode $redeemCode)
    {
        return view('admin.redeem_code.show', compact('redeemCode'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RedeemCode $redeemCode)
    {
        return view('admin.redeem_code.edit', compact('redeemCode'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RedeemCode $redeemCode)
    {
        $request->validate([
            'type' => 'required|in:discount,cashback,free_shipping',
            'value' => 'required|numeric|min:0',
            'value_type' => 'required|in:percentage,fixed',
            'max_usage' => 'required|integer|min:' . $redeemCode->used_count,
            'min_purchase' => 'nullable|numeric|min:0',
            'valid_from' => 'required|date',
            'valid_until' => 'required|date|after:valid_from',
            'description' => 'nullable|string|max:500',
            'status' => 'required|in:active,inactive,expired'
        ]);

        $redeemCode->update([
            'type' => $request->type,
            'value' => $request->value,
            'value_type' => $request->value_type,
            'max_usage' => $request->max_usage,
            'min_purchase' => $request->min_purchase,
            'valid_from' => $request->valid_from,
            'valid_until' => $request->valid_until,
            'description' => $request->description,
            'status' => $request->status
        ]);

        return redirect()->route('admin.redeem_code.index')
            ->with('success', 'Kode redeem berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RedeemCode $redeemCode)
    {
        $redeemCode->delete();
        return redirect()->route('admin.redeem_code.index')
            ->with('success', 'Kode redeem berhasil dihapus!');
    }

    /**
     * Toggle status of redeem code
     */
    public function toggleStatus(RedeemCode $redeemCode)
    {
        $newStatus = $redeemCode->status === 'active' ? 'inactive' : 'active';
        $redeemCode->update(['status' => $newStatus]);

        return redirect()->route('admin.redeem_code.index')
            ->with('success', 'Status kode redeem berhasil diubah!');
    }

    /**
     * Generate multiple codes
     */
    public function generateMultiple(Request $request)
    {
        $request->validate([
            'count' => 'required|integer|min:1|max:100',
            'type' => 'required|in:discount,cashback,free_shipping',
            'value' => 'required|numeric|min:0',
            'value_type' => 'required|in:percentage,fixed',
            'max_usage' => 'required|integer|min:1',
            'min_purchase' => 'nullable|numeric|min:0',
            'valid_from' => 'required|date',
            'valid_until' => 'required|date|after:valid_from',
            'description' => 'nullable|string|max:500',
        ]);

        $codes = [];
        for ($i = 0; $i < $request->count; $i++) {
            $codes[] = RedeemCode::create([
                'code' => RedeemCode::generateCode(),
                'type' => $request->type,
                'value' => $request->value,
                'value_type' => $request->value_type,
                'max_usage' => $request->max_usage,
                'min_purchase' => $request->min_purchase,
                'valid_from' => $request->valid_from,
                'valid_until' => $request->valid_until,
                'description' => $request->description,
                'status' => 'active'
            ]);
        }

        return redirect()->route('admin.redeem_code.index')
            ->with('success', "Berhasil membuat {$request->count} kode redeem!");
    }
}
