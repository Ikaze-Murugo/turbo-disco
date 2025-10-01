<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Amenity;

class RwandaAmenitiesSeeder extends Seeder
{
    public function run()
    {
        $amenities = [
            // Restaurants in Kigali
            ['name' => 'Heaven Restaurant', 'type' => 'restaurant', 'address' => 'KG 7 Ave, Kacyiru, Kigali', 'latitude' => -1.9441, 'longitude' => 30.0619, 'phone' => '+250 788 123 456'],
            ['name' => 'Sole Luna', 'type' => 'restaurant', 'address' => 'KG 2 Ave, Kacyiru, Kigali', 'latitude' => -1.9500, 'longitude' => 30.0600, 'phone' => '+250 788 234 567'],
            ['name' => 'Khana Khazana', 'type' => 'restaurant', 'address' => 'KG 1 Ave, Kacyiru, Kigali', 'latitude' => -1.9400, 'longitude' => 30.0620, 'phone' => '+250 788 345 678'],
            ['name' => 'Repub Lounge', 'type' => 'restaurant', 'address' => 'KG 5 Ave, Kacyiru, Kigali', 'latitude' => -1.9450, 'longitude' => 30.0610, 'phone' => '+250 788 456 789'],
            ['name' => 'Meze Fresh', 'type' => 'restaurant', 'address' => 'KG 3 Ave, Kacyiru, Kigali', 'latitude' => -1.9480, 'longitude' => 30.0590, 'phone' => '+250 788 567 890'],
            
            // Cafes
            ['name' => 'Bourbon Coffee', 'type' => 'cafe', 'address' => 'KG 7 Ave, Kacyiru, Kigali', 'latitude' => -1.9441, 'longitude' => 30.0619, 'phone' => '+250 788 678 901'],
            ['name' => 'Question Coffee', 'type' => 'cafe', 'address' => 'KG 2 Ave, Kacyiru, Kigali', 'latitude' => -1.9500, 'longitude' => 30.0600, 'phone' => '+250 788 789 012'],
            ['name' => 'Café Neo', 'type' => 'cafe', 'address' => 'KG 1 Ave, Kacyiru, Kigali', 'latitude' => -1.9400, 'longitude' => 30.0620, 'phone' => '+250 788 890 123'],
            ['name' => 'Inzora Rooftop Café', 'type' => 'cafe', 'address' => 'KG 5 Ave, Kacyiru, Kigali', 'latitude' => -1.9450, 'longitude' => 30.0610, 'phone' => '+250 788 901 234'],
            
            // Schools
            ['name' => 'Kigali International School', 'type' => 'school', 'address' => 'KG 7 Ave, Kacyiru, Kigali', 'latitude' => -1.9441, 'longitude' => 30.0619, 'phone' => '+250 788 012 345'],
            ['name' => 'Green Hills Academy', 'type' => 'school', 'address' => 'KG 2 Ave, Kacyiru, Kigali', 'latitude' => -1.9500, 'longitude' => 30.0600, 'phone' => '+250 788 123 456'],
            ['name' => 'Kigali Parents School', 'type' => 'school', 'address' => 'KG 1 Ave, Kacyiru, Kigali', 'latitude' => -1.9400, 'longitude' => 30.0620, 'phone' => '+250 788 234 567'],
            ['name' => 'École Belge de Kigali', 'type' => 'school', 'address' => 'KG 5 Ave, Kacyiru, Kigali', 'latitude' => -1.9450, 'longitude' => 30.0610, 'phone' => '+250 788 345 678'],
            ['name' => 'Kigali Christian School', 'type' => 'school', 'address' => 'KG 3 Ave, Kacyiru, Kigali', 'latitude' => -1.9480, 'longitude' => 30.0590, 'phone' => '+250 788 456 789'],
            
            // Hospitals
            ['name' => 'King Faisal Hospital', 'type' => 'hospital', 'address' => 'KG 7 Ave, Kacyiru, Kigali', 'latitude' => -1.9441, 'longitude' => 30.0619, 'phone' => '+250 788 567 890'],
            ['name' => 'Kigali University Teaching Hospital', 'type' => 'hospital', 'address' => 'KG 2 Ave, Kacyiru, Kigali', 'latitude' => -1.9500, 'longitude' => 30.0600, 'phone' => '+250 788 678 901'],
            ['name' => 'Kibagabaga Hospital', 'type' => 'hospital', 'address' => 'KG 1 Ave, Kacyiru, Kigali', 'latitude' => -1.9400, 'longitude' => 30.0620, 'phone' => '+250 788 789 012'],
            ['name' => 'La Croix du Sud Hospital', 'type' => 'hospital', 'address' => 'KG 5 Ave, Kacyiru, Kigali', 'latitude' => -1.9450, 'longitude' => 30.0610, 'phone' => '+250 788 890 123'],
            
            // Banks
            ['name' => 'Bank of Kigali', 'type' => 'bank', 'address' => 'KG 7 Ave, Kacyiru, Kigali', 'latitude' => -1.9441, 'longitude' => 30.0619, 'phone' => '+250 788 901 234'],
            ['name' => 'Equity Bank', 'type' => 'bank', 'address' => 'KG 2 Ave, Kacyiru, Kigali', 'latitude' => -1.9500, 'longitude' => 30.0600, 'phone' => '+250 788 012 345'],
            ['name' => 'GT Bank', 'type' => 'bank', 'address' => 'KG 1 Ave, Kacyiru, Kigali', 'latitude' => -1.9400, 'longitude' => 30.0620, 'phone' => '+250 788 123 456'],
            ['name' => 'Access Bank', 'type' => 'bank', 'address' => 'KG 5 Ave, Kacyiru, Kigali', 'latitude' => -1.9450, 'longitude' => 30.0610, 'phone' => '+250 788 234 567'],
            ['name' => 'Ecobank', 'type' => 'bank', 'address' => 'KG 3 Ave, Kacyiru, Kigali', 'latitude' => -1.9480, 'longitude' => 30.0590, 'phone' => '+250 788 345 678'],
            
            // Supermarkets
            ['name' => 'Nakumatt', 'type' => 'supermarket', 'address' => 'KG 7 Ave, Kacyiru, Kigali', 'latitude' => -1.9441, 'longitude' => 30.0619, 'phone' => '+250 788 456 789'],
            ['name' => 'Simba Supermarket', 'type' => 'supermarket', 'address' => 'KG 2 Ave, Kacyiru, Kigali', 'latitude' => -1.9500, 'longitude' => 30.0600, 'phone' => '+250 788 567 890'],
            ['name' => 'T2000 Supermarket', 'type' => 'supermarket', 'address' => 'KG 1 Ave, Kacyiru, Kigali', 'latitude' => -1.9400, 'longitude' => 30.0620, 'phone' => '+250 788 678 901'],
            ['name' => 'Kigali Heights Mall', 'type' => 'shopping_mall', 'address' => 'KG 5 Ave, Kacyiru, Kigali', 'latitude' => -1.9450, 'longitude' => 30.0610, 'phone' => '+250 788 789 012'],
            
            // Pharmacies
            ['name' => 'Pharmacie de Kacyiru', 'type' => 'pharmacy', 'address' => 'KG 7 Ave, Kacyiru, Kigali', 'latitude' => -1.9441, 'longitude' => 30.0619, 'phone' => '+250 788 890 123'],
            ['name' => 'Pharmacie de la Paix', 'type' => 'pharmacy', 'address' => 'KG 2 Ave, Kacyiru, Kigali', 'latitude' => -1.9500, 'longitude' => 30.0600, 'phone' => '+250 788 901 234'],
            ['name' => 'Pharmacie de Kimisagara', 'type' => 'pharmacy', 'address' => 'KG 1 Ave, Kacyiru, Kigali', 'latitude' => -1.9400, 'longitude' => 30.0620, 'phone' => '+250 788 012 345'],
            
            // Gyms
            ['name' => 'Fitness First', 'type' => 'gym', 'address' => 'KG 7 Ave, Kacyiru, Kigali', 'latitude' => -1.9441, 'longitude' => 30.0619, 'phone' => '+250 788 123 456'],
            ['name' => 'Body Works Gym', 'type' => 'gym', 'address' => 'KG 2 Ave, Kacyiru, Kigali', 'latitude' => -1.9500, 'longitude' => 30.0600, 'phone' => '+250 788 234 567'],
            ['name' => 'FitLife Gym', 'type' => 'gym', 'address' => 'KG 5 Ave, Kacyiru, Kigali', 'latitude' => -1.9450, 'longitude' => 30.0610, 'phone' => '+250 788 345 678'],
            
            // Parks
            ['name' => 'Amahoro Stadium', 'type' => 'park', 'address' => 'KG 7 Ave, Kacyiru, Kigali', 'latitude' => -1.9441, 'longitude' => 30.0619, 'phone' => '+250 788 456 789'],
            ['name' => 'Kigali Genocide Memorial', 'type' => 'park', 'address' => 'KG 2 Ave, Kacyiru, Kigali', 'latitude' => -1.9500, 'longitude' => 30.0600, 'phone' => '+250 788 567 890'],
            ['name' => 'Nyamirambo Stadium', 'type' => 'park', 'address' => 'KG 1 Ave, Kacyiru, Kigali', 'latitude' => -1.9400, 'longitude' => 30.0620, 'phone' => '+250 788 678 901'],
            
            // Bus Stops
            ['name' => 'Kacyiru Bus Stop', 'type' => 'bus_stop', 'address' => 'KG 7 Ave, Kacyiru, Kigali', 'latitude' => -1.9441, 'longitude' => 30.0619],
            ['name' => 'Kimisagara Bus Stop', 'type' => 'bus_stop', 'address' => 'KG 2 Ave, Kacyiru, Kigali', 'latitude' => -1.9500, 'longitude' => 30.0600],
            ['name' => 'Nyamirambo Bus Stop', 'type' => 'bus_stop', 'address' => 'KG 1 Ave, Kacyiru, Kigali', 'latitude' => -1.9400, 'longitude' => 30.0620],
            ['name' => 'Remera Bus Stop', 'type' => 'bus_stop', 'address' => 'KG 5 Ave, Kacyiru, Kigali', 'latitude' => -1.9450, 'longitude' => 30.0610],
            
            // Gas Stations
            ['name' => 'Shell Kacyiru', 'type' => 'gas_station', 'address' => 'KG 7 Ave, Kacyiru, Kigali', 'latitude' => -1.9441, 'longitude' => 30.0619, 'phone' => '+250 788 789 012'],
            ['name' => 'Total Kimisagara', 'type' => 'gas_station', 'address' => 'KG 2 Ave, Kacyiru, Kigali', 'latitude' => -1.9500, 'longitude' => 30.0600, 'phone' => '+250 788 890 123'],
            ['name' => 'Engen Nyamirambo', 'type' => 'gas_station', 'address' => 'KG 1 Ave, Kacyiru, Kigali', 'latitude' => -1.9400, 'longitude' => 30.0620, 'phone' => '+250 788 901 234'],
            
            // Post Offices
            ['name' => 'Kacyiru Post Office', 'type' => 'post_office', 'address' => 'KG 7 Ave, Kacyiru, Kigali', 'latitude' => -1.9441, 'longitude' => 30.0619, 'phone' => '+250 788 012 345'],
            ['name' => 'Kimisagara Post Office', 'type' => 'post_office', 'address' => 'KG 2 Ave, Kacyiru, Kigali', 'latitude' => -1.9500, 'longitude' => 30.0600, 'phone' => '+250 788 123 456'],
            
            // Police Stations
            ['name' => 'Kacyiru Police Station', 'type' => 'police_station', 'address' => 'KG 7 Ave, Kacyiru, Kigali', 'latitude' => -1.9441, 'longitude' => 30.0619, 'phone' => '+250 788 234 567'],
            ['name' => 'Kimisagara Police Station', 'type' => 'police_station', 'address' => 'KG 2 Ave, Kacyiru, Kigali', 'latitude' => -1.9500, 'longitude' => 30.0600, 'phone' => '+250 788 345 678'],
            
            // Fire Stations
            ['name' => 'Kacyiru Fire Station', 'type' => 'fire_station', 'address' => 'KG 7 Ave, Kacyiru, Kigali', 'latitude' => -1.9441, 'longitude' => 30.0619, 'phone' => '+250 788 456 789'],
            ['name' => 'Kimisagara Fire Station', 'type' => 'fire_station', 'address' => 'KG 2 Ave, Kacyiru, Kigali', 'latitude' => -1.9500, 'longitude' => 30.0600, 'phone' => '+250 788 567 890'],
        ];
        
        foreach ($amenities as $amenity) {
            Amenity::updateOrCreate(
                ['name' => $amenity['name'], 'type' => $amenity['type']],
                $amenity
            );
        }
        
        $this->command->info('Rwanda amenities seeded successfully!');
    }
}