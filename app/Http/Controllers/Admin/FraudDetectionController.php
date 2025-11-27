<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FraudScore;
use App\Models\User;
use App\Models\Property;
use App\Services\FraudDetectionService;
use Illuminate\Http\Request;

class FraudDetectionController extends Controller
{
    protected FraudDetectionService $fraudService;

    public function __construct(FraudDetectionService $fraudService)
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->isAdmin()) {
                abort(403, 'Unauthorized access');
            }
            return $next($request);
        });
        
        $this->fraudService = $fraudService;
    }

    /**
     * Display fraud detection dashboard.
     */
    public function index(Request $request)
    {
        $type = $request->get('type', 'all'); // 'all', 'users', 'properties'
        $riskLevel = $request->get('risk_level'); // 'low', 'medium', 'high', 'critical'
        $reviewed = $request->get('reviewed'); // 'yes', 'no'

        $query = FraudScore::with(['scoreable', 'reviewer'])
            ->orderBy('fraud_score', 'desc');

        // Filter by type
        if ($type === 'users') {
            $query->forUsers();
        } elseif ($type === 'properties') {
            $query->forProperties();
        }

        // Filter by risk level
        if ($riskLevel) {
            $query->where('risk_level', $riskLevel);
        }

        // Filter by reviewed status
        if ($reviewed === 'yes') {
            $query->where('admin_reviewed', true);
        } elseif ($reviewed === 'no') {
            $query->where('admin_reviewed', false);
        }

        $fraudScores = $query->paginate(20);

        // Statistics
        $stats = [
            'total_flagged' => FraudScore::flagged()->count(),
            'unreviewed' => FraudScore::unreviewed()->count(),
            'critical' => FraudScore::where('risk_level', 'critical')->count(),
            'high' => FraudScore::where('risk_level', 'high')->count(),
            'medium' => FraudScore::where('risk_level', 'medium')->count(),
            'low' => FraudScore::where('risk_level', 'low')->count(),
            'users_flagged' => FraudScore::forUsers()->flagged()->count(),
            'properties_flagged' => FraudScore::forProperties()->flagged()->count(),
        ];

        return view('admin.fraud-detection.index', compact('fraudScores', 'stats', 'type', 'riskLevel', 'reviewed'));
    }

    /**
     * Show detailed fraud score for a specific entity.
     */
    public function show($id)
    {
        $fraudScore = FraudScore::with(['scoreable', 'reviewer'])->findOrFail($id);
        
        return view('admin.fraud-detection.show', compact('fraudScore'));
    }

    /**
     * Mark fraud score as reviewed.
     */
    public function review(Request $request, $id)
    {
        $request->validate([
            'notes' => 'nullable|string|max:1000',
        ]);

        $fraudScore = FraudScore::findOrFail($id);
        $fraudScore->markAsReviewed(auth()->id(), $request->notes);

        return redirect()->back()->with('success', 'Fraud score marked as reviewed.');
    }

    /**
     * Recalculate fraud score for a specific entity.
     */
    public function recalculate($id)
    {
        $fraudScore = FraudScore::with('scoreable')->findOrFail($id);
        
        if ($fraudScore->scoreable_type === User::class) {
            $this->fraudService->calculateUserFraudScore($fraudScore->scoreable);
        } elseif ($fraudScore->scoreable_type === Property::class) {
            $this->fraudService->calculatePropertyFraudScore($fraudScore->scoreable);
        }

        return redirect()->back()->with('success', 'Fraud score recalculated successfully.');
    }

    /**
     * Run fraud detection on all users.
     */
    public function runDetectionUsers()
    {
        $users = User::whereIn('role', ['landlord', 'renter'])->limit(100)->get();
        $flaggedCount = 0;

        foreach ($users as $user) {
            $fraudScore = $this->fraudService->calculateUserFraudScore($user);
            if ($fraudScore->is_flagged) {
                $flaggedCount++;
            }
        }

        return redirect()->back()->with('success', "Processed {$users->count()} users. Flagged: {$flaggedCount}");
    }

    /**
     * Run fraud detection on all properties.
     */
    public function runDetectionProperties()
    {
        $properties = Property::whereIn('status', ['active', 'pending'])->limit(100)->get();
        $flaggedCount = 0;

        foreach ($properties as $property) {
            $fraudScore = $this->fraudService->calculatePropertyFraudScore($property);
            if ($fraudScore->is_flagged) {
                $flaggedCount++;
            }
        }

        return redirect()->back()->with('success', "Processed {$properties->count()} properties. Flagged: {$flaggedCount}");
    }

    /**
     * Export fraud detection data.
     */
    public function export(Request $request)
    {
        $type = $request->get('type', 'all');
        
        $query = FraudScore::with('scoreable')->orderBy('fraud_score', 'desc');

        if ($type === 'users') {
            $query->forUsers();
        } elseif ($type === 'properties') {
            $query->forProperties();
        }

        $fraudScores = $query->get();

        $csvData = "ID,Type,Entity ID,Fraud Score,Risk Level,Is Flagged,Reviewed,Created At\n";
        
        foreach ($fraudScores as $score) {
            $csvData .= sprintf(
                "%d,%s,%d,%d,%s,%s,%s,%s\n",
                $score->id,
                class_basename($score->scoreable_type),
                $score->scoreable_id,
                $score->fraud_score,
                $score->risk_level,
                $score->is_flagged ? 'Yes' : 'No',
                $score->admin_reviewed ? 'Yes' : 'No',
                $score->created_at->format('Y-m-d H:i:s')
            );
        }

        return response($csvData)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="fraud_detection_' . date('Y-m-d') . '.csv"');
    }
}
