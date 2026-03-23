<?php

namespace App\Http\Controllers;

use App\Models\User;

class RankingController extends Controller
{
    public function index()
    {
        $users = User::orderByDesc('points')
            ->select('id', 'name', 'city', 'points')
            ->paginate(50);

        return view('ranking', compact('users'));
    }
}
