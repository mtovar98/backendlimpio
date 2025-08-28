<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Routine extends Model
{
    use SoftDeletes;
    // Llave primaria personalizada
    protected $primaryKey = 'id_routines';

    // No usamos timestamps en esta tabla
    public $timestamps = false;

    // Campos que se pueden asignar masivamente
    protected $fillable = [
        'routines_name',
        'routines_description',
        'routines_duration',
        'routines_frequency',
    ];

    /**
     * Relación: una rutina tiene muchas asignaciones a usuarios.
     */
    public function userRoutines(): HasMany
    {
        return $this->hasMany(UserRoutine::class, 'id_routines', 'id_routines');
    }
}
