<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\BiLink;
use Illuminate\Validation\Rules;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    /**
     * Show the login form.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('auth.login');
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        // Validate the login request
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        // Attempt to authenticate the user
        if (Auth::attempt($credentials, $request->boolean('remember'))) {

            $user = Auth::user();

            if (!$user->is_active) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Your account has been deactivated. Please contact administrator.',
                ])->onlyInput('email');
            }

            // Update last login timestamp
            $user->update([
                'last_login_at' => now(),
            ]);

            // Regenerate session for security
            $request->session()->regenerate();

            //
            // If the user is a normal user, redirect to BI report
            if ($user->is_user) {

                // $this->showReport();
                // return;
                return redirect()->route('reports.show');

            }else{
                // Otherwise go to dashboard
                return redirect()->intended('/dashboard');
            }
        }

        // Authentication failed
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }


    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Show the dashboard after login.
     *
     * @return \Illuminate\View\View
     */
    public function dashboard()
    {
         // Get users who have logged in recently (last 30 minutes)
        $connectedUsers = User::where('is_active', true)
                            // ->where('last_login_at', '>=', now()->subMinutes(1))
                            ->orderBy('last_login_at', 'desc')
                            ->get();

        // Get session data from database (if using database sessions)
        $sessions = [];
        if (config('session.driver') === 'database') {
            $sessions = \DB::table('sessions')
                         ->where('last_activity', '>=', now()->subMinutes(30)->timestamp)
                         ->whereNotNull('user_id')
                         ->orderBy('last_activity', 'desc')
                         ->get();
        }

        $biLinks = BiLink::where('is_active', true)
                        ->orderBy('created_at', 'desc')
                        ->get();
        return view('dashboard' , compact('biLinks', 'sessions' , 'connectedUsers'));
    }



    public function open_bi_report(User $user)
    {
        $biLink = BiLink::find($user->bi_link_id);

        if (!$biLink) {
            return redirect()->back()->with('error', 'BI Link not found for this user.');
        }

        return redirect($biLink->link);
    }
}
