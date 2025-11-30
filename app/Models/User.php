<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
        'verification_token',
        'verification_expires_at',
        'is_verified',
        // Profile fields
        'profile_picture',
        'bio',
        'phone_number',
        'phone_verified_at',
        'phone_verification_code',
        'phone_verification_code_expires_at',
        'date_of_birth',
        'gender',
        'location',
        'website',
        'social_links',
        'preferences',
        'last_active_at',
        'profile_completion_percentage',
        'business_name',
        'business_license',
        'verification_status',
        'admin_level',
        'admin_permissions',
        'emergency_contact',
        // Notification preferences
        'email_notifications',
        'push_notifications',
        'notify_property_updates',
        'notify_messages',
        'notify_reviews',
        'marketing_emails',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'verification_expires_at' => 'datetime',
            'is_verified' => 'boolean',
            'date_of_birth' => 'date',
            'social_links' => 'array',
            'preferences' => 'array',
            'last_active_at' => 'datetime',
            'admin_permissions' => 'array',
            'emergency_contact' => 'array',
            'phone_verified_at' => 'datetime',
            'phone_verification_code_expires_at' => 'datetime',
            'email_notifications' => 'boolean',
            'push_notifications' => 'boolean',
            'notify_property_updates' => 'boolean',
            'notify_messages' => 'boolean',
            'notify_reviews' => 'boolean',
            'marketing_emails' => 'boolean',
        ];
    }

    // Role check methods
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isLandlord()
    {
        return $this->role === 'landlord';
    }

    public function isRenter()
    {
        return $this->role === 'renter';
    }

    // Relationships
    public function properties()
    {
        return $this->hasMany(Property::class, 'landlord_id');
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function favoriteProperties()
    {
        return $this->belongsToMany(Property::class, 'favorites')
                    ->withPivot(['list_name', 'notes', 'created_at'])
                    ->withTimestamps();
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function landlordReviews()
    {
        return $this->hasMany(Review::class, 'landlord_id');
    }

    public function emailPreferences()
    {
        return $this->hasOne(UserEmailPreference::class);
    }

    public function searchHistories()
    {
        return $this->hasMany(SearchHistory::class);
    }

    // ML-related relationships
    public function userEvents()
    {
        return $this->hasMany(UserEvent::class);
    }

    public function propertyEdits()
    {
        return $this->hasMany(PropertyEdit::class);
    }

    public function pushNotificationTokens()
    {
        return $this->hasMany(PushNotificationToken::class);
    }

    public function fraudScore()
    {
        return $this->morphOne(FraudScore::class, 'scoreable')->latest();
    }

    public function fraudScores()
    {
        return $this->morphMany(FraudScore::class, 'scoreable');
    }

    // Helper method to check if phone is verified
    public function hasVerifiedPhone()
    {
        return !is_null($this->phone_verified_at);
    }

    public function savedSearches()
    {
        return $this->hasMany(SavedSearch::class);
    }

    public function propertyComparisons()
    {
        return $this->hasMany(PropertyComparison::class);
    }

    // Report relationships
    public function reports()
    {
        return $this->hasMany(Report::class, 'reporter_id');
    }

    public function reportedReports()
    {
        return $this->hasMany(Report::class, 'reported_user_id');
    }

    public function reportComments()
    {
        return $this->hasMany(ReportComment::class);
    }

    public function reportNotifications()
    {
        return $this->hasMany(ReportNotification::class);
    }

    public function unreadReportNotifications()
    {
        return $this->hasMany(ReportNotification::class)->unread();
    }

    public function messageReportNotifications()
    {
        return $this->hasMany(MessageReportNotification::class);
    }

    public function unreadMessageReportNotifications()
    {
        return $this->hasMany(MessageReportNotification::class)->unread();
    }

    public function approvedLandlordReviews()
    {
        return $this->hasMany(Review::class, 'landlord_id')->where('is_approved', true);
    }

    /**
     * Get average rating as a landlord
     */
    public function getAverageLandlordRating()
    {
        return $this->approvedLandlordReviews()->avg('landlord_rating');
    }

    /**
     * Get review count as a landlord
     */
    public function getLandlordReviewCount()
    {
        return $this->approvedLandlordReviews()->count();
    }

    // Email verification methods
    /**
     * Generate a verification token for the user
     */
    public function generateVerificationToken()
    {
        $this->verification_token = bin2hex(random_bytes(32));
        $this->verification_expires_at = now()->addHours(24);
        $this->save();
        
        return $this->verification_token;
    }

    /**
     * Verify the user's email
     */
    public function verifyEmail()
    {
        $this->is_verified = true;
        $this->email_verified_at = now();
        $this->verification_token = null;
        $this->verification_expires_at = null;
        $this->save();
    }

    /**
     * Check if the user's email is verified
     */
    public function isEmailVerified()
    {
        return $this->is_verified && $this->email_verified_at !== null;
    }

    /**
     * Check if the verification token is valid
     */
    public function isVerificationTokenValid($token)
    {
        return $this->verification_token === $token && 
               $this->verification_expires_at && 
               $this->verification_expires_at->isFuture();
    }

    /**
     * Clear the verification token
     */
    public function clearVerificationToken()
    {
        $this->verification_token = null;
        $this->verification_expires_at = null;
        $this->save();
    }

    // Message Report relationships
    public function messageReports()
    {
        return $this->hasMany(MessageReport::class, 'sender_id');
    }

    public function reportedMessageReports()
    {
        return $this->hasMany(MessageReport::class, 'recipient_id');
    }

    public function messageReportComments()
    {
        return $this->hasMany(MessageReportComment::class);
    }


    // Admin role relationships
    public function adminRoles()
    {
        return $this->belongsToMany(AdminRole::class, 'admin_user_roles', 'user_id', 'role_id')
                    ->withPivot(['assigned_by', 'assigned_at', 'expires_at', 'is_active'])
                    ->wherePivot('is_active', true)
                    ->withTimestamps();
    }

    public function allAdminRoles()
    {
        return $this->belongsToMany(AdminRole::class, 'admin_user_roles', 'user_id', 'role_id')
                    ->withPivot(['assigned_by', 'assigned_at', 'expires_at', 'is_active'])
                    ->withTimestamps();
    }

    public function assignedTickets()
    {
        return $this->hasMany(TicketAssignment::class, 'assigned_to');
    }

    public function activeTickets()
    {
        return $this->assignedTickets()->active();
    }

    public function completedTickets()
    {
        return $this->assignedTickets()->byStatus('completed');
    }

    // Admin permission methods
    public function hasAdminRole(string $roleName): bool
    {
        return $this->adminRoles()->where('name', $roleName)->exists();
    }

    public function hasAdminPermission(string $permission): bool
    {
        return $this->adminRoles()
                    ->whereHas('permissions', function($query) use ($permission) {
                        $query->where('name', $permission);
                    })
                    ->exists();
    }

    public function hasAdminLevel(int $minLevel): bool
    {
        return $this->adminRoles()->where('level', '>=', $minLevel)->exists();
    }

    public function getHighestAdminLevel(): int
    {
        return $this->adminRoles()->max('level') ?? 0;
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasAdminLevel(4);
    }

    public function isAdminManager(): bool
    {
        return $this->hasAdminLevel(3);
    }

    public function isSeniorAdmin(): bool
    {
        return $this->hasAdminLevel(2);
    }

    public function isJuniorAdmin(): bool
    {
        return $this->hasAdminLevel(1);
    }

    public function getAdminPermissions(): array
    {
        return $this->adminRoles()
                    ->with('permissions')
                    ->get()
                    ->pluck('permissions')
                    ->flatten()
                    ->pluck('name')
                    ->unique()
                    ->toArray();
    }

    // Profile relationships
    public function userStatistics()
    {
        return $this->hasOne(UserStatistics::class);
    }

    public function landlordStatistics()
    {
        return $this->hasOne(LandlordStatistics::class);
    }

    public function adminStatistics()
    {
        return $this->hasOne(AdminStatistics::class);
    }

    // Profile helper methods
    public function getProfilePictureUrl()
    {
        if ($this->profile_picture) {
            return asset('storage/' . $this->profile_picture);
        }
        return asset('images/default-avatar.png');
    }

    public function getProfileCompletionPercentage()
    {
        $totalFields = 8; // Total profile fields
        $completedFields = 0;

        if ($this->profile_picture) $completedFields++;
        if ($this->bio) $completedFields++;
        if ($this->phone_number) $completedFields++;
        if ($this->date_of_birth) $completedFields++;
        if ($this->gender) $completedFields++;
        if ($this->location) $completedFields++;
        if ($this->website) $completedFields++;
        
        // Handle social_links safely - check if it's an array and has content
        $socialLinks = $this->social_links;
        if (is_array($socialLinks) && count($socialLinks) > 0) {
            $completedFields++;
        }

        return round(($completedFields / $totalFields) * 100);
    }

    public function updateLastActive()
    {
        $this->update(['last_active_at' => now()]);
    }

    public function getSocialLinks()
    {
        return $this->social_links ?? [];
    }

    public function getPreferences()
    {
        return $this->preferences ?? [];
    }

    public function getEmergencyContact()
    {
        return $this->emergency_contact ?? [];
    }

    // Role-specific profile methods
    public function getRenterProfile()
    {
        return [
            'basic_info' => [
                'name' => $this->name,
                'email' => $this->email,
                'profile_picture' => $this->getProfilePictureUrl(),
                'bio' => $this->bio,
                'location' => $this->location,
                'phone_number' => $this->phone_number,
            ],
            'statistics' => $this->userStatistics,
            'completion_percentage' => $this->getProfileCompletionPercentage(),
        ];
    }

    public function getLandlordProfile()
    {
        return [
            'basic_info' => [
                'name' => $this->name,
                'email' => $this->email,
                'profile_picture' => $this->getProfilePictureUrl(),
                'bio' => $this->bio,
                'location' => $this->location,
                'phone_number' => $this->phone_number,
                'business_name' => $this->business_name,
                'business_license' => $this->business_license,
                'verification_status' => $this->verification_status,
            ],
            'statistics' => $this->landlordStatistics,
            'completion_percentage' => $this->getProfileCompletionPercentage(),
        ];
    }

    public function getAdminProfile()
    {
        return [
            'basic_info' => [
                'name' => $this->name,
                'email' => $this->email,
                'profile_picture' => $this->getProfilePictureUrl(),
                'bio' => $this->bio,
                'location' => $this->location,
                'phone_number' => $this->phone_number,
                'admin_level' => $this->admin_level,
                'admin_permissions' => $this->admin_permissions,
            ],
            'statistics' => $this->adminStatistics,
            'completion_percentage' => $this->getProfileCompletionPercentage(),
        ];
    }
}