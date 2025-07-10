<?php

namespace App\Http\Controllers\Auth;

use App\Models\Business;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Mail\ResetPasswordEmail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        try {
            DB::beginTransaction();
            $user = User::where('email', $request->email)->first();
            if (!$user) {
                return back()->with('error', 'Error', )
                    ->with('error_message', 'Las credenciales proporcionadas no coinciden con nuestros registros.');
            }

            if (!Hash::check($request->password, $user->password)) {
                return back()->with('error', 'Error')
                    ->with('error_message', 'Credenciales Inválidas.')
                    ->withInput();
            }

            Auth::login($user, $request->filled('remember'));
            $request->session()->regenerate();
            $user->update(['last_login_at' => now()]);

            session(['ambiente' => $request->ambiente ?? env("AMBIENTE")]);

            $role = $user->roles->first()->name;
            $redirect = route('login');

            switch ($role) {
                case 'admin':
                    $redirect = route('admin.dashboard');
                    break;
                case 'business':
                case 'atm':
                    if ($user->businesses->count() > 1) {
                        $redirect = route('business.select');
                    } else {
                        $userBusiness = $user->businesses->first();
                        $business = Business::find($userBusiness->business_id);
                        if (!$business) {
                            return redirect()->route('login')
                                ->with('error', 'Error')
                                ->with('error_message', 'El negocio no existe o no tienes acceso a él.');
                        }
                        if (!$business->active) {
                            return redirect()->route('login')
                                ->with('error', 'Error')
                                ->with('error_message', 'El negocio se ha desactivado por falta de pago. Por favor, realice su pago y contacte a soporte para reactivarlo.');
                        } else {
                            session(['business' => $business->id]);
                            $redirect = route('business.dashboard');
                        }
                    }
                    break;
            }

            DB::commit();
            return redirect($redirect)->with('success_message', 'Has iniciado sesión correctamente.');
        } catch (\Exception $e) {
            logger($e);
            DB::rollBack();
            return redirect()->route("login")->with('error', 'Error')
                ->with('error_message', 'Ocurrió un error.');
        }
    }

    public function resetPassword()
    {
        return view("auth.reset-password");
    }

    public function sendEmailResetPassword(Request $request)
    {
        $user = User::where("email", $request->email)->first();
        if (!$user) {
            return back()->withErrors([
                'email' => 'No se encontró ningún usuario con ese correo electrónico.',
            ])->withInput();
        }

        DB::beginTransaction();
        try {
            $token = Str::random(60);
            $user->reset_password_token = $token;
            $user->reset_password_at = now()->addMinutes(30);
            $user->save();
            $url = route('password.change', ['token' => $token]);
            Mail::to($user->email)->send(new ResetPasswordEmail($token, $url, $user->name));
            DB::commit();
            return redirect()->route("reset-password")
                ->with("success", "Correo enviado")->with("success_message", "Se ha enviado un correo electrónico con las instrucciones para restablecer tu contraseña.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors([
                'email' => 'Ocurrió un error al intentar enviar el correo.',
            ])->withInput();
        }
    }

    public function showChangePasswordForm(Request $request)
    {
        $user = User::where("reset_password_token", $request->token)->first();

        if (
            $user->reset_password_at &&
            Carbon::now()->lessThanOrEqualTo($user->reset_password_at)
        ) {
            $token = $request->token;
            return view("auth.change-password", compact("token"));
        }
        return redirect()
            ->route("reset-password")
            ->with("error", "Error")->with("error_message", "El token ha expirado.");
    }

    public function changePassword(Request $request)
    {
        $rules = [
            "new_password" => "required|string",
            "confirm_password" => "required|string",
            "token_password" => "required|string"
        ];

        $validated = $request->validate($rules);
        $user = User::where("reset_password_token", $validated["token_password"])->first();

        if (!$user)
            return response()->json([
                "error" => "Usuario no encontrado"
            ], 404);

        if ($validated["new_password"] === $validated["confirm_password"]) {
            DB::beginTransaction();
            try {
                $user->password = Hash::make($validated["new_password"]);
                $user->save();
                DB::commit();
                return response()->json([
                    "success" => "Contraseña actualizada correctamente"
                ], 200);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    "error" => "Error al actualizar la contraseña"
                ], 500);
            }
        } else {
            return response()->json([
                "error" => "Las contraseñas no coinciden"
            ], 400);
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
