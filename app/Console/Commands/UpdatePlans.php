<?php

namespace App\Console\Commands;

use App\Models\Business;
use App\Models\BusinessPlan;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdatePlans extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-plans';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Actualiza el tipo de plan de acuerdo a mensual/anual';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $businesses = Business::all();
        foreach ($businesses as $business) {
            /** @var Business $business */
            $plan = BusinessPlan::where('business_id', $business->id)->first();
            $registroFeId = $business->ensureRegistroFeId();

            if (! $registroFeId) {
                $this->warn("Negocio {$business->nombre} no tiene un registrofe_id válido. Se omitirá la actualización del plan_type.");
                continue;
            }

            if ($plan) {
                $record_registrofe = DB::connection('registro_fe')
                    ->table('empresas')
                    ->where('id', $registroFeId)
                    ->first();
                $planType = $record_registrofe->pago;
                $plan->billing_type = $planType === 'Anual' ? 'yearly' : 'monthly';
                $plan->save();
                $this->info("Negocio {$business->nombre} actualizado con plan_type: {$planType}");
            } else {
                $this->warn("Negocio {$business->nombre} tiene un nit no válido: {$business->nit}");
            }
        }
    }
}
