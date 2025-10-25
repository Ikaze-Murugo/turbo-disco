<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Property;
use App\Models\User;

class TestPropertiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get an existing user or create a landlord
        $landlord = User::where('role', 'landlord')->first();
        if (!$landlord) {
            $landlord = User::where('role', 'admin')->first();
        }
        if (!$landlord) {
            $landlord = User::first();
        }

        // Create test properties
        $properties = [
            [
                'title' => 'Modern Apartment in Kigali',
                'description' => 'Beautiful modern apartment with great amenities',
                'price' => 500000,
                'type' => 'apartment',
                'bedrooms' => 2,
                'bathrooms' => 2,
                'area' => 80,
                'address' => 'Kigali, Rwanda',
                'location' => 'Kigali',
                'neighborhood' => 'Kacyiru',
                'status' => 'active',
                'landlord_id' => $landlord->id,
            ],
            [
                'title' => 'Cozy House in Remera',
                'description' => 'Perfect family home with garden',
                'price' => 750000,
                'type' => 'house',
                'bedrooms' => 3,
                'bathrooms' => 2,
                'area' => 120,
                'address' => 'Remera, Kigali',
                'location' => 'Kigali',
                'neighborhood' => 'Remera',
                'status' => 'active',
                'landlord_id' => $landlord->id,
            ],
            [
                'title' => 'Luxury Villa in Nyarutarama',
                'description' => 'Spacious villa with pool and garden',
                'price' => 1200000,
                'type' => 'villa',
                'bedrooms' => 4,
                'bathrooms' => 3,
                'area' => 200,
                'address' => 'Nyarutarama, Kigali',
                'location' => 'Kigali',
                'neighborhood' => 'Nyarutarama',
                'status' => 'active',
                'landlord_id' => $landlord->id,
            ],
        ];

        foreach ($properties as $propertyData) {
            Property::firstOrCreate(
                ['title' => $propertyData['title']],
                $propertyData
            );
        }

        $this->command->info('Test properties created successfully!');
    }
}