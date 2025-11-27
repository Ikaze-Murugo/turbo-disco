<?php

namespace App\Services;

use App\Models\Property;
use App\Models\PropertyEdit;
use Illuminate\Support\Facades\Request;

class PropertyEditTracker
{
    /**
     * Track property creation.
     */
    public static function trackCreation(Property $property, $userId): void
    {
        try {
            foreach ($property->getAttributes() as $field => $value) {
                if (in_array($field, ['id', 'created_at', 'updated_at'])) {
                    continue;
                }

                PropertyEdit::create([
                    'property_id' => $property->id,
                    'user_id' => $userId,
                    'field_name' => $field,
                    'old_value' => null,
                    'new_value' => is_array($value) ? json_encode($value) : $value,
                    'edit_type' => 'create',
                    'ip_address' => Request::ip(),
                    'user_agent' => Request::userAgent(),
                ]);
            }
        } catch (\Exception $e) {
            logger()->error('Failed to track property creation: ' . $e->getMessage());
        }
    }

    /**
     * Track property updates.
     */
    public static function trackUpdate(Property $property, array $changes, $userId): void
    {
        try {
            foreach ($changes as $field => $values) {
                PropertyEdit::create([
                    'property_id' => $property->id,
                    'user_id' => $userId,
                    'field_name' => $field,
                    'old_value' => is_array($values['old'] ?? null) ? json_encode($values['old']) : ($values['old'] ?? null),
                    'new_value' => is_array($values['new'] ?? null) ? json_encode($values['new']) : ($values['new'] ?? null),
                    'edit_type' => 'update',
                    'ip_address' => Request::ip(),
                    'user_agent' => Request::userAgent(),
                ]);
            }
        } catch (\Exception $e) {
            logger()->error('Failed to track property update: ' . $e->getMessage());
        }
    }

    /**
     * Get property changes from dirty attributes.
     */
    public static function getChanges(Property $property): array
    {
        $changes = [];
        
        foreach ($property->getDirty() as $field => $newValue) {
            $changes[$field] = [
                'old' => $property->getOriginal($field),
                'new' => $newValue,
            ];
        }
        
        return $changes;
    }
}
