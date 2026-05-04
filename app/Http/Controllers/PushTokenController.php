<?php

namespace App\Http\Controllers;

use App\Models\PushToken;
use App\Services\FcmService;
use Illuminate\Http\Request;

class PushTokenController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'token' => ['required', 'string', 'max:512'],
        ]);

        $userId = $request->user()->id;

        // Remove todos os tokens antigos deste utilizador e guarda só o mais recente
        PushToken::where('user_id', $userId)->delete();
        PushToken::create([
            'user_id' => $userId,
            'token'   => $request->token,
        ]);

        return response()->json(['ok' => true]);
    }

    public function check(Request $request)
    {
        $request->validate([
            'token' => ['required', 'string', 'max:512'],
        ]);

        $exists = PushToken::where('token', $request->token)
            ->where('user_id', $request->user()->id)
            ->exists();

        return response()->json(['registered' => $exists]);
    }

    public function sendManual(Request $request)
    {
        $request->validate([
            'title' => ['required', 'string', 'max:100'],
            'body'  => ['required', 'string', 'max:300'],
        ]);

        $sent = app(FcmService::class)->sendToAll($request->title, $request->body);

        return back()->with('success', "Notificação enviada para {$sent} dispositivo(s).");
    }
}
