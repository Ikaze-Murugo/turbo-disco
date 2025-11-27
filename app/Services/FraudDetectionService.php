<?php

namespace App\Services;

use App\Models\User;
use App\Models\Property;
use App\Models\FraudScore;
use App\Models\IpReputation;
use Illuminate\Support\Facades\DB;

class FraudDetectionService
{
    /**
     * Calculate fraud score for a user (Phase 1: Rule-based heuristics).
     */
    public function calculateUserFraudScore(User $user): FraudScore
    {
        $score = 0;
        $riskFactors = [];
        $scoreBreakdown = [];

        // Factor 1: Profile completion (0-20 points)
        $profileScore = $this->evaluateProfileCompletion($user);
        $score += $profileScore['score'];
        $scoreBreakdown['profile_completion'] = $profileScore;
        if ($profileScore['is_risk']) {
            $riskFactors[] = $profileScore['factor'];
        }

        // Factor 2: Verification status (0-25 points)
        $verificationScore = $this->evaluateVerificationStatus($user);
        $score += $verificationScore['score'];
        $scoreBreakdown['verification'] = $verificationScore;
        if ($verificationScore['is_risk']) {
            $riskFactors[] = $verificationScore['factor'];
        }

        // Factor 3: Account age vs activity (0-20 points)
        $activityScore = $this->evaluateAccountActivity($user);
        $score += $activityScore['score'];
        $scoreBreakdown['activity'] = $activityScore;
        if ($activityScore['is_risk']) {
            $riskFactors[] = $activityScore['factor'];
        }

        // Factor 4: Listing behavior (0-20 points)
        $listingScore = $this->evaluateListingBehavior($user);
        $score += $listingScore['score'];
        $scoreBreakdown['listing_behavior'] = $listingScore;
        if ($listingScore['is_risk']) {
            $riskFactors[] = $listingScore['factor'];
        }

        // Factor 5: Report history (0-15 points)
        $reportScore = $this->evaluateReportHistory($user);
        $score += $reportScore['score'];
        $scoreBreakdown['reports'] = $reportScore;
        if ($reportScore['is_risk']) {
            $riskFactors[] = $reportScore['factor'];
        }

        // Determine risk level
        $riskLevel = $this->determineRiskLevel($score);
        $isFlagged = $score >= 60; // Auto-flag if score >= 60

        // Create or update fraud score
        return FraudScore::updateOrCreate(
            [
                'scoreable_type' => User::class,
                'scoreable_id' => $user->id,
            ],
            [
                'fraud_score' => $score,
                'risk_level' => $riskLevel,
                'risk_factors' => $riskFactors,
                'score_breakdown' => $scoreBreakdown,
                'model_version' => 'phase1_v1.0',
                'is_flagged' => $isFlagged,
            ]
        );
    }

    /**
     * Calculate fraud score for a property.
     */
    public function calculatePropertyFraudScore(Property $property): FraudScore
    {
        $score = 0;
        $riskFactors = [];
        $scoreBreakdown = [];

        // Factor 1: Price anomaly (0-25 points)
        $priceScore = $this->evaluatePriceAnomaly($property);
        $score += $priceScore['score'];
        $scoreBreakdown['price'] = $priceScore;
        if ($priceScore['is_risk']) {
            $riskFactors[] = $priceScore['factor'];
        }

        // Factor 2: Description quality (0-20 points)
        $descriptionScore = $this->evaluateDescription($property);
        $score += $descriptionScore['score'];
        $scoreBreakdown['description'] = $descriptionScore;
        if ($descriptionScore['is_risk']) {
            $riskFactors[] = $descriptionScore['factor'];
        }

        // Factor 3: Image quality (0-20 points)
        $imageScore = $this->evaluateImages($property);
        $score += $imageScore['score'];
        $scoreBreakdown['images'] = $imageScore;
        if ($imageScore['is_risk']) {
            $riskFactors[] = $imageScore['factor'];
        }

        // Factor 4: Landlord reputation (0-20 points)
        $landlordScore = $this->evaluateLandlordReputation($property);
        $score += $landlordScore['score'];
        $scoreBreakdown['landlord'] = $landlordScore;
        if ($landlordScore['is_risk']) {
            $riskFactors[] = $landlordScore['factor'];
        }

        // Factor 5: Edit frequency (0-15 points)
        $editScore = $this->evaluateEditFrequency($property);
        $score += $editScore['score'];
        $scoreBreakdown['edits'] = $editScore;
        if ($editScore['is_risk']) {
            $riskFactors[] = $editScore['factor'];
        }

        $riskLevel = $this->determineRiskLevel($score);
        $isFlagged = $score >= 60;

        return FraudScore::updateOrCreate(
            [
                'scoreable_type' => Property::class,
                'scoreable_id' => $property->id,
            ],
            [
                'fraud_score' => $score,
                'risk_level' => $riskLevel,
                'risk_factors' => $riskFactors,
                'score_breakdown' => $scoreBreakdown,
                'model_version' => 'phase1_v1.0',
                'is_flagged' => $isFlagged,
            ]
        );
    }

