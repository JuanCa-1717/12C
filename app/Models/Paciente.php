<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory; //Nos va a permitir agregar seeds o datos de prueba
use Illuminate\Database\Eloquent\Relations\HasMany;

class Paciente extends Model
{
    use HasFactory;
    protected $table = 'pacientes';
    protected $fillable = [
        'nombre',
        'apellido',
        'fecha_nacimiento',
        'genero',
        'telefono',
        'email',
        'direccion',
        'numero_identificacion',
    ];

    //Convierte autamticamente un tipo de dato a un tipo de fecha manipulable para poder hacer calculos
    protected function casts(): array
    {
        return [
            'fecha_nacimiento' => 'date',
        ];
    }
    //Establecemos la relacion de uno a muchos entre pacientes y citas, un paciente puede tener muchas citas pero una cita solo puede tener un paciente
    public function citas(): HasMany
    {
        return $this->hasMany(Cita::class, 'paciente_id');
    }
}
