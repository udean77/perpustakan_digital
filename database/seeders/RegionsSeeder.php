<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class RegionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Fetching provinces from Komerce API...');
        
        $provincesResponse = Http::withHeaders([
            'key' => env('RAJAONGKIR_API_KEY')
        ])->get('https://api-sandbox.collaborator.komerce.id/starter/province');

        if ($provincesResponse->failed()) {
            $this->command->error('Failed to fetch provinces.');
            return;
        }

        $provinces = $provincesResponse->json()['rajaongkir']['results'];
        
        DB::table('provinces')->truncate();
        foreach ($provinces as $province) {
            DB::table('provinces')->insert([
                'id' => $province['province_id'],
                'name' => $province['province'],
            ]);
        }
        
        $this->command->info('Provinces seeded successfully.');
        $this->command->info('Fetching cities for each province...');

        DB::table('cities')->truncate();
        foreach ($provinces as $province) {
            $this->command->line('Fetching cities for ' . $province['province']);
            
            $citiesResponse = Http::withHeaders([
                'key' => env('RAJAONGKIR_API_KEY')
            ])->get('https://api-sandbox.collaborator.komerce.id/starter/city', [
                'province' => $province['province_id']
            ]);

            if ($citiesResponse->failed()) {
                $this->command->error('Failed to fetch cities for province ID: ' . $province['province_id']);
                continue;
            }

            $cities = $citiesResponse->json()['rajaongkir']['results'];

            foreach ($cities as $city) {
                DB::table('cities')->insert([
                    'id' => $city['city_id'],
                    'province_id' => $city['province_id'],
                    'name' => $city['city_name'],
                    'type' => $city['type'],
                    'postal_code' => $city['postal_code'],
                ]);
            }
            // Small delay to avoid hitting API rate limits
            sleep(1);
        }

        $this->command->info('Cities seeded successfully.');
    }
}
