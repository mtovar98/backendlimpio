<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    // Llave primaria personalizada
    protected $primaryKey = 'id_attendances';

    // Activar timestamps (created_at, updated_at)
    public $timestamps = true;

    // Campos rellenables
    protected $fillable = [
        'id_users',
    ];

    /**
     * Relación: una asistencia pertenece a un usuario.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_users', 'id_users');
    }
}
