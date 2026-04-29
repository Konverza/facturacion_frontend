<?php

namespace App\Console\Commands;

use App\Models\Business;
use Illuminate\Console\Command;

class SyncRegistroFeStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'business:sync-registro-fe-status
                            {--id= : ID del negocio a procesar}
                            {--nit= : NIT del negocio a procesar}
                            {--dry-run : Muestra los cambios sin actualizar Registro FE}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Actualiza el estado en Registro FE de los negocios con registrofe_id según el ambiente actual';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $targetPayload = (new Business())->getRegistroFeCompletedPayload();
        $dryRun = (bool) $this->option('dry-run');

        if ($targetPayload === null) {
            $this->error('AMBIENTE_HACIENDA no tiene un mapeo válido para sincronizar Registro FE.');

            return self::FAILURE;
        }

        $businesses = Business::query()
            ->when($this->option('id'), function ($query, $businessId) {
                $query->where('id', $businessId);
            })
            ->when($this->option('nit'), function ($query, $nit) {
                $normalizedNit = preg_replace('/\D/', '', (string) $nit);

                $query->whereRaw("REPLACE(nit, '-', '') = ?", [$normalizedNit]);
            })
            ->whereNotNull('registrofe_id')
            ->orderBy('id')
            ->get();

        if ($businesses->isEmpty()) {
            $this->warn('No se encontraron negocios con registrofe_id para procesar.');

            return self::SUCCESS;
        }

        $this->info(sprintf(
            'Sincronizando Registro FE con estado "%s" y etapa "%s"%s.',
            $targetPayload['estado'],
            $targetPayload['etapa'],
            $dryRun ? ' en modo simulación' : ''
        ));

        $processed = 0;
        $updated = 0;
        $failed = 0;

        foreach ($businesses as $business) {
            /** @var Business $business */
            ++$processed;

            try {
                if ($dryRun) {
                    ++$updated;
                    $this->line("[DRY-RUN] Negocio #{$business->id} ({$business->nombre}) actualizaría registrofe_id {$business->registrofe_id} a etapa {$targetPayload['etapa']}.");
                    continue;
                }

                if ($business->syncRegistroFeCompletedStatus()) {
                    ++$updated;
                    $this->info("[OK] Negocio #{$business->id} ({$business->nombre}) actualizado en Registro FE a etapa {$targetPayload['etapa']}.");
                    continue;
                }

                ++$failed;
                $this->warn("[SKIP] Negocio #{$business->id} ({$business->nombre}) no se pudo actualizar a etapa {$targetPayload['etapa']}. Verifica registrofe_id o el estado actual en Registro FE.");
            } catch (\Throwable $exception) {
                ++$failed;
                $this->error("[ERROR] Negocio #{$business->id} ({$business->nombre}): {$exception->getMessage()}");
            }
        }

        $this->newLine();
        $this->info("Proceso completado. Procesados: {$processed}. Actualizados: {$updated}. Fallidos: {$failed}.");

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }
}