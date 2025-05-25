<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'required|string|email|max:255|unique:users',
            'password'    => 'required|string|min:6|confirmed',
            'user_image'  => 'nullable|string',
        ]);

        $user = User::create([
            'name'           => $request->name,
            'email'          => $request->email,
            'password'       => Hash::make($request->password),
            'token'          => Str::random(60),
            'connection_id'  => 0,
            'user_image'     => $request->user_image ?? '',
            'user_status'    => 'Online',
        ]);

        return view('index');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'email' => 'Credenciais inválidas.',
            ])->withInput();
        }

        Auth::login($user);

        $user->update([
            'user_status' => 'Online',
            'token' => Str::random(60),
        ]);

        return view('/index');
    }
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'Você saiu com sucesso.');
    }

    public function profile(){
        if(Auth::check()){
            $profile = User::find(Auth::id());
            return view('profile',compact('profile'));
        }
        return redirect('login');
    }




}
