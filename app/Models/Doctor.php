<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; //Nos va a permitir agregar seeds o datos de prueba
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;
use App\Models\Especialidad;

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

    public function especialidades(): BelongsToMany
    {
        return $this->belongsToMany(Especialidad::class, 'doctor_especialidades', 'doctor_id', 'especialidad_id');
    }
    public function citas(): HasMany
    {
        return $this->hasMany(Cita::class, 'doctor_id');
    }
}
