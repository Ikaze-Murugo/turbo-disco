<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Property;
use App\Models\Review;
use App\Models\Report;
use App\Models\MessageReport;
use App\Models\Message;
use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Redirect based on user role
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->isLandlord()) {
            return redirect()->route('landlord.dashboard');
        } elseif ($user->isRenter()) {
            return redirect()->route('renter.dashboard');
        }
        
        return redirect()->route('home');
    }
}
