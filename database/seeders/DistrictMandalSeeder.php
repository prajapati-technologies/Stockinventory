<?php

namespace Database\Seeders;

use App\Models\District;
use App\Models\Mandal;
use Illuminate\Database\Seeder;

class DistrictMandalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Sample districts and mandals data
        // Admin will need to add their specific districts and mandals
        $districts = [
            'Krishna' => ['Vijayawada', 'Machilipatnam', 'Gudivada', 'Pedana', 'Nandigama'],
            'Guntur' => ['Guntur', 'Tenali', 'Narasaraopet', 'Mangalagiri', 'Sattenapalle'],
            'West Godavari' => ['Eluru', 'Bhimavaram', 'Tanuku', 'Tadepalligudem', 'Kovvur'],
            'East Godavari' => ['Kakinada', 'Rajahmundry', 'Amalapuram', 'Tuni', 'Peddapuram'],
            'Visakhapatnam' => ['Visakhapatnam', 'Anakapalle', 'Narsipatnam', 'Yelamanchili', 'Bheemunipatnam'],
        ];

        foreach ($districts as $districtName => $mandals) {
            $district = District::create([
                'name' => $districtName,
                'is_active' => true,
            ]);

            foreach ($mandals as $mandalName) {
                Mandal::create([
                    'district_id' => $district->id,
                    'name' => $mandalName,
                    'is_active' => true,
                ]);
            }
        }
    }
}
