<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    // Llave primaria personalizada
    protected $primaryKey = 'id_plans';

    // No usamos timestamps en esta tabla
    public $timestamps = false;

    // Campos que se pueden asignar masivamente
    protected $fillable = [
        'plans_name',
        'plans_price',
        'plans_duration_days',
    ];

    /**
     * Relación: un plan tiene muchos pagos.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'id_plans', 'id_plans');
    }
}
