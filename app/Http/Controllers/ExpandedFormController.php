<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\ExpandedFormResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExpandedFormController extends Controller
{
    public function show(Activity $activity)
    {
        // Exibe o formulário expandido
        return view('activities.expanded-form', compact('activity'));
    }

    public function submit(Request $request, Activity $activity)
    {
        $user = $request->user();

        // Campos extras incluindo religião e graduação
        $fields = [
            'nome_completo' => 'required|string|max:255',
            'idade' => 'required|integer|min:10|max:120',
            'cidade' => 'required|string|max:255',
            'telefone' => 'required|string|max:20',
            'religiao' => 'required|string|max:30',
            'religiao_outro' => 'nullable|string|max:50',
            'graduacao' => 'required|string|max:30',
            'interesses' => 'nullable|string|max:255',
        ];

        $validated = $request->validate($fields);

        // Se religião for "Outro", salva o campo extra
        if ($validated['religiao'] === 'Outro' && !empty($validated['religiao_outro'])) {
            $validated['religiao'] = 'Outro: ' . $validated['religiao_outro'];
        }
        unset($validated['religiao_outro']);

        DB::transaction(function () use ($user, $activity, $validated) {
            ExpandedFormResponse::create([
                'user_id' => $user->id,
                'activity_id' => $activity->id,
                'responses' => $validated,
            ]);
            // Marca como participante confirmado e dá 25 pontos
            $activity->participants()->attach($user->id, [
                'status' => 'confirmado',
                'confirmed_at' => now(),
            ]);
            $user->increment('points', 25);
        });

        return redirect()->route('activities.show', $activity)->with('success', 'Formulário enviado! Você ganhou +25 pontos.');
    }
}
