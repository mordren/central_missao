<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        $query = Activity::orderByDesc('date_time');

        if ($request->filter === 'abertas') {
            $query->where('deadline', '>=', now());
        } elseif ($request->filter === 'encerradas') {
            $query->where('deadline', '<', now());
        }

        $activities = $query->paginate(20);

        return view('activities.index', compact('activities'));
    }

    public function create()
    {
        return view('activities.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'type' => ['required', 'in:evento_presencial,denuncia,tarefa_manual,convite'],
            'date_time' => ['required', 'date', 'after_or_equal:now'],
            'deadline' => ['required', 'date', 'after_or_equal:date_time'],
            'location' => ['nullable', 'string', 'max:255'],
            'points' => ['required', 'integer', 'min:1'],
        ], [
            'title.required' => 'O título é obrigatório.',
            'type.required' => 'Selecione o tipo da atividade.',
            'date_time.required' => 'A data/hora é obrigatória.',
            'date_time.after_or_equal' => 'A data deve ser futura.',
            'deadline.required' => 'O prazo é obrigatório.',
            'deadline.after_or_equal' => 'O prazo deve ser igual ou posterior à data da atividade.',
            'points.required' => 'A pontuação é obrigatória.',
            'points.min' => 'A pontuação deve ser pelo menos 1.',
        ]);

        $activity = Activity::create([
            ...$validated,
            'created_by' => auth()->id(),
            'qr_code' => Str::uuid()->toString(),
        ]);

        return redirect()->route('activities.show', $activity)->with('success', 'Atividade "' . $activity->title . '" criada com sucesso!');
    }

    public function show(Activity $activity)
    {
        $activity->load(['creator', 'confirmedParticipants']);
        return view('activities.show', compact('activity'));
    }

    public function edit(Activity $activity)
    {
        return view('activities.edit', compact('activity'));
    }

    public function update(Request $request, Activity $activity)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'type' => ['required', 'in:evento_presencial,denuncia,tarefa_manual,convite'],
            'date_time' => ['required', 'date'],
            'deadline' => ['required', 'date', 'after_or_equal:date_time'],
            'location' => ['nullable', 'string', 'max:255'],
            'points' => ['required', 'integer', 'min:1'],
        ], [
            'title.required' => 'O título é obrigatório.',
            'type.required' => 'Selecione o tipo da atividade.',
            'date_time.required' => 'A data/hora é obrigatória.',
            'deadline.required' => 'O prazo é obrigatório.',
            'deadline.after_or_equal' => 'O prazo deve ser igual ou posterior à data da atividade.',
            'points.required' => 'A pontuação é obrigatória.',
            'points.min' => 'A pontuação deve ser pelo menos 1.',
        ]);

        $activity->update($validated);

        return redirect()->route('activities.show', $activity)->with('success', 'Atividade atualizada com sucesso!');
    }

    public function qrcode(Activity $activity)
    {
        return view('activities.qrcode', compact('activity'));
    }

    public function confirmPresence(Activity $activity, string $token)
    {
        if ($token !== $activity->qr_code) {
            return redirect()->route('dashboard')->with('error', 'QR Code inválido.');
        }

        if ($activity->isExpired()) {
            return redirect()->route('activities.show', $activity)->with('error', 'Esta atividade já foi encerrada.');
        }

        $user = auth()->user();

        // Verifica se já confirmou
        $existing = $activity->participants()->where('user_id', $user->id)->first();
        if ($existing) {
            return redirect()->route('activities.show', $activity)->with('error', 'Você já confirmou presença nesta atividade.');
        }

        // Confirma presença
        $activity->participants()->attach($user->id, [
            'status' => 'confirmado',
            'confirmed_at' => now(),
        ]);

        // Adiciona pontos
        $user->increment('points', $activity->points);

        return redirect()->route('activities.show', $activity)->with('success', "Presença confirmada! Você ganhou +{$activity->points} pontos.");
    }
}
