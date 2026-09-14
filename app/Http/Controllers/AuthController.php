<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        $users = User::all();
        return view('auth.login', compact('users'));
    }

    public function loginAs(Request $request, $id)
    {
        $user = User::findOrFail($id);
        Auth::login($user);
        return redirect()->route('repository.index')->with('success', "Logged in as {$user->name} ({$user->role} - {$user->department})");
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }
}
