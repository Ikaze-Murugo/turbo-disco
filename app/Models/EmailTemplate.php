<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmailTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'subject',
        'content',
        'variables',
        'category',
        'is_active',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'variables' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function campaigns(): HasMany
    {
        return $this->hasMany(EmailCampaign::class, 'template_id');
    }

    /**
     * Get available template variables
     */
    public function getAvailableVariables(): array
    {
        return [
            '{{user_name}}' => 'User\'s full name',
            '{{user_email}}' => 'User\'s email address',
            '{{platform_name}}' => 'Platform name',
            '{{unsubscribe_link}}' => 'Unsubscribe link',
            '{{current_date}}' => 'Current date',
            '{{current_year}}' => 'Current year',
        ];
    }

    /**
     * Process template content with variables
     */
    public function processContent(array $variables = []): string
    {
        $content = $this->content;
        
        foreach ($variables as $key => $value) {
            $content = str_replace($key, $value, $content);
        }
        
        return $content;
    }
}