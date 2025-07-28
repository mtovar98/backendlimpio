<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserRoutine extends Model
{
    // Tabla personalizada
    protected $table = 'user_routines';

    // Llave primaria personalizada
    protected $primaryKey = 'id_user_routines';

    // Activar timestamps (created_at, updated_at)
    public $timestamps = true;

    // Campos rellenables
    protected $fillable = [
        'id_users',
        'id_routines',
    ];

    /**
     * Relación: una asignación de rutina pertenece a un usuario.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_users', 'id_users');
    }

    /**
     * Relación: una asignación de rutina pertenece a una rutina.
     */
    public function routine(): BelongsTo
    {
        return $this->belongsTo(Routine::class, 'id_routines', 'id_routines');
    }
}
