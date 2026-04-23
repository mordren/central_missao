<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'type',
        'date_time',
        'deadline',
        'location',
        'points',
        'banner',
        'qr_code',
        'created_by',
    ];

    protected $casts = [
        'date_time' => 'datetime',
        'deadline' => 'datetime',
        'points' => 'integer',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function participants()
    {
        return $this->belongsToMany(User::class)
            ->withPivot('status', 'confirmed_at', 'rsvp_confirmed', 'did_participate', 'points_awarded', 'penalty_applied')
            ->withTimestamps();
    }

    public function confirmedParticipants()
    {
        return $this->participants()->wherePivot('status', 'confirmado');
    }

    public function rsvpParticipants()
    {
        return $this->participants()->wherePivot('rsvp_confirmed', true);
    }

    public function isExpired(): bool
    {
        return $this->deadline?->isPast() ?? false;
    }

    public function typeLabel(): string
    {
        return match ($this->type) {
            'evento_presencial' => 'Evento Presencial',
            'denuncia' => 'Denúncia',
            'tarefa_manual' => 'Tarefa Manual',
            'convite' => 'Convite/Indicação',
            default => $this->type,
        };
    }
}
