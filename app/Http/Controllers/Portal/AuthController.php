<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Applicant;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;

class AuthController extends Controller
{
    public function showRegister(Request $request)
    {
        return view('portal.register', ['email' => $request->query('email')]);
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:80'],
            'last_name' => ['required', 'string', 'max:80'],
            'email' => ['required', 'email', 'max:255', 'unique:applicants,email'],
            'phone' => ['nullable', 'string', 'max:40'],
            'password' => ['required', 'confirmed', PasswordRule::min(8)],
        ]);
        $data['email'] = strtolower($data['email']);
        $applicant = Applicant::create($data);
        $claimed = $applicant->claimApplications();

        Auth::guard('applicant')->login($applicant);
        $request->session()->regenerate();
        $applicant->forceFill(['last_login_at' => now()])->save();

        return redirect()->route('portal.dashboard')->with('status', 'Welcome, '.$applicant->first_name.'. Your applicant account is ready.'.($claimed ? " We linked {$claimed} existing ".Str::plural('application', $claimed).' to it.' : ''));
    }

    public function showLogin()
    {
        return view('portal.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate(['email' => ['required', 'email'], 'password' => ['required']]);
        if (! Auth::guard('applicant')->attempt(['email' => strtolower($credentials['email']), 'password' => $credentials['password']], $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'Those details do not match our records.'])->onlyInput('email');
        }
        $request->session()->regenerate();
        $applicant = Auth::guard('applicant')->user();
        $applicant->forceFill(['last_login_at' => now()])->save();
        $applicant->claimApplications();

        return redirect()->intended(route('portal.dashboard'));
    }

    public function logout(Request $request)
    {
        Auth::guard('applicant')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    public function showForgot()
    {
        return view('portal.forgot');
    }

    public function sendReset(Request $request)
    {
        $request->validate(['email' => ['required', 'email']]);
        Password::broker('applicants')->sendResetLink(['email' => strtolower($request->email)]);

        // Same reply whether or not the address exists.
        return back()->with('status', 'If an applicant account exists for that address, a reset link has been emailed.');
    }

    public function showReset(Request $request, string $token)
    {
        return view('portal.reset', ['token' => $token, 'email' => $request->query('email')]);
    }

    public function reset(Request $request)
    {
        $request->validate(['token' => ['required'], 'email' => ['required', 'email'], 'password' => ['required', 'confirmed', PasswordRule::min(8)]]);
        $status = Password::broker('applicants')->reset(
            ['email' => strtolower($request->email), 'password' => $request->password, 'password_confirmation' => $request->password_confirmation, 'token' => $request->token],
            function (Applicant $applicant, string $password) {
                $applicant->forceFill(['password' => $password, 'remember_token' => Str::random(60)])->save();
                event(new PasswordReset($applicant));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('portal.login')->with('status', 'Your password has been reset. Please sign in.')
            : back()->withErrors(['email' => __($status)])->onlyInput('email');
    }
}
