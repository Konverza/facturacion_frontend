<?php

namespace App\Http\Controllers\Admin;

use App\Models\Business;
use App\Models\BusinessUser;
use App\Models\PuntoVenta;
use Bus;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('businesses.business')->get();
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
            'role' => 'required',
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

    public function userBusinesses(string $id)
    {
        $user = User::with('businesses.business')->find($id);
        if (!$user) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }

        $business_ids = [];

        $user->businesses = $user->businesses->map(function ($business) use (&$business_ids) {
            $business->default_pos = PuntoVenta::find($business->default_pos_id);
            return $business;
        });

        $user->businesses->map(function ($business) use (&$business_ids) {
            $business_ids[] = $business->business_id;
        });

        $businesses = Business::whereNotIn('id', $business_ids)->get();

        return view('admin.users.businesses', [
            'user' => $user,
            'businesses' => $businesses,
        ]);
    }

    public function storeBusinessUser(Request $request){
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'business_id' => 'required|exists:business,id',
            'default_pos_id' => 'nullable|exists:punto_ventas,id',
            'only_default_pos' => 'nullable|boolean',
            'branch_selector' => 'nullable|boolean', // Indicates if the user can select branches
        ]);

        DB::beginTransaction();
        try {
            $user = User::find($request->user_id);
            if (!$user) {
                return redirect()->back()->with('error', 'Usuario no encontrado');
            }

            BusinessUser::create([
                'business_id' => $request->business_id,
                'user_id' => $user->id,
                'role' => 'negocio', // Default role for business user
                'default_pos_id' => $request->default_pos_id,
                'only_default_pos' => $request->only_default_pos ?? false,
                'branch_selector' => $request->branch_selector ?? false,
            ]);

            DB::commit();
            return redirect()->route('admin.users.businesses', ['id' => $user->id])
                ->with('success', 'Exito')->with("success_message", "Negocio asociado correctamente");
        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Error al asociar negocio: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error')->with("error_message", "Ha ocurrido un error al asociar el negocio");
        }
    }
    public function editBusinessUser(string $id)
    {
        $businessUser = BusinessUser::with('business', 'defaultPos')->find($id);
        if (!$businessUser) {
            return redirect()->back()->with('error', 'Asociación de negocio no encontrada');
        }

        $businesses = Business::all();
        $puntosVenta = PuntoVenta::where('business_id', $businessUser->business_id)->get();

        return view('admin.users.edit_business', [
            'businessUser' => $businessUser,
            'businesses' => $businesses,
            'puntosVenta' => $puntosVenta,
        ]);
    }

    public function getBusinessUserJson(string $user_id, string $business_id)
    {
        $businessUser = BusinessUser::with(['business', 'defaultPos.sucursal'])
            ->where('user_id', $user_id)
            ->where('id', $business_id)
            ->first();
        
        if (!$businessUser) {
            return response()->json(['error' => 'Asociación no encontrada'], 404);
        }

        return response()->json([
            'id' => $businessUser->id,
            'business_id' => $businessUser->business_id,
            'default_pos_id' => $businessUser->default_pos_id,
            'sucursal_id' => $businessUser->defaultPos ? $businessUser->defaultPos->sucursal_id : null,
            'only_default_pos' => $businessUser->only_default_pos,
            'branch_selector' => $businessUser->branch_selector,
        ]);
    }

    public function updateBusinessUser(Request $request, string $user_id, string $business_id)
    {
        $request->validate([
            'business_id' => 'required|exists:business,id',
            'pos' => 'nullable|exists:punto_ventas,id',
            'only_default_pos' => 'nullable',
            'branch_selector' => 'nullable',
        ]);

        DB::beginTransaction();
        try {
            $businessUser = BusinessUser::where('user_id', $user_id)
                ->where('id', $business_id)
                ->first();
                
            if (!$businessUser) {
                return redirect()->back()->with('error', 'Asociación de negocio no encontrada');
            }

            $businessUser->business_id = $request->business_id;
            $businessUser->default_pos_id = $request->pos;
            $businessUser->only_default_pos = $request->has('only_default_pos') ? 1 : 0;
            $businessUser->branch_selector = $request->has('branch_selector') ? 1 : 0;
            $businessUser->save();

            DB::commit();
            return redirect()->route('admin.users.businesses', ['id' => $businessUser->user_id])
                ->with('success', 'Exito')->with("success_message", "Asociación de negocio actualizada correctamente");
        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Error al actualizar asociación de negocio: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error')->with("error_message", "Ha ocurrido un error al actualizar la asociación de negocio");
        }
    }

    public function destroyBusinessUser(string $user_id, string $business_id)
    {
        DB::beginTransaction();
        try {
            $businessUser = BusinessUser::find($business_id);
            if (!$businessUser) {
                return redirect()->back()->with('error', 'Asociación de negocio no encontrada');
            }

            $businessUser->delete();
            DB::commit();
            return redirect()->route('admin.users.businesses', ['id' => $businessUser->user_id])
                ->with('success', 'Exito')->with("success_message", "Asociación de negocio eliminada correctamente");
        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Error al eliminar asociación de negocio: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error')->with("error_message", "Ha ocurrido un error al eliminar la asociación de negocio");
        }
    }
}
