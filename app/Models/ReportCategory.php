<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReportCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'icon',
        'color',
        'report_type',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    // Relationships
    public function reports(): HasMany
    {
        return $this->hasMany(Report::class, 'category', 'name');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('report_type', $type);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    // Static method to get default categories
    public static function getDefaultCategories()
    {
        return [
            // Property report categories
            [
                'name' => 'inappropriate_content',
                'description' => 'Inappropriate or offensive content',
                'icon' => 'exclamation-triangle',
                'color' => '#EF4444',
                'report_type' => 'property',
                'sort_order' => 1,
            ],
            [
                'name' => 'fake_listing',
                'description' => 'Fake or misleading property listing',
                'icon' => 'times-circle',
                'color' => '#F59E0B',
                'report_type' => 'property',
                'sort_order' => 2,
            ],
            [
                'name' => 'fraud',
                'description' => 'Suspected fraud or scam',
                'icon' => 'shield-exclamation',
                'color' => '#DC2626',
                'report_type' => 'property',
                'sort_order' => 3,
            ],
            [
                'name' => 'spam',
                'description' => 'Spam or duplicate listing',
                'icon' => 'spam',
                'color' => '#6B7280',
                'report_type' => 'property',
                'sort_order' => 4,
            ],

            // User report categories
            [
                'name' => 'harassment',
                'description' => 'Harassment or bullying',
                'icon' => 'user-times',
                'color' => '#DC2626',
                'report_type' => 'user',
                'sort_order' => 1,
            ],
            [
                'name' => 'inappropriate_content',
                'description' => 'Inappropriate behavior or content',
                'icon' => 'exclamation-triangle',
                'color' => '#EF4444',
                'report_type' => 'user',
                'sort_order' => 2,
            ],
            [
                'name' => 'fraud',
                'description' => 'Suspected fraud or scam',
                'icon' => 'shield-exclamation',
                'color' => '#DC2626',
                'report_type' => 'user',
                'sort_order' => 3,
            ],
            [
                'name' => 'spam',
                'description' => 'Spam or fake account',
                'icon' => 'spam',
                'color' => '#6B7280',
                'report_type' => 'user',
                'sort_order' => 4,
            ],

            // Message report categories
            [
                'name' => 'inappropriate_content',
                'description' => 'Inappropriate or offensive message',
                'icon' => 'exclamation-triangle',
                'color' => '#EF4444',
                'report_type' => 'message',
                'sort_order' => 1,
            ],
            [
                'name' => 'harassment',
                'description' => 'Harassment or bullying',
                'icon' => 'user-times',
                'color' => '#DC2626',
                'report_type' => 'message',
                'sort_order' => 2,
            ],
            [
                'name' => 'spam',
                'description' => 'Spam or unwanted messages',
                'icon' => 'spam',
                'color' => '#6B7280',
                'report_type' => 'message',
                'sort_order' => 3,
            ],

            // Bug report categories
            [
                'name' => 'technical_issue',
                'description' => 'Technical problem or bug',
                'icon' => 'bug',
                'color' => '#F59E0B',
                'report_type' => 'bug',
                'sort_order' => 1,
            ],

            // Feature request categories
            [
                'name' => 'feature_request',
                'description' => 'New feature suggestion',
                'icon' => 'lightbulb',
                'color' => '#10B981',
                'report_type' => 'feature_request',
                'sort_order' => 1,
            ],
        ];
    }
}