<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $query = Member::with('user');

        if ($search = $request->input('search')) {
            $query->whereHas('user', fn($q) =>
                $q->where('fullName', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%")
            );
        }

        $members = $query->paginate(20)->withQueryString();
        return view('admin.members', compact('members'));
    }

    public function show(Member $member)
    {
        $member->load(['user', 'loans.book', 'fines', 'reservations.book']);
        return view('admin.member_show', compact('member'));
    }

    public function toggleActive(Member $member)
    {
        $member->user->update(['isActive' => !$member->user->isActive]);
        $status = $member->user->isActive ? 'activated' : 'deactivated';
        return back()->with('success', "Member account {$status}.");
    }
}
