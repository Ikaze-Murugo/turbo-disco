# ML Data Collection & Fraud Detection Implementation

## Overview

This implementation adds comprehensive ML data collection capabilities and Phase 1 fraud detection to the Murugo platform.

## What's Included

### Database Schema Changes

1. **user_events** - Track user behavior for fraud detection
2. **property_edits** - Track all property modifications
3. **ip_reputation** - Store IP reputation data
4. **fraud_scores** - Store fraud detection scores and risk assessments
5. **Enhanced images table** - Add metadata for duplicate detection
6. **Enhanced users table** - Add phone verification fields

### Models

- `UserEvent` - User behavior tracking
- `PropertyEdit` - Property modification tracking
- `IpReputation` - IP reputation management
- `FraudScore` - Fraud score storage and management
- Updated `User`, `Property`, and `Image` models with ML relationships

### Services

- `FraudDetectionService` - Phase 1 rule-based fraud detection
- `PropertyEditTracker` - Automatic property edit tracking

### Middleware

- `TrackUserEventsMiddleware` - Automatic page view tracking

### Admin Dashboard

- Fraud Detection Dashboard (`/admin/fraud-detection`)
- View all fraud scores with filtering
- Review and mark scores as reviewed
- Run fraud detection manually
- Export fraud detection data to CSV

### Console Commands

```bash
# Run fraud detection on users
php artisan fraud:detect --users

# Run fraud detection on properties
php artisan fraud:detect --properties

# Run on both
php artisan fraud:detect --all

# Limit number of records processed
php artisan fraud:detect --all --limit=50
```

## Installation Steps

### 1. Run Migrations

```bash
php artisan migrate
```

This will create all the new tables and add fields to existing tables.

### 2. Register Middleware (Optional)

To enable automatic user event tracking, add to `app/Http/Kernel.php`:

```php
protected $middlewareGroups = [
    'web' => [
        // ... existing middleware
        \App\Http\Middleware\TrackUserEventsMiddleware::class,
    ],
];
```

**Note:** This is optional. You can enable it later when you're ready to start collecting event data.

### 3. Schedule Fraud Detection (Optional)

Add to `app/Console/Kernel.php` to run fraud detection daily:

```php
protected function schedule(Schedule $schedule)
{
    // Run fraud detection daily at 2 AM
    $schedule->command('fraud:detect --all --limit=500')->dailyAt('02:00');
}
```

## Usage

### Running Fraud Detection

#### Via Command Line

```bash
# Scan 100 users (default)
php artisan fraud:detect --users

# Scan 100 properties (default)
php artisan fraud:detect --properties

# Scan both with custom limit
php artisan fraud:detect --all --limit=200
```

#### Via Admin Dashboard

1. Navigate to `/admin/fraud-detection`
2. Click "Scan Users" or "Scan Properties"
3. View results and review flagged entities

### Viewing Fraud Scores

1. Go to `/admin/fraud-detection`
2. Filter by:
   - Type (Users/Properties)
   - Risk Level (Critical/High/Medium/Low)
   - Review Status (Reviewed/Unreviewed)
3. Click "View" to see detailed breakdown
4. Mark as reviewed with optional notes

### Understanding Fraud Scores

**Score Range:** 0-100 (higher = more suspicious)

**Risk Levels:**
- **Low** (0-29): Normal behavior
- **Medium** (30-49): Some suspicious indicators
- **High** (50-69): Multiple red flags
- **Critical** (70-100): Highly suspicious, immediate review needed

**Auto-Flagging:** Scores >= 60 are automatically flagged for admin review

### User Fraud Detection Factors

1. **Profile Completion** (0-20 points)
   - Very low completion (<30%): 20 points
   - Low completion (<50%): 10 points

2. **Verification Status** (0-25 points)
   - Email not verified: 15 points
   - Phone not verified: 10 points

3. **Account Activity** (0-20 points)
   - New account (<7 days) with many listings (>5): 20 points
   - Very new account (<1 day) with multiple listings (>2): 15 points

4. **Listing Behavior** (0-20 points)
   - Rapid listing creation (>5 in 1 hour): 20 points
   - Fast listing creation (>3 in 1 hour): 10 points

5. **Report History** (0-15 points)
   - Multiple fraud reports (>=3): 15 points
   - Has fraud reports (>=1): 8 points

