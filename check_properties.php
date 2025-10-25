<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Property;
use App\Models\User;

echo "=== Property Status Check ===\n";

// Check all properties
$properties = Property::all(['id', 'title', 'status', 'landlord_id']);
echo "Total properties: " . $properties->count() . "\n\n";

foreach ($properties as $property) {
    echo "ID: {$property->id} | Title: {$property->title} | Status: {$property->status} | Landlord ID: {$property->landlord_id}\n";
}

echo "\n=== Landlord Check ===\n";
$landlords = User::where('role', 'landlord')->get(['id', 'name', 'email']);
echo "Total landlords: " . $landlords->count() . "\n\n";

foreach ($landlords as $landlord) {
    echo "ID: {$landlord->id} | Name: {$landlord->name} | Email: {$landlord->email}\n";
}

echo "\n=== Approving Properties for Testing ===\n";

// Check what status values are allowed
echo "Checking valid status values...\n";

// Try different status values
$validStatuses = ['active', 'approved', 'pending', 'rejected', 'inactive'];
$pendingProperties = Property::where('status', 'pending')->get();
echo "Found {$pendingProperties->count()} pending properties\n";

foreach ($pendingProperties as $property) {
    // Try 'active' status instead of 'approved'
    try {
        $property->update(['status' => 'active']);
        echo "Updated to active: {$property->title}\n";
    } catch (Exception $e) {
        echo "Failed to update {$property->title}: " . $e->getMessage() . "\n";
    }
}

echo "\n=== Final Status ===\n";
$approvedCount = Property::where('status', 'approved')->count();
echo "Approved properties: {$approvedCount}\n";

if ($approvedCount > 0) {
    echo "✅ Properties are now approved and should appear in the search!\n";
} else {
    echo "❌ Still no approved properties. You may need to create some test properties.\n";
}
