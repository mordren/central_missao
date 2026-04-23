<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'phone', 'email', 'password', 'role', 'city', 'neighborhood', 'referral_code', 'referred_by', 'points', 'date_of_birth', 'religion', 'education_level', 'higher_course', 'profession', 'how_known', 'first_spokesperson', 'pauta1', 'pauta2', 'pauta3', 'political_ambition', 'current_status', 'profile_completed_at', 'force_password_change'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'points' => 'integer',
            'force_password_change' => 'boolean',
        ];
    }

    public function activities()
    {
        return $this->belongsToMany(Activity::class)
            ->withPivot('status', 'confirmed_at', 'rsvp_confirmed', 'did_participate', 'points_awarded', 'penalty_applied')
            ->withTimestamps();
    }

    public function createdActivities()
    {
        return $this->hasMany(Activity::class, 'created_by');
    }

    public function isAdmin(): bool
    {
        return $this->role === 'administrador';
    }

    public function isCoordinator(): bool
    {
        return $this->role === 'coordenador';
    }

    public function canManageActivities(): bool
    {
        return in_array($this->role, ['coordenador', 'administrador']);
    }
}
