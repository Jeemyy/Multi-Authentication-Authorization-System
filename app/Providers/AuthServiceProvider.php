<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use App\Http\Middleware\SetPermission;
use App\Models\Admin;
use App\Models\User;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Auth\Access\Response;


class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //    Post::class => PostPolicy::class,
        User::class => UserPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        ## Create User
        // Gate::define('add_user', function(Admin $admin){
        //     // return Auth::guard('admin')->user()->hasAnyPermission(['add_user']);
        //     return $admin->hasAnyPermission('add_user');
        // });

        ## Edit User
        // Gate::define('edit_user', function(Admin $admin){
        //     // return Auth::guard('admin')->user()->hasAnyPermission('edit_user');
        //     return $admin->hasAnyPermission('edit_user')? Response::allow():
        //     Response::deny('Somthing Woring');
        // });

        ## Show User
        // Gate::define('show_user', function(Admin $admin){
        //     return $admin->hasAnyPermission('show_user')? Response::allow()
        //     :Response::deny('Somthing Wroing');
        // });
    }
}
