<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\User;
use App\Models\ActivitySubmission;
use App\Services\AttendanceService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ActivityController extends Controller
{
    public function index(Request $request, AttendanceService $attendanceService)
    {
        $attendanceService->processLazyPenalties();

        $query = Activity::withCount('rsvpParticipants')->orderByDesc('date_time');

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
            'location' => ['nullable', 'string', 'max:255'],
            'points' => ['required', 'integer', 'min:1'],
            // banner max in kilobytes (10240 KB = 10 MB)
            'banner' => ['nullable', 'image', 'max:10240'],
        ], [
            'title.required' => 'O título é obrigatório.',
            'type.required' => 'Selecione o tipo da atividade.',
            'date_time.required' => 'A data/hora é obrigatória.',
            'date_time.after_or_equal' => 'A data deve ser futura.',
            'points.required' => 'A pontuação é obrigatória.',
            'points.min' => 'A pontuação deve ser pelo menos 1.',
            'banner.max' => 'O banner deve ter no máximo 10MB.',
        ]);

        $bannerPath = null;
        if ($request->hasFile('banner')) {
            $file = $request->file('banner');
            $filename = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
            $destination = public_path('images/activities');
            if (!file_exists($destination)) {
                mkdir($destination, 0755, true);
            }
            $file->move($destination, $filename);
            // Save with public/ prefix for correct URL access
            $bannerPath = 'public/images/activities/' . $filename;
        }

        // calculate deadline automatically for presencial events (24 hours after start)
        $deadline = null;
        if (isset($validated['type']) && $validated['type'] === 'evento_presencial') {
            $deadline = Carbon::parse($validated['date_time'])->addHours(24);
        }

        $activity = Activity::create([
            ...$validated,
            'deadline' => $deadline,
            'created_by' => auth()->id(),
            'qr_code' => Str::uuid()->toString(),
            'banner' => $bannerPath,
        ]);

        app(AttendanceService::class)->processLazyPenalties();

        return redirect()->route('activities.show', $activity)->with('success', 'Atividade "' . $activity->title . '" criada com sucesso!');
    }

    public function show(Activity $activity)
    {
        $activity->load(['creator', 'confirmedParticipants', 'rsvpParticipants']);

        $rsvpParticipants      = $activity->rsvpParticipants;
        $confirmedParticipants = $activity->confirmedParticipants;
        $rsvpCount             = $rsvpParticipants->count();
        $userRsvp              = auth()->check() && $rsvpParticipants->contains('id', auth()->id());

        $userSubmission = null;
        if (auth()->check()) {
            $userSubmission = ActivitySubmission::where('activity_id', $activity->id)
                ->where('user_id', auth()->id())
                ->latest('submitted_at')
                ->first();
        }

        return view('activities.show', compact('activity', 'userSubmission', 'rsvpCount', 'userRsvp', 'rsvpParticipants', 'confirmedParticipants'));
    }

    public function sharePreview(Activity $activity)
    {
        // Return share page with meta tags (for WhatsApp preview)
        // Page contains JavaScript to redirect to activity
        return view('activities.share', compact('activity'));
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
            'location' => ['nullable', 'string', 'max:255'],
            'points' => ['required', 'integer', 'min:1'],
            'banner' => ['nullable', 'image', 'max:10240'],
        ], [
            'title.required' => 'O título é obrigatório.',
            'type.required' => 'Selecione o tipo da atividade.',
            'date_time.required' => 'A data/hora é obrigatória.',
            'points.required' => 'A pontuação é obrigatória.',
            'points.min' => 'A pontuação deve ser pelo menos 1.',
            'banner.max' => 'O banner deve ter no máximo 10MB.',
        ]);

        $updateData = $validated;

        // calculate deadline automatically for presencial events (24 hours after start)
        if (isset($validated['type']) && $validated['type'] === 'evento_presencial') {
            $updateData['deadline'] = Carbon::parse($validated['date_time'])->addHours(24);
        } else {
            $updateData['deadline'] = null;
        }

        if ($request->hasFile('banner')) {
            $file = $request->file('banner');
            $filename = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
            $destination = public_path('images/activities');
            if (!file_exists($destination)) {
                mkdir($destination, 0755, true);
            }
            $file->move($destination, $filename);
            $bannerPath = 'public/images/activities/' . $filename;

            // Remove old file if exists
            if ($activity->banner) {
                // Handle both formats: 'public/images/activities/file' or 'images/activities/file'
                $oldRelative = Str::startsWith($activity->banner, 'public/') 
                    ? substr($activity->banner, 7)  // Remove 'public/' prefix
                    : $activity->banner;
                $oldFullPath = public_path($oldRelative);
                if (file_exists($oldFullPath)) {
                    @unlink($oldFullPath);
                }
            }

            $updateData['banner'] = $bannerPath;
        }

        $activity->update($updateData);

        return redirect()->route('activities.show', $activity)->with('success', 'Atividade atualizada com sucesso!');
    }

    public function qrcode(Activity $activity)
    {
        return view('activities.qrcode', compact('activity'));
    }

    public function confirmPresence(Activity $activity, string $token, AttendanceService $attendanceService)
    {
        if ($token !== $activity->qr_code) {
            return redirect()->route('dashboard')->with('error', 'QR Code inválido.');
        }

        if ($activity->isExpired()) {
            return redirect()->route('activities.show', $activity)->with('error', 'Esta atividade já foi encerrada.');
        }

        $user   = auth()->user();
        $result = $attendanceService->recordParticipation($user, $activity);

        if ($result === 'already_participated') {
            return redirect()->route('activities.show', $activity)->with('error', 'Você já registrou presença nesta atividade.');
        }

        $pts = $result === 'double_points' ? $activity->points * 2 : $activity->points;
        $msg = $result === 'double_points'
            ? "Presença confirmada! Você ganhou +{$pts} pontos (bônus por inscrição prévia!)."
            : "Presença confirmada! Você ganhou +{$pts} pontos.";

        return redirect()->route('activities.show', $activity)->with('success', $msg);
    }

    public function confirmRsvp(Activity $activity, AttendanceService $attendanceService)
    {
        if ($activity->date_time->isPast()) {
            return redirect()->route('activities.show', $activity)->with('error', 'As inscrições para esta missão estão encerradas.');
        }

        $result = $attendanceService->confirmRsvp(auth()->user(), $activity);

        return match ($result) {
            'confirmed'         => redirect()->route('activities.show', $activity)->with('success', 'Inscrição confirmada! Apareça e ganhe pontos em dobro.'),
            'already_confirmed' => redirect()->route('activities.show', $activity)->with('error', 'Você já está inscrito nesta missão.'),
            'unauthorized'      => redirect()->route('activities.show', $activity)->with('error', 'Apenas membros participantes podem confirmar inscrição.'),
        };
    }
    public function cancelRsvp(Activity $activity, AttendanceService $attendanceService)
    {
        $result = $attendanceService->cancelRsvp(auth()->user(), $activity);

        return match ($result) {
            'cancelled'           => redirect()->route('activities.show', $activity)->with('success', 'Inscrição cancelada com sucesso.'),
            'not_confirmed'       => redirect()->route('activities.show', $activity)->with('error', 'Você não está inscrito nesta missão.'),
            'already_participated'=> redirect()->route('activities.show', $activity)->with('error', 'Não é possível cancelar após registrar presença.'),
            'unauthorized'        => redirect()->route('activities.show', $activity)->with('error', 'Ação não permitida.'),
        };
    }}
