<?php

namespace App\Mail;

use App\Services\OctopusService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DteMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $dte;
    protected $pdfPath;
    protected $jsonPath;
    protected $tipos_dte;
    protected $octopusService;

    /**
     * Create a new message instance.
     */
    public function __construct($dte, $pdfPath, $jsonPath)
    {
        $this->octopusService = new OctopusService();
        $this->dte = $dte;
        $this->pdfPath = $pdfPath;
        $this->jsonPath = $jsonPath;
        $this->tipos_dte = $this->octopusService->getCatalog("CAT-002");
    }
    public function build()
    {

        $this->dte["documento"] = json_decode($this->dte["documento"], true);
        $total = 0;
        $receptor = "";
        // $logo = Http::get($this->octopus_url . '/datos_empresa/nit/' . $business->nit . '/logo')->json() ?? null;
        $logo = $this->octopusService->get("/datos_empresa/nit/" . $this->dte["nit"] . "/logo");

        switch($this->dte["tipo_dte"]){
            case "01":
            case "03":
            case "11":
                $total = "$".number_format($this->dte["documento"]["resumen"]["totalPagar"], 2);
                $receptor = $this->dte["documento"]["receptor"]["nombre"];
                break;
            case "04":
            case "05":
            case "06":
                $total = "$".number_format($this->dte["documento"]["resumen"]["montoTotalOperacion"], 2);
                $receptor = $this->dte["documento"]["receptor"]["nombre"];
                break;
            case "07":
                $total = "$".$this->dte["documento"]["resumen"]["totalIVAretenido"];
                $receptor = $this->dte["documento"]["receptor"]["nombre"];
                break;
            case "14":
                $total = "$".$this->dte["documento"]["resumen"]["totalCompra"];
                $receptor = $this->dte["documento"]["sujetoExcluido"]["nombre"];
                break;
        }

        return $this->subject('Facturación Electrónica Konverza')
            ->view('mail.dte')
            ->with([
                'emisor' => $this->dte["documento"]["emisor"]["nombre"],
                'receptor' => $receptor,
                'tipo_dte' => $this->tipos_dte[$this->dte["tipo_dte"]],
                'numero_control' => $this->dte["documento"]["identificacion"]["numeroControl"],
                'codigo_generacion' => $this->dte["codGeneracion"],
                'sello_recibido' => $this->dte["selloRecibido"],
                'nit_emisor' => $this->dte["documento"]["emisor"]["nit"],
                'fecha_emision' => \Carbon\Carbon::parse($this->dte["fhProcesamiento"])->format('d/m/Y H:i:s'),
                'total' => $total,
                'logo' => $logo,
            ])
            ->attach($this->pdfPath)
            ->attach($this->jsonPath);
    }
}
