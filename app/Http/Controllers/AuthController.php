<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\URL;
use Illuminate\View\View;
use Throwable;

class AuthController extends Controller
{
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $defaultRole = Role::where('name', 'admin')->first() ?? Role::first();

        if (!$defaultRole) {
            return back()->withErrors(['email' => 'Pendaftaran belum dapat diproses karena role default belum tersedia.'])->withInput();
        }

        User::create([
            'role_id' => $defaultRole->id,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'is_active' => false,
        ]);

        return to_route('login')->with('status', 'Pendaftaran berhasil. Tunggu persetujuan Owner sebelum masuk ke sistem.');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !$user->is_active) {
            return back()->withErrors(['email' => 'Akun tidak ditemukan atau sedang tidak aktif.'])->withInput();
        }

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended('/dashboard')->with('success', 'Selamat datang kembali, ' . $user->name . ' (' . ucwords(str_replace('_', ' ', $user->role->name ?? 'User')) . ')');
        }

        return back()->withErrors(['email' => 'Email atau password salah.'])->withInput();
    }

    public function redirectToProvider(string $provider): RedirectResponse
    {
        $this->ensureSupportedProvider($provider);

        if (!config("services.{$provider}.client_id") || !config("services.{$provider}.client_secret")) {
            return to_route('login')->withErrors([
                'social' => 'Login sosial belum dikonfigurasi oleh administrator.',
            ]);
        }

        return Socialite::driver($provider)->redirect();
    }

    public function handleProviderCallback(string $provider): RedirectResponse
    {
        $this->ensureSupportedProvider($provider);

        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (InvalidStateException|Throwable $exception) {
            report($exception);

            return to_route('login')->withErrors([
                'social' => 'Login dengan ' . ucfirst($provider) . ' gagal. Silakan coba lagi.',
            ]);
        }

        $email = $socialUser->getEmail();

        if (!$email) {
            return to_route('login')->withErrors([
                'social' => 'Akun ' . ucfirst($provider) . ' tidak memberikan alamat email.',
            ]);
        }

        $user = User::where('oauth_provider', $provider)
            ->where('oauth_id', $socialUser->getId())
            ->first();

        if (!$user) {
            $user = User::where('email', $email)->first();
        }

        if (!$user) {
            $managementRole = Role::where('name', 'management')->first();

            if (!$managementRole) {
                return to_route('login')->withErrors([
                    'social' => 'Pendaftaran belum dapat diproses karena role default belum tersedia.',
                ]);
            }

            User::create([
                'role_id' => $managementRole->id,
                'name' => $socialUser->getName() ?: $email,
                'email' => $email,
                'password' => null,
                'oauth_provider' => $provider,
                'oauth_id' => $socialUser->getId(),
                'is_active' => false,
            ]);

            return to_route('login')->with('status', 'Pendaftaran berhasil. Tunggu persetujuan Super Admin sebelum masuk ke sistem.');
        }

        if ($user->oauth_provider && ($user->oauth_provider !== $provider || $user->oauth_id !== $socialUser->getId())) {
            return to_route('login')->withErrors([
                'social' => 'Email tersebut sudah terhubung dengan metode login lain.',
            ]);
        }

        $user->forceFill([
            'oauth_provider' => $provider,
            'oauth_id' => $socialUser->getId(),
        ])->save();

        if (!$user->is_active) {
            return to_route('login')->withErrors([
                'social' => 'Akun belum aktif. Tunggu persetujuan Super Admin.',
            ]);
        }

        Auth::login($user);
        request()->session()->regenerate();

        return redirect()->intended('/dashboard')->with('success', 'Selamat datang kembali, ' . $user->name . '.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('success', 'Anda telah berhasil keluar dari sistem.');
    }

    public function showForgotPasswordForm(): View
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request): RedirectResponse
    {
        $validated = $request->validate(['email' => ['required', 'email']]);

        if (app()->environment(['local', 'testing'])) {
            $user = User::where('email', $validated['email'])->first();

            if (!$user) {
                return back()->withErrors(['email' => 'Email tersebut belum terdaftar.'])->withInput();
            }

            $token = Password::broker()->createToken($user);

            return back()->with([
                'status' => 'Tautan reset siap digunakan.',
                'reset_email' => $user->email,
                'reset_url' => URL::route('password.reset', ['token' => $token, 'email' => $user->email]),
            ]);
        }

        try {
            return $this->sendResetLinkByEmail($validated);
        } catch (Throwable $exception) {
            report($exception);

            return back()->withErrors(['email' => 'Tautan reset gagal dikirim. Periksa konfigurasi email server.'])->withInput();
        }
    }

    private function sendResetLinkByEmail(array $credentials): RedirectResponse
    {
        $status = Password::sendResetLink($credentials);

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('status', __($status));
        }

        return back()->withErrors(['email' => __($status)])->withInput($credentials);
    }

    private function ensureSupportedProvider(string $provider): void
    {
        abort_unless(in_array($provider, ['google', 'linkedin'], true), 404);
    }

    public function showResetPasswordForm(string $token): View
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => request()->string('email')->toString(),
        ]);
    }

    public function resetPassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $status = Password::reset($validated, function ($user, $password) {
            $user->forceFill(['password' => Hash::make($password)])->save();
        });

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login')->with('status', __($status));
        }

        return back()->withErrors(['email' => __($status)])->withInput($request->only('email'));
    }
}
