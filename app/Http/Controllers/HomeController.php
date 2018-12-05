<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;


class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Role::create(['name' => 'writer']);
        // Permission::create(['name' => 'writer post']);
        // $role = Role::findById(1);
        //$permission = Permission::create(['name' => 'edit post']);

       // $role->givePermissionTo($permission);
        // $permission->assignRole($role);

        // auth()->user()->givePermissionTo('edit post');
        //auth()->user()->assignRole('Designer');

       // return auth()->user()->getPermissionsViaRoles();
        return view('home');
    }

    public function get()
    {
       return 'get';
    }
}
