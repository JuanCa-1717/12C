<?php

namespace App\Services;

use App\Models\Especialidad;
use Illuminate\Pagination\LengthAwarePaginator;

class EspecialidadService
{
    /**
     * Lista y filtra las especialidades.
     * * @param array $filters Contiene los criterios de búsqueda.
     * @return LengthAwarePaginator
     */
    public function list(array $filters = []): LengthAwarePaginator
    {
        // Inicializa el constructor de consultas de Eloquent
        $query = Especialidad::query();

        // Filtro de búsqueda por nombre
        // Si el parámetro 'q' tiene un valor, se aplica una cláusula WHERE con LIKE
        if (!empty($filters['q'])) {
            $query->where('nombre', 'like', '%' . $filters['q'] . '%');
        }

        // Retorna los resultados paginados
        // Se aplica un orden ascendente por el campo 'nombre'
        return $query
            ->orderBy('nombre')
            ->paginate($this->resolverPerPage($filters));
    }

    /**
     * Método auxiliar para determinar la cantidad de elementos por página.
     * Basado en la lógica del código, este método debe existir en la clase.
     */
    public function resolverPerPage(array $filters)
    {
        // Retorna el valor de 'per_page' si existe, de lo contrario un valor por defecto (ej. 15)
        return (bool)$especialidad->delete();
    }

    public function resolvePerPage(array $filters): int
    {
        // Obtiene el valor de 'per_page' del arreglo de filtros, 
        // lo convierte a entero y usa 15 como valor por defecto si no existe.
        $perPage = (int) ($filters['per_page'] ?? 15);

        // Si el valor resultante es menor o igual a 0, retorna 15 por defecto.
        if ($perPage <= 0) return 15;

        // Retorna el valor, pero lo limita a un máximo de 100 para evitar sobrecarga.
        return min($perPage, 100);
    }

}