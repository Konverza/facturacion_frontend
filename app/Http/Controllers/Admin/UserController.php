<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        $roles = [
            "admin" => [
                "name" => "Administrador",
                "icon" => "user-circle"
            ],
            "business" => [
                "name" => "Negocio",
                "icon" => "building-store"
            ],
            "atm" => [
                "name" => "Cajero",
                "icon" => "cash-register"
            ],
        ];

        $users = $users->map(function ($user) use ($roles) {
            $user->role = $user->getRoleNames()->first();
            return $user;
        });


        return view('admin.users.index',[
            'users' => $users,
            'roles' => $roles,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:4',
            'confirm_password' => 'required|same:password',
        ]);

        DB::beginTransaction();
        try {
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->save();
            $user->assignRole($request->role);

            DB::commit();
            return redirect()->route('admin.users.index')
                ->with('success', 'Exito')->with("success_message", "Usuario creado correctamente");
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('admin.users.index')
                ->with('error', 'Error')->with("error_message", "Ha ocurrido un error al crear el usuario");
        }
    }

    public function destroy(string $id)
    {
        $user = User::find($id);
        DB::beginTransaction();
        try {
            $user->delete();
            DB::commit();
            return redirect()->route('admin.users.index')
                ->with('success', 'Exito')->with("success_message", "Usuario eliminado correctamente");
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('admin.users.index')
                ->with('error', 'Error')->with("error_message", "Ha ocurrido un error al eliminar el usuario");
        }
    }

    public function edit(string $id)
    {
        $user = User::find($id);
        $user->role = $user->getRoleNames()->first();
        return response()->json($user);
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email',
            'role' => 'required',
            'password' => 'nullable|min:4',
            'confirm_password' => 'nullable|same:password',
        ]);

        $user = User::find($id);
        DB::beginTransaction();
        try {
            $user->name = $request->name;
            $user->email = $request->email;
            if ($request->password && $request->confirm_password) {
                $user->password = Hash::make($request->password);
            }
            $user->save();
            $user->syncRoles($request->role);
            DB::commit();
            return redirect()->route('admin.users.index')
                ->with('success', 'Exito')->with("success_message", "Usuario actualizado correctamente");
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('admin.users.index')
                ->with('error', 'Error')->with("error_message", "Ha ocurrido un error al actualizar el usuario");
        }
    }
}
