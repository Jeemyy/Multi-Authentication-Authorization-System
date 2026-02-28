<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Http\Requests\RoleRequest;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = Role::all();
        return view('back.roles.index', get_defined_vars());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $groups = Permission::where('guard_name', 'admin')->get();
        return view('back.roles.create', get_defined_vars());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RoleRequest $request)
    {
        $data = $request->validated();
        $role = Role::create([
            'name'        => $data['name'],
            'guard_name'  => 'admin',
        ]);
        if(isset($data['permissionArray'])){
            foreach($data['permissionArray'] as $item => $value){
                $role->givePermissionTo($item);
            }
        }
        return to_route('back.roles.index')->with('success', "Role Created Successfully");
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role)
    {
        $groups = Permission::all();
        return view('back.roles.show', get_defined_vars());
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        $groups = Permission::where('guard_name', 'admin')->get();
        return view('back.roles.edit', get_defined_vars());
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RoleRequest $request, Role $role)
    {
        $data = $request->validated();
        $role->update([
            'name' => $data['name'],
        ]);
        $role->syncPermissions();
        if(isset($data['permissionArray']) && !empty($data['permissionArray'])){
            foreach($data['permissionArray'] as $item => $value){
                $role->givePermissionTo($item);
            }
        }
        return to_route('back.roles.index')->with('success', "Role Updated Successfully");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        $role->delete();
        return to_route('back.roles.index')->with('success', "Role Deleted Successfully");
    }
}
