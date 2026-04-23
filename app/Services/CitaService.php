<?php

namespace App\Services;

use App\Models\Cita;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CitaService
{
    // CONSTANTES DE ESTADOS POSIBLES UNA CITA
    private const ESTADOS_VALIDOS = ['pendiente', 'confirmada', 'cancelada', 'completada'];

        public function list(array $filters = []): LengthAwarePaginator
    {
        $query = Cita::query()->with(['doctor', 'paciente']);

        // Filtros exactos existentes
        if (! empty($filters['doctor_id']))   $query->where('doctor_id', $filters['doctor_id']);
        if (! empty($filters['paciente_id'])) $query->where('paciente_id', $filters['paciente_id']);
        if (! empty($filters['estado']))      $query->where('estado', $filters['estado']);
        if (! empty($filters['fecha']))       $query->whereDate('fecha', $filters['fecha']);

        // 1. Filtro de búsqueda general (basado en tu imagen)
        // Busca en el nombre del doctor o del paciente si se envía un término 'q'
        if (! empty($filters['q'])) {
            $term = '%' . $filters['q'] . '%';
            $query->where(function ($q) use ($term) {
                $q->whereHas('doctor', fn($dq) => $dq->where('name', 'like', $term))
                ->orWhereHas('paciente', fn($pq) => $pq->where('name', 'like', $term));
            });
        }

        // 2. Filtro por Rol (basado en tu imagen)
        // Nota: Esto asume que el modelo Cita tiene una relación con roles 
        // o quieres filtrar citas de doctores con un rol específico.
        if (! empty($filters['role'])) {
            $role = $filters['role'];
            $query->whereHas('doctor.roles', fn($q) => $q->where('name', $role));
        }

        return $query->orderBy('fecha')
                    ->orderBy('hora')
                    ->paginate($this->resolvePerPage($filters));
    }
    public function create(array $data): Cita {
    $this->validateAvailability($data['doctor_id'], $data['fecha'], $data['hora']);

    return Cita::create($data)->load(['doctor', 'paciente']);
    }

    public function update(Cita $cita, array $data): Cita {

    $mergedData = array_merge(
        $cita->only(['doctor_id', 'fecha', 'hora']),
        $data
    );

    $this->validateAvailability(
        $mergedData['doctor_id'],
        $mergedData['fecha'],
        $mergedData['hora'],
        $cita->id
    );

    $cita->update($data);

        return $cita->load(['doctor', 'paciente']);
    }

        public function changeStatus(Cita $cita, string $estado): Cita 
    {
        if (! in_array($estado, self::ESTADOS_VALIDOS, true)) {
            throw ValidationException::withMessages([
                'estado' => ['Estado de cita no valida'],
            ]);
        }

        $cita->update(['estado' => $estado]);

        return $cita->refresh();
    }

    public function delete(Cita $cita): bool{
        return (bool) $cita->delete();
    }

    public function validateAvailability(
    int $doctorId,
    string $fecha,
    string $hora,
    ?int $ignoreCitaId = null
    ): void {
        $query = Cita::query()
            ->where('doctor_id', $doctorId)
            ->whereDate('fecha', $fecha)
            ->whereTime('hora', $hora)
            ->where('estado', '!=', 'cancelada');

        if ($ignoreCitaId !== null) {
            $query->where('id', '!=', $ignoreCitaId);
        }

        if ($query->exists()) {
            throw ValidationException::withMessages([
                'hora' => ['El doctor ya tiene una cita programada para esta fecha y hora.'],
            ]);
        }
    }

        private function resolvePerPage(array $filters): int
    {
        $perPage = (int) ($filters['per_page'] ?? 15);

        if ($perPage <= 0) return 15;

        return min($perPage, 100);
    }

}