<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionService
{
	public function listRoles(array $filters = []): LengthAwarePaginator
	{
		$query = Role::query()->with('permissions');

		if (! empty($filters['q'])) {
			$query->where('name', 'like', '%' . $filters['q'] . '%');
		}

		if (! empty($filters['guard_name'])) {
			$query->where('guard_name', $filters['guard_name']);
		}

		return $query->orderBy('name')->paginate($this->resolvePerPage($filters));
	}

	public function createRole(array $data): Role
	{
		$role = Role::create([
			'name' => $data['name'],
			'guard_name' => $data['guard_name'] ?? 'web',
		]);

		$this->forgetPermissionCache();

		return $role->load('permissions');
	}

	public function updateRole(Role $role, array $data): Role
	{
		$role->update([
			'name' => $data['name'] ?? $role->name,
			'guard_name' => $data['guard_name'] ?? $role->guard_name,
		]);

		$this->forgetPermissionCache();

		return $role->refresh()->load('permissions');
	}

	public function deleteRole(Role $role): bool
	{
		$deleted = (bool) $role->delete();
		$this->forgetPermissionCache();
		return $deleted;
	}

	public function getRolePermissions(Role $role): Collection
	{
		return $role->permissions()->orderBy('name')->get();
	}

	public function addPermissionToRole(Role $role, string $permissionName): Role
	{
		$role->givePermissionTo($permissionName);
		$this->forgetPermissionCache();

		return $role->refresh()->load('permissions');
	}

	public function removePermissionFromRole(Role $role, string $permissionName): Role
	{
		$role->revokePermissionTo($permissionName);
		$this->forgetPermissionCache();

		return $role->refresh()->load('permissions');
	}

	public function replaceRolePermissions(Role $role, string ...$permissionNames): Role
	{
		$role->syncPermissions($permissionNames);
		$this->forgetPermissionCache();

		return $role->refresh()->load('permissions');
	}

	private function resolvePerPage(array $filters): int
	{
		$perPage = (int) ($filters['per_page'] ?? 15);

		if ($perPage <= 0) {
			return 15;
		}

		return min($perPage, 100);
	}

	private function forgetPermissionCache(): void
	{
		app(PermissionRegistrar::class)->forgetCachedPermissions();
	}
}
