<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Closure;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
   public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'phone_number' => ['nullable', 'string', 'regex:/^(08)[0-9]{8,11}$/'],
            'password' => ['required', 'confirmed', Rules\Password::min(8)->mixedCase()->numbers()],
            'is_organizer' => ['nullable', 'boolean'],
            'g-recaptcha-response' => [
                'required',
                function (string $attribute, mixed $value, Closure $fail) use ($request) {
                    $captcha = new \Anhskohbo\NoCaptcha\NoCaptcha(
                        env('NOCAPTCHA_SECRET'), // <-- Baca langsung dari .env
                        env('NOCAPTCHA_SITEKEY')  // <-- Baca langsung dari .env
                    );
                    if (!$captcha->verifyResponse($value, $request->ip())) {
                        $fail('Verifikasi reCAPTCHA gagal.');
                    }
                },
            ],
        ]);

        $role = $request->boolean('is_organizer') ? 'organizer' : 'user';

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phone_number, // <-- Simpan nomor telepon
            'password' => Hash::make($request->password),
            'role' => $role,
        ]);

        event(new Registered($user));

        Auth::login($user);

        // 4. Arahkan berdasarkan peran
        if ($user->role === 'organizer') {
            return redirect(route('events.index'));
        }

        return redirect(config('auth.redirects.home', '/'));;
    }
}
