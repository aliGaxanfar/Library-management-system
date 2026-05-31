<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->passwordHash)) {
            return back()
                ->withErrors(['email' => 'Invalid email or password.'])
                ->withInput();
        }

        if (!$user->isActive) {
            return back()->withErrors(['email' => 'Your account has been deactivated.']);
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        if ($user->isAdmin())     return redirect()->route('admin.dashboard');
        if ($user->isLibrarian()) return redirect()->route('librarian.dashboard');
        return redirect()->route('member.dashboard');
    }

    public function showRegister()
    {
        return view('auth.register');
    }
public function register(Request $request)
{
   
    $data = $request->validate([
        'fullName'        => 'required|string|max:100',
        'email'           => 'required|email|unique:users,email',
        'password'        => 'required|min:6|confirmed',
        'membershipType'  => 'required|in:STANDARD,PREMIUM',
    ]);

    $user = User::create([
        'fullName'     => $data['fullName'],
        'email'        => $data['email'],
        'passwordHash' => Hash::make($data['password']),
        'role'         => 'MEMBER',
        'isActive'     => true,
    ]);

    $memberId = 'MBR-' . str_pad($user->userId, 5, '0', STR_PAD_LEFT);

    Member::create([
        'userId'         => $user->userId,
        'memberId'       => $memberId,
        'membershipType' => $data['membershipType'],
        'joinDate'       => Carbon::today(),
        'expiryDate'     => Carbon::today()->addYear(),
        'totalFinesDue'  => 0,
    ]);

    Auth::login($user);
$request->session()->regenerate();

return redirect()->route('member.dashboard')
    ->with('success', 'Welcome ' . $user->fullName . '! Your account has been created.');

  
    
}

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}