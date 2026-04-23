<?php

namespace App\Services;

use App\Models\Paciente;
use Illuminate\Support\Facades\DB;

use Illuminate\Pagination\LengthAwarePaginator;

class PacienteService{

    public function list(array $filters = []): LengthAwarePaginator
    {
        $query = Paciente::query();

        if (! empty($filters['q'])) {
            $term = '%' . $filters['q'] . '%';
            $query->where(function ($subquery) use ($term) {
                $subquery->where('nombre', 'like', $term)
                    ->orWhere('apellido', 'like', $term)
                    ->orWhere('numero_identificacion', 'like', $term)
                    ->orWhere('email', 'like', $term);
            });
        }

        return $query->orderBy('apellido')->orderBy('nombre')
            ->paginate($this->resolvePerPage($filters));
    }
    public function create(array $data): Paciente
    {
        return DB::transaction(function () use ($data){
            return Paciente::create($data); //Insercion del nuevo paciente en la base de datos
        });
    }

    public function update(Paciente $paciente, array $data):Paciente{
        $paciente->update($data);
        return $paciente->refresh();

    }
    public function delete(Paciente $paciente):bool{
        return (bool) $paciente->delete();

    }
    private function resolvePerPage(array $filters): int
    {
        $perPage = (int)($filters['per_page'] ?? 15);
        if ($perPage <= 0 ) return 15;
        return min ($perPage, 100);
    }
}