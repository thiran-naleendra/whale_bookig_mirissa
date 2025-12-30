<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminAuthController extends Controller
{
    public function showLogin()
    {
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        $admin = Admin::where('email', $data['email'])->where('status', 1)->first();
        if (!$admin || !Hash::check($data['password'], $admin->password)) {
            return back()->with('error', 'Invalid login.');
        }

        session(['admin_id' => $admin->id, 'admin_name' => $admin->name, 'admin_role' => $admin->role]);
        $admin->last_login_at = now();
        $admin->save();

        return redirect()->route('admin.bookings');
    }

    public function logout()
    {
        session()->forget(['admin_id','admin_name','admin_role']);
        return redirect()->route('admin.login')->with('success', 'Logged out.');
    }
}
