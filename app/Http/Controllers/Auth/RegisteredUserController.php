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
            'emri' => ['required', 'string', 'max:100'],
            'mbiemri' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:perdoruesit,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'rol_id' => ['required', 'integer', 'exists:rolet,rol_id'],
        ]);

        $user = User::create([
            'emri' => $request->emri,
            'mbiemri' => $request->mbiemri,
            'email' => $request->email,
            'fjalekalimi_hash' => Hash::make($request->password),
            'rol_id' => $request->rol_id,
            'aktiv' => true,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
