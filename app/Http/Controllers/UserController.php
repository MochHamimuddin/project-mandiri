<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Check if current user is superadmin_kppmining
     */
    private function isSuperadminKppMining()
    {
        return Auth::check() && Auth::user()->username === 'superadmin_kppmining';
    }

    /**
     * Display a listing of users.
     */
    public function index()
    {
        if (!$this->isSuperadminKppMining()) {
            abort(403, 'Unauthorized access. Only superadmin_kppmining can access this page.');
        }

        $users = User::with('role')->whereNull('deleted_at')->get();
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        if (!$this->isSuperadminKppMining()) {
            abort(403, 'Unauthorized access. Only superadmin_kppmining can access this page.');
        }

        $roles = Role::all();
        return view('users.create', compact('roles'));
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request)
    {
        if (!$this->isSuperadminKppMining()) {
            abort(403, 'Unauthorized access. Only superadmin_kppmining can access this page.');
        }

        $validator = Validator::make($request->all(), [
            'username' => 'required|string|unique:data_users|max:50',
            'nama_lengkap' => 'required|string|max:100',
            'email' => 'required|email|unique:data_users|max:100',
            'password' => 'required|string|min:8|confirmed',
            'no_telp' => 'nullable|string|max:20',
            'code_role' => 'required|exists:data_role,code_role',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        User::create([
            'username' => $request->username,
            'nama_lengkap' => $request->nama_lengkap,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'no_telp' => $request->no_telp,
            'code_role' => $request->code_role,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('users.index')
            ->with('success', 'User created successfully');
    }

    /**
     * Show the form for editing a user.
     */
    public function edit(User $user)
    {
        if (!$this->isSuperadminKppMining()) {
            abort(403, 'Unauthorized access. Only superadmin_kppmining can access this page.');
        }

        $roles = Role::all();
        return view('users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, User $user)
    {
        if (!$this->isSuperadminKppMining()) {
            abort(403, 'Unauthorized access. Only superadmin_kppmining can access this page.');
        }

        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:50|unique:data_users,username,' . $user->id,
            'nama_lengkap' => 'required|string|max:100',
            'email' => 'required|email|max:100|unique:data_users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'no_telp' => 'nullable|string|max:20',
            'code_role' => 'required|exists:data_role,code_role',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $updateData = [
            'username' => $request->username,
            'nama_lengkap' => $request->nama_lengkap,
            'email' => $request->email,
            'no_telp' => $request->no_telp,
            'code_role' => $request->code_role,
            'updated_by' => Auth::id(),
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        return redirect()->route('users.index')
            ->with('success', 'User updated successfully');
    }

    public function destroy(User $user)
    {
        if (!$this->isSuperadminKppMining()) {
            abort(403, 'Unauthorized access. Only superadmin_kppmining can access this page.');
        }

        $user->update([
            'deleted_at' => now(),
            'deleted_by' => Auth::id()
        ]);

        return redirect()->route('users.index')
            ->with('success', 'User deleted successfully');
    }

}
