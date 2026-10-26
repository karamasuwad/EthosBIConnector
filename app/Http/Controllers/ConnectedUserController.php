<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ConnectedUserController extends Controller
{
    /**
     * Display a listing of connected users.
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search'));
        $perPage = in_array((int) $request->query('per_page'), [10, 25, 50, 100]) ? (int) $request->query('per_page') : 10;

        // Get users who have logged in recently (last 30 minutes)
        $connectedUsers = User::where('is_active', true)
                            // ->where('last_login_at', '>=', now()->subMinutes(30))
                            ->whereDate('last_login_at', Carbon::today())
                            ->when($search !== '', fn ($q) => $q->where(function ($q) use ($search) {
                                $q->where('name', 'like', "%{$search}%")
                                  ->orWhere('email', 'like', "%{$search}%")
                                  ->orWhere('telephone', 'like', "%{$search}%")
                                  ->orWhere('bi_link_name', 'like', "%{$search}%");
                            }))
                            ->orderBy('last_login_at', 'desc')
                            ->paginate($perPage)
                            ->withQueryString();

        // Get session data from database (if using database sessions)
        $sessions = [];
        if (config('session.driver') === 'database') {
            $sessions = DB::table('sessions')
                         ->where('last_activity', '>=', now()->subMinutes(30)->timestamp)
                         ->orderBy('last_activity', 'desc')
                         ->get();
        }

        return view('connected-users.index', compact('connectedUsers', 'sessions'));
    }

    /**
     * Get active sessions count.
     */
    public function getActiveCount()
    {
        $activeCount = User::where('is_active', true)
                          ->where('last_login_at', '>=', now()->subMinutes(30))
                          ->count();

        return response()->json(['count' => $activeCount]);
    }
}
