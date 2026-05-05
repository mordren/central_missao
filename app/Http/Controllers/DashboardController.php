<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\ActivityPhoto;
use App\Models\ActivitySubmission;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $now = Carbon::now();

        $month = $request->get('month', $now->month);
        $year = $request->get('year', $now->year);

        $currentDate = Carbon::createFromDate($year, $month, 1);

        // Missões abertas (não expiradas)
        $openActivities = Activity::where('deadline', '>=', $now)
            ->orderBy('date_time')
            ->take(10)
            ->get();

        // Atividades do mês para o calendário
        $monthActivities = Activity::whereMonth('date_time', $month)
            ->whereYear('date_time', $year)
            ->orderBy('date_time')
            ->get();

        // Histórico do usuário (atividades confirmadas)
        $history = $user->activities()
            ->wherePivot('status', 'confirmado')
            ->orderByPivot('confirmed_at', 'desc')
            ->take(5)
            ->get();

        // Agrupar atividades por dia para o calendário
        $activitiesByDay = $monthActivities->groupBy(function ($activity) {
            return $activity->date_time->day;
        });

        // Dados para o JS do calendário (sem closures)
        $activitiesByDayJs = [];
        foreach ($activitiesByDay as $day => $acts) {
            $activitiesByDayJs[$day] = $acts->map(fn ($a) => [
                'id' => $a->id,
                'title' => $a->title,
                'time' => $a->date_time->format('H\\hi'),
                'type' => $a->typeLabel(),
                'points' => $a->points,
                'banner' => $a->banner ? url($a->banner) : null,
            ])->values()->all();
        }

        // Pending items for coord/admin
        $pendingSubmissions = collect();
        $pendingPhotos = collect();
        if ($user->canManageActivities()) {
            $pendingSubmissions = ActivitySubmission::where('status', 'pending')
                ->with(['user', 'activity'])
                ->orderByDesc('submitted_at')
                ->get();
            $pendingPhotos = ActivityPhoto::where('status', 'pending')
                ->with(['uploader', 'activity'])
                ->orderByDesc('created_at')
                ->get();
        }

        // Ranking position
        $userRank = User::where('points', '>', $user->points)->count() + 1;

        return view('dashboard', compact(
            'user',
            'openActivities',
            'monthActivities',
            'history',
            'activitiesByDay',
            'activitiesByDayJs',
            'currentDate',
            'month',
            'year',
            'pendingSubmissions',
            'pendingPhotos',
            'userRank'
        ));
    }
}
