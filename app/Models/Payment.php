<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    // Llave primaria personalizada
    protected $primaryKey = 'id_payments';

    // Activar timestamps (created_at, updated_at)
    public $timestamps = true;

    // Campos rellenables
    protected $fillable = [
        'id_users',
        'id_plans',
        'payments_expires_at',
    ];

    /**
     * Relación: un pago pertenece a un usuario.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_users', 'id_users');
    }

    /**
     * Relación: un pago pertenece a un plan.
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class, 'id_plans', 'id_plans');
    }
}
