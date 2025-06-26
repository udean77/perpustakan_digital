<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ShippingController extends Controller
{
    protected $apiKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey = env('RAJAONGKIR_API_KEY');
        $this->baseUrl = 'https://api-sandbox.collaborator.komerce.id/v2';
    }

    public function searchCities(Request $request)
    {
        $searchTerm = $request->input('q');

        if (empty($searchTerm)) {
            return response()->json(['data' => []]);
        }
        
        $response = Http::withHeaders([
            'key' => $this->apiKey
        ])->get($this->baseUrl . '/destination', [
            'q' => $searchTerm
        ]);

        if ($response->failed()) {
            return response()->json(['error' => 'Gagal mencari kota.', 'details' => $response->json()], 500);
        }

        return $response->json();
    }

    public function checkOngkir(Request $request)
    {
        $request->validate([
            'origin' => 'required',
            'destination' => 'required',
            'weight' => 'required|integer',
            'courier' => 'required',
        ]);

        $response = Http::withHeaders([
            'key' => $this->apiKey
        ])->post($this->baseUrl . '/cost', [
            'origin' => $request->origin,
            'destination' => $request->destination,
            'weight' => $request->weight,
            'courier' => $request->courier,
        ]);

        if ($response->failed()) {
            return response()->json(['error' => 'Gagal mengecek ongkir dari Komerce.', 'details' => $response->json()], 500);
        }

        return $response->json();
    }
}
