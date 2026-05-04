<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class RankingController extends Controller
{
    public function index()
    {
        $users = User::orderByDesc('points')
            ->select('id', 'name', 'nickname', 'city', 'points', 'avatar_path', 'avatar_url')
            ->paginate(50);

        return view('ranking', compact('users'));
    }

    public function reset()
    {
        // Reset all points to 0
        DB::table('users')->update(['points' => 0]);

        return redirect()->route('ranking')->with('success', 'Ranking zerado com sucesso. Todos os usuários voltaram a 0 pontos.');
    }
}
