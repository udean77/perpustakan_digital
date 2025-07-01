<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use App\Models\RedeemCode;
use Illuminate\Http\Request;

class PromotionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $promotions = Promotion::with('redeemCode')->latest()->paginate(10);
        return view('admin.promotions.index', compact('promotions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $redeemCodes = RedeemCode::where('is_active', true)->where('expires_at', '>', now())->orWhereNull('expires_at')->get();
        return view('admin.promotions.create', compact('redeemCodes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'redeem_code_id' => 'nullable|exists:redeem_codes,id',
            'expires_at' => 'nullable|date',
            'is_active' => 'nullable|boolean',
        ]);

        $imagePath = $request->file('image')->store('promotions', 'public');

        Promotion::create([
            'title' => $validatedData['title'],
            'description' => $validatedData['description'],
            'image_path' => $imagePath,
            'redeem_code_id' => $validatedData['redeem_code_id'],
            'expires_at' => $validatedData['expires_at'],
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.promotions.index')->with('success', 'Promosi berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