### Property Fraud Detection Factors

1. **Price Anomaly** (0-25 points)
   - Price >50% below market average: 25 points
   - Price >30% below market average: 15 points

2. **Description Quality** (0-20 points)
   - Contact info in description: 20 points
   - Very short description (<50 chars): 15 points
   - Short description (<100 chars): 8 points

3. **Image Quality** (0-20 points)
   - No images: 20 points
   - Only one image: 10 points

4. **Landlord Reputation** (0-20 points)
   - Landlord has high fraud score (>=70): 20 points
   - Landlord has moderate fraud score (>=50): 10 points

5. **Edit Frequency** (0-15 points)
   - Frequent edits (>10 in 24h): 15 points
   - Multiple edits (>5 in 24h): 8 points

## Data Collection

### Automatic Data Collection

Once deployed, the system automatically collects:

- **Page Views** - Every page visit (when middleware enabled)
- **Property Edits** - All property modifications (requires integration)
- **User Registrations** - New user data

### Manual Data Collection

You can manually trigger fraud detection:

```bash
php artisan fraud:detect --all
```

### Property Edit Tracking

To track property edits, add to your Property controller:

```php
use App\Services\PropertyEditTracker;

// After creating a property
PropertyEditTracker::trackCreation($property, auth()->id());

// Before updating a property
$changes = PropertyEditTracker::getChanges($property);
$property->save();
PropertyEditTracker::trackUpdate($property, $changes, auth()->id());
```

## API Integration (Future)

### IP Reputation Services

To enhance fraud detection, integrate with:

1. **AbuseIPDB** - Free tier available
   - Sign up: https://www.abuseipdb.com/
   - Add API key to `.env`: `ABUSEIPDB_API_KEY=your_key`

2. **MaxMind GeoIP2** - Detect VPNs, proxies, Tor
   - Sign up: https://www.maxmind.com/
   - Add license key to `.env`: `MAXMIND_LICENSE_KEY=your_key`

## Performance Considerations

### Database Indexes

All tables include proper indexes for performance:
- `user_events`: Indexed on user_id, session_id, event_type, created_at
- `property_edits`: Indexed on property_id, user_id, field_name, created_at
- `fraud_scores`: Indexed on fraud_score, risk_level, is_flagged

### Data Retention

Consider implementing data retention policies:

```bash
# Delete old user events (older than 90 days)
php artisan db:table('user_events')->where('created_at', '<', now()->subDays(90))->delete();
```

### Batch Processing

Use the `--limit` option to process in batches:

```bash
# Process 50 at a time
php artisan fraud:detect --all --limit=50
```

## Monitoring

### Key Metrics to Monitor

1. **Flagged Entities** - Check `/admin/fraud-detection` daily
2. **Unreviewed Scores** - Review and mark as reviewed
3. **Critical Risk** - Immediate attention required
4. **False Positives** - Adjust scoring thresholds if needed

### Logs

Fraud detection errors are logged to `storage/logs/laravel.log`

## Troubleshooting

### Migration Errors

If migrations fail:

```bash
# Check current migration status
php artisan migrate:status

# Rollback last migration
php artisan migrate:rollback

# Re-run migrations
php artisan migrate
```

### Missing Relationships

If you see "Call to undefined relationship" errors:

```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Dump autoload
composer dump-autoload
```

### Middleware Not Working

If user events aren't being tracked:

1. Verify middleware is registered in `app/Http/Kernel.php`
2. Clear route cache: `php artisan route:clear`
3. Check logs for errors

## Next Steps (Phase 2)

After Phase 1 is stable, consider:

1. **Anomaly Detection** - Implement Isolation Forest for pattern detection
2. **Image Duplicate Detection** - Use perceptual hashing
3. **NLP for Descriptions** - Detect spam/fraud in text
4. **IP Reputation Integration** - Connect to AbuseIPDB/MaxMind
5. **Machine Learning Models** - Train supervised models with labeled data

## Support

For questions or issues:
- Check logs: `storage/logs/laravel.log`
- Review this documentation
- Contact: dadishimwe0@gmail.com

---

**Version:** 1.0  
**Last Updated:** November 24, 2025  
**Model Version:** phase1_v1.0
