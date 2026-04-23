<?php

namespace App\Services;

use App\Models\Doctor;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;


class DoctorService
{
    public function list(array $filters = []): LengthAwarePaginator
    {
        $query = Doctor::query()->with('especialidades');

        if (!empty($filters['estado'])) {
            $query->where('estado', $filters['estado']);
        }

        // Validacion para el filtro de busqueda por nombre
        if (!empty($filters['q'])) {
            $term = '%' . $filters['q'] . '%';

            $query->where(function ($subquery) use ($term) {
                $subquery->where('nombre', 'like', $term)
                    ->orWhere('apellido', 'like', $term)
                    ->orWhere('numero_cologiado', 'like', $term)
                    ->orWhere('email', 'like', $term);
            });
        }

        return $query->orderBy('apellido')->orderBy('nombre')
            ->paginate($this->resolvePerPage($filters));
    }

    public function resolvePerPage(array $filters): int
    {
        $perPage = (int) ($filters['per_page'] ?? 15);

        if ($perPage <= 0) {
            return 15;
        }

        return min($perPage, 100);
    }

    //Implementacion del metodo create para crear un nuevo doctor, recibe un array con los datos del doctor a crear, retorna el doctor creado
    public function create(array $data): Doctor
    {
        return DB::transaction(function () use ($data) {
            return Doctor::create($data); //Insercion del nuevo doctor en la base de datos
        });
    }

    public function update(Doctor $doctor, array $data): Doctor
    {
        return DB::transaction(function () use ($doctor, $data) {
            $doctor->update($data); //Actualizacion del doctor en la base de datos
            return $doctor->refresh()->load('especialidades'); //Retorna el doctor actualizado
        });
    }

    public function getEspecialidades(Doctor $doctor): Collection
    {
        return $doctor->especialidades()->orderby('nombre')->get();//A este doctor le vamos a dar una especialidad
    }

    //Metodo para agregar especialidades pero sin eliminar las que ya estan definidas
    public function addEspecialidades(Doctor $doctor, int $especialidadId): Doctor
    {
        return DB::transaction(function () use ($doctor, $especialidadId) {
            $doctor->especialidades()->syncWithoutDetaching([$especialidadId]); //Agrega la especialidad al doctor
            return $doctor->refresh()->load('especialidades'); //Retorna el doctor actualizado
        });
    }

    public function removeEspecialidad(Doctor $doctor, int $especialidadId): Doctor
    {
        return DB::transaction(function () use ($doctor, $especialidadId) {
            $doctor->especialidades()->detach($especialidadId); //Elimina la especialidad del doctor
            return $doctor->refresh()->load('especialidades'); //Retorna el doctor actualizado
        });
    }
    public function replaceEspecialidades(Doctor $doctor, int ...$especialidadIds): Doctor
    {
        return DB::transaction(function () use ($doctor, $especialidadIds) {
            $doctor->especialidades()->sync($especialidadIds); //Elimina las especialidades del doctor
            return $doctor->refresh()->load('especialidades'); //Retorna el doctor actualizado
        });
    }
    public function changeStatus(Doctor $doctor, string $estado): Doctor
    {
        return DB::transaction(function () use ($doctor, $estado) {
            $doctor->update(['estado' => $estado]); //Actualizacion del estado del doctor en la base de datos
            return $doctor->refresh(); //Retorna el doctor actualizado
        });
    }

    public function delete(Doctor $doctor): bool
    {
        return (bool) $doctor->delete(); //Elimina el doctor de la base de datos de forma logica, retorna true si se elimino correctamente, false en caso contrari
    }
}