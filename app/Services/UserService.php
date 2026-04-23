<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class UserService
{
    /**
     * Lista y filtra los usuarios del sistema.
     */
    public function list(array $filters = []): LengthAwarePaginator
    {
        $query = User::query()->with(['roles', 'permissions']);

        // Filtro de búsqueda por nombre o email
        if (! empty($filters['q'])) {
            $term = '%' . $filters['q'] . '%';
            $query->where(function (Builder $q) use ($term) {
                $q->where('name', 'like', $term)
                  ->orWhere('email', 'like', $term);
            });
        }

        // Filtro por nombre de rol
        if (! empty($filters['role'])) {
            $role = $filters['role'];
            $query->whereHas('roles', function (Builder $q) use ($role) {
                $q->where('name', $role);
            });
        }

        return $query->orderBy('name')
            ->paginate($this->resolvePerPage($filters));
    }

    /**
     * Resuelve la cantidad de elementos por página.
     */
    private function resolvePerPage(array $filters): int
    {
        $perPage = (int) ($filters['per_page'] ?? 15);
        if ($perPage <= 0) return 15;
        return min($perPage, 100);
    }

        public function create(array $data): User
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
        ]);
    }

        public function update(User $user, array $data): User
    {
        $payload = [
            'name'  => $data['name']  ?? $user->name,
            'email' => $data['email'] ?? $user->email,
        ];

        if (! empty($data['password'])) {
            $payload['password'] = $data['password'];
        }

        $user->update($payload);

        return $user->refresh()->load(['roles', 'permissions']);
    }

}