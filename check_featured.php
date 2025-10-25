<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Property;

echo "=== Property Featured Status Check ===\n";

$properties = Property::all(['id', 'title', 'is_featured', 'featured_until']);
echo "Total properties: " . $properties->count() . "\n\n";

foreach ($properties as $property) {
    echo "ID: {$property->id} | Title: {$property->title} | Featured: " . ($property->is_featured ? 'YES' : 'NO') . " | Until: " . ($property->featured_until ? $property->featured_until : 'NULL') . "\n";
}

echo "\n=== Fixing Featured Status ===\n";

// Unfeature all properties to reset them
$updated = Property::where('is_featured', true)->update([
    'is_featured' => false,
    'featured_until' => null,
    'priority' => 'low'
]);

echo "Updated {$updated} properties to not featured\n";

echo "\n=== Final Status ===\n";
$featuredCount = Property::where('is_featured', true)->count();
echo "Featured properties: {$featuredCount}\n";

if ($featuredCount === 0) {
    echo "✅ All properties are now not featured!\n";
} else {
    echo "❌ Still have featured properties.\n";
}
