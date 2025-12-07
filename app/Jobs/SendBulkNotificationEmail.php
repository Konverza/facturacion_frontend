<?php

namespace App\Jobs;

use App\Mail\CustomNotificationMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendBulkNotificationEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $jobId;
    public $subject;
    public $content;
    public $recipients;

    /**
     * Create a new job instance.
     */
    public function __construct($jobId, $subject, $content, $recipients)
    {
        $this->jobId = $jobId;
        $this->subject = $subject;
        $this->content = $content;
        $this->recipients = $recipients;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $jobs = Cache::get('notification_jobs', []);
        
        if (!isset($jobs[$this->jobId])) {
            Log::warning("Job {$this->jobId} no encontrado en caché");
            return;
        }

        // Actualizar estado a procesando
        $jobs[$this->jobId]['status'] = 'processing';
        Cache::put('notification_jobs', $jobs, now()->addDays(7));

        $sent = 0;
        $failed = 0;

        foreach ($this->recipients as $recipient) {
            try {
                Mail::to($recipient)->send(
                    new CustomNotificationMail($this->subject, $this->content)
                );
                $sent++;
            } catch (\Exception $e) {
                Log::error("Error enviando email a {$recipient}: " . $e->getMessage());
                $failed++;
            }

            // Actualizar progreso
            $jobs = Cache::get('notification_jobs', []);
            $jobs[$this->jobId]['sent'] = $sent;
            $jobs[$this->jobId]['failed'] = $failed;
            Cache::put('notification_jobs', $jobs, now()->addDays(7));

            // Pequeña pausa para no saturar el servidor de correo
            usleep(100000); // 0.1 segundos
        }

        // Actualizar estado final
        $jobs = Cache::get('notification_jobs', []);
        $jobs[$this->jobId]['status'] = 'completed';
        $jobs[$this->jobId]['completed_at'] = now()->toDateTimeString();
        Cache::put('notification_jobs', $jobs, now()->addDays(7));

        Log::info("Job {$this->jobId} completado. Enviados: {$sent}, Fallidos: {$failed}");
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("Job {$this->jobId} falló: " . $exception->getMessage());
        
        $jobs = Cache::get('notification_jobs', []);
        if (isset($jobs[$this->jobId])) {
            $jobs[$this->jobId]['status'] = 'failed';
            $jobs[$this->jobId]['error'] = $exception->getMessage();
            Cache::put('notification_jobs', $jobs, now()->addDays(7));
        }
    }
}
