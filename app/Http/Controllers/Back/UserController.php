<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileStoreRequest;
use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    const DIRECTORY = 'back.users.';
    function __construct(){
        // $this->middleware('setPermission::add_user')->only(['create', 'store']);
        // $this->middleware('setPermission::show_user')->only(['show']);
        // $this->middleware('setPermission::edit_user')->only(['edit', 'update']);
        // $this->middleware('setPermission::destroy')->only(['destroy']);
    }
    public function index()
    {
        $users = User::all();
        return view(self::DIRECTORY.'index', get_defined_vars());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $admin = Auth::guard('admin')->user();
        // if(Gate::forUser($admin)->allows('add_user')){
        //     return view('back.users.create');
        // }
        // abort(403);

        Gate::forUser($admin)->authorize('add_user');
        return view(self::DIRECTORY.'create', get_defined_vars());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProfileStoreRequest $request)
    {
        $data = $request->validated();
        $user = User::create($data);
        return to_route(self::DIRECTORY.'index')->with('success', "User Created Successfully");
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        Gate::forUser(Auth::guard('admin')->user())->authorize('show_user');
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $admin = Auth::guard('admin')->user();

        Gate::forUser($admin)->authorize('edit_user');
        return view(self::DIRECTORY.'edit', get_defined_vars());
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserUpdateRequest $request, User $user)
    {
        $data = $request->validated();
        $user->update($data);
        return to_route(self::DIRECTORY.'index')->with('success', "User Updated Successfully");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();
        return to_route(self::DIRECTORY.'index')->with('success', 'User Deleted Successfully');
    }
}
