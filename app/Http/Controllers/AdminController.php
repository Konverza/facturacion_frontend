<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

use App\Mail\UsuarioMail;
use App\Models\Plan;
use App\Models\BusinessPlan;
use App\Models\Business;
use App\Models\BusinessUser;
use App\Models\User;

class AdminController extends Controller
{
    public function dashboard()
    {
        $octopus_statistics = Http::get(env('OCTOPUS_API_URL') . '/dtes/statistics/');
        $octopus_statistics = $octopus_statistics->json();
        $clientes = Business::with('plan')->get();

        return view('admin.dashboard', compact('octopus_statistics', 'clientes'));
    }

    public function negocios()
    {
        $clientes = Business::with('plan')->get();
        return view('admin.negocios', compact('clientes'));
    }

    public function negocios_json()
    {
        $clientes = Business::select('nit', 'nombre')->get();
        $clientesJson = $clientes->map(function ($item) {
            return [
                'value' => $item->id,
                'label' => $item->nit . " - " . $item->nombre
            ];
        });
        return response()->json($clientesJson);
    }

    public function new_negocio()
    {
        $planes = Plan::all();
        $tipo_establecimiento = DB::table('cat_009')->select('codigo', 'valores')->get();
        return view('admin.new_negocio', compact('planes', 'tipo_establecimiento'));
    }

    public function store_negocio(Request $request)
    {
        $actividad_economica = $request->actividad_economica;
        $actividad_economica = explode(" - ", $actividad_economica);
        $codActividad = $actividad_economica[0];
        $descActividad = ucwords($actividad_economica[1]);

        $de_response = Http::post(env('OCTOPUS_API_URL') . '/datos_empresa', [
            'nombre' => $request->nombre,
            'nit' => $request->nit,
            'nrc' => $request->nrc,
            'codActividad' => $codActividad,
            'descActividad' => $descActividad,
            'nombreComercial' => $request->nombreComercial,
            'tipoEstablecimiento' => $request->tipoEstablecimiento,
            'departamento' => $request->departamento,
            'municipio' => $request->municipio,
            'complemento' => $request->complemento,
            'telefono' => $request->telefono,
            'correo' => $request->correo,
            'codEstable' => $request->codEstable,
            'codEstableMH' => $request->codEstableMH,
            'codPuntoVenta' => $request->codPuntoVenta,
            'codPuntoVentaMH' => $request->codPuntoVentaMH,
        ]);

        if ($de_response->status() == 201) {
            $api_password = $request->api_password;
            $certificate_password = $request->certificate_password;
            $crt_file = $request->file('crt_file');

            $credenciales_multipart = [
                [
                    'name' => 'nit',
                    'contents' => $request->nit
                ],
                [
                    'name' => 'nrc',
                    'contents' => $request->nrc
                ],
                [
                    'name' => 'api_password',
                    'contents' => $api_password
                ],
                [
                    'name' => 'certificate_password',
                    'contents' => $certificate_password
                ],
                [
                    'name' => 'file',
                    'contents' => fopen($crt_file, 'r'),
                    'filename' => $crt_file->getClientOriginalName()
                ]
            ];

            $credenciales_response = Http::attach($credenciales_multipart)->post(env('OCTOPUS_API_URL') . '/credenciales');

            if ($credenciales_response->status() == 201) {
                $logo_file = $request->file('logo');
                $logo_multipart = [
                    [
                        'name' => 'nit',
                        'contents' => $request->nit
                    ],
                    [
                        'name' => 'file',
                        'contents' => fopen($logo_file, 'r'),
                        'filename' => $logo_file->getClientOriginalName()
                    ]
                ];
                $logo_response = Http::attach($logo_multipart)->post(env('OCTOPUS_API_URL') . '/credenciales/logo');

                if ($logo_response->status() == 201) {
                    $negocio = new Business();
                    $negocio->nit = $request->nit;
                    $negocio->nombre = $request->nombreComercial;
                    $negocio->plan_id = $request->plan_id;
                    $negocio->dui = $request->dui;
                    $negocio->telefono = $request->telefono_responsable;
                    $negocio->correo_responsable = $request->correo_responsable;
                    $negocio->nombre_responsable = $request->nombre_responsable;
                    $negocio->save();

                    $business_plan = new BusinessPlan();
                    $business_plan->nit = $request->nit;
                    $business_plan->plan_id = $request->plan_id;
                    $business_plan->dtes = json_encode($request->dtes);
                    $business_plan->save();

                    $password = Str::password(8, true, true, false);
                    $usuario = new User();
                    $usuario->name = $request->nombre_responsable;
                    $usuario->email = $request->correo_responsable;
                    $usuario->password = bcrypt($password);
                    $usuario->save();
                    $usuario->assignRole('negocio');

                    $business_user = new BusinessUser();
                    $business_user->user_id = $usuario->id;
                    $business_user->business_id = $negocio->id;
                    $business_user->role = 'negocio';
                    $business_user->save();

                    Mail::to($request->correo_responsable)
                        ->send(new UsuarioMail(
                            $request->nombre_responsable,
                            $request->correo_responsable,
                            $password
                        ));

                    return redirect()->route('admin.negocios')->with('success', 'Negocio registrado exitosamente');
                } else {
                    return redirect()->route('admin.negocios')->with('error', 'Error al subir el logo');
                }
            } else {
                return redirect()->route('admin.negocios')->with('error', 'Error al registrar las credenciales');
            }

        } else {
            return redirect()->route('admin.negocios')->with('error', 'Error al registrar el negocio');
        }
    }

    public function registrar_pago()
    {
        return view('admin.registrar_pago');
    }

    public function settings()
    {
        return view('admin.settings');
    }

    public function usuarios()
    {
        $usuarios = User::with('roles')->get();
        $roles = \Spatie\Permission\Models\Role::all();
        return view('admin.usuarios', compact('usuarios', 'roles'));
    }

    public function registrar_usuario(Request $request)
    {
        $usuario = new User();
        $usuario->name = $request->name;
        $usuario->email = $request->correo;
        $usuario->password = bcrypt($request->password);
        $usuario->save();

        if($request->tipoUsuario == "2")
        {
            $usuario->assignRole($request->userType);
            $business_user = new BusinessUser();
            $business_user->user_id = $usuario->id;
            $request_business = explode($request->nit_negocio, " - ");
            $business = Business::where('nit', $request_business[0])->first();
            $business_user->business_id = $business->id;
            $business_user->role = $request->userType;
            $business_user->save();

            return redirect()->route('admin.usuarios')->with('success', 'Usuario registrado exitosamente');
        }
        else
        {
            $usuario->assignRole("super-admin");
            return redirect()->route('admin.usuarios')->with('success', 'Usuario registrado exitosamente');
        }
    }

}