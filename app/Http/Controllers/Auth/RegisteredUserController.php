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
use Illuminate\Validation\ValidationException;
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
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->merge(['role' => $request->input('role', 'parent')]);

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => [
                'required',
                'confirmed',
                Rules\Password::defaults(),
                // Ensure password is unique across all users
                function ($attribute, $value, $fail) {
                    $allUsers = User::all();
                    foreach ($allUsers as $existingUser) {
                        if (Hash::check($value, $existingUser->password)) {
                            $fail('This password is already in use by another user. Please choose a different password.');
                            break;
                        }
                    }
                },
            ],
            'role' => ['required', 'in:nurse,doctor,parent'],
            'phone' => ['nullable', 'string', 'regex:/^(07|06)[0-9]{8}$/'],
            'facility_name' => ['nullable', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
        ];

        // Special validation for nurse license - must contain special characters (not just plain text)
        if ($request->role === 'nurse') {
            $rules['license_number'] = [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) {
                    // Must contain at least one special character (not just letters/numbers)
                    if (!preg_match('/[^a-zA-Z0-9\s]/', $value)) {
                        $fail('License number must contain special characters. Examples: LICENSE-2024#001, RN/12345, NMC*TZ*12345');
                    }
                    // Must contain at least one number
                    if (!preg_match('/[0-9]/', $value)) {
                        $fail('License number must contain at least one number.');
                    }
                    // Must contain at least one letter
                    if (!preg_match('/[a-zA-Z]/', $value)) {
                        $fail('License number must contain at least one letter.');
                    }
                },
            ];
        } else {
            $rules['license_number'] = ['nullable', 'string', 'max:255'];
        }

        $request->validate($rules);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'phone' => $request->phone,
            'facility_name' => $request->facility_name,
            'license_number' => $request->license_number,
            'location' => $request->location,
            'is_active' => true,
        ]);

        event(new Registered($user));

        Auth::login($user);

        // Redirect to the generic dashboard route so the fallback dashboard can resolve the role-specific destination.
        return redirect()->intended(route('dashboard'));
    }

    /**
     * Get the dashboard route for a given role.
     */
    private function getDashboardRoute(string $role): string
    {
        return match ($role) {
            'nurse' => route('nurse.dashboard'),
            'doctor' => route('doctor.dashboard'),
            'parent' => route('parent.dashboard'),
            default => route('dashboard'),
        };
    }
}