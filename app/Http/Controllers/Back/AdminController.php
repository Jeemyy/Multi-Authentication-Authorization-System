<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminRequest;
use App\Http\Requests\AdminUpdateRequest;
use App\Models\Admin;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $admins = Admin::all();
        return view('back.admins.index', get_defined_vars());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::where('guard_name', 'admin')->get();
        return view('back.admins.create', get_defined_vars());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AdminRequest $request)
    {
        $data = $request->validated();
        $admin = Admin::create($data);
        if(isset($data['role'])){
            $admin->assignRole($data['role']);
        }
        return to_route('back.admins.index')->with('success', "Admin Created Successfully");
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Admin $admin)
    {
        // $role = Admin::find($admin->id)->roles()->first();
        // $roles = Role::where('guard_name', "admin")->get();
        $roles = Role::all();
        return view('back.admins.edit', get_defined_vars());
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AdminUpdateRequest $request, Admin $admin)
    {
        $data = $request->validated();
        // dd($data);
        $admin->update([
            'name' => $data['name'],
            'email' => $data['email'],
        ]);
        if(isset($data['role'])){ $admin->syncRoles([$data['role']]); }
        return to_route('back.admins.index')->with('success', "Admin Updated Successfully");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Admin $admin)
    {
        $admin->syncRoles();
        $admin->delete();
        return to_route('back.admins.index')->with('success', "Admin Deleted Successfully");
    }
}
