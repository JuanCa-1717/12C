<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory; //Nos va a permitir agregar seeds o datos de prueba

class Especialidad extends Model
{
    use HasFactory;
    protected $table = 'especialidades';


    protected $fillable = ['nombre','descripcion'];

    //Funcion que permite la relacion de muchos a muchos entre la tabla especialidades y doctores, una especialidad puede tener muchos doctores y un doctor puede tener muchas especialidades
    public function doctores(): BelongsToMany
    {
        return $this->belongsToMany(Doctor::class, 'doctor_especialidad', 'especialidad_id', 'doctor_id');
    }
}
