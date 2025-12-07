<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SendBulkNotificationEmail;
use App\Models\Business;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    public function index()
    {
        // Obtener trabajos de notificación recientes
        $recentJobs = Cache::get('notification_jobs', []);
        
        return view('admin.notifications.index', [
            'recentJobs' => collect($recentJobs)->sortByDesc('created_at')->take(10)
        ]);
    }

    public function create()
    {
        // Obtener lista de clientes (negocios)
        $businesses = Business::select('id', 'nombre', 'nit', 'correo_responsable')
            ->orderBy('nombre')
            ->get();

        // Obtener lista de usuarios
        $users = User::select('id', 'name', 'email')
            ->orderBy('name')
            ->get();

        return view('admin.notifications.create', [
            'businesses' => $businesses,
            'users' => $users
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'recipient_type' => 'required|in:businesses,users,custom',
            'recipients' => 'required|array|min:1',
            'recipients.*' => 'required|email'
        ]);

        try {
            // Generar un ID único para este trabajo
            $jobId = 'notification_' . time() . '_' . uniqid();
            
            // Guardar información del trabajo en caché
            $jobData = [
                'id' => $jobId,
                'subject' => $request->subject,
                'total' => count($request->recipients),
                'sent' => 0,
                'failed' => 0,
                'status' => 'pending',
                'created_at' => now()->toDateTimeString(),
                'created_by' => auth()->user()->name
            ];

            // Obtener trabajos existentes
            $jobs = Cache::get('notification_jobs', []);
            $jobs[$jobId] = $jobData;
            Cache::put('notification_jobs', $jobs, now()->addDays(7));

            // Despachar el trabajo a la cola
            SendBulkNotificationEmail::dispatch(
                $jobId,
                $request->subject,
                $request->content,
                $request->recipients
            );

            return response()->json([
                'success' => true,
                'message' => 'Las notificaciones están siendo enviadas en segundo plano',
                'job_id' => $jobId
            ]);

        } catch (\Exception $e) {
            Log::error('Error al enviar notificaciones: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar las notificaciones: ' . $e->getMessage()
            ], 500);
        }
    }

    public function progress($jobId)
    {
        $jobs = Cache::get('notification_jobs', []);
        $job = $jobs[$jobId] ?? null;

        if (!$job) {
            return response()->json([
                'success' => false,
                'message' => 'Trabajo no encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $job
        ]);
    }

    public function getRecipients(Request $request)
    {
        $request->validate([
            'type' => 'required|in:businesses,users',
            'ids' => 'required|array|min:1'
        ]);

        $emails = [];

        if ($request->type === 'businesses') {
            $emails = Business::whereIn('id', $request->ids)
                ->whereNotNull('email')
                ->where('email', '!=', '')
                ->pluck('email')
                ->toArray();
        } else {
            $emails = User::whereIn('id', $request->ids)
                ->whereNotNull('email')
                ->where('email', '!=', '')
                ->pluck('email')
                ->toArray();
        }

        return response()->json([
            'success' => true,
            'emails' => array_values(array_unique($emails))
        ]);
    }
}
