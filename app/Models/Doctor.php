<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Doctor extends Model
{
    
    use HasFactory;

    protected $table = 'doctores';

    protected $fillable = [
        'nombre',
        'apellido',
        'numero_colegiado',
        'telefono',
        'email',
        'estado',
    ];
    public function especialidades(): BelongsToMany{

    return $this->belongsToMany(
        Especialidad::class, 'doctor_especialidad', 'doctor_id', 'especialidad_id'
    );
    }

    public function citas(): HasMany
    {
        return $this->hasMany(Cita::class, 'doctor_id');
    }

}
