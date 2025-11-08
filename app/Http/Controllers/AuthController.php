<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Display the registration form.
     */
    public function showRegistrationForm(Request $request): View
    {
        return view('auth.register', [
            'referralCode' => $request->query('ref'),
        ]);
    }

    /**
     * Handle an incoming registration request.
     */
    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'min:8'],
            'referral_code' => ['nullable', 'string', 'exists:users,referral_code'],
        ]);

        $referrer = $this->resolveReferrer($validated['referral_code'] ?? null);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'referral_code' => $this->generateUniqueReferralCode(),
            'referred_by' => $referrer?->id,
        ]);

        app(TreeController::class)->place($user, $referrer);

        Auth::login($user);

        return redirect()
            ->route('home')
            ->with([
                'status' => 'Registration successful.',
                'referral_link' => route('register', ['ref' => $user->referral_code]),
            ]);
    }

    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
            'remember' => ['nullable', 'boolean'],
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt([
            'email' => $credentials['email'],
            'password' => $credentials['password'],
        ], $remember)) {
            $request->session()->regenerate();

            return redirect()
                ->intended(route('home'))
                ->with('status', 'Welcome back!');
        }

        return back()->withErrors([
            'email' => 'These credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Generate a unique referral code.
     */
    protected function generateUniqueReferralCode(): string
    {
        do {
            $code = Str::upper(Str::random(8));
        } while (User::where('referral_code', $code)->exists());

        return $code;
    }

    /**
     * Resolve the referrer id from a referral code.
     */
    protected function resolveReferrer(?string $code): ?User
    {
        if ($code === null) {
            return null;
        }

        return User::where('referral_code', $code)->first();
    }

    public function validateReferral(Request $request): JsonResponse
    {
        $code = $request->query('code');

        if ($code === null || $code === '') {
            return response()->json([
                'valid' => false,
                'message' => 'Referral code is required.',
            ]);
        }

        $exists = User::where('referral_code', $code)->exists();

        return response()->json([
            'valid' => $exists,
            'message' => $exists ? 'Referral code found.' : 'Referral code not found.',
        ]);
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('status', 'You have been logged out.');
    }
}

