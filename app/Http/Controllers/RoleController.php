<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() 
    {
        return Role::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) 
    {
        $data = $request->validate([
            'name' => 'required',
            'slug' => 'required',
        ]);

        $existing = Role::where('slug', $data['slug'])->first();

        if (! $existing) {
            $role = Role::create([
                'name' => $data['name'],
                'slug' => $data['slug'],
            ]);

            return $role;
        }

        return response(['error' => 1, 'message' => __('Role already exists.')], 409);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Role  $role
     * @return \App\Models\Role $role
     */
    public function show(Role $role) 
    {
        return $role;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response|Role
     */
    public function update(Request $request, Role $role = null) 
    {
        if (! $role) {
            return response(['error' => 1, 'message' => __('Role doesnt exist.')], 404);
        }

        $role->name = $request->name ?? $role->name;

        if ($request->slug) {
            if ($role->slug != 'admin' && $role->slug != 'super-admin') {
                //No permitiremos cambiar el Slug del Admin, porque hará que las rutas sean inaccesibles debido a que fallará la verificación de la Ability.
                $role->slug = $request->slug;
            }
        }

        $role->update();

        return $role;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function destroy(Role $role) 
    {
        if ($role->slug != 'admin' && $role->slug != 'super-admin') {
            //No permitiremos eliminar el Slug del Admin, porque hará que las rutas sean inaccesibles debido a que fallará la verificación de la Ability.
            $role->delete();

            return response(['error' => 0, 'message' => __('Role has been deleted.')]);
        }

        return response(['error' => 1, 'message' => __('You cannot delete this role.')], 422);
    }
}
