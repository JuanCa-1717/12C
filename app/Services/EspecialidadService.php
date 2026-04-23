<?php
//Capeta de servicios que esta en el directorio app
namespace App\Services;

use App\Models\Especialidad;    
use Illuminate\Pagination\LengthAwarePaginator;

class EspecialidadService
{
    //Esta funcion retorna una lista paginada de especialidades, recibe el numero de pagina y el numero de elementos por pagina
    //En el index, cuando se necesita rellenar la tabla de especialidades, se llama a esta funcion para obtener la lista paginada de especialidades
    //Nos pagina automaticamente con paginate()
    public function list(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        //La consulta base para obtener las especialidades
        
        $query = Especialidad::query();

        // Aplicar filtros si se proporcionan
        if (!empty($filters['q'])) {
            $query->where('nombre', 'like', '%' . $filters['q'] . '%');
        }

        return $query
        ->orderBy('nombre')
        ->paginate($this->resolvePerPage($filters)); //Segun el numero de elementos por pagina, se paginara la lista de especialidades
    }

    public function delete(Especialidad $especialidad)
    {
        return (bool) $especialidad->delete();
    }

    public function resolvePerPage(array $filters): int
    {
        $perPage = (int)($filters['per_page'] ?? 15); //Si no se proporciona el numero de elementos por pagina, se paginara con 15 elementos por pagina
        
        if($perPage <= 0)return 15; //Si el numero de elementos por pagina es menor o igual a 0, se paginara con 15 elementos por pagina
    
        return min($perPage, 100); //Si el numero de elementos por pagina es mayor a 100, se paginara con 100 elementos por pagina
    }
}