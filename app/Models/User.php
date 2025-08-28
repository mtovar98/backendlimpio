<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    // Llave primaria personalizada
    protected $primaryKey = 'id_users';

    // Campos que se pueden asignar masivamente
    protected $fillable = [
        'first_name',
        'last_name',
        'id_number',
        'phone',
        'email',
        'birth_date',
        'id_roles',
        'users_active',
        'password',
    ];

    // Ocultar el password al serializar
    protected $hidden = [
        'password',
    ];

    protected $appends = ['plan_status', 'plan_expires_at', 'plan_is_active'];

    public function getPlanExpiresAtAttribute()
    {
        $p = $this->payments()
            ->orderByDesc('payments_expires_at')
            ->first();

        return $p?->payments_expires_at ? (string) $p->payments_expires_at : null;
    }

    public function getPlanStatusAttribute()
    {
        $expires = $this->plan_expires_at;
        if (!$expires) return 'vencido';
        return now()->toDateString() <= $expires ? 'vigente' : 'vencido';
    }

    public function getPlanIsActiveAttribute()
    {
        return $this->plan_status === 'vigente';
    }


    /**
     * Relación: un usuario pertenece a un rol.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'id_roles', 'id_roles');
    }

    /**
     * Relación: un usuario tiene muchas asistencias.
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class, 'id_users', 'id_users');
    }

    /**
     * Relación: un usuario tiene muchos pagos.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'id_users', 'id_users');
    }

    /**
     * Relación: un usuario tiene asignadas muchas rutinas.
     */
    public function userRoutines(): HasMany
    {
        return $this->hasMany(UserRoutine::class, 'id_users', 'id_users');
    }
}