    // User evaluation methods

    private function evaluateProfileCompletion(User $user): array
    {
        $completion = $user->profile_completion_percentage ?? 0;
        
        if ($completion < 30) {
            return ['score' => 20, 'is_risk' => true, 'factor' => 'Very low profile completion (<30%)', 'value' => $completion];
        } elseif ($completion < 50) {
            return ['score' => 10, 'is_risk' => true, 'factor' => 'Low profile completion (<50%)', 'value' => $completion];
        }
        
        return ['score' => 0, 'is_risk' => false, 'factor' => null, 'value' => $completion];
    }

    private function evaluateVerificationStatus(User $user): array
    {
        $score = 0;
        $factors = [];
        
        if (!$user->is_verified) {
            $score += 15;
            $factors[] = 'Email not verified';
        }
        
        if (!$user->hasVerifiedPhone()) {
            $score += 10;
            $factors[] = 'Phone not verified';
        }
        
        return [
            'score' => $score,
            'is_risk' => $score > 0,
            'factor' => implode(', ', $factors),
            'value' => ['email_verified' => $user->is_verified, 'phone_verified' => $user->hasVerifiedPhone()]
        ];
    }

    private function evaluateAccountActivity(User $user): array
    {
        $accountAge = now()->diffInDays($user->created_at);
        $propertyCount = $user->properties()->count();
        
        // New account with many listings is suspicious
        if ($accountAge < 7 && $propertyCount > 5) {
            return ['score' => 20, 'is_risk' => true, 'factor' => 'New account with many listings', 'value' => ['age_days' => $accountAge, 'properties' => $propertyCount]];
        } elseif ($accountAge < 1 && $propertyCount > 2) {
            return ['score' => 15, 'is_risk' => true, 'factor' => 'Very new account with multiple listings', 'value' => ['age_days' => $accountAge, 'properties' => $propertyCount]];
        }
        
        return ['score' => 0, 'is_risk' => false, 'factor' => null, 'value' => ['age_days' => $accountAge, 'properties' => $propertyCount]];
    }

    private function evaluateListingBehavior(User $user): array
    {
        // Check for rapid listing creation
        $recentListings = $user->properties()->where('created_at', '>=', now()->subHour())->count();
        
        if ($recentListings > 5) {
            return ['score' => 20, 'is_risk' => true, 'factor' => 'Rapid listing creation (>5 in 1 hour)', 'value' => $recentListings];
        } elseif ($recentListings > 3) {
            return ['score' => 10, 'is_risk' => true, 'factor' => 'Fast listing creation (>3 in 1 hour)', 'value' => $recentListings];
        }
        
        return ['score' => 0, 'is_risk' => false, 'factor' => null, 'value' => $recentListings];
    }

    private function evaluateReportHistory(User $user): array
    {
        $reportCount = DB::table('reports')
            ->where('reported_user_id', $user->id)
            ->whereIn('category', ['fraud', 'fake_listing', 'spam'])
            ->count();
        
        if ($reportCount >= 3) {
            return ['score' => 15, 'is_risk' => true, 'factor' => 'Multiple fraud reports', 'value' => $reportCount];
        } elseif ($reportCount >= 1) {
            return ['score' => 8, 'is_risk' => true, 'factor' => 'Has fraud reports', 'value' => $reportCount];
        }
        
        return ['score' => 0, 'is_risk' => false, 'factor' => null, 'value' => $reportCount];
    }

    // Property evaluation methods

