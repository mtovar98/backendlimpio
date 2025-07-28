<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    // Llave primaria personalizada
    protected $primaryKey = 'id_roles';

    // No estamos usando created_at/updated_at en esta tabla
    public $timestamps = false;

    // Campos rellenables
    protected $fillable = [
        'roles_name',
    ];

    /**
     * Un rol tiene muchos usuarios
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'id_roles', 'id_roles');
    }
}

