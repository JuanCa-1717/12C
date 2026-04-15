<?php

namespace App\Services;

use App\Models\Doctor;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class DoctorService
{
    /**
     * Lista los doctores aplicando filtros de búsqueda y estado.
     */
    public function list(array $filters = []): LengthAwarePaginator
    {
        // Iniciamos la consulta cargando la relación de especialidades
        $query = Doctor::query()->with('especialidades');

        // Filtro por estado (Completado según la lógica visual)
        if (!empty($filters['estado'])) {
            $query->where('estado', $filters['estado']);
        }

        // Filtro de búsqueda global (Nombre, Apellido, Colegiado, Email)
        if (!empty($filters['q'])) {
            $term = '%' . $filters['q'] . '%';

            // Usamos una subconsulta (clausura) para agrupar los OR y no romper otros filtros
            $query->where(function($subquery) use ($term) {
                $subquery->where('nombre', 'like', $term)
                    ->orWhere('apellido', 'like', $term)
                    ->orWhere('numero_colegiado', 'like', $term)
                    ->orWhere('email', 'like', $term);
            });
        }

        // Ordenamiento doble y paginación dinámica
        return $query->orderBy('apellido')
            ->orderBy('nombre')
            ->paginate($this->resolvePerPage($filters));
    }

    /**
     * Resuelve la cantidad de elementos por página.
     */
    public function resolvePerPage(array $filters): int
    {
        $perPage = (int) ($filters['per_page'] ?? 15);

        if ($perPage <= 0) return 15;

        return min($perPage, 100);
    }
        /**
     * Crea un nuevo registro de Doctor.
     * * @param array $data Datos del doctor.
     * @return Doctor
     */
    public function create(array $data): Doctor
    {
        // Se utiliza una transacción de DB para asegurar la integridad de los datos
        return DB::transaction(function () use ($data) {
            $doctor = Doctor::create($data);
            
            // Es necesario retornar la instancia creada dentro del closure
            return $doctor;
        });
    }

    /**
     * Actualiza un registro de Doctor existente.
     * * @param Doctor $doctor Instancia del modelo a actualizar.
     * @param array $data Nuevos datos.
     * @return Doctor
     */
    public function update(Doctor $doctor, array $data): Doctor
    {
        return DB::transaction(function () use ($doctor, $data) {
            // Actualiza el modelo con los datos proporcionados
            $doctor->update($data);

            // Retorna el objeto doctor actualizado
            return $doctor;
        });
    }
}