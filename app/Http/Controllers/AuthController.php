<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (auth()->check()) {
            auth()->logout();
            session()->invalidate();
            session()->regenerateToken();

            return redirect()->route('login')
                ->with('alert_type', 'info')
                ->with('alert_message', 'Silakan login kembali');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required'
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            if (Auth::attempt($credentials)) {
                $request->session()->regenerate();
                session(['last_activity' => now()]);

                return response()->json([
                    'success' => true,
                    'redirect' => url('/dashboard'),
                    'message' => 'Login berhasil!'
                ]);
            }

            return response()->json([
                'success' => false,
                'errors' => ['username' => 'Username atau password salah.']
            ], 422);
        }

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            session(['last_activity' => now()]);
            $request->session()->flash('alert_type', 'success');
            $request->session()->flash('alert_message', 'Login berhasil!');
            $request->session()->flash('alert_title', 'Sukses');
            $request->session()->flash('alert_timer', 2000);
            $request->session()->flash('alert_showConfirmButton', false);

            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'username' => 'Username atau password salah.',
        ]);
    }

    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
{
    $validator = Validator::make($request->all(), [
        'username' => 'required|unique:data_users|max:255',
        'nama_lengkap' => 'required|max:255',
        'email' => 'required|email|unique:data_users|max:255',
        'password' => [
            'required',
            'confirmed',
            'min:8'
        ],
        'no_telp' => 'nullable|max:20'
    ], [
        'password.confirmed' => 'Konfirmasi password tidak cocok',
        'password.min' => 'Password minimal 8 karakter'
    ]);
    if ($request->wantsJson() || $request->ajax()) {
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        try {
            $user = User::create([
                'username' => $request->username,
                'nama_lengkap' => $request->nama_lengkap,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'no_telp' => $request->no_telp,
                'code_role' => '002',
            ]);

            Auth::login($user);
            session(['last_activity' => now()]);

            return response()->json([
                'success' => true,
                'message' => 'Registrasi berhasil!',
                'redirect' => url('/login')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Registrasi gagal. Silakan coba lagi.'
            ], 500);
        }
    }
    if ($validator->fails()) {
        return redirect()->back()
            ->withErrors($validator)
            ->withInput();
    }

    try {
        $user = User::create([
            'username' => $request->username,
            'nama_lengkap' => $request->nama_lengkap,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'no_telp' => $request->no_telp,
            'code_role' => '002',
        ]);

        Auth::login($user);
        session(['last_activity' => now()]);

        $request->session()->flash('alert_type', 'success');
        $request->session()->flash('alert_message', 'Registrasi berhasil!');
        $request->session()->flash('alert_title', 'Sukses');
        $request->session()->flash('alert_timer', 2000);
        $request->session()->flash('alert_showConfirmButton', false);

        return redirect('/login');

    } catch (\Exception $e) {
        return back()->with('alert_type', 'error')
                   ->with('alert_message', 'Registrasi gagal. Silakan coba lagi.');
    }
}

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        Session::forget('last_activity');

        return redirect('/')->with('alert_type', 'success')
                          ->with('alert_message', 'Anda telah logout.');
    }
}
