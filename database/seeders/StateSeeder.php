<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\State;

class StateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $states = [
            // Special entries (top of dropdown)
            ['name' => 'All India', 'sort_order' => 1],
            ['name' => 'Central',   'sort_order' => 2],

            // All States & UTs (alphabetical)
            ['name' => 'Andaman & Nicobar Islands'],
            ['name' => 'Andhra Pradesh'],
            ['name' => 'Arunachal Pradesh'],
            ['name' => 'Assam'],
            ['name' => 'Bihar'],
            ['name' => 'Chandigarh'],
            ['name' => 'Chhattisgarh'],
            ['name' => 'Dadra & Nagar Haveli & Daman & Diu'],
            ['name' => 'Delhi'],
            ['name' => 'Goa'],
            ['name' => 'Gujarat'],
            ['name' => 'Haryana'],
            ['name' => 'Himachal Pradesh'],
            ['name' => 'Jammu & Kashmir'],
            ['name' => 'Jharkhand'],
            ['name' => 'Karnataka'],
            ['name' => 'Kerala'],
            ['name' => 'Ladakh'],
            ['name' => 'Lakshadweep'],
            ['name' => 'Madhya Pradesh'],
            ['name' => 'Maharashtra'],
            ['name' => 'Manipur'],
            ['name' => 'Meghalaya'],
            ['name' => 'Mizoram'],
            ['name' => 'Nagaland'],
            ['name' => 'Odisha'],
            ['name' => 'Puducherry'],
            ['name' => 'Punjab'],
            ['name' => 'Rajasthan'],
            ['name' => 'Sikkim'],
            ['name' => 'Tamil Nadu'],
            ['name' => 'Telangana'],
            ['name' => 'Tripura'],
            ['name' => 'Uttar Pradesh'],
            ['name' => 'Uttarakhand'],
            ['name' => 'West Bengal'],
        ];

        foreach ($states as $i => $data) {
            State::firstOrCreate(
                ['name' => $data['name']],
                ['sort_order' => $data['sort_order'] ?? 100]
            );
        }
    }
}