    private function evaluatePriceAnomaly(Property $property): array
    {
        // Get average price for similar properties
        $avgPrice = Property::where('location', $property->location)
            ->where('bedrooms', $property->bedrooms)
            ->where('type', $property->type)
            ->where('status', 'active')
            ->avg('price');
        
        if (!$avgPrice || $avgPrice == 0) {
            return ['score' => 0, 'is_risk' => false, 'factor' => null, 'value' => null];
        }
        
        $deviation = abs($property->price - $avgPrice) / $avgPrice;
        
        // Price is suspiciously low (>50% below average)
        if ($property->price < $avgPrice && $deviation > 0.5) {
            return ['score' => 25, 'is_risk' => true, 'factor' => 'Price significantly below market average', 'value' => ['price' => $property->price, 'avg' => $avgPrice, 'deviation' => round($deviation * 100, 2)]];
        } elseif ($property->price < $avgPrice && $deviation > 0.3) {
            return ['score' => 15, 'is_risk' => true, 'factor' => 'Price below market average', 'value' => ['price' => $property->price, 'avg' => $avgPrice, 'deviation' => round($deviation * 100, 2)]];
        }
        
        return ['score' => 0, 'is_risk' => false, 'factor' => null, 'value' => ['price' => $property->price, 'avg' => $avgPrice, 'deviation' => round($deviation * 100, 2)]];
    }

    private function evaluateDescription(Property $property): array
    {
        $description = $property->description ?? '';
        $length = strlen($description);
        
        // Check for contact info in description (bypass platform)
        if (preg_match('/\b\d{3}[-.\s]?\d{3}[-.\s]?\d{4}\b/', $description) || 
            preg_match('/\b[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Z|a-z]{2,}\b/', $description)) {
            return ['score' => 20, 'is_risk' => true, 'factor' => 'Contact info in description', 'value' => $length];
        }
        
        // Very short description
        if ($length < 50) {
            return ['score' => 15, 'is_risk' => true, 'factor' => 'Very short description', 'value' => $length];
        } elseif ($length < 100) {
            return ['score' => 8, 'is_risk' => true, 'factor' => 'Short description', 'value' => $length];
        }
        
        return ['score' => 0, 'is_risk' => false, 'factor' => null, 'value' => $length];
    }

    private function evaluateImages(Property $property): array
    {
        $imageCount = $property->images()->count();
        
        if ($imageCount == 0) {
            return ['score' => 20, 'is_risk' => true, 'factor' => 'No images', 'value' => 0];
        } elseif ($imageCount == 1) {
            return ['score' => 10, 'is_risk' => true, 'factor' => 'Only one image', 'value' => 1];
        }
        
        return ['score' => 0, 'is_risk' => false, 'factor' => null, 'value' => $imageCount];
    }

    private function evaluateLandlordReputation(Property $property): array
    {
        $landlord = $property->landlord;
        $landlordFraudScore = $landlord->fraudScore;
        
        if ($landlordFraudScore && $landlordFraudScore->fraud_score >= 70) {
            return ['score' => 20, 'is_risk' => true, 'factor' => 'Landlord has high fraud score', 'value' => $landlordFraudScore->fraud_score];
        } elseif ($landlordFraudScore && $landlordFraudScore->fraud_score >= 50) {
            return ['score' => 10, 'is_risk' => true, 'factor' => 'Landlord has moderate fraud score', 'value' => $landlordFraudScore->fraud_score];
        }
        
        return ['score' => 0, 'is_risk' => false, 'factor' => null, 'value' => $landlordFraudScore?->fraud_score];
    }

    private function evaluateEditFrequency(Property $property): array
    {
        $recentEdits = $property->propertyEdits()->where('created_at', '>=', now()->subDay())->count();
        
        if ($recentEdits > 10) {
            return ['score' => 15, 'is_risk' => true, 'factor' => 'Frequent edits (>10 in 24h)', 'value' => $recentEdits];
        } elseif ($recentEdits > 5) {
            return ['score' => 8, 'is_risk' => true, 'factor' => 'Multiple edits (>5 in 24h)', 'value' => $recentEdits];
        }
        
        return ['score' => 0, 'is_risk' => false, 'factor' => null, 'value' => $recentEdits];
    }

    private function determineRiskLevel(int $score): string
    {
        if ($score >= 70) return 'critical';
        if ($score >= 50) return 'high';
        if ($score >= 30) return 'medium';
        return 'low';
    }
}
