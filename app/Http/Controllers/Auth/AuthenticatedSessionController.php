<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use PragmaRX\Google2FA\Google2FA;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = User::where('email', $request->email)->first();

        if ($user->google2fa_secret) {
            $request->session()->put('2fa:user:id', $user->id);

            Auth::logout();

            return redirect()->route('verify.2fa.login');
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }

    public function verifyLogin2FA(Request $request)
    {
        $request->validate([
            'token' => 'required|numeric',
        ]);

        $userId = $request->session()->get('2fa:user:id');
        $user = User::findOrFail($userId);

        $google2fa = new Google2FA();
        $valid = $google2fa->verifyKey($user->google2fa_secret, $request->token, 5);

        if ($valid) {
            Auth::login($user);

            $request->session()->forget('2fa:user:id');
            
            return redirect()->route('dashboard');
        }

        return back()->withErrors(['token' => 'Invalid authentication token.']);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
