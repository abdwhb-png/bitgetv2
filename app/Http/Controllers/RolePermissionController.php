<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Enums\RolesEnum;
use Illuminate\Http\Request;
use App\Enums\PermissionsEnum;
use Spatie\Permission\Models\Role;
use App\Concerns\ValidationRulesTrait;
use Spatie\Permission\Models\Permission;

class RolePermissionController extends Controller
{
    use ValidationRulesTrait;


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $root = RolesEnum::ROOT->value;

        return [
            'roles' => Role::when(!request()->user()->hasRole($root), function ($query) use ($root) {
                $query->where('name', '!=', $root);
            })->get(),
            'permissions' => Permission::orderBy('name')->when(!request()->user()->hasRole($root), function ($query) {
                $query->where('name', '!=', PermissionsEnum::EDITPERMISSIONS->value);
            })->get(),
        ];
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(User $user, Request $request)
    {
        $validated = $request->validate($this->rolesPermsRules('update', $request->section));

        if ($validated['section'] == 'roles') {
            $user->syncRoles($validated['roles']);
        }

        if ($validated['section'] == 'permissions') {
            $user->syncPermissions(array_values($validated['permissions']));
        }

        return back(303)->with('status', 'The ' . $validated['section'] . ' have been updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}