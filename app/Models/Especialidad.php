<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Especialidad extends Model
{
    
    use HasFactory;

    protected $table = 'especialidades';

    protected $fillable = ['nombre', 'descripcion'];

    public function doctores(): BelongsToMany
    {
        return $this->belongsToMany(Doctor::class, 'doctor_especialidad', 'especialidad_id', 'doctor_id');
    }

}
