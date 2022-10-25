<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Exceptions\MissingAbilityException;
use Laravel\Sanctum\PersonalAccessToken;
use App\Http\Resources\UserResource;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return UserResource::collection(User::paginate(10));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $creds = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'name' => 'nullable|string',
        ]);

        $user = User::where('email', $creds['email'])->first();
        if ($user) {
            return response(['error' => 1, 'message' => __('User already exists.')], 409);
        }

        $user = User::create([
            'name' => $creds['name'],
            'email' => $creds['email'],
            'password' => Hash::make($creds['password']),
        ]);

        $defaultRoleSlug = env('DEFAULT_ROLE_SLUG');
        $user->roles()->attach(Role::where('slug', $defaultRoleSlug)->first());

        return $user;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return new UserResource($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $user->name = $request->name ?? $user->name;
        $user->email = $request->email ?? $user->email;
        $user->password = $request->password ? Hash::make($request->password) : $user->password;
        $user->email_verified_at = $request->email_verified_at ?? $user->email_verified_at;

        //Comprobar si el usuario que ha iniciado sesión está actualizando su propio registro.
        $loggedInUser = $request->user();
            if ($loggedInUser->id == $user->id) {
                $user->update();
            } elseif ($loggedInUser->tokenCan('administrador') || $loggedInUser->tokenCan('super-administrador')) {
                $user->update();
            } else {
                throw new MissingAbilityException(__('Not authorized.'));
            }
        return $user;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $adminRole = Role::where('slug', 'admin')->first();
        $userRoles = $user->roles;

        if ($userRoles->contains($adminRole)) {
            //Si el usuario actual es Admin, y si solo hay un usuario tipo Admin, no se podra eliminar el usuario.
            $numberOfAdmins = Role::where('slug', 'admin')->first()->users()->count();
            if (1 == $numberOfAdmins) {
                return response(['error' => 1, 'message' => __('Create another admin user, before deleting this only admin user.')], 409);
            }
        }

        $user->delete();

        return response(['error' => 0, 'message' => __('User has been deleted.')]);
    }

    public function me(Request $request) 
    {
        return $request->user();
    }

    public function login(Request $request) 
    {
        $creds = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $creds['email'])->first();
            if (! $user || ! Hash::check($request->password, $user->password)) {
                return response(['error' => 1, 'message' => __('Invalid credentials.')], 401);
            }
       
        $roles = $user->roles->pluck('slug')->all();

        $plainTextToken = $user->createToken('compac-api-token', $roles)->plainTextToken;

        return response(['error' => 0, 'id' => $user->id, 'token' => $plainTextToken], 200);
    }

    public function logout(Request $request, User $user)
    {
        //Obtener token desde la solicitud (headers)
        $accessToken = $request->bearerToken();
        //Obtener token de acceso de la base de datos 
        $token = PersonalAccessToken::findToken($accessToken);
        //Revocar token
        $token->delete();
    }
}
